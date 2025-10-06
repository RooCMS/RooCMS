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

    private DB $db;

	// Content types
	public array $content_types = [
		'page' => ['title' => 'Content'],
		'feed' => ['title' => 'Feed']
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



	/**
	* Init class
	*/
	public function __construct(DB $db) {

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
						id, slug, parent_id, nav, title, meta_title, meta_description, meta_keywords, 
						noindex, page_type, sort, childs, created_at, updated_at
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
			'access' => $this->access
		];
	}

}