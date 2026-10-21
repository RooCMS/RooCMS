<?php declare(strict_types=1);

/**
 * HTTP tests for the public site structure API (/api/v1/structure/*)
 *
 * Read-only, no credentials: the tests never create or change data.
 * Tests that need an unpublished page discover one in the existing tree and skip when there is none
 * (the admin side, which creates its own draft pages, is covered by AcpStructureTest).
 */
require_once __DIR__.'/ApiTestCase.php';

class StructureTest extends ApiTestCase {

    private const PAGE_KEYS = [
        'id', 'parent_id', 'slug', 'status', 'title', 'meta_title', 'meta_description', 'meta_keywords',
        'noindex', 'page_type', 'nav', 'sort', 'childs', 'level', 'access', 'created_at', 'updated_at',
        'published_at', 'url', 'is_published',
    ];


    /**
     * Pages of the public tree as a list
     */
    private function tree_pages(string $query = ''): array {
        $response = $this->api('GET', '/v1/structure/tree'.$query);
        $this->assertApiSuccess($response, 200, 'tree'.$query.':');

        return array_values($this->data($response, 'tree') ?? []);
    }


    /**
     * First page of the tree with a status other than "active", or null
     */
    private function find_unpublished_page(): ?array {
        foreach($this->tree_pages() as $page) {
            if($page['status'] !== 'active') {
                return $page;
            }
        }

        return null;
    }


    // ------------------------------------------------------------------
    // tree
    // ------------------------------------------------------------------

    public function testTreeIsPublicAndHasExpectedShape(): void {
        $response = $this->api('GET', '/v1/structure/tree');

        $this->assertApiSuccess($response);
        $this->assertTrue(isset($response['json']['timestamp']), 'timestamp is missing');

        $data = $this->data($response);
        $this->assertTrue(is_array($data['tree'] ?? null), 'data.tree must be an array');
        $this->assertSame(count($data['tree']), $data['count'], 'count must equal number of tree items');

        $filters = $data['filters'] ?? [];
        $this->assertSame(0, $filters['parent_id'] ?? null);
        $this->assertSame(0, $filters['max_level'] ?? null);
        $this->assertSame(false, $filters['only_navigation'] ?? null);
        $this->assertSame(false, $filters['only_published'] ?? null);
        $this->assertTrue(array_key_exists('status_filter', $filters) && $filters['status_filter'] === null, 'status_filter must be null');
    }


    public function testTreeContainsHomePage(): void {
        $home = null;
        foreach($this->tree_pages() as $page) {
            if($page['slug'] === 'index') {
                $home = $page;
            }
        }

        $this->assertTrue($home !== null, 'tree must contain the home page (slug=index)');
        $this->assertSame(1, (int)$home['id']);
        $this->assertSame('active', $home['status']);
    }


    public function testTreeItemsHaveRequiredFields(): void {
        foreach($this->tree_pages() as $page) {
            foreach(['id', 'slug', 'parent_id', 'status', 'nav', 'title', 'page_type', 'sort', 'childs', 'level'] as $key) {
                $this->assertTrue(array_key_exists($key, $page), 'tree item '.($page['id'] ?? '?').' has no field '.$key);
            }
            $this->assertTrue(in_array($page['status'], ['draft', 'active', 'inactive'], true), 'unexpected status '.$page['status']);
            $this->assertTrue(in_array($page['page_type'], ['page', 'feed'], true), 'unexpected page_type '.$page['page_type']);
        }
    }


    public function testTreePublishedFilterReturnsOnlyActivePages(): void {
        foreach($this->tree_pages('?published=1') as $page) {
            $this->assertSame('active', $page['status'], 'page '.$page['id'].' is not active');
        }
    }


    public function testTreeNavigationFilterReturnsOnlyNavPages(): void {
        foreach($this->tree_pages('?navigation=1') as $page) {
            $this->assertTrue((string)$page['nav'] === '1' || $page['nav'] === true, 'page '.$page['id'].' is not a navigation page');
        }
    }


