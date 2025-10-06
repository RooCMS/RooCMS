<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      https://www.roocms.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program. If not, see https://www.gnu.org/licenses/
 */

//#########################################################
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################



/**
 * Structure Controller
 * API for site structure management
 */
class StructureController extends BaseController {

    private readonly StructureService $structureService;


    /**
     * Constructor
     */
    public function __construct(StructureService $structureService, Db $db, Request $request) {
        parent::__construct($db, $request);
        $this->structureService = $structureService;
    }


    /**
     * Get site structure tree
     * GET /api/v1/structure/tree
     * 
     * Query parameters:
     * - parent_id (int): Parent page ID (default: 0)
     * - max_level (int): Maximum nesting level (default: 0 = unlimited)
     * - navigation (flag): Only navigation pages
     * - published (flag): Only published pages
     */
    public function tree(): void {
        $this->log_request('structure_tree');

        try {
            $params = $this->get_query_params();
            
            $parent_id = max(0, (int)($params['parent_id'] ?? 0));
            $max_level = max(0, (int)($params['max_level'] ?? 0));
            $only_navigation = isset($params['navigation']);
            $only_published = isset($params['published']);

            $tree = $this->structureService->get_site_tree(
                parent_id: $parent_id,
                max_level: $max_level,
                include_children: true,
                only_published: $only_published,
                only_navigation: $only_navigation
            );

            if ($tree === null) {
                $this->error_response('Failed to load site tree', 500);
                return;
            }

            $this->json_response([
                'tree' => $tree,
                'count' => count($tree),
                'filters' => [
                    'parent_id' => $parent_id,
                    'max_level' => $max_level,
                    'only_navigation' => $only_navigation,
                    'only_published' => $only_published
                ]
            ]);

        } catch (Exception $e) {
            error_log('Structure tree error: ' . $e->getMessage());
            $this->error_response('Failed to load site tree', 500);
        }
    }


    /**
     * Get page by ID
     * GET /api/v1/structure/page/{id}
     * 
     * @param int $id Page ID
     */
    public function show_page(int $id): void {
        $this->log_request('structure_show_page', ['id' => $id]);

        if ($id <= 0) {
            $this->error_response('Invalid page ID', 400);
            return;
        }

        try {
            $page = $this->structureService->get_page_by_id($id);

            if (!$page) {
                $this->not_found_response('Page not found');
                return;
            }

            $this->json_response($page);

        } catch (Exception $e) {
            error_log('Structure show page error: ' . $e->getMessage());
            $this->error_response('Failed to load page', 500);
        }
    }


    /**
     * Get page by slug
     * GET /api/v1/structure/page/slug/{slug}
     * 
     * @param string $slug Page slug
     */
    public function show_page_by_slug(string $slug): void {
        $this->log_request('structure_show_page_by_slug', ['slug' => $slug]);

        $slug = trim($slug);
        if (empty($slug)) {
            $this->error_response('Invalid slug', 400);
            return;
        }

        try {
            $page = $this->structureService->get_page_by_slug($slug);

            if (!$page) {
                $this->not_found_response('Page not found');
                return;
            }

            $this->json_response($page);

        } catch (Exception $e) {
            error_log('Structure show page by slug error: ' . $e->getMessage());
            $this->error_response('Failed to load page', 500);
        }
    }


    /**
     * Get navigation menu
     * GET /api/v1/structure/navigation
     * 
     * Query parameters:
     * - parent_id (int): Parent page ID for submenu (default: 0)
     * - max_level (int): Maximum menu depth (default: 3)
     */
    public function navigation(): void {
        $this->log_request('structure_navigation');

        try {
            $params = $this->get_query_params();
            
            $parent_id = max(0, (int)($params['parent_id'] ?? 0));
            $max_level = min(10, max(1, (int)($params['max_level'] ?? 3))); // Limit to 10 levels max

            $navigation = $this->structureService->get_navigation_menu($parent_id, $max_level);

            $this->json_response([
                'navigation' => $navigation,
                'count' => count($navigation),
                'parent_id' => $parent_id,
                'max_level' => $max_level
            ]);

        } catch (Exception $e) {
            error_log('Structure navigation error: ' . $e->getMessage());
            $this->error_response('Failed to load navigation', 500);
        }
    }


    /**
     * Get breadcrumbs for a page
     * GET /api/v1/structure/breadcrumbs/{id}
     * 
     * @param int $id Page ID to get breadcrumbs for
     */
    public function breadcrumbs(int $id): void {
        $this->log_request('structure_breadcrumbs', ['id' => $id]);

        if ($id <= 0) {
            $this->error_response('Invalid page ID', 400);
            return;
        }

        try {
            // Load page for breadcrumbs
            $success = $this->structureService->load_current_page($id);
            if (!$success) {
                $this->not_found_response('Page not found');
                return;
            }

            $breadcrumbs = $this->structureService->get_breadcrumbs();

            $this->json_response([
                'breadcrumbs' => $breadcrumbs,
                'count' => count($breadcrumbs),
                'page_id' => $id
            ]);

        } catch (Exception $e) {
            error_log('Structure breadcrumbs error: ' . $e->getMessage());
            $this->error_response('Failed to load breadcrumbs', 500);
        }
    }


