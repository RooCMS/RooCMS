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
 * Class Structure 
 * Provides structure-related functionality and manipulation
 */
class Structure { 

    private Db $db;

	// Content types
	public array $content_types = [
		'page' => ['title' => 'Content'],
		'feed' => ['title' => 'Feed']
	];

	// denied slugs
	public const DENIED_SLUGS = [
		'login', 				// login page
		'register', 			// register page
		'register-complete', 	// register complete page
		'password-forgot', 		// forgot password page
		'password-reset', 		// reset password page
		'verify-email', 		// verify email page
		'profile', 				// profile page
		'profile-edit', 		// profile edit page
		'offline', 				// offline page
		'403', 					// access denied
		'404', 					// page not found
		'acp', 					// admin control panel
		'acp/debug', 			// admin debug page
		'acp/settings', 		// admin settings page
		'acp/users', 			// admin users page
		'acp/structure', 		// admin structure page
	];
	
	// site tree
	public array $sitetree = [];

	// on/off
	public bool $access = true;

	// aliases
	private array $slugs= [];

	// page vars
	public int $page_id = 1;				// page sid
	public int $page_parent_id = 0;			// Parent id
	public string $page_slug = "index";		// unique slug name (was alias)
	public string $page_status = "draft";	// page status (draft, active, inactive)
	public string $page_title = "";			// title page
	public string $page_meta_title = "";	// Meta Title
	public string $page_meta_desc = "";		// Meta description
	public string $page_meta_keys = "";		// Meta keywords
	public bool $page_noindex = false;		// Meta noindex
	public string $page_type = "page";		// page type
	public bool $page_nav = true;			// show in navigation
	public int $page_sort = 0;				// sort order
	public int $page_childs = 0;			// number of children
	public int $page_created_at = 0;		// creation timestamp
	public int $page_updated_at = 0;		// update timestamp
	public int $page_published_at = 0;		// publish timestamp



	/**
	* Init class
	*/
	public function __construct(Db $db) {

        $this->db = $db;

		// load site tree
		$this->sitetree = $this->load_tree();

		if(!empty($this->sitetree)) {
			$this->update_tree_parent();
		}
	}


	/**
	 * Construct tree for site (step 1)
	 *
	 * @param int     $parent   - id for start construction tree.
	 * @param int     $maxlevel - set level for get sublevels, if param == 0, return all sublevels
	 * @param bool    $child    - set false if you dont get sublevels.
	 *
	 * @return array|false - return false if tree not construct, or return array tree.
	 */
	public function load_tree(int $parent = 0, int $maxlevel = 0, bool $child = true): array|false {

		static $use = false;
		$tree = [];

		// Get query all data from DB Делаем единичный запрос в БД собирая данные по структуре сайта.
		if(!$use) {
			$query = "SELECT
						id, slug, parent_id, status, nav, title, meta_title, meta_description, meta_keywords,
						noindex, page_type, sort, childs, created_at, updated_at, published_at
					FROM " . TABLE_STRUCTURE . " ORDER BY sort";
			
			$rows = $this->db->fetch_all($query);
			
			foreach($rows as $row) {
				// structure
				$row['level']  = 0;
				$row['parent'] = 0;
				$row['access'] = true; // On default access allowed TODO: add access control

				$tree[$row['id']] = $row;

				$this->slugs[$row['slug']] = $row['id'];
			}

			$use = true;
		}
		else {
			$tree = $this->sitetree;
		}

		// construct tree
		if(!empty($tree)) {
			return $this->construct_tree($tree, $parent, $maxlevel, $child);
		}
		else {
			return false;
		}
	}


