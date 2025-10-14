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
 * StructureManageService
 * Service layer for working with site structure management
 * Contains business logic, validation, and error handling
 */
class StructureManageService {

    use StructureCommonService;

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

            // Use model to create page (model will validate slug and check for denied slugs)
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
            // Get existing page (combines existence check and data fetch)
            $existing_page = $this->structure->get_admin_page_by_id($page_id);
            if (!$existing_page) {
                return null;
            }

            // Protect home page slug from changes
            $is_home_page = $page_id === 1 || $existing_page['slug'] === 'index';
            $is_slug_change = isset($data['slug']) && $data['slug'] !== 'index';
            if ($is_home_page && $is_slug_change) {
                throw new Exception('Cannot change slug of home page. Home page must always have slug="index"');
            }

            // Validate data
            $validation_errors = $this->validate_admin_page_data($data, $page_id);
            if (!empty($validation_errors)) {
                throw new Exception('Validation errors: ' . implode(', ', $validation_errors));
            }

            // Update page
            if (!$this->structure->update_page($page_id, $data)) {
                return null;
            }

            // Update parent child counts if parent changed
            $parent_changed = isset($data['parent_id']) && $data['parent_id'] !== $existing_page['parent_id'];
            if ($parent_changed) {
                $this->structure->update_parent_childs_count($existing_page['parent_id']); // Old parent
                $this->structure->update_parent_childs_count($data['parent_id']); // New parent
            }

            return $this->get_admin_page_by_id($page_id);

        } catch (Exception $e) {
            error_log('Error updating page: ' . $e->getMessage());
            throw $e;
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

        // Defense against deleting the main page by ID
        if ($page_id === 1) {
            throw new Exception('Cannot delete home page (ID=1)');
        }

        try {
            // Get page info (combines existence check and data fetch)
            $page_info = $this->structure->get_admin_page_by_id($page_id);
            if (!$page_info) {
                return false;
            }

            // Defense against deleting the main page by slug
            if ($page_info['slug'] === 'index') {
                throw new Exception('Cannot delete home page (slug=index)');
            }

            // Check if page has children (business rule)
            if ($page_info['childs'] > 0) {
                throw new Exception('Cannot delete page with children. Delete children first.');
            }

            // Use model to delete page
            $success = $this->structure->delete_page($page_id);

            if ($success) {
                // Use model to update parent childs count
                $this->structure->update_parent_childs_count($page_info['parent_id']);
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
            // Use get_admin_page_by_id to check existence (avoids duplicate DB call)
            if (!$this->get_admin_page_by_id($page_id)) {
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
        // Validate input data (business logic)
        foreach ($pages as $page_data) {
            if (!isset($page_data['id']) || !isset($page_data['sort'])) {
                throw new DomainException('Each page must have id and sort fields', 400);
            }

            if (!$this->structure->page_exists((int)$page_data['id'])) {
                throw new DomainException('Page with ID ' . $page_data['id'] . ' does not exist', 404);
            }
        }

        try {
            // Use model to update sort orders
            foreach ($pages as $page_data) {
                $success = $this->structure->update_page_sort((int)$page_data['id'], (int)$page_data['sort']);
                if (!$success) {
                    throw new Exception('Failed to update sort order for page ' . $page_data['id']);
                }
            }

            return true;

        } catch (Exception $e) {
            throw $e;
        }
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
     * Validate admin page data
     * 
     * @param array $data Input data
     * @param int|null $exclude_id ID to exclude from slug validation
     * @return array Validation errors
     */
    private function validate_admin_page_data(array $data, ?int $exclude_id = null): array {
        $errors = [];
        $is_creating = $exclude_id === null;

        // Required fields for creation
        if ($is_creating) {
            if (empty($data['slug'])) {
                $errors[] = 'Slug is required';
            }
            if (empty($data['title'])) {
                $errors[] = 'Title is required';
            }
        } else {
            // For updates - only validate if fields are being changed
            if (isset($data['slug']) && empty($data['slug'])) {
                $errors[] = 'Slug cannot be empty';
            }
            if (isset($data['title']) && empty($data['title'])) {
                $errors[] = 'Title cannot be empty';
            }
        }

        // Optional field validations
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