    /**
     * Get breadcrumbs for a page by slug
     * GET /api/v1/structure/breadcrumbs/slug/{slug}
     * 
     * @param string $slug Page slug to get breadcrumbs for
     */
    public function breadcrumbs_by_slug(string $slug): void {
        $this->log_request('structure_breadcrumbs_by_slug', ['slug' => $slug]);

        $slug = trim($slug);
        if (empty($slug)) {
            $this->error_response('Invalid slug', 400);
            return;
        }

        try {
            // Load page for breadcrumbs
            $success = $this->structureService->load_current_page($slug);
            if (!$success) {
                $this->not_found_response('Page not found');
                return;
            }

            $breadcrumbs = $this->structureService->get_breadcrumbs();

            $this->json_response([
                'breadcrumbs' => $breadcrumbs,
                'count' => count($breadcrumbs),
                'page_slug' => $slug
            ]);

        } catch (Exception $e) {
            error_log('Structure breadcrumbs by slug error: ' . $e->getMessage());
            $this->error_response('Failed to load breadcrumbs', 500);
        }
    }


    /**
     * Get SEO metadata for a page
     * GET /api/v1/structure/seo/{id}
     * 
     * @param int $id Page ID to get SEO data for
     */
    public function seo(int $id): void {
        $this->log_request('structure_seo', ['id' => $id]);

        if ($id <= 0) {
            $this->error_response('Invalid page ID', 400);
            return;
        }

        try {
            // Load page for SEO data
            $success = $this->structureService->load_current_page($id);
            if (!$success) {
                $this->not_found_response('Page not found');
                return;
            }

            $seo = $this->structureService->get_seo_meta();

            $this->json_response([
                'seo' => $seo,
                'page_id' => $id
            ]);

        } catch (Exception $e) {
            error_log('Structure SEO error: ' . $e->getMessage());
            $this->error_response('Failed to load SEO metadata', 500);
        }
    }


    /**
     * Get SEO metadata for a page by slug
     * GET /api/v1/structure/seo/slug/{slug}
     * 
     * @param string $slug Page slug to get SEO data for
     */
    public function seo_by_slug(string $slug): void {
        $this->log_request('structure_seo_by_slug', ['slug' => $slug]);

        $slug = trim($slug);
        if (empty($slug)) {
            $this->error_response('Invalid slug', 400);
            return;
        }

        try {
            // Load page for SEO data
            $success = $this->structureService->load_current_page($slug);
            if (!$success) {
                $this->not_found_response('Page not found');
                return;
            }

            $seo = $this->structureService->get_seo_meta();

            $this->json_response([
                'seo' => $seo,
                'page_slug' => $slug
            ]);

        } catch (Exception $e) {
            error_log('Structure SEO by slug error: ' . $e->getMessage());
            $this->error_response('Failed to load SEO metadata', 500);
        }
    }


    /**
     * Get current page information
     * GET /api/v1/structure/current
     * 
     * Query parameters:
     * - page (string|int): Page ID or slug to set as current
     */
    public function current(): void {
        $this->log_request('structure_current');

        try {
            $params = $this->get_query_params();
            $page_identifier = $params['page'] ?? null;

            // Load specific page if identifier provided
            if ($page_identifier !== null) {
                $success = $this->structureService->load_current_page($page_identifier);
                if (!$success) {
                    $this->error_response('Failed to load specified page', 400);
                    return;
                }
            }

            $current_page = $this->structureService->get_current_page();

            $this->json_response([
                'current_page' => $current_page,
                'page_identifier' => $page_identifier
            ]);

        } catch (Exception $e) {
            error_log('Structure current error: ' . $e->getMessage());
            $this->error_response('Failed to get current page', 500);
        }
    }


    /**
     * Search pages
     * GET /api/v1/structure/search
     * 
     * Query parameters:
     * - query (string): Search query
     * - type (string): Page type filter (page, feed)
     * - limit (int): Maximum results (default: 20, max: 100)
     */
    public function search(): void {
        $this->log_request('structure_search');

        try {
            $params = $this->get_query_params();
            
            $query = trim((string)($params['query'] ?? ''));
            if (empty($query)) {
                $this->error_response('Search query is required', 400);
                return;
            }

            if (strlen($query) < 2) {
                $this->error_response('Search query must be at least 2 characters', 400);
                return;
            }

            $type_filter = isset($params['type']) && in_array($params['type'], ['page', 'feed']) 
                ? $params['type'] 
                : null;

            $limit = min(100, max(1, (int)($params['limit'] ?? 20)));

            // Get all pages and filter in memory (simple implementation)
            $tree = $this->structureService->get_site_tree();
            if ($tree === null) {
                $this->error_response('Failed to search pages', 500);
                return;
            }

            $results = [];
            $count = 0;

            foreach ($tree as $page) {
                if ($count >= $limit) {
                    break;
                }

                // Type filter
                if ($type_filter && $page['page_type'] !== $type_filter) {
                    continue;
                }

                // Search in title, slug, and meta fields
                $search_fields = [
                    $page['title'],
                    $page['slug'],
                    $page['meta_title'],
                    $page['meta_description'],
                    $page['meta_keywords']
                ];

                $search_text = mb_strtolower(implode(' ', $search_fields));
                $query_lower = mb_strtolower($query);

                if (mb_strpos($search_text, $query_lower) !== false) {
                    $results[] = [
                        'id' => $page['id'],
                        'slug' => $page['slug'],
                        'title' => $page['title'],
                        'page_type' => $page['page_type'],
                        'url' => $page['url'],
                        'nav' => $page['nav'],
                        'level' => $page['level']
                    ];
                    $count++;
                }
            }

            $this->json_response([
                'results' => $results,
                'count' => count($results),
                'query' => $query,
                'type_filter' => $type_filter,
                'limit' => $limit
            ]);

        } catch (Exception $e) {
            error_log('Structure search error: ' . $e->getMessage());
            $this->error_response('Failed to search pages', 500);
        }
    }
}