	/**
	 * Construct tree for site (step 2)
	 *
	 * @param array   $unit     - fresh data tree
	 * @param int     $parent   - id for start construction tree.
	 * @param int     $maxlevel - set level for get sublevels, if param == 0, return all sublevels
	 * @param bool    $child    - set false if you dont get sublevels.
	 * @param int     $level    - this param for this handler. Dont use handly.
	 *
	 * @return array|null
	 */
	private function construct_tree(array $unit, int $parent = 0, int $maxlevel = 0, bool $child = true, int $level = 0): ?array {

		// create array
		$tree = [];

		foreach($unit as $i => $value) {
			if($value['parent_id'] == $parent) {
				// update level
				$value['level'] = $level;

				// add branch(s)
				$tree[$value['id']] = $value;

				// check child
				if($child && ($maxlevel == 0 || $level+1 <= $maxlevel)) {

					$subtree = $this->construct_tree($unit, $value['id'], $maxlevel, $child, $level + 1);

					if(is_array($subtree)) {
						$tree = $tree + $subtree;
					}
				}
			}
		}

		// be back
		return !empty($tree) ? $tree : null;
	}


	/**
	 * Load page data by page identifier
	 * 
	 * @param string|int|null $page_identifier Page ID or slug
	 */
	public function load_page_data(string|int|null $page_identifier = null): void {

		// const for default structure id
        // TODO: add default structure id
		if(!defined('PAGEID')) {
			define('PAGEID', 1);
		}

		// init id for vars
		$lid = PAGEID;

		if($page_identifier !== null) {
			// Check if it's numeric ID
			if(is_numeric($page_identifier) && isset($this->sitetree[(int)$page_identifier])) {
				$lid = (int)$page_identifier;
			}
			// Check if it's slug/alias
			elseif(is_string($page_identifier) && isset($this->slugs[$page_identifier])) {
				$lid = $this->slugs[$page_identifier];
			}
		}

		// set vars if page exists
		if(isset($this->sitetree[$lid])) {
			$this->set_page_vars($this->sitetree[$lid]);
		}
	}


    /**
    * Sets main class variables based on page data.
    * Sets main class variables based on page data.
    *   Identifier of structural unit
    *   Unique identifier of structural unit
    *   Title of structural unit
    *
    * @param array $data - parameters of called page
    */
	private function set_page_vars(array $data): void {

		// set vars
		$this->page_id          = (int)$data['id'];
		$this->page_parent_id   = (int)$data['parent_id'];
		$this->page_slug        = (string)$data['slug'];
		$this->page_status      = (string)$data['status'];
		$this->page_title       = (string)$data['title'];
		$this->page_meta_title  = !empty(trim($data['meta_title'])) ? $data['meta_title'] : $data['title'];
		$this->page_meta_desc   = (string)$data['meta_description'];
		$this->page_meta_keys   = (string)$data['meta_keywords'];
		$this->page_noindex     = (bool)$data['noindex'];
		$this->page_type        = (string)$data['page_type'];
		$this->page_nav         = (bool)$data['nav'];
		$this->page_sort        = (int)$data['sort'];
		$this->page_childs      = (int)$data['childs'];
		$this->page_created_at  = (int)$data['created_at'];
		$this->page_updated_at  = (int)$data['updated_at'];
		$this->page_published_at = (int)$data['published_at'];

		// access
		$this->access = $data['access'];
	}


	/**
	 * Collect information about the parent structural element in the site tree.
	 */
	private function update_tree_parent(): void {
		foreach($this->sitetree as $k => $v) {
			if($v['parent_id'] != 0 && isset($this->sitetree[$v['parent_id']])) {
				$this->sitetree[$k]['parent'] = $this->sitetree[$v['parent_id']];
			}
		}
	}


	/**
	 * Get page by ID
	 * 
	 * @param int $page_id Page ID
	 * @return array|null Page data or null if not found
	 */
	public function get_page_by_id(int $page_id): ?array {
		return $this->sitetree[$page_id] ?? null;
	}


	/**
	 * Get page by slug
	 * 
	 * @param string $slug Page slug
	 * @return array|null Page data or null if not found
	 */
	public function get_page_by_slug(string $slug): ?array {
		$page_id = $this->slugs[$slug] ?? null;
		return $page_id ? $this->get_page_by_id($page_id) : null;
	}