    public function testTreeStatusFilter(): void {
        foreach(['draft', 'active', 'inactive'] as $status) {
            $response = $this->api('GET', '/v1/structure/tree?status='.$status);
            $this->assertApiSuccess($response, 200, 'status='.$status.':');

            foreach(array_values($this->data($response, 'tree') ?? []) as $page) {
                $this->assertSame($status, $page['status']);
            }
            $this->assertSame($status, $this->data($response)['filters']['status_filter']);
        }
    }


    public function testTreeMaxLevelLimitsDepth(): void {
        foreach($this->tree_pages('?max_level=1') as $page) {
            $this->assertTrue((int)$page['level'] <= 1, 'page '.$page['id'].' has level '.$page['level']);
        }
    }


    public function testTreeNegativeParametersAreClampedToZero(): void {
        $response = $this->api('GET', '/v1/structure/tree?parent_id=-5&max_level=-3');

        $this->assertApiSuccess($response);
        $this->assertSame(0, $this->data($response)['filters']['parent_id']);
        $this->assertSame(0, $this->data($response)['filters']['max_level']);
    }


    public function testTreeOfUnknownParentIsHandled(): void {
        $response = $this->api('GET', '/v1/structure/tree?parent_id=999999999');

        // must not be a server error: either an empty tree or an explicit client error
        $this->assertTrue($response['status'] < 500, 'unknown parent_id must not produce 5xx, got '.$response['status']);
        $this->assertTrue($response['json'] !== null, 'response must be JSON');
    }


    public function testTreeSqlInjectionAttemptsAreHarmless(): void {
        $response = $this->api('GET', '/v1/structure/tree?parent_id='.rawurlencode("1' OR '1'='1").'&status='.rawurlencode("draft' OR 1=1 --"));

        $this->assertTrue($response['status'] < 500, 'got '.$response['status']);
        $this->assertTrue($response['json'] !== null);
    }


    // KNOWN BUG: GET /v1/structure/tree is public and returns draft/inactive pages (unpublished content leaks)
    public function testTreeWithoutFiltersDoesNotExposeUnpublishedPages(): void {
        $page = $this->find_unpublished_page();
        if($page === null) {
            $this->skip('no draft/inactive pages on the server');
        }

        $this->fail('public tree exposes '.$page['status'].' page id='.$page['id'].' slug='.$page['slug']);
    }


    // ------------------------------------------------------------------
    // page by id / slug
    // ------------------------------------------------------------------

    public function testShowHomePageById(): void {
        $response = $this->api('GET', '/v1/structure/page/1');

        $this->assertApiSuccess($response);

        $page = $this->data($response);
        foreach(self::PAGE_KEYS as $key) {
            $this->assertTrue(array_key_exists($key, $page), 'page has no field '.$key);
        }
        $this->assertSame(1, $page['id']);
        $this->assertSame('index', $page['slug']);
        $this->assertSame('/', $page['url']);
        $this->assertTrue(is_bool($page['nav']) && is_bool($page['noindex']) && is_bool($page['is_published']), 'flags must be booleans');
    }


    public function testShowPageByIdAndSlugReturnSamePage(): void {
        $by_id = $this->api('GET', '/v1/structure/page/1');
        $by_slug = $this->api('GET', '/v1/structure/page/slug/index');

        $this->assertApiSuccess($by_id);
        $this->assertApiSuccess($by_slug);
        $this->assertSame($this->data($by_id), $this->data($by_slug));
    }


    public function testShowMissingPageReturns404(): void {
        $response = $this->api('GET', '/v1/structure/page/999999999');

        $this->assertApiError(404, $response);
        $this->assertSame(404, $response['json']['status_code']);
        $this->assertTrue(isset($response['json']['message']), 'error must have message');
        $this->assertTrue(isset($response['json']['timestamp']), 'error must have timestamp');
    }


    public function testShowPageWithZeroIdReturns400(): void {
        $this->assertApiError(400, $this->api('GET', '/v1/structure/page/0'));
    }


    public function testShowPageWithInvalidIdFormatDoesNotMatchRoute(): void {
        foreach(['abc', '-1', '1.5', '1abc'] as $id) {
            $response = $this->api('GET', '/v1/structure/page/'.$id);
            $this->assertApiError(404, $response, 'id='.$id.':');
        }
    }


