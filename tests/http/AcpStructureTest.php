<?php declare(strict_types=1);

/**
 * HTTP tests for the admin site structure API (/api/v1/acp/structure*)
 *
 * Access control tests (no token / regular user) run without credentials.
 * Admin tests need ROOCMS_TEST_ADMIN_LOGIN / ROOCMS_TEST_ADMIN_PASSWORD, otherwise they are skipped.
 *
 * Every page created by a test is deleted in tearDown (children before parents).
 * The home page (id=1) and pages the tests did not create are never modified.
 */
require_once __DIR__.'/ApiTestCase.php';

class AcpStructureTest extends ApiTestCase {

    private const ADMIN_PAGE_KEYS = [
        'id', 'slug', 'parent_id', 'status', 'nav', 'title', 'meta_title', 'meta_description', 'meta_keywords',
        'noindex', 'page_type', 'sort', 'childs', 'created_at', 'updated_at', 'published_at', 'is_published',
    ];

    /** @var int[] ids of pages created by the current test */
    private array $created = [];

    private bool $cleanup_registered = false;


    // ------------------------------------------------------------------
    // helpers
    // ------------------------------------------------------------------

    /**
     * Delete everything the test created. Children have bigger ids than their parents,
     * so several passes in descending id order are enough
     */
    private function delete_created_pages(): void {
        $token = $this->token_for('admin');

        for($pass = 0; $pass < 5 && $this->created; $pass++) {
            rsort($this->created);

            foreach($this->created as $i => $id) {
                $response = $this->api('DELETE', '/v1/acp/structure/'.$id, null, $token);
                if($response['status'] === 200 || $response['status'] === 404) {
                    unset($this->created[$i]);
                }
            }

            $this->created = array_values($this->created);
        }
    }


    private function track_page(int $id): void {
        $this->created[] = $id;

        if(!$this->cleanup_registered) {
            $this->cleanup_registered = true;
            $this->on_cleanup(function() {
                $this->delete_created_pages();
            });
        }
    }


    private function admin(string $method, string $path, ?array $json = null): array {
        return $this->api($method, '/v1/acp/structure'.$path, $json, $this->token_for('admin'));
    }


    private function unique_slug(): string {
        return 'zt-'.bin2hex(random_bytes(5));
    }


    /**
     * Create a page through the API (must succeed) and register it for cleanup
     *
     * @return array page as returned by the API
     */
    private function create_page(array $overrides = []): array {
        $payload = array_merge(['slug' => $this->unique_slug(), 'title' => 'Test page'], $overrides);

        $response = $this->admin('POST', '', $payload);
        $this->assertApiSuccess($response, 201, 'create page:');

        $page = $this->data($response);
        $this->track_page((int)$page['id']);

        return $page;
    }


    private function get_page(int $id): array {
        $response = $this->admin('GET', '/'.$id);
        $this->assertApiSuccess($response, 200, 'get page '.$id.':');

        return $this->data($response);
    }


    // ------------------------------------------------------------------
    // access control (executable without admin credentials)
    // ------------------------------------------------------------------

    /**
     * @return array<int, array{0:string, 1:string, 2:?array}>
     */
    private function protected_routes(): array {
        // id 999999999 never exists: even if protection was broken nothing real can be changed
        return [
            ['GET',    '/v1/acp/structure', null],
            ['GET',    '/v1/acp/structure/1', null],
            ['POST',   '/v1/acp/structure', ['slug' => 'zt-denied', 'title' => 'x']],
            ['PUT',    '/v1/acp/structure/999999999', ['title' => 'x']],
            ['DELETE', '/v1/acp/structure/999999999', null],
            ['PATCH',  '/v1/acp/structure/999999999/status', ['status' => 'active']],
            ['PUT',    '/v1/acp/structure/reorder', ['pages' => []]],
        ];
    }