	/**
	 * Get current page data
	 * 
	 * @return array Current page data
	 */
	public function get_current_page(): array {
		return [
			'id' => $this->page_id,
			'parent_id' => $this->page_parent_id,
			'slug' => $this->page_slug,
			'status' => $this->page_status,
			'title' => $this->page_title,
			'meta_title' => $this->page_meta_title,
			'meta_description' => $this->page_meta_desc,
			'meta_keywords' => $this->page_meta_keys,
			'noindex' => $this->page_noindex,
			'page_type' => $this->page_type,
			'nav' => $this->page_nav,
			'sort' => $this->page_sort,
			'childs' => $this->page_childs,
			'created_at' => $this->page_created_at,
			'updated_at' => $this->page_updated_at,
			'published_at' => $this->page_published_at,
			'access' => $this->access
		];
	}


	// ==============================================
	// ADMIN METHODS (Data Layer)
	// ==============================================

	/**
	 * Get pages with filters and pagination (for admin)
	 * 
	 * @param array $filters Array of filters
	 * @param int $limit Limit results
	 * @param int $offset Offset for pagination
	 * @return array Array with pages and total count
	 */
	public function get_admin_pages(array $filters = [], int $limit = 50, int $offset = 0): array {
		$query = "SELECT id, slug, parent_id, status, nav, title, meta_title, meta_description, meta_keywords, 
		                noindex, page_type, sort, childs, created_at, updated_at, published_at
		         FROM " . TABLE_STRUCTURE;
		
		$where_conditions = [];
		$params_bind = [];

		// Apply filters
		if (!empty($filters['status']) && in_array($filters['status'], ['draft', 'active', 'inactive'])) {
			$where_conditions[] = "status = ?";
			$params_bind[] = $filters['status'];
		}

		if (isset($filters['parent_id']) && is_numeric($filters['parent_id'])) {
			$where_conditions[] = "parent_id = ?";
			$params_bind[] = (int)$filters['parent_id'];
		}

		if (!empty($filters['search'])) {
			$where_conditions[] = "(title LIKE ? OR slug LIKE ?)";
			$search_param = '%' . $filters['search'] . '%';
			$params_bind[] = $search_param;
			$params_bind[] = $search_param;
		}

		if (!empty($where_conditions)) {
			$query .= " WHERE " . implode(" AND ", $where_conditions);
		}

		$query .= " ORDER BY sort ASC, id ASC LIMIT ? OFFSET ?";
		$params_bind[] = min(200, max(1, $limit));
		$params_bind[] = max(0, $offset);

		$pages = $this->db->fetch_all($query, $params_bind);

		// Get total count
		$count_query = "SELECT COUNT(*) as total FROM " . TABLE_STRUCTURE;
		if (!empty($where_conditions)) {
			$count_query .= " WHERE " . implode(" AND ", $where_conditions);
			$count_params = array_slice($params_bind, 0, -2);
		} else {
			$count_params = [];
		}
		
		$total_result = $this->db->fetch_assoc($count_query, $count_params);
		$total = (int)$total_result['total'];

		return [
			'pages' => $pages,
			'total' => $total
		];
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

		$query = "SELECT id, slug, parent_id, status, nav, title, meta_title, meta_description, meta_keywords, 
		                noindex, page_type, sort, childs, created_at, updated_at, published_at
		         FROM " . TABLE_STRUCTURE . " WHERE id = ?";
		
		return $this->db->fetch_assoc($query, [$page_id]) ?: null;
	}