    public function testShowMissingSlugReturns404(): void {
        $this->assertApiError(404, $this->api('GET', '/v1/structure/page/slug/no-such-page-'.bin2hex(random_bytes(4))));
    }


    public function testShowPageBySlugWithMarkupReturns404NotServerError(): void {
        $response = $this->api('GET', '/v1/structure/page/slug/'.rawurlencode('<script>alert(1)'));

        $this->assertApiError(404, $response);
    }


    public function testShowPageBySlugWithSqlInjectionReturns404(): void {
        $response = $this->api('GET', '/v1/structure/page/slug/'.rawurlencode("x' OR '1'='1"));

        $this->assertApiError(404, $response);
    }


    public function testDeniedSlugsAreNotPublicPages(): void {
        // reserved slugs ("login", "acp", ...) must never resolve to a structure page
        foreach(['login', 'register', 'profile', 'acp', '404', 'offline'] as $slug) {
            $response = $this->api('GET', '/v1/structure/page/slug/'.rawurlencode($slug));
            $this->assertApiError(404, $response, 'slug='.$slug.':');
        }
    }


    public function testSlugWithSlashDoesNotMatchRoute(): void {
        // "acp/structure" is in DENIED_SLUGS and contains a slash: the route has a single segment
        $this->assertApiError(404, $this->api('GET', '/v1/structure/page/slug/acp/structure'));
    }


    // KNOWN BUG: GET /v1/structure/page/{id} returns draft/inactive pages to anonymous users
    public function testUnpublishedPageIsNotAvailableByIdPublicly(): void {
        $page = $this->find_unpublished_page();
        if($page === null) {
            $this->skip('no draft/inactive pages on the server');
        }

        $response = $this->api('GET', '/v1/structure/page/'.$page['id']);
        $this->assertApiError(404, $response, $page['status'].' page id='.$page['id'].' must be hidden:');
    }


    // KNOWN BUG: GET /v1/structure/page/slug/{slug} returns draft/inactive pages to anonymous users
    public function testUnpublishedPageIsNotAvailableBySlugPublicly(): void {
        $page = $this->find_unpublished_page();
        if($page === null) {
            $this->skip('no draft/inactive pages on the server');
        }

        $response = $this->api('GET', '/v1/structure/page/slug/'.rawurlencode($page['slug']));
        $this->assertApiError(404, $response, $page['status'].' page slug='.$page['slug'].' must be hidden:');
    }


    // ------------------------------------------------------------------
    // navigation
    // ------------------------------------------------------------------

    public function testNavigationShape(): void {
        $response = $this->api('GET', '/v1/structure/navigation');

        $this->assertApiSuccess($response);

        $data = $this->data($response);
        $this->assertTrue(is_array($data['navigation']), 'navigation must be a list');
        $this->assertSame(count($data['navigation']), $data['count']);
        $this->assertSame(0, $data['parent_id']);
        $this->assertSame(3, $data['max_level'], 'default max_level is 3');

        foreach($data['navigation'] as $item) {
            foreach(['id', 'title', 'slug', 'url', 'level', 'has_children', 'sort'] as $key) {
                $this->assertTrue(array_key_exists($key, $item), 'navigation item has no field '.$key);
            }
            $this->assertTrue(is_bool($item['has_children']), 'has_children must be boolean');
        }
    }


    public function testNavigationIsSortedBySortField(): void {
        $items = $this->data($this->api('GET', '/v1/structure/navigation'), 'navigation');

        for($i = 1; $i < count($items); $i++) {
            $this->assertTrue($items[$i - 1]['sort'] <= $items[$i]['sort'], 'navigation must be sorted by sort');
        }
    }


    public function testNavigationContainsOnlyPublishedPages(): void {
        $published = [];
        foreach($this->tree_pages('?published=1&navigation=1') as $page) {
            $published[(int)$page['id']] = true;
        }

        foreach($this->data($this->api('GET', '/v1/structure/navigation'), 'navigation') as $item) {
            $this->assertTrue(isset($published[(int)$item['id']]), 'navigation item '.$item['id'].' is not an active navigation page');
        }
    }