    public function testAdminRoutesRequireToken(): void {
        foreach($this->protected_routes() as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body);

            $this->assertApiError(401, $response, $method.' '.$path.' without token:');
        }
    }


    public function testAdminRoutesRejectInvalidToken(): void {
        foreach($this->protected_routes() as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body, 'not.a.valid-token');

            $this->assertApiError(401, $response, $method.' '.$path.' with bad token:');
        }
    }


    public function testAdminRoutesRejectMalformedAuthorizationHeader(): void {
        $response = $this->http('GET', '/api/v1/acp/structure', null, ['Authorization' => 'Basic dGVzdDp0ZXN0']);

        $this->assertApiError(401, $response);
    }


    public function testAdminRoutesAreForbiddenForRegularUser(): void {
        $user = $this->create_user();

        foreach($this->protected_routes() as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body, $user['token']);

            $this->assertApiError(403, $response, $method.' '.$path.' as regular user:');
        }
    }


    public function testRegularUserCannotCreatePage(): void {
        $user = $this->create_user();
        $slug = $this->unique_slug();

        $response = $this->api('POST', '/v1/acp/structure', ['slug' => $slug, 'title' => 'Should not exist'], $user['token']);
        $this->assertApiError(403, $response);

        // the page must not exist
        $this->assertApiError(404, $this->api('GET', '/v1/structure/page/slug/'.$slug));
    }


    public function testModeratorCannotUseAdminRoutes(): void {
        $token = $this->token_for('moderator');

        foreach($this->protected_routes() as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body, $token);

            $this->assertApiError(403, $response, $method.' '.$path.' as moderator:');
        }
    }


    // ------------------------------------------------------------------
    // index: listing, filters, pagination (admin)
    // ------------------------------------------------------------------

    public function testIndexShape(): void {
        $response = $this->admin('GET', '');

        $this->assertApiSuccess($response);

        $data = $this->data($response);
        $this->assertTrue(is_array($data['pages']), 'pages must be a list');
        $this->assertTrue(count($data['pages']) > 0, 'there is at least the home page');
        $this->assertSame(array_values($data['pages']), $data['pages'], 'pages must be a list');

        foreach(['total', 'limit', 'offset', 'pages', 'current_page'] as $key) {
            $this->assertTrue(isset($data['pagination'][$key]), 'pagination has no field '.$key);
        }
        $this->assertSame(50, $data['pagination']['limit'], 'default limit is 50');
        $this->assertSame(0, $data['pagination']['offset']);
        $this->assertSame(1, (int)$data['pagination']['current_page']);

        foreach(['status', 'parent_id', 'search'] as $key) {
            $this->assertTrue(array_key_exists($key, $data['filters']), 'filters has no field '.$key);
        }

        foreach($data['pages'] as $page) {
            foreach(self::ADMIN_PAGE_KEYS as $key) {
                $this->assertTrue(array_key_exists($key, $page), 'page '.($page['id'] ?? '?').' has no field '.$key);
            }
        }
    }


    public function testIndexIsSortedBySortThenId(): void {
        $pages = $this->data($this->admin('GET', '?limit=200'), 'pages');

        for($i = 1; $i < count($pages); $i++) {
            $prev = $pages[$i - 1];
            $cur = $pages[$i];
            $this->assertTrue(
                $prev['sort'] < $cur['sort'] || ($prev['sort'] === $cur['sort'] && $prev['id'] < $cur['id']),
                'pages '.$prev['id'].' and '.$cur['id'].' are not ordered by sort, id'
            );
        }
    }


    public function testIndexLimitIsCappedAndClamped(): void {
        $cases = [1000 => 200, 0 => 1, -7 => 1, 3 => 3];

        foreach($cases as $sent => $expected) {
            $response = $this->admin('GET', '?limit='.$sent);

            $this->assertApiSuccess($response, 200, 'limit='.$sent.':');
            $this->assertSame($expected, $this->data($response)['pagination']['limit'], 'limit='.$sent);
            $this->assertTrue(count($this->data($response, 'pages')) <= $expected, 'too many pages for limit='.$sent);
        }
    }


    public function testIndexNegativeOffsetIsClampedToZero(): void {
        $response = $this->admin('GET', '?offset=-10');

        $this->assertApiSuccess($response);
        $this->assertSame(0, $this->data($response)['pagination']['offset']);
    }


    public function testIndexPagination(): void {
        $this->create_page();
        $this->create_page();

        $first = $this->data($this->admin('GET', '?limit=1&offset=0'));
        $second = $this->data($this->admin('GET', '?limit=1&offset=1'));

        $this->assertSame(1, count($first['pages']));
        $this->assertSame(1, count($second['pages']));
        $this->assertTrue($first['pages'][0]['id'] !== $second['pages'][0]['id'], 'pages of different offsets must differ');
        $this->assertTrue($first['pagination']['total'] >= 3, 'home page and two created pages at least');
        $this->assertSame((int)ceil($first['pagination']['total'] / 1), (int)$first['pagination']['pages']);
        $this->assertSame(1, (int)$first['pagination']['current_page']);
        $this->assertSame(2, (int)$second['pagination']['current_page']);
    }


    public function testIndexFilterByStatus(): void {
        $draft = $this->create_page();

        foreach(['draft', 'active', 'inactive'] as $status) {
            $response = $this->admin('GET', '?limit=200&status='.$status);

            $this->assertApiSuccess($response, 200, 'status='.$status.':');
            foreach($this->data($response, 'pages') as $page) {
                $this->assertSame($status, $page['status']);
            }
        }

        $ids = array_column($this->data($this->admin('GET', '?limit=200&status=draft'), 'pages'), 'id');
        $this->assertTrue(in_array($draft['id'], $ids, true), 'new draft page must be in the draft list');
    }


    public function testIndexIgnoresUnknownStatusFilter(): void {
        $response = $this->admin('GET', '?status=bogus');

        $this->assertApiSuccess($response);
        $this->assertTrue(count($this->data($response, 'pages')) > 0, 'unknown status must be ignored, not filter everything out');
    }


    public function testIndexFilterByParent(): void {
        $parent = $this->create_page();
        $child = $this->create_page(['parent_id' => $parent['id']]);

        $response = $this->admin('GET', '?parent_id='.$parent['id']);

        $this->assertApiSuccess($response);
        $this->assertSame([$child['id']], array_column($this->data($response, 'pages'), 'id'));
        $this->assertSame(1, $this->data($response)['pagination']['total']);
    }


    public function testIndexSearchByTitleAndSlug(): void {
        $needle = 'ztneedle'.bin2hex(random_bytes(4));
        $by_title = $this->create_page(['title' => 'Title '.$needle]);
        $by_slug = $this->create_page(['slug' => 'slug-'.$needle]);
        $this->create_page(['title' => 'Unrelated page']);

        $response = $this->admin('GET', '?search='.$needle);

        $this->assertApiSuccess($response);
        $ids = array_column($this->data($response, 'pages'), 'id');
        sort($ids);
        $expected = [$by_title['id'], $by_slug['id']];
        sort($expected);
        $this->assertSame($expected, $ids);
        $this->assertSame($needle, $this->data($response)['filters']['search']);
    }


    public function testIndexSearchWithSqlWildcardsAndInjectionIsHarmless(): void {
        foreach(["' OR '1'='1", '%', '_', '\\'] as $search) {
            $response = $this->admin('GET', '?search='.rawurlencode($search));

            $this->assertTrue($response['status'] < 500, 'search "'.$search.'" got '.$response['status']);
            $this->assertTrue($response['json'] !== null);
        }
    }


    public function testIndexWithNoMatchesReturnsEmptyList(): void {
        $response = $this->admin('GET', '?search=zz'.bin2hex(random_bytes(8)));

        $this->assertApiSuccess($response);
        $this->assertSame([], $this->data($response, 'pages'));
        $this->assertSame(0, $this->data($response)['pagination']['total']);
    }


    // ------------------------------------------------------------------
    // create
    // ------------------------------------------------------------------

    public function testCreatePageWithMinimalDataUsesDefaults(): void {
        $slug = $this->unique_slug();
        $response = $this->admin('POST', '', ['slug' => $slug, 'title' => 'Minimal page']);

        $this->assertApiSuccess($response, 201);

        $page = $this->data($response);
        $this->track_page((int)$page['id']);

        foreach(self::ADMIN_PAGE_KEYS as $key) {
            $this->assertTrue(array_key_exists($key, $page), 'created page has no field '.$key);
        }
        $this->assertSame($slug, $page['slug']);
        $this->assertSame('Minimal page', $page['title']);
        $this->assertSame(1, $page['parent_id'], 'default parent is the home page');
        $this->assertSame('draft', $page['status']);
        $this->assertSame('page', $page['page_type']);
        $this->assertSame(false, $page['nav']);
        $this->assertSame(false, $page['noindex']);
        $this->assertSame(100, $page['sort']);
        $this->assertSame(0, $page['childs']);
        $this->assertSame(false, $page['is_published']);
        $this->assertTrue($page['created_at'] > 0 && $page['updated_at'] >= $page['created_at']);
    }


    public function testCreatePageWithAllFields(): void {
        $page = $this->create_page([
            'status'           => 'inactive',
            'nav'              => true,
            'title'            => 'Full page',
            'meta_title'       => 'Meta title',
            'meta_description' => 'Meta description',
            'meta_keywords'    => 'a, b, c',
            'noindex'          => true,
            'page_type'        => 'feed',
            'sort'             => 321,
        ]);

        $this->assertSame('inactive', $page['status']);
        $this->assertSame(true, $page['nav']);
        $this->assertSame('Meta title', $page['meta_title']);
        $this->assertSame('Meta description', $page['meta_description']);
        $this->assertSame('a, b, c', $page['meta_keywords']);
        $this->assertSame(true, $page['noindex']);
        $this->assertSame('feed', $page['page_type']);
        $this->assertSame(321, $page['sort']);

        // what was saved is what is read back
        $this->assertSame($page, $this->get_page($page['id']));
    }


    public function testCreateWithFutureDateIsNotPublishedYet(): void {
        $page = $this->create_page(['status' => 'active', 'published_at' => '2099-01-01 00:00:00']);

        $this->assertSame('active', $page['status']);
        $this->assertTrue($page['published_at'] > time(), 'published_at must be in the future');
        $this->assertSame(false, $page['is_published']);
    }


    public function testCreateActivePageWithoutDateIsPublished(): void {
        $page = $this->create_page(['status' => 'active']);

        $this->assertSame(true, $page['is_published']);
    }


    public function testCreateRequiresSlugAndTitle(): void {
        $cases = [
            'empty body'      => [],
            'no slug'         => ['title' => 'No slug'],
            'no title'        => ['slug' => $this->unique_slug()],
            'empty slug'      => ['slug' => '', 'title' => 'Empty slug'],
            'empty title'     => ['slug' => $this->unique_slug(), 'title' => ''],
            'blank title'     => ['slug' => $this->unique_slug(), 'title' => '   '],
            'markup title'    => ['slug' => $this->unique_slug(), 'title' => '<b></b>'],
        ];

        foreach($cases as $name => $payload) {
            $response = $this->admin('POST', '', $payload);

            if($response['status'] === 201) {
                $this->track_page((int)$this->data($response, 'id'));
            }
            $this->assertApiError(400, $response, $name.':');
        }
    }


    public function testCreateWithNonJsonBodyIsRejected(): void {
        $response = $this->http('POST', '/api/v1/acp/structure', 'slug=zt-form&title=x', [
            'Authorization' => 'Bearer '.$this->token_for('admin'),
            'Content-Type'  => 'text/plain',
        ]);

        $this->assertApiError(400, $response);
    }


    public function testCreateWithMalformedJsonIsRejected(): void {
        $response = $this->http('POST', '/api/v1/acp/structure', '{"slug": "zt-broken", "title": ', [
            'Authorization' => 'Bearer '.$this->token_for('admin'),
            'Content-Type'  => 'application/json',
        ]);

        $this->assertApiError(400, $response);
    }


    public function testCreateRejectsDuplicateSlug(): void {
        $page = $this->create_page();

        $response = $this->admin('POST', '', ['slug' => $page['slug'], 'title' => 'Duplicate']);

        if($response['status'] === 201) {
            $this->track_page((int)$this->data($response, 'id'));
        }
        $this->assertApiError(400, $response);
        $this->assertStringContains('exists', (string)$response['json']['message']);
    }


    public function testCreateRejectsHomeSlug(): void {
        $response = $this->admin('POST', '', ['slug' => 'index', 'title' => 'Second home']);

        if($response['status'] === 201) {
            $this->track_page((int)$this->data($response, 'id'));
        }
        $this->assertApiError(400, $response);
    }


    public function testCreateRejectsDeniedSlugs(): void {
        foreach(['login', 'register', 'profile', 'acp', '404', 'offline', 'verify-email', 'password-reset'] as $slug) {
            $response = $this->admin('POST', '', ['slug' => $slug, 'title' => 'Denied']);

            if($response['status'] === 201) {
                $this->track_page((int)$this->data($response, 'id'));
            }
            $this->assertApiError(400, $response, 'slug='.$slug.':');
            $this->assertStringContains('reserved', (string)$response['json']['message'], 'slug='.$slug);
        }
    }


    public function testCreateRejectsSlugsWithSlashes(): void {
        // "acp/structure", "acp/users" ... are reserved, and in general a slug is a single path segment
        foreach(['acp/structure', 'acp/users', 'a/b'] as $slug) {
            $response = $this->admin('POST', '', ['slug' => $slug, 'title' => 'Slash']);

            if($response['status'] === 201) {
                $this->track_page((int)$this->data($response, 'id'));
            }
            $this->assertApiError(400, $response, 'slug='.$slug.':');
        }
    }


    public function testCreateRejectsInvalidSlugCharacters(): void {
        foreach(['UpperCase', 'with space', 'dot.dot', 'under score!', 'привет', 'a%20b', "x'y", 'a;b'] as $slug) {
            $response = $this->admin('POST', '', ['slug' => $slug, 'title' => 'Bad slug']);

            if($response['status'] === 201) {
                $this->track_page((int)$this->data($response, 'id'));
            }
            $this->assertApiError(400, $response, 'slug="'.$slug.'":');
        }
    }


    public function testCreateAcceptsHyphensUnderscoresAndDigits(): void {
        $slug = 'zt-a_1-'.bin2hex(random_bytes(3));
        $page = $this->create_page(['slug' => $slug]);

        $this->assertSame($slug, $page['slug']);
    }


    public function testCreateSlugLengthLimit(): void {
        $max = 'zt'.bin2hex(random_bytes(4));
        $max = $max.str_repeat('a', 100 - strlen($max));
        $page = $this->create_page(['slug' => $max]);
        $this->assertSame($max, $page['slug']);

        $response = $this->admin('POST', '', ['slug' => $max.'b', 'title' => 'Too long slug']);
        if($response['status'] === 201) {
            $this->track_page((int)$this->data($response, 'id'));
        }
        $this->assertApiError(400, $response, '101 characters:');
    }


    public function testCreateRejectsInvalidEnumsAndParent(): void {
        $cases = [
            'status'    => ['status' => 'published'],
            'page_type' => ['page_type' => 'blog'],
            'parent 0'  => ['parent_id' => 0],
            'parent -1' => ['parent_id' => -1],
        ];

        foreach($cases as $name => $extra) {
            $response = $this->admin('POST', '', array_merge(['slug' => $this->unique_slug(), 'title' => 'Invalid'], $extra));

            if($response['status'] === 201) {
                $this->track_page((int)$this->data($response, 'id'));
            }
            $this->assertApiError(400, $response, $name.':');
        }
    }


    // KNOWN BUG (from code): parent_id is not checked for existence, an orphan page is created with 201 (expected 4xx)
    public function testCreateRejectsNonExistentParent(): void {
        $response = $this->admin('POST', '', ['slug' => $this->unique_slug(), 'title' => 'Orphan', 'parent_id' => 999999999]);

        if($response['status'] === 201) {
            $this->track_page((int)$this->data($response, 'id'));
        }
        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'expected 4xx for a missing parent, got '.$response['status']);
    }


    // POSSIBLE BUG (from code): a non-string slug reaches validate_slug(string) and raises TypeError (expected 400, not 500)
    public function testCreateWithArraySlugIsClientError(): void {
        $response = $this->admin('POST', '', ['slug' => ['a'], 'title' => 'Array slug']);

        if($response['status'] === 201) {
            $this->track_page((int)$this->data($response, 'id'));
        }
        $this->assertApiError(400, $response);
    }


    public function testCreateStripsTagsFromTextFields(): void {
        $page = $this->create_page([
            'title'            => '<b>Bold</b> title<script>alert(1)</script>',
            'meta_title'       => '<i>Meta</i> title',
            'meta_description' => '<img src=x onerror=alert(1)>Description',
            'meta_keywords'    => '<u>k1</u>, k2',
        ]);

        foreach(['title', 'meta_title', 'meta_description', 'meta_keywords'] as $field) {
            $this->assertStringNotContains('<', $page[$field], $field.' must not contain tags');
            $this->assertStringNotContains('>', $page[$field], $field.' must not contain tags');
        }
        $this->assertStringContains('Bold', $page['title']);
        $this->assertStringContains('Description', $page['meta_description']);

        // and the stored values are clean for the public API too
        $public = $this->api('GET', '/v1/structure/page/'.$page['id'], null, $this->token_for('admin'));
        $this->assertStringNotContains('<script', $public['body']);
    }


    public function testCreateTrimsWhitespaceInTitle(): void {
        $page = $this->create_page(['title' => "   Padded title \n"]);

        $this->assertSame('Padded title', $page['title']);
    }


    public function testCreateWithTooLongTitleIsClientError(): void {
        // title column is varchar(255)
        $response = $this->admin('POST', '', ['slug' => $this->unique_slug(), 'title' => str_repeat('T', 5000)]);

        if($response['status'] === 201) {
            $this->track_page((int)$this->data($response, 'id'));
        }
        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'expected 4xx for a 5000-character title, got '.$response['status'].': '.substr($response['body'], 0, 200));
    }


    public function testCreateWithTooLongMetaFieldsIsClientError(): void {
        foreach(['meta_title', 'meta_description', 'meta_keywords'] as $field) {
            $response = $this->admin('POST', '', ['slug' => $this->unique_slug(), 'title' => 'Long meta', $field => str_repeat('M', 5000)]);

            if($response['status'] === 201) {
                $this->track_page((int)$this->data($response, 'id'));
            }
            $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, $field.': expected 4xx for 5000 characters, got '.$response['status']);
        }
    }


    public function testCreateDoesNotLeakSqlErrors(): void {
        $response = $this->admin('POST', '', ['slug' => $this->unique_slug(), 'title' => str_repeat('T', 5000)]);

        if($response['status'] === 201) {
            $this->track_page((int)$this->data($response, 'id'));
        }
        $message = (string)($response['json']['message'] ?? '');
        foreach(['SQLSTATE', 'PDOException', 'INSERT INTO', 'Data too long'] as $leak) {
            $this->assertStringNotContains($leak, $message, 'error message leaks database details');
        }
    }


    public function testCreateChildUpdatesParentChildsCount(): void {
        $parent = $this->create_page();
        $this->assertSame(0, $parent['childs']);

        $this->create_page(['parent_id' => $parent['id']]);
        $this->assertSame(1, $this->get_page($parent['id'])['childs']);

        $this->create_page(['parent_id' => $parent['id']]);
        $this->assertSame(2, $this->get_page($parent['id'])['childs']);
    }


    public function testCreatedPageAppearsInIndex(): void {
        $page = $this->create_page();

        $response = $this->admin('GET', '?search='.$page['slug']);
        $this->assertSame([$page['id']], array_column($this->data($response, 'pages'), 'id'));
    }


    // ------------------------------------------------------------------
    // show
    // ------------------------------------------------------------------

    public function testShowReturnsAdminPage(): void {
        $page = $this->create_page(['title' => 'Show me']);

        $response = $this->admin('GET', '/'.$page['id']);

        $this->assertApiSuccess($response);
        $this->assertSame($page, $this->data($response));
    }


    public function testShowHomePage(): void {
        $page = $this->get_page(1);

        $this->assertSame(1, $page['id']);
        $this->assertSame('index', $page['slug']);
    }


    public function testShowShowsDraftPagesToAdmin(): void {
        $page = $this->create_page(['status' => 'draft']);

        $this->assertSame('draft', $this->get_page($page['id'])['status']);
    }


    public function testShowMissingPageReturns404(): void {
        $this->assertApiError(404, $this->admin('GET', '/999999999'));
    }


    public function testShowZeroIdReturns400(): void {
        $this->assertApiError(400, $this->admin('GET', '/0'));
    }


    public function testShowInvalidIdFormatReturns404(): void {
        foreach(['abc', '-1', '1.5'] as $id) {
            $this->assertApiError(404, $this->admin('GET', '/'.$id), 'id='.$id.':');
        }
    }


    // ------------------------------------------------------------------
    // update
    // ------------------------------------------------------------------

    public function testUpdateChangesOnlyProvidedFields(): void {
        $page = $this->create_page(['title' => 'Before', 'meta_title' => 'Meta stays', 'sort' => 150, 'nav' => true]);

        $response = $this->admin('PUT', '/'.$page['id'], ['title' => 'After']);

        $this->assertApiSuccess($response);

        $updated = $this->data($response);
        $this->assertSame('After', $updated['title']);
        $this->assertSame($page['slug'], $updated['slug']);
        $this->assertSame('Meta stays', $updated['meta_title']);
        $this->assertSame(150, $updated['sort']);
        $this->assertSame(true, $updated['nav']);
        $this->assertSame('draft', $updated['status']);
        $this->assertSame($page['created_at'], $updated['created_at']);
        $this->assertSame($updated, $this->get_page($page['id']));
    }


    public function testUpdateAllFields(): void {
        $page = $this->create_page();
        $new_slug = $this->unique_slug();

        $response = $this->admin('PUT', '/'.$page['id'], [
            'slug'             => $new_slug,
            'status'           => 'inactive',
            'nav'              => true,
            'title'            => 'Updated',
            'meta_title'       => 'Updated meta',
            'meta_description' => 'Updated description',
            'meta_keywords'    => 'x, y',
            'noindex'          => true,
            'page_type'        => 'feed',
            'sort'             => 77,
        ]);

        $this->assertApiSuccess($response);

        $updated = $this->data($response);
        $this->assertSame($new_slug, $updated['slug']);
        $this->assertSame('inactive', $updated['status']);
        $this->assertSame(true, $updated['nav']);
        $this->assertSame('Updated', $updated['title']);
        $this->assertSame('Updated meta', $updated['meta_title']);
        $this->assertSame('Updated description', $updated['meta_description']);
        $this->assertSame('x, y', $updated['meta_keywords']);
        $this->assertSame(true, $updated['noindex']);
        $this->assertSame('feed', $updated['page_type']);
        $this->assertSame(77, $updated['sort']);
    }


    public function testUpdateCanKeepOwnSlug(): void {
        $page = $this->create_page();

        $response = $this->admin('PUT', '/'.$page['id'], ['slug' => $page['slug'], 'title' => 'Same slug']);

        $this->assertApiSuccess($response);
        $this->assertSame($page['slug'], $this->data($response, 'slug'));
    }


    public function testUpdateStripsTags(): void {
        $page = $this->create_page();

        $response = $this->admin('PUT', '/'.$page['id'], [
            'title'            => '<script>alert(1)</script>Safe title',
            'meta_description' => '<b>Safe</b> description',
        ]);

        $this->assertApiSuccess($response);
        $this->assertSame('alert(1)Safe title', $this->data($response, 'title'));
        $this->assertSame('Safe description', $this->data($response, 'meta_description'));
    }


    public function testUpdateMissingPageReturns404(): void {
        $this->assertApiError(404, $this->admin('PUT', '/999999999', ['title' => 'Ghost']));
    }


    public function testUpdateZeroIdReturns400(): void {
        $this->assertApiError(400, $this->admin('PUT', '/0', ['title' => 'Zero']));
    }


    public function testUpdateRejectsEmptyTitleAndSlug(): void {
        $page = $this->create_page(['title' => 'Keep me']);

        $this->assertApiError(400, $this->admin('PUT', '/'.$page['id'], ['title' => '']));
        $this->assertApiError(400, $this->admin('PUT', '/'.$page['id'], ['slug' => '']));

        $this->assertSame('Keep me', $this->get_page($page['id'])['title']);
        $this->assertSame($page['slug'], $this->get_page($page['id'])['slug']);
    }


    public function testUpdateRejectsInvalidValues(): void {
        $page = $this->create_page();

        $cases = [
            'status'    => ['status' => 'published'],
            'page_type' => ['page_type' => 'blog'],
            'parent 0'  => ['parent_id' => 0],
            'slug chars' => ['slug' => 'Bad Slug'],
        ];

        foreach($cases as $name => $payload) {
            $this->assertApiError(400, $this->admin('PUT', '/'.$page['id'], $payload), $name.':');
        }

        $this->assertSame($page, $this->get_page($page['id']), 'page must not change after rejected updates');
    }


    public function testUpdateRejectsDuplicateAndDeniedSlug(): void {
        $first = $this->create_page();
        $second = $this->create_page();

        $this->assertApiError(400, $this->admin('PUT', '/'.$second['id'], ['slug' => $first['slug']]), 'duplicate slug:');
        $this->assertApiError(400, $this->admin('PUT', '/'.$second['id'], ['slug' => 'index']), 'home slug:');

        foreach(['login', 'acp', '404'] as $slug) {
            $this->assertApiError(400, $this->admin('PUT', '/'.$second['id'], ['slug' => $slug]), 'denied slug '.$slug.':');
        }

        $this->assertSame($second['slug'], $this->get_page($second['id'])['slug']);
    }


    public function testUpdateMovesPageAndRecalculatesChildsCounts(): void {
        $parent_a = $this->create_page();
        $parent_b = $this->create_page();
        $child = $this->create_page(['parent_id' => $parent_a['id']]);

        $this->assertSame(1, $this->get_page($parent_a['id'])['childs']);

        $response = $this->admin('PUT', '/'.$child['id'], ['parent_id' => $parent_b['id']]);
        $this->assertApiSuccess($response);
        $this->assertSame($parent_b['id'], $this->data($response, 'parent_id'));

        $this->assertSame(0, $this->get_page($parent_a['id'])['childs']);
        $this->assertSame(1, $this->get_page($parent_b['id'])['childs']);
    }


    // KNOWN BUG (from code): a page can be made its own parent (cycle), expected 4xx
    public function testUpdateRejectsPageAsItsOwnParent(): void {
        $page = $this->create_page();

        // runs before the generic cleanup: put the page back so that it can be deleted
        $this->on_cleanup(function() use ($page) {
            $this->admin('PUT', '/'.$page['id'], ['parent_id' => 1]);
        });

        $response = $this->admin('PUT', '/'.$page['id'], ['parent_id' => $page['id']]);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'expected 4xx for parent_id = own id, got '.$response['status']);
    }


    // KNOWN BUG (from code): a page can be moved under its own descendant (cycle), expected 4xx
    public function testUpdateRejectsMovingPageUnderItsDescendant(): void {
        $parent = $this->create_page();
        $child = $this->create_page(['parent_id' => $parent['id']]);

        $this->on_cleanup(function() use ($parent) {
            $this->admin('PUT', '/'.$parent['id'], ['parent_id' => 1]);
        });

        $response = $this->admin('PUT', '/'.$parent['id'], ['parent_id' => $child['id']]);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'expected 4xx for moving a page under its child, got '.$response['status']);
    }


    // KNOWN BUG (from code): parent_id of an update is not checked for existence
    public function testUpdateRejectsNonExistentParent(): void {
        $page = $this->create_page();

        $response = $this->admin('PUT', '/'.$page['id'], ['parent_id' => 999999999]);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'expected 4xx for a missing parent, got '.$response['status']);
    }


    public function testHomePageSlugCannotBeChanged(): void {
        // rejected before anything is written
        $response = $this->admin('PUT', '/1', ['slug' => 'zt-hacked-home']);

        $this->assertApiError(400, $response);
        $this->assertSame('index', $this->get_page(1)['slug']);
    }


    public function testHomePageCannotBeMovedUnderAnotherPage(): void {
        $other = $this->create_page();

        // rejected before anything is written
        $response = $this->admin('PUT', '/1', ['parent_id' => $other['id']]);

        $this->assertApiError(400, $response);
        $this->assertTrue($this->get_page(1)['parent_id'] !== $other['id'], 'home page must keep its parent');
    }


    // ------------------------------------------------------------------
    // status
    // ------------------------------------------------------------------

    public function testChangeStatusToActivePublishesPage(): void {
        $page = $this->create_page();
        $this->assertSame(0, $page['published_at']);

        $response = $this->admin('PATCH', '/'.$page['id'].'/status', ['status' => 'active']);

        $this->assertApiSuccess($response);
        $this->assertSame($page['id'], $this->data($response, 'id'));
        $this->assertSame('active', $this->data($response, 'status'));
        $this->assertTrue(isset($this->data($response)['message']), 'message is missing');

        $stored = $this->get_page($page['id']);
        $this->assertSame('active', $stored['status']);
        $this->assertTrue($stored['published_at'] > 0, 'published_at must be set on activation');
        $this->assertSame(true, $stored['is_published']);
    }


    public function testChangeStatusThroughAllValues(): void {
        $page = $this->create_page();

        foreach(['active', 'inactive', 'draft', 'active'] as $status) {
            $this->assertApiSuccess($this->admin('PATCH', '/'.$page['id'].'/status', ['status' => $status]), 200, $status.':');
            $this->assertSame($status, $this->get_page($page['id'])['status']);
        }
    }


    public function testChangeStatusRejectsInvalidStatus(): void {
        $page = $this->create_page();

        foreach(['published', 'ACTIVE', '', 'archived'] as $status) {
            $this->assertApiError(400, $this->admin('PATCH', '/'.$page['id'].'/status', ['status' => $status]), 'status="'.$status.'":');
        }
        $this->assertApiError(400, $this->admin('PATCH', '/'.$page['id'].'/status', []), 'no status:');
        $this->assertApiError(400, $this->admin('PATCH', '/'.$page['id'].'/status', ['status' => ['active']]), 'array status:');

        $this->assertSame('draft', $this->get_page($page['id'])['status']);
    }


    public function testChangeStatusOfMissingPageReturns404(): void {
        $this->assertApiError(404, $this->admin('PATCH', '/999999999/status', ['status' => 'active']));
    }


    public function testChangeStatusWithZeroIdReturns400(): void {
        $this->assertApiError(400, $this->admin('PATCH', '/0/status', ['status' => 'active']));
    }


    // ------------------------------------------------------------------
    // public visibility of created pages
    // ------------------------------------------------------------------

    public function testActivePageIsVisiblePublicly(): void {
        $page = $this->create_page(['status' => 'active', 'nav' => true]);

        $by_id = $this->api('GET', '/v1/structure/page/'.$page['id']);
        $this->assertApiSuccess($by_id);
        $this->assertSame($page['slug'], $this->data($by_id, 'slug'));
        $this->assertSame(true, $this->data($by_id, 'is_published'));
        $this->assertSame('/'.$page['slug'], $this->data($by_id, 'url'));

        $by_slug = $this->api('GET', '/v1/structure/page/slug/'.$page['slug']);
        $this->assertApiSuccess($by_slug);
        $this->assertSame($page['id'], $this->data($by_slug, 'id'));

        $published_ids = array_column(array_values($this->data($this->api('GET', '/v1/structure/tree?published=1'), 'tree')), 'id');
        $this->assertTrue(in_array($page['id'], $published_ids, true), 'active page must be in the published tree');

        $nav_ids = array_column($this->data($this->api('GET', '/v1/structure/navigation'), 'navigation'), 'id');
        $this->assertTrue(in_array($page['id'], $nav_ids, true), 'active nav page must be in the navigation');

        $search = $this->api('GET', '/v1/structure/search?query='.$page['slug']);
        $this->assertTrue(in_array($page['id'], array_column($this->data($search, 'results'), 'id'), true), 'active page must be found by search');

        $this->assertApiSuccess($this->api('GET', '/v1/structure/seo/'.$page['id']));
        $this->assertApiSuccess($this->api('GET', '/v1/structure/breadcrumbs/slug/'.$page['slug']));
    }


    public function testActivePageWithoutNavIsNotInNavigation(): void {
        $page = $this->create_page(['status' => 'active', 'nav' => false]);

        $nav_ids = array_column($this->data($this->api('GET', '/v1/structure/navigation'), 'navigation'), 'id');
        $this->assertTrue(!in_array($page['id'], $nav_ids, true), 'page with nav=false must not be in the navigation');
    }


    public function testDraftAndInactivePagesAreNotInPublishedTreeOrNavigation(): void {
        $draft = $this->create_page(['status' => 'draft', 'nav' => true]);
        $inactive = $this->create_page(['status' => 'inactive', 'nav' => true]);

        $published_ids = array_column(array_values($this->data($this->api('GET', '/v1/structure/tree?published=1'), 'tree')), 'id');
        $nav_ids = array_column($this->data($this->api('GET', '/v1/structure/navigation'), 'navigation'), 'id');

        foreach([$draft, $inactive] as $page) {
            $this->assertTrue(!in_array($page['id'], $published_ids, true), $page['status'].' page must not be in the published tree');
            $this->assertTrue(!in_array($page['id'], $nav_ids, true), $page['status'].' page must not be in the navigation');
        }
    }


    // KNOWN BUG: GET /v1/structure/page/{id} returns a draft page to anonymous users (expected 404)
    public function testDraftPageIsHiddenFromPublicPageEndpoints(): void {
        $page = $this->create_page(['status' => 'draft']);

        $this->assertApiError(404, $this->api('GET', '/v1/structure/page/'.$page['id']), 'by id:');
        $this->assertApiError(404, $this->api('GET', '/v1/structure/page/slug/'.$page['slug']), 'by slug:');
    }


    // KNOWN BUG: inactive page is returned to anonymous users (expected 404)
    public function testInactivePageIsHiddenFromPublicPageEndpoints(): void {
        $page = $this->create_page(['status' => 'inactive']);

        $this->assertApiError(404, $this->api('GET', '/v1/structure/page/'.$page['id']), 'by id:');
        $this->assertApiError(404, $this->api('GET', '/v1/structure/page/slug/'.$page['slug']), 'by slug:');
    }


    // KNOWN BUG: public tree/search/status listings include draft pages (expected: hidden from anonymous users)
    public function testDraftPageIsHiddenFromPublicListings(): void {
        $page = $this->create_page(['status' => 'draft']);

        $tree_ids = array_column(array_values($this->data($this->api('GET', '/v1/structure/tree'), 'tree')), 'id');
        $this->assertTrue(!in_array($page['id'], $tree_ids, true), 'draft page is in the public tree');

        $search = $this->api('GET', '/v1/structure/search?query='.$page['slug']);
        $this->assertTrue(!in_array($page['id'], array_column($this->data($search, 'results'), 'id'), true), 'draft page is found by public search');

        $by_status = $this->api('GET', '/v1/structure/status/draft');
        $this->assertTrue(!in_array($page['id'], array_column($this->data($by_status, 'pages') ?? [], 'id'), true), 'draft page is listed by /structure/status/draft');
    }


    public function testPageDisappearsFromPublishedTreeWhenDeactivated(): void {
        $page = $this->create_page(['status' => 'active', 'nav' => true]);

        $this->assertApiSuccess($this->admin('PATCH', '/'.$page['id'].'/status', ['status' => 'inactive']));

        $published_ids = array_column(array_values($this->data($this->api('GET', '/v1/structure/tree?published=1'), 'tree')), 'id');
        $this->assertTrue(!in_array($page['id'], $published_ids, true));

        $nav_ids = array_column($this->data($this->api('GET', '/v1/structure/navigation'), 'navigation'), 'id');
        $this->assertTrue(!in_array($page['id'], $nav_ids, true));
    }


    public function testChildIsInBreadcrumbsUnderParent(): void {
        $parent = $this->create_page(['status' => 'active']);
        $child = $this->create_page(['status' => 'active', 'parent_id' => $parent['id']]);

        $response = $this->api('GET', '/v1/structure/breadcrumbs/'.$child['id']);

        $this->assertApiSuccess($response);
        $slugs = array_column($this->data($response, 'breadcrumbs'), 'slug');
        $this->assertSame(['index', $parent['slug'], $child['slug']], $slugs);
    }


    // ------------------------------------------------------------------
    // delete
    // ------------------------------------------------------------------

    public function testDeletePage(): void {
        $parent = $this->create_page();
        $page = $this->create_page(['parent_id' => $parent['id'], 'status' => 'active']);
        $this->assertSame(1, $this->get_page($parent['id'])['childs']);

        $response = $this->admin('DELETE', '/'.$page['id']);

        $this->assertApiSuccess($response);
        $this->assertSame($page['id'], $this->data($response, 'deleted_id'));
        $this->assertTrue(isset($this->data($response)['message']), 'message is missing');

        $this->assertApiError(404, $this->admin('GET', '/'.$page['id']));
        $this->assertApiError(404, $this->api('GET', '/v1/structure/page/'.$page['id']));
        $this->assertApiError(404, $this->api('GET', '/v1/structure/page/slug/'.$page['slug']));
        $this->assertSame(0, $this->get_page($parent['id'])['childs'], 'parent childs count must be recalculated');
    }


    public function testDeleteTwiceReturns404(): void {
        $page = $this->create_page();

        $this->assertApiSuccess($this->admin('DELETE', '/'.$page['id']));
        $this->assertApiError(404, $this->admin('DELETE', '/'.$page['id']));
    }


    public function testDeleteMissingPageReturns404(): void {
        $this->assertApiError(404, $this->admin('DELETE', '/999999999'));
    }


    public function testDeleteZeroIdReturns400(): void {
        $this->assertApiError(400, $this->admin('DELETE', '/0'));
    }


    public function testHomePageCannotBeDeleted(): void {
        $response = $this->admin('DELETE', '/1');

        $this->assertApiError(403, $response);
        $this->assertSame('index', $this->get_page(1)['slug'], 'home page must still exist');
    }


    public function testPageWithChildrenCannotBeDeleted(): void {
        $parent = $this->create_page();
        $child = $this->create_page(['parent_id' => $parent['id']]);

        $response = $this->admin('DELETE', '/'.$parent['id']);

        $this->assertApiError(400, $response);
        $this->assertStringContains('children', (string)$response['json']['message']);
        $this->assertSame($parent['id'], $this->get_page($parent['id'])['id'], 'parent must still exist');
        $this->assertSame($child['id'], $this->get_page($child['id'])['id'], 'child must still exist');

        // children first, then the parent
        $this->assertApiSuccess($this->admin('DELETE', '/'.$child['id']));
        $this->assertApiSuccess($this->admin('DELETE', '/'.$parent['id']));
    }


    public function testDeletedSlugCanBeReused(): void {
        $page = $this->create_page();
        $this->assertApiSuccess($this->admin('DELETE', '/'.$page['id']));

        $again = $this->create_page(['slug' => $page['slug']]);

        $this->assertSame($page['slug'], $again['slug']);
    }


    // ------------------------------------------------------------------
    // reorder
    // ------------------------------------------------------------------

    public function testReorderPages(): void {
        $first = $this->create_page(['sort' => 500]);
        $second = $this->create_page(['sort' => 501]);

        $response = $this->admin('PUT', '/reorder', ['pages' => [
            ['id' => $first['id'], 'sort' => 701],
            ['id' => $second['id'], 'sort' => 700],
        ]]);

        $this->assertApiSuccess($response);
        $this->assertSame(2, $this->data($response, 'updated_count'));
        $this->assertTrue(isset($this->data($response)['message']), 'message is missing');

        $this->assertSame(701, $this->get_page($first['id'])['sort']);
        $this->assertSame(700, $this->get_page($second['id'])['sort']);

        // the order is visible in the listing
        $ids = array_column($this->data($this->admin('GET', '?limit=200&search=zt-'), 'pages'), 'id');
        $this->assertTrue(array_search($second['id'], $ids, true) < array_search($first['id'], $ids, true), 'page with smaller sort must come first');
    }


    public function testReorderIsNotSwallowedByIdRoute(): void {
        // PUT /acp/structure/reorder is a different route from PUT /acp/structure/{id}
        $response = $this->admin('PUT', '/reorder', ['pages' => []]);

        $this->assertApiSuccess($response);
        $this->assertSame(0, $this->data($response, 'updated_count'));
    }


    public function testReorderRequiresPagesArray(): void {
        foreach([[], ['pages' => 'x'], ['pages' => 5], ['pages' => null]] as $payload) {
            $this->assertApiError(400, $this->admin('PUT', '/reorder', $payload), json_encode($payload).':');
        }
    }


    public function testReorderRequiresIdAndSort(): void {
        $page = $this->create_page(['sort' => 500]);

        $this->assertApiError(400, $this->admin('PUT', '/reorder', ['pages' => [['id' => $page['id']]]]), 'no sort:');
        $this->assertApiError(400, $this->admin('PUT', '/reorder', ['pages' => [['sort' => 5]]]), 'no id:');

        $this->assertSame(500, $this->get_page($page['id'])['sort']);
    }


    public function testReorderWithMissingPageChangesNothing(): void {
        $page = $this->create_page(['sort' => 500]);

        $response = $this->admin('PUT', '/reorder', ['pages' => [
            ['id' => $page['id'], 'sort' => 600],
            ['id' => 999999999, 'sort' => 1],
        ]]);

        $this->assertApiError(404, $response);
        $this->assertSame(500, $this->get_page($page['id'])['sort'], 'validation must happen before any update');
    }


    // KNOWN BUG (from code): update_page_sort() relies on rowCount() > 0; setting the same sort in the same second leaves updated_at
    // unchanged, MySQL reports 0 changed rows and the endpoint answers 500 "Failed to reorder pages" instead of 200
    public function testReorderIsIdempotent(): void {
        $page = $this->create_page(['sort' => 500]);
        $payload = ['pages' => [['id' => $page['id'], 'sort' => 650]]];

        $this->assertApiSuccess($this->admin('PUT', '/reorder', $payload), 200, 'first call:');
        $this->assertApiSuccess($this->admin('PUT', '/reorder', $payload), 200, 'repeated call with the same sort:');
    }
}