	/**
	 * Create new page
	 * 
	 * @param array $data Page data
	 * @return int|false Created page ID or false on error
	 * @throws Exception If validation fails
	 */
	public function create_page(array $data): int|false {
		// Validate slug
		$slug_validation = $this->validate_slug($data['slug']);
		if (!$slug_validation['valid']) {
			throw new Exception($slug_validation['error']);
		}

		$current_time = time();
		
		$insert_data = [
			'slug' => $data['slug'],
			'parent_id' => (int)($data['parent_id'] ?? 1),
			'status' => $data['status'] ?? 'draft',
			'nav' => isset($data['nav']) && $data['nav'] ? '1' : '0',
			'title' => $data['title'],
			'meta_title' => $data['meta_title'] ?? null,
			'meta_description' => $data['meta_description'] ?? null,
			'meta_keywords' => $data['meta_keywords'] ?? null,
			'noindex' => isset($data['noindex']) && $data['noindex'] ? '1' : '0',
			'page_type' => $data['page_type'] ?? 'page',
			'sort' => (int)($data['sort'] ?? 100),
			'childs' => 0,
			'created_at' => $current_time,
			'updated_at' => $current_time,
			'published_at' => isset($data['published_at']) ? strtotime($data['published_at']) : 0
		];

		$insert = $this->db->insert_array($insert_data, TABLE_STRUCTURE);
		
		if ($insert) {
			return (int)$this->db->insert_id();
		}
		
		return false;
	}


	/**
	 * Update existing page
	 * 
	 * @param int $page_id Page ID
	 * @param array $data Update data
	 * @return bool Success
	 * @throws Exception If validation fails
	 */
	public function update_page(int $page_id, array $data): bool {
		if ($page_id <= 0) {
			return false;
		}

		// Validate slug if it's being updated
		if (isset($data['slug'])) {
			$slug_validation = $this->validate_slug($data['slug'], $page_id);
			if (!$slug_validation['valid']) {
				throw new Exception($slug_validation['error']);
			}
		}

		// Prepare update data
		$update_data = [
			'updated_at' => time()
		];

		// Only update provided fields
		$updatable_fields = ['slug', 'parent_id', 'status', 'nav', 'title', 'meta_title', 
		                   'meta_description', 'meta_keywords', 'noindex', 'page_type', 'sort'];
		
		foreach ($updatable_fields as $field) {
			if (array_key_exists($field, $data)) {
				if ($field === 'nav' || $field === 'noindex') {
					$update_data[$field] = $data[$field] ? '1' : '0';
				} elseif ($field === 'parent_id' || $field === 'sort') {
					$update_data[$field] = (int)$data[$field];
				} else {
					$update_data[$field] = $data[$field];
				}
			}
		}

		// Handle published_at
		if (array_key_exists('published_at', $data)) {
			$update_data['published_at'] = is_string($data['published_at'])
				? strtotime($data['published_at'])
				: (int)$data['published_at'];
		}

		$success = $this->db->update_array($update_data, TABLE_STRUCTURE, 'id = ?', [$page_id]);
		
		return $success;
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

		// Additional protection against deleting the main page at the level of the model
		if ($page_id === 1) {
			return false; // Do not delete the main page
		}

		// Additional protection against deleting the main page at the level of the model
		$page_data = $this->get_admin_page_by_id($page_id);
		if ($page_data && $page_data['slug'] === 'index') {
			return false; // Do not delete the page with slug='index'
		}

		$stmt = $this->db->query('DELETE FROM ' . TABLE_STRUCTURE . ' WHERE id = ?', [$page_id]);
		return $stmt->rowCount() > 0;
	}


	/**
	 * Check if page exists
	 * 
	 * @param int $page_id Page ID
	 * @return bool True if exists
	 */
	public function page_exists(int $page_id): bool {
		if ($page_id <= 0) {
			return false;
		}

		$result = $this->db->fetch_assoc("SELECT id FROM " . TABLE_STRUCTURE . " WHERE id = ?", [$page_id]);
		return $result !== false;
	}


	/**
	 * Check if slug exists
	 * 
	 * @param string $slug Slug to check
	 * @param int|null $exclude_id ID to exclude from check
	 * @return bool True if exists
	 */
	public function slug_exists(string $slug, ?int $exclude_id = null): bool {
		$query = "SELECT id FROM " . TABLE_STRUCTURE . " WHERE slug = ?";
		$params = [$slug];

		if ($exclude_id !== null) {
			$query .= " AND id != ?";
			$params[] = $exclude_id;
		}

		$result = $this->db->fetch_assoc($query, $params);
		return $result !== false;
	}


