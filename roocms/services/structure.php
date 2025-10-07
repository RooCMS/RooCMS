<?php declare(strict_types=1);
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
 * StructureService
 * Service layer for working with site structure
 * Contains business logic, validation, and error handling
 */
class StructureService {

    private Structure $structure;
    private SiteSettings $settings;


    /**
     * Constructor
     * 
     * @param Structure $structure
     * @param SiteSettings $settings
     */
    public function __construct(Structure $structure, SiteSettings $settings) {
        $this->structure = $structure;
        $this->settings = $settings;
    }


    /**
     * Get site tree with filters
     * 
     * @param int $parent_id Parent ID (default 0 - root)
     * @param int $max_level Max level of nesting (0 = no restrictions)
     * @param bool $include_children Include children
     * @param bool $only_published Only published pages
     * @param bool $only_navigation Only pages for navigation
     * @return array|null Tree structure or null on error
     */
    public function get_site_tree(int $parent_id = 0, int $max_level = 0, bool $include_children = true, bool $only_published = false, bool $only_navigation = false): ?array {
        try {
            $tree = $this->structure->load_tree($parent_id, $max_level, $include_children);
            
            if ($tree === false) {
                return null;
            }

            // Apply filters
            if ($only_published || $only_navigation) {
                $tree = $this->filter_tree($tree, $only_published, $only_navigation);
            }

            return $tree;
        } catch (Exception $e) {
            error_log('Error loading site tree: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Get page by ID with validation
     * 
     * @param int $page_id Page ID
     * @return array|null Page data or null if not found
     */
    public function get_page_by_id(int $page_id): ?array {
        if ($page_id <= 0) {
            return null;
        }

        try {
            $page = $this->structure->get_page_by_id($page_id);
            
            if ($page) {
                return $this->format_page_data($page);
            }
            
            return null;
        } catch (Exception $e) {
            error_log('Error getting page by ID ' . $page_id . ': ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Get page by slug with validation
     * 
     * @param string $slug Page slug
     * @return array|null Page data or null if not found
     */
    public function get_page_by_slug(string $slug): ?array {
        $slug = trim($slug);
        
        if (empty($slug) || !$this->validate_slug($slug)) {
            return null;
        }

        try {
            $page = $this->structure->get_page_by_slug($slug);
            
            if ($page) {
                return $this->format_page_data($page);
            }
            
            return null;
        } catch (Exception $e) {
            error_log('Error getting page by slug "' . $slug . '": ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Load page data and set as current
     * 
     * @param string|int|null $identifier Page ID or slug
     * @return bool Success
     */
    public function load_current_page(string|int|null $identifier = null): bool {
        try {
            $this->structure->load_page_data($identifier);
            return true;
        } catch (Exception $e) {
            error_log('Error loading current page: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Get data of current page
     * 
     * @return array Current page data
     */
    public function get_current_page(): array {
        try {
            $page = $this->structure->get_current_page();
            return $this->format_page_data($page);
        } catch (Exception $e) {
            error_log('Error getting current page: ' . $e->getMessage());
            return $this->get_default_page_data();
        }
    }


    /**
     * Get navigation menu
     * 
     * @param int $parent_id Parent ID (0 for main menu)
     * @param int $max_level Max level of menu
     * @return array Navigation items
     */
    public function get_navigation_menu(int $parent_id = 0, int $max_level = 3): array {
        try {
            $tree = $this->get_site_tree($parent_id, $max_level, true, true, true);
            
            if (!$tree) {
                return [];
            }

            return $this->build_navigation_array($tree);
        } catch (Exception $e) {
            error_log('Error building navigation menu: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * Get breadcrumbs for current page
     * 
     * @return array Breadcrumbs array
     */
    public function get_breadcrumbs(): array {
        try {
            $current_page = $this->structure->get_current_page();
            $breadcrumbs = [];
            
            // Add main page
            if ($current_page['id'] !== 1) {
                $home_page = $this->get_page_by_id(1);
                if ($home_page) {
                    $breadcrumbs[] = [
                        'title' => $home_page['title'],
                        'slug' => $home_page['slug'],
                        'url' => '/',
                        'is_current' => false
                    ];
                }
            }

            // Build path to current page
            $path = $this->build_page_path($current_page['id']);
            
            foreach ($path as $page) {
                if ($page['id'] === 1) continue; // Main page already added
                
                $breadcrumbs[] = [
                    'title' => $page['title'],
                    'slug' => $page['slug'],
                    'url' => '/' . $page['slug'],
                    'is_current' => $page['id'] === $current_page['id']
                ];
            }

            return $breadcrumbs;
        } catch (Exception $e) {
            error_log('Error building breadcrumbs: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * Get SEO metadata for current page
     * 
     * @return array SEO data
     */
    public function get_seo_meta(): array {
        try {
            $current = $this->structure->get_current_page();
            
            return [
                'title' => $current['meta_title'] ?: $current['title'],
                'description' => $current['meta_description'] ?: $this->settings->get_by_key('site_description'),
                'keywords' => $current['meta_keywords'],
                'noindex' => (bool)$current['noindex'],
                'canonical' => $this->build_canonical_url($current['slug']),
                'og_title' => $current['title'],
                'og_description' => $current['meta_description'] ?: $this->settings->get_by_key('site_description'),
                'og_type' => 'website'
            ];
        } catch (Exception $e) {
            error_log('Error getting SEO meta: ' . $e->getMessage());
            return $this->get_default_seo_meta();
        }
    }


    /**
     * Validate slug
     * 
     * @param string $slug Slug to check
     * @return bool Validation result
     */
    private function validate_slug(string $slug): bool {
        // Check length
        if (strlen($slug) > 255) {
            return false;
        }

        // Check allowed characters (latin, numbers, dash, underscore)
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $slug)) {
            return false;
        }

        // Slug should not start or end with a dash
        if (str_starts_with($slug, '-') || str_ends_with($slug, '-')) {
            return false;
        }

        return true;
    }


    /**
     * Filter tree of pages
     * 
     * @param array $tree Tree of pages
     * @param bool $only_published Only published
     * @param bool $only_navigation Only for navigation
     * @return array Filtered tree
     */
    private function filter_tree(array $tree, bool $only_published = false, bool $only_navigation = false): array {
        $filtered = [];

        foreach ($tree as $page) {
            // Filter by navigation
            if ($only_navigation && !$page['nav']) {
                continue;
            }

            // Filter by published status
            if ($only_published && $page['status'] !== 'active') {
                continue;
            }

            // Filter by accessibility
            if ($only_published && !$page['access']) {
                continue;
            }

            $filtered[$page['id']] = $page;
        }

        return $filtered;
    }


    /**
     * Formatting page data
     * 
     * @param array $page Raw page data
     * @return array Formatted data
     */
    private function format_page_data(array $page): array {
        return [
            'id' => (int)$page['id'],
            'parent_id' => (int)$page['parent_id'],
            'slug' => (string)$page['slug'],
            'status' => (string)($page['status'] ?? 'draft'),
            'title' => (string)$page['title'],
            'meta_title' => (string)($page['meta_title'] ?? $page['title']),
            'meta_description' => (string)($page['meta_description'] ?? ''),
            'meta_keywords' => (string)($page['meta_keywords'] ?? ''),
            'noindex' => (bool)($page['noindex'] ?? false),
            'page_type' => (string)($page['page_type'] ?? 'page'),
            'nav' => (bool)($page['nav'] ?? false),
            'sort' => (int)($page['sort'] ?? 0),
            'childs' => (int)($page['childs'] ?? 0),
            'level' => (int)($page['level'] ?? 0),
            'access' => (bool)($page['access'] ?? true),
            'created_at' => (int)($page['created_at'] ?? 0),
            'updated_at' => (int)($page['updated_at'] ?? 0),
            'published_at' => (int)($page['published_at'] ?? 0),
            'url' => $this->build_page_url($page['slug']),
            'is_published' => $this->is_page_published($page)
        ];
    }


    /**
     * Building navigation array
     * 
     * @param array $tree Tree of pages
     * @return array array navigation
     */
    private function build_navigation_array(array $tree): array {
        $navigation = [];

        foreach ($tree as $page) {
            if (!$page['nav']) continue;

            $nav_item = [
                'id' => $page['id'],
                'title' => $page['title'],
                'slug' => $page['slug'],
                'url' => $this->build_page_url($page['slug']),
                'level' => $page['level'],
                'has_children' => $page['childs'] > 0,
                'sort' => $page['sort']
            ];

            $navigation[] = $nav_item;
        }

        // Sort by sort field
        usort($navigation, fn($a, $b) => $a['sort'] <=> $b['sort']);

        return $navigation;
    }


    /**
     * Build path to page (breadcrumbs)
     * 
     * @param int $page_id Page ID
     * @return array Path to page
     */
    private function build_page_path(int $page_id): array {
        $path = [];
        $current_id = $page_id;

        // Prevent infinite loop
        $max_depth = 10; // max depth of page path
        $depth = 0;

        while ($current_id > 0 && $depth < $max_depth) {
            $page = $this->structure->get_page_by_id($current_id);
            
            if (!$page) {
                break;
            }

            array_unshift($path, $page);
            $current_id = (int)$page['parent_id'];
            $depth++;
        }

        return $path;
    }


    /**
     * Build page URL
     * 
     * @param string $slug Page slug
     * @return string Page URL
     */
    private function build_page_url(string $slug): string {
        if ($slug === 'index') {
            return '/';
        }
        
        return '/' . ltrim($slug, '/');
    }


    /**
     * Build canonical URL
     * 
     * @param string $slug Page slug
     * @return string Canonical URL
     */
    private function build_canonical_url(string $slug): string {
        $domain = $this->settings->get_by_key('site_domain');
        
        return 'https://' . $domain . $this->build_page_url($slug);
    }


    /**
     * Get default page data
     * 
     * @return array Default page data
     */
    private function get_default_page_data(): array {
        return [
            'id' => 1,
            'parent_id' => 0,
            'slug' => 'index',
            'status' => 'active',
            'title' => $this->settings->get_by_key('site_name'),
            'meta_title' => $this->settings->get_by_key('site_name'),
            'meta_description' => $this->settings->get_by_key('site_description'),
            'meta_keywords' => '',
            'noindex' => false,
            'page_type' => 'page',
            'nav' => true,
            'sort' => 1,
            'childs' => 0,
            'level' => 0,
            'access' => true,
            'created_at' => time(),
            'updated_at' => time(),
            'published_at' => time(),
            'url' => '/',
            'is_published' => true
        ];
    }


    /**
     * Get default SEO metadata
     * 
     * @return array Default SEO metadata
     */
    private function get_default_seo_meta(): array {
        return [
            'title' => $this->settings->get_by_key('site_name'),
            'description' => $this->settings->get_by_key('site_description'),
            'keywords' => '',
            'noindex' => false,
            'canonical' => $this->build_canonical_url('index'),
            'og_title' => $this->settings->get_by_key('site_name'),
            'og_description' => $this->settings->get_by_key('site_description'),
            'og_type' => 'website'
        ];
    }


    /**
     * Check if page is published
     * 
     * @param array $page Page data
     * @return bool True if published
     */
    private function is_page_published(array $page): bool {
        // Check status
        if ($page['status'] !== 'active') {
            return false;
        }

        // Check published_at timestamp
        $published_at = (int)($page['published_at'] ?? 0);
        if ($published_at > 0 && $published_at > time()) {
            return false; // Published in the future
        }

        return true;
    }


    /**
     * Get all pages for admin with filtering and pagination
     * 
     * @param array $filters Filters array
     * @param int $limit Limit results
     * @param int $offset Offset for pagination
     * @return array Result with pages and pagination info
     */
    public function get_admin_pages(array $filters = [], int $limit = 50, int $offset = 0): array {
        try {
            // Use model to get data
            $result = $this->structure->get_admin_pages($filters, $limit, $offset);

            // Format pages with business logic
            $formatted_pages = array_map([$this, 'format_admin_page_data'], $result['pages']);

            return [
                'pages' => $formatted_pages,
                'pagination' => [
                    'total' => $result['total'],
                    'limit' => min(200, max(1, $limit)),
                    'offset' => max(0, $offset),
                    'pages' => ceil($result['total'] / min(200, max(1, $limit))),
                    'current_page' => floor(max(0, $offset) / min(200, max(1, $limit))) + 1
                ]
            ];

        } catch (Exception $e) {
            error_log('Error getting admin pages: ' . $e->getMessage());
            return [
                'pages' => [],
                'pagination' => [
                    'total' => 0,
                    'limit' => $limit,
                    'offset' => $offset,
                    'pages' => 0,
                    'current_page' => 1
                ]
            ];
        }
    }


    /**
     * Get admin page by ID
     * 
     * @param int $page_id Page ID
     * @return array|null Page data or null if not found
     */
    public function get_admin_page_by_id(int $page_id): ?array {
        if ($page_id <= 0) {
            return null;
        }

        try {
            $page = $this->structure->get_admin_page_by_id($page_id);
            
            return $page ? $this->format_admin_page_data($page) : null;

        } catch (Exception $e) {
            error_log('Error getting admin page by ID ' . $page_id . ': ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Create new page
     * 
     * @param array $data Page data
     * @return array|null Created page data or null on error
     */
    public function create_page(array $data): ?array {
        try {
            // Validate required fields and business rules
            $validation_errors = $this->validate_admin_page_data($data);
            if (!empty($validation_errors)) {
                throw new Exception('Validation errors: ' . implode(', ', $validation_errors));
            }

            // Check if slug is unique (business rule)
            if ($this->structure->slug_exists($data['slug'])) {
                throw new Exception('Slug already exists');
            }

            // Use model to create page
            $page_id = $this->structure->create_page($data);

            if ($page_id) {
                // Update parent childs count (business logic)
                $parent_id = (int)($data['parent_id'] ?? 1);
                $this->structure->update_parent_childs_count($parent_id);

                // Return created page
                return $this->get_admin_page_by_id($page_id);
            }

            return null;

        } catch (Exception $e) {
            error_log('Error creating page: ' . $e->getMessage());
            throw $e; // Re-throw to let controller handle specific error messages
        }
    }


    /**
     * Update existing page
     * 
     * @param int $page_id Page ID
     * @param array $data Update data
     * @return array|null Updated page data or null on error
     */
    public function update_page(int $page_id, array $data): ?array {
        if ($page_id <= 0) {
            return null;
        }

        try {
            // Check if page exists (business rule)
            if (!$this->structure->page_exists($page_id)) {
                return null;
            }

            // Get existing page for comparison
            $existing_page = $this->structure->get_admin_page_by_id($page_id);
            if (!$existing_page) {
                return null;
            }

            // Validate data (business logic)
            $validation_errors = $this->validate_admin_page_data($data, $page_id);
            if (!empty($validation_errors)) {
                throw new Exception('Validation errors: ' . implode(', ', $validation_errors));
            }

            // Check if slug is unique (excluding current page) (business rule)
            if (isset($data['slug']) && $data['slug'] !== $existing_page['slug'] && $this->structure->slug_exists($data['slug'], $page_id)) {
                throw new Exception('Slug already exists');
            }

            // Use model to update page
            $success = $this->structure->update_page($page_id, $data);

            if ($success) {
                // Update parent childs count if parent changed (business logic)
                if (isset($data['parent_id']) && $data['parent_id'] !== $existing_page['parent_id']) {
                    $this->structure->update_parent_childs_count($existing_page['parent_id']); // Old parent
                    $this->structure->update_parent_childs_count($data['parent_id']); // New parent
                }

                return $this->get_admin_page_by_id($page_id);
            }

            return null;

        } catch (Exception $e) {
            error_log('Error updating page: ' . $e->getMessage());
            throw $e; // Re-throw to let controller handle specific error messages
        }
    }


    /**
     * Delete page
     * 
     * @param int $page_id Page ID
     * @return bool Success
     */
    public function delete_page(int $page_id): bool {
        if ($page_id <= 0) {
            return false;
        }

        // Prevent deletion of home page (business rule)
        if ($page_id === 1) {
            throw new Exception('Cannot delete home page');
        }

        try {
            // Check if page exists and get parent info
            $page = $this->structure->get_parent_info($page_id);
            if (!$page) {
                return false;
            }

            // Check if page has children (business rule)
            if ($page['childs'] > 0) {
                throw new Exception('Cannot delete page with children. Delete children first.');
            }

            // Use model to delete page
            $success = $this->structure->delete_page($page_id);

            if ($success) {
                // Update parent childs count (business logic)
                $this->structure->update_parent_childs_count($page['parent_id']);
                return true;
            }

            return false;

        } catch (Exception $e) {
            error_log('Error deleting page: ' . $e->getMessage());
            throw $e; // Re-throw to let controller handle error response
        }
    }


    /**
     * Change page status
     * 
     * @param int $page_id Page ID
     * @param string $status New status
     * @return bool Success
     */
    public function change_page_status(int $page_id, string $status): bool {
        if ($page_id <= 0 || !in_array($status, ['draft', 'active', 'inactive'])) {
            return false;
        }

        try {
            // Check if page exists (business rule)
            if (!$this->structure->page_exists($page_id)) {
                return false;
            }

            // Use model to update status
            return $this->structure->update_page_status($page_id, $status);

        } catch (Exception $e) {
            error_log('Error changing page status: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Reorder pages
     * 
     * @param array $pages Array of pages with id and sort
     * @return bool Success
     */
    public function reorder_pages(array $pages): bool {
        try {
            // Validate input data (business logic)
            foreach ($pages as $page_data) {
                if (!isset($page_data['id']) || !isset($page_data['sort'])) {
                    throw new Exception('Each page must have id and sort fields');
                }

                if (!$this->structure->page_exists((int)$page_data['id'])) {
                    throw new Exception('Page with ID ' . $page_data['id'] . ' does not exist');
                }
            }

            // Use model to update sort orders
            foreach ($pages as $page_data) {
                $success = $this->structure->update_page_sort((int)$page_data['id'], (int)$page_data['sort']);
                if (!$success) {
                    throw new Exception('Failed to update sort order for page ' . $page_data['id']);
                }
            }

            return true;

        } catch (Exception $e) {
            error_log('Error reordering pages: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Validate admin page data
     * 
     * @param array $data Input data
     * @param int|null $exclude_id ID to exclude from slug validation
     * @return array Validation errors
     */
    private function validate_admin_page_data(array $data, ?int $exclude_id = null): array {
        $errors = [];

        // Required fields for creation
        if ($exclude_id === null) { // Creating new page
            if (empty($data['slug'])) {
                $errors[] = 'Slug is required';
            } elseif (!$this->validate_slug_format($data['slug'])) {
                $errors[] = 'Invalid slug format';
            }

            if (empty($data['title'])) {
                $errors[] = 'Title is required';
            }
        } else { // Updating existing page
            if (isset($data['slug'])) {
                if (empty($data['slug'])) {
                    $errors[] = 'Slug cannot be empty';
                } elseif (!$this->validate_slug_format($data['slug'])) {
                    $errors[] = 'Invalid slug format';
                }
            }

            if (isset($data['title']) && empty($data['title'])) {
                $errors[] = 'Title cannot be empty';
            }
        }

        // Optional validations
        if (isset($data['status']) && !in_array($data['status'], ['draft', 'active', 'inactive'])) {
            $errors[] = 'Invalid status';
        }

        if (isset($data['page_type']) && !in_array($data['page_type'], ['page', 'feed'])) {
            $errors[] = 'Invalid page type';
        }

        if (isset($data['parent_id']) && (int)$data['parent_id'] < 0) {
            $errors[] = 'Invalid parent ID';
        }

        return $errors;
    }


    /**
     * Validate slug format
     * 
     * @param string $slug Slug to validate
     * @return bool True if valid
     */
    private function validate_slug_format(string $slug): bool {
        if (strlen($slug) > 255 || strlen($slug) < 1) {
            return false;
        }

        // Check allowed characters
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $slug)) {
            return false;
        }

        // Slug should not start or end with a dash
        if (str_starts_with($slug, '-') || str_ends_with($slug, '-')) {
            return false;
        }

        return true;
    }


    /**
     * Format page data for admin response
     * 
     * @param array $page Raw page data
     * @return array Formatted data
     */
    private function format_admin_page_data(array $page): array {
        return [
            'id' => (int)$page['id'],
            'slug' => (string)$page['slug'],
            'parent_id' => (int)$page['parent_id'],
            'status' => (string)$page['status'],
            'nav' => (bool)((string)$page['nav'] === '1'),
            'title' => (string)$page['title'],
            'meta_title' => (string)($page['meta_title'] ?? ''),
            'meta_description' => (string)($page['meta_description'] ?? ''),
            'meta_keywords' => (string)($page['meta_keywords'] ?? ''),
            'noindex' => (bool)((string)$page['noindex'] === '1'),
            'page_type' => (string)$page['page_type'],
            'sort' => (int)$page['sort'],
            'childs' => (int)$page['childs'],
            'created_at' => (int)$page['created_at'],
            'updated_at' => (int)$page['updated_at'],
            'published_at' => (int)$page['published_at'],
            'is_published' => $this->is_page_published($page)
        ];
    }

}