    public function testNavigationMaxLevelIsClamped(): void {
        $cases = [100 => 10, 0 => 1, -5 => 1, 2 => 2];

        foreach($cases as $sent => $expected) {
            $response = $this->api('GET', '/v1/structure/navigation?max_level='.$sent);
            $this->assertApiSuccess($response, 200, 'max_level='.$sent.':');
            $this->assertSame($expected, $this->data($response)['max_level'], 'max_level='.$sent);
        }
    }


    public function testNavigationNegativeParentIsClampedToZero(): void {
        $response = $this->api('GET', '/v1/structure/navigation?parent_id=-3');

        $this->assertApiSuccess($response);
        $this->assertSame(0, $this->data($response)['parent_id']);
    }


    // ------------------------------------------------------------------
    // breadcrumbs / seo / current
    // ------------------------------------------------------------------

    public function testBreadcrumbsOfExistingPage(): void {
        $page = null;
        foreach($this->tree_pages() as $candidate) {
            if((int)$candidate['id'] !== 1 && (int)$candidate['parent_id'] === 1 && $candidate['status'] === 'active') {
                $page = $candidate;
                break;
            }
        }
        if($page === null) {
            $this->skip('no active top-level page except the home page');
        }

        $response = $this->api('GET', '/v1/structure/breadcrumbs/'.$page['id']);
        $this->assertApiSuccess($response);

        $data = $this->data($response);
        $this->assertSame((int)$page['id'], $data['page_id']);
        $this->assertSame(count($data['breadcrumbs']), $data['count']);
        $this->assertSame('index', $data['breadcrumbs'][0]['slug'], 'trail starts with the home page');

        $last = end($data['breadcrumbs']);
        $this->assertSame($page['slug'], $last['slug']);
        $this->assertTrue($last['is_current'], 'last crumb is the current page');
    }


    public function testBreadcrumbsBySlugMatchBreadcrumbsById(): void {
        $by_id = $this->api('GET', '/v1/structure/breadcrumbs/1');
        $by_slug = $this->api('GET', '/v1/structure/breadcrumbs/slug/index');

        $this->assertApiSuccess($by_id);
        $this->assertApiSuccess($by_slug);
        $this->assertSame($this->data($by_id, 'breadcrumbs'), $this->data($by_slug, 'breadcrumbs'));
        $this->assertSame('index', $this->data($by_slug, 'page_slug'));
    }


    public function testBreadcrumbsWithZeroIdReturns400(): void {
        $this->assertApiError(400, $this->api('GET', '/v1/structure/breadcrumbs/0'));
        $this->assertApiError(400, $this->api('GET', '/v1/structure/seo/0'));
    }


    // KNOWN BUG: breadcrumbs of a non-existent page return 200 with an empty list instead of 404
    public function testBreadcrumbsOfMissingPageReturn404(): void {
        $this->assertApiError(404, $this->api('GET', '/v1/structure/breadcrumbs/999999999'));
    }


    // KNOWN BUG: breadcrumbs of a non-existent slug return 200 with an empty list instead of 404
    public function testBreadcrumbsOfMissingSlugReturn404(): void {
        $this->assertApiError(404, $this->api('GET', '/v1/structure/breadcrumbs/slug/no-such-page-'.bin2hex(random_bytes(4))));
    }


    public function testSeoOfHomePage(): void {
        $response = $this->api('GET', '/v1/structure/seo/1');

        $this->assertApiSuccess($response);

        $seo = $this->data($response, 'seo');
        foreach(['title', 'description', 'keywords', 'noindex', 'canonical', 'og_title', 'og_description', 'og_type'] as $key) {
            $this->assertTrue(array_key_exists($key, $seo), 'seo has no field '.$key);
        }
        $this->assertSame(1, $this->data($response, 'page_id'));
        $this->assertTrue(is_bool($seo['noindex']), 'noindex must be boolean');
        $this->assertStringContains('http', $seo['canonical']);
        $this->assertSame('website', $seo['og_type']);
    }


    public function testSeoBySlugMatchesSeoById(): void {
        $by_id = $this->api('GET', '/v1/structure/seo/1');
        $by_slug = $this->api('GET', '/v1/structure/seo/slug/index');

        $this->assertApiSuccess($by_slug);
        $this->assertSame($this->data($by_id, 'seo'), $this->data($by_slug, 'seo'));
        $this->assertSame('index', $this->data($by_slug, 'page_slug'));
    }