	/**
	 * Check if slug is denied
	 * 
	 * @param string $slug Slug to check
	 * @return bool True if slug is denied
	 */
	public function is_slug_denied(string $slug): bool {
		return in_array($slug, self::DENIED_SLUGS, true);
	}


	/**
	 * Validate slug for creation or update
	 * 
	 * @param string $slug Slug to validate
	 * @param int|null $exclude_id ID to exclude from uniqueness check
	 * @return array Validation result with 'valid' boolean and 'error' message
	 */
	public function validate_slug(string $slug, ?int $exclude_id = null): array {
		// Check if slug is empty
		if (empty(trim($slug))) {
			return ['valid' => false, 'error' => 'Slug cannot be empty'];
		}

		// Check if slug is denied
		if ($this->is_slug_denied($slug)) {
			return ['valid' => false, 'error' => 'This slug is reserved and cannot be used'];
		}

		// Check if slug already exists
		if ($this->slug_exists($slug, $exclude_id)) {
			return ['valid' => false, 'error' => 'Slug already exists'];
		}

		// Additional slug format validation
		if (!preg_match('/^[a-z0-9-_]+$/', $slug)) {
			return ['valid' => false, 'error' => 'Slug can only contain lowercase letters, numbers, hyphens, and underscores'];
		}

		if (strlen($slug) > 100) {
			return ['valid' => false, 'error' => 'Slug is too long (maximum 100 characters)'];
		}

		return ['valid' => true, 'error' => null];
	}


	/**
	 * Get parent page info
	 * 
	 * @param int $parent_id Parent ID
	 * @return array|null Parent page data or null if not found
	 */
	public function get_parent_info(int $parent_id): ?array {
		if ($parent_id <= 0) {
			return null;
		}

		$result = $this->db->fetch_assoc(
			"SELECT parent_id, childs FROM " . TABLE_STRUCTURE . " WHERE id = ?", 
			[$parent_id]
		);
		
		return $result ?: null;
	}


	/**
	 * Update parent childs count
	 * 
	 * @param int $parent_id Parent page ID
	 * @return bool Success
	 */
	public function update_parent_childs_count(int $parent_id): bool {
		if ($parent_id <= 0) {
			return false;
		}

		$count_result = $this->db->fetch_assoc(
			"SELECT COUNT(*) as childs FROM " . TABLE_STRUCTURE . " WHERE parent_id = ?", 
			[$parent_id]
		);

		$childs_count = (int)$count_result['childs'];

		$stmt = $this->db->query(
			'UPDATE ' . TABLE_STRUCTURE . ' SET childs = ?, updated_at = ? WHERE id = ?',
			[$childs_count, time(), $parent_id]
		);
		
		return $stmt->rowCount() > 0;
	}


	/**
	 * Update page status
	 * 
	 * @param int $page_id Page ID
	 * @param string $status New status
	 * @return bool Success
	 */
	public function update_page_status(int $page_id, string $status): bool {
		if ($page_id <= 0 || !in_array($status, ['draft', 'active', 'inactive'])) {
			return false;
		}

		$update_data = [
			'status' => $status,
			'updated_at' => time()
		];

		// Set published_at if changing to active
		if ($status === 'active') {
			$update_data['published_at'] = time();
		}

		$success = $this->db->update_array($update_data, TABLE_STRUCTURE, 'id = ?', [$page_id]);
		
		return $success;
	}


	/**
	 * Update page sort order
	 * 
	 * @param int $page_id Page ID
	 * @param int $sort New sort order
	 * @return bool Success
	 */
	public function update_page_sort(int $page_id, int $sort): bool {
		if ($page_id <= 0) {
			return false;
		}

		$stmt = $this->db->query(
			'UPDATE ' . TABLE_STRUCTURE . ' SET sort = ?, updated_at = ? WHERE id = ?',
			[$sort, time(), $page_id]
		);
		
		return $stmt->rowCount() > 0;
	}

}