    // KNOWN BUG: SEO of a non-existent page id returns 200 with the HOME page metadata instead of 404
    public function testSeoOfMissingPageReturns404(): void {
        $this->assertApiError(404, $this->api('GET', '/v1/structure/seo/999999999'));
    }


    // KNOWN BUG: SEO of a non-existent slug returns 200 with the HOME page metadata instead of 404
    public function testSeoOfMissingSlugReturns404(): void {
        $this->assertApiError(404, $this->api('GET', '/v1/structure/seo/slug/no-such-page-'.bin2hex(random_bytes(4))));
    }


    public function testCurrentWithoutParameterReturnsHomePage(): void {
        $response = $this->api('GET', '/v1/structure/current');

        $this->assertApiSuccess($response);
        $this->assertSame(1, $this->data($response)['current_page']['id']);
        $this->assertTrue(array_key_exists('page_identifier', $this->data($response)) && $this->data($response)['page_identifier'] === null, 'page_identifier must be null');
    }


    public function testCurrentWithSlugAndId(): void {
        foreach(['index', '1'] as $identifier) {
            $response = $this->api('GET', '/v1/structure/current?page='.$identifier);

            $this->assertApiSuccess($response, 200, 'page='.$identifier.':');
            $this->assertSame('index', $this->data($response)['current_page']['slug']);
        }
    }


    // KNOWN BUG: /structure/current?page=<unknown> silently returns the home page (the controller intends 400 "Failed to load specified page")
    public function testCurrentWithUnknownPageIsAnError(): void {
        $response = $this->api('GET', '/v1/structure/current?page=no-such-page-'.bin2hex(random_bytes(4)));

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'expected 4xx, got '.$response['status']);
    }


    // ------------------------------------------------------------------
    // search
    // ------------------------------------------------------------------

    public function testSearchRequiresQuery(): void {
        $this->assertApiError(400, $this->api('GET', '/v1/structure/search'));
        $this->assertApiError(400, $this->api('GET', '/v1/structure/search?query='));
        $this->assertApiError(400, $this->api('GET', '/v1/structure/search?query=%20%20'));
    }


    public function testSearchRejectsTooShortQuery(): void {
        $this->assertApiError(400, $this->api('GET', '/v1/structure/search?query=a'));
    }


    public function testSearchFindsHomePageCaseInsensitively(): void {
        foreach(['index', 'INDEX', 'InDeX'] as $query) {
            $response = $this->api('GET', '/v1/structure/search?query='.$query);
            $this->assertApiSuccess($response, 200, 'query='.$query.':');

            $ids = array_column($this->data($response, 'results'), 'id');
            $this->assertTrue(in_array(1, $ids, true), 'home page must be found by "'.$query.'"');
        }
    }


    public function testSearchResultShape(): void {
        $response = $this->api('GET', '/v1/structure/search?query=index');

        $this->assertApiSuccess($response);

        $data = $this->data($response);
        $this->assertSame('index', $data['query']);
        $this->assertNull($data['type_filter']);
        $this->assertSame(20, $data['limit']);
        $this->assertSame(count($data['results']), $data['count']);

        foreach($data['results'] as $result) {
            foreach(['id', 'slug', 'title', 'page_type', 'url', 'nav', 'level'] as $key) {
                $this->assertTrue(array_key_exists($key, $result), 'result has no field '.$key);
            }
        }
    }


    // KNOWN BUG: search results have "url": null (the raw tree row has no url), the page endpoints return "/" or "/slug"
    public function testSearchResultsHaveUrl(): void {
        $results = $this->data($this->api('GET', '/v1/structure/search?query=index'), 'results');

        $this->assertTrue(count($results) > 0, 'home page must be found');
        foreach($results as $result) {
            $this->assertTrue(is_string($result['url']) && $result['url'] !== '', 'result '.$result['id'].' has no url');
        }
    }


    public function testSearchWithoutMatchesReturnsEmptyList(): void {
        $response = $this->api('GET', '/v1/structure/search?query=zz'.bin2hex(random_bytes(6)));

        $this->assertApiSuccess($response);
        $this->assertSame([], $this->data($response, 'results'));
        $this->assertSame(0, $this->data($response, 'count'));
    }


    public function testSearchTypeFilter(): void {
        $response = $this->api('GET', '/v1/structure/search?query=index&type=feed');
        $this->assertApiSuccess($response);
        $this->assertSame('feed', $this->data($response, 'type_filter'));
        foreach($this->data($response, 'results') as $result) {
            $this->assertSame('feed', $result['page_type']);
        }

        // the home page is a "page", not a "feed"
        $response = $this->api('GET', '/v1/structure/search?query=index&type=page');
        $this->assertSame('page', $this->data($response, 'type_filter'));
        $this->assertTrue(in_array(1, array_column($this->data($response, 'results'), 'id'), true));
    }


    public function testSearchUnknownTypeIsIgnored(): void {
        $response = $this->api('GET', '/v1/structure/search?query=index&type=bogus');

        $this->assertApiSuccess($response);
        $this->assertNull($this->data($response, 'type_filter'));
    }


    public function testSearchLimitIsClamped(): void {
        $cases = [0 => 1, -4 => 1, 1000 => 100, 5 => 5];

        foreach($cases as $sent => $expected) {
            $response = $this->api('GET', '/v1/structure/search?query=in&limit='.$sent);
            $this->assertApiSuccess($response, 200, 'limit='.$sent.':');
            $this->assertSame($expected, $this->data($response, 'limit'), 'limit='.$sent);
            $this->assertTrue($this->data($response, 'count') <= $expected, 'result count must not exceed limit');
        }
    }


    public function testSearchWithMarkupAndSqlIsHarmless(): void {
        foreach(['<script>alert(1)</script>', "x' OR '1'='1", '%_%_'] as $query) {
            $response = $this->api('GET', '/v1/structure/search?query='.rawurlencode($query));

            $this->assertTrue($response['status'] < 500, 'query "'.$query.'" got '.$response['status']);
            $this->assertTrue($response['json'] !== null);
            if($response['status'] === 200) {
                $this->assertStringNotContains('<script>', $response['body'], 'query must not be reflected unescaped');
            }
        }
    }


    // ------------------------------------------------------------------
    // pages by status
    // ------------------------------------------------------------------

    public function testPagesByStatusShape(): void {
        foreach(['draft', 'active', 'inactive'] as $status) {
            $response = $this->api('GET', '/v1/structure/status/'.$status);

            $this->assertApiSuccess($response, 200, 'status='.$status.':');

            $data = $this->data($response);
            $this->assertSame($status, $data['status']);
            $this->assertSame(count($data['pages']), $data['count']);
            $this->assertSame(array_values($data['pages']), $data['pages'], 'pages must be a list');
            foreach($data['pages'] as $page) {
                $this->assertSame($status, $page['status']);
            }
        }
    }


    public function testPagesByInvalidStatusReturns400(): void {
        foreach(['bogus', 'ACTIVE', 'published', '1'] as $status) {
            $this->assertApiError(400, $this->api('GET', '/v1/structure/status/'.$status), 'status='.$status.':');
        }
    }


    // KNOWN BUG: /v1/structure/status/draft and /inactive are public and list unpublished pages
    public function testUnpublishedStatusesAreNotPubliclyListed(): void {
        foreach(['draft', 'inactive'] as $status) {
            $response = $this->api('GET', '/v1/structure/status/'.$status);

            if($response['status'] === 200 && ($this->data($response, 'count') ?? 0) > 0) {
                $this->fail('public endpoint lists '.$this->data($response, 'count').' '.$status.' pages');
            }
        }
    }


    // ------------------------------------------------------------------
    // misc
    // ------------------------------------------------------------------

    public function testPublicEndpointsAreReadOnly(): void {
        foreach(['/v1/structure/tree', '/v1/structure/page/1', '/v1/structure/navigation', '/v1/structure/search'] as $path) {
            foreach(['POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
                $response = $this->api($method, $path, []);
                $this->assertTrue(in_array($response['status'], [404, 405], true), $method.' '.$path.' must not be routed, got '.$response['status']);
            }
        }
    }
}
