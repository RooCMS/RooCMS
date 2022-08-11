<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class Structure
 */
class Structure {

	# vars
	public $content_types 		= array('html'  => array('title' => 'HTML'),
					        'story' => array('title' => 'История'),
					        'php'   => array('title' => 'PHP'),
					        'feed'  => array('title' => 'Лента'));
	# site tree
	public $sitetree		= [];

	# on/off (release in future)
	public $access			= true;

	# aliases
	private $aliases 		= [];

	# page vars
	public $page_sid		= 1;				# page sid
	public $page_parent		= 0;				# Parent id
	public $page_alias		= "index";			# unique alias name
	public $page_title		= "Добро пожаловать [RooCMS]";	# title page
	public $page_meta_title		= "";				# Meta Title
	public $page_meta_desc		= "";				# Meta description
	public $page_meta_keys		= "";				# Meta keywords
	public $page_noindex		= false;			# Meta noindex
	public $page_type		= "html";			# page type
	public $page_group_access	= array(0);			# allowed acces to user group (sep. comma)
	public $page_rss		= false;			# on/off RSS feed
	public $page_show_child_feeds	= 'none';			# feed option for show childs feed
	public $page_items_per_page	= 10;				# show items on per page
	public $page_items_sorting	= "datepublication";		# type sorting for feed
	public $page_items		= 0;				# show amount items on feed
	public $page_thumb_img_width	= 0;				# in pixels
	public $page_thumb_img_height	= 0;				# in pixels
	public $page_append_info_before	= "";				# info
	public $page_append_info_after	= "";				# info



	/**
	* Init class
	*
	* @param bool $ui - use true only user interface
	*/
	public function __construct(bool $ui=true) {

		# load site tree
		$this->sitetree = $this->load_tree();

		if(!empty($this->sitetree)) {
			$this->update_tree_parent();
		}

        	# user interface loaded
        	if($ui) {
			$this->load_ui();
		}
	}


	/**
	 * Construct tree for site (step 1)
	 *
	 * @param int     $parent   - id for start construction tree.
	 * @param int     $maxlevel - set level for get sublevels, if param == 0, return all sublevels
	 * @param boolean $child    - set false if you dont get sublevels.
	 *
	 * @return array|null|false - return false if tree not construct, or return rray tree.
	 */
	public function load_tree(int $parent=0, int $maxlevel=0, bool $child=true) {

		global $db, $users;
		static $use = false;
		$tree = [];

		# Делаем единичный запрос в БД собирая данные по структуре сайта.
		if(!$use) {
			$q = $db->query("SELECT 
						id, nav, alias, parent_id,  
						title, meta_title, meta_description, meta_keywords, noindex, rss,
						page_type, sort, childs, items, show_child_feeds, group_access, 
						items_per_page, items_sorting, thumb_img_width, thumb_img_height,
						append_info_before, append_info_after
					FROM ".STRUCTURE_TABLE." ORDER BY sort");
			while($row = $db->fetch_assoc($q)) {

				# structure
				$row['level']  = 0;
				$row['parent'] = 0;

				# group access
				$row['group_access'] = $users->get_gid_access_granted($row['group_access']);

				# access
				$row['access'] = $users->title == "a" || array_key_exists(0, $row['group_access']) || array_key_exists($users->gid, $row['group_access']);


				$tree[$row['id']] = $row;

				$this->aliases[$row['alias']] = $row['id'];
			}

			$use = true;
		}
		else {
			$tree = $this->sitetree;
		}

		# construct tree
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
	 * @param boolean $child    - set false if you dont get sublevels.
	 * @param int     $level    - this param for this handler. Dont use handly.
	 *
	 * @return array|null
	 */
	private function construct_tree(array $unit, int $parent=0, int $maxlevel=0, bool $child=true, int $level=0) {

		# create array
		if($level == 0) {
			$tree = [];
		}

		foreach($unit AS $i=>$value) {
			if($value['parent_id'] == $parent) {
				# update level
				$value['level'] = $level;

				# add branch(s)
				$tree[$value['id']] = $value;

				# check child
				if($child && ($maxlevel == 0 || $level+1 <= $maxlevel)) {

					$subtree = $this->construct_tree($unit, $value['id'], $maxlevel, $child, $level + 1);

					if(is_array($subtree)) {
						$tree = $tree + $subtree;
					}
				}
			}
		}

		# be back
		if(!empty($tree)) {
			return $tree;
		}
	}


	/**
	 * Load ui data
	 */
	private function load_ui() {

		global $get;

		# const for default structure id
		if(!defined('PAGEID')) {
			define('PAGEID', 1);
		}

		# init id for vars
		$lid = PAGEID;

		if(isset($get->_page)) {
			if(isset($this->sitetree[$get->_page])) {
				$lid = $get->_page;
			}

			if(isset($this->aliases[$get->_page])) {
				$lid = $this->aliases[$get->_page];
			}
		}

		# set vars
		$this->set_page_vars($this->sitetree[$lid]);
	}


        /**
        * Устанавливает основные переменные класса
        * В так же передает в шаблоны глобальные перенменные:
        *   Идентификатор структурной еденицы
        *   Алиас структурной еденицы
        *   Заголовок структурной еденицы
        *
        * @param array $data - параметры вызванной страницы
        */
	private function set_page_vars(array $data) {

		global $config, $smarty;

        	# set vars
		$this->page_sid                = $data['id'];
		$this->page_parent             = $data['parent_id'];
		$this->page_alias              = $data['alias'];
		$this->page_title              = $data['title'];
		$this->page_meta_title         = (trim($data['meta_title']) != "") ? $data['meta_title'] : $data['title'];
		$this->page_meta_desc          = (trim($data['meta_description']) != "") ? $data['meta_description'] : $config->meta_description;
		$this->page_meta_keys          = (trim($data['meta_keywords']) != "") ? $data['meta_keywords'] : $config->meta_keywords;
		$this->page_noindex            = (bool) $data['noindex'];
		$this->page_type               = $data['page_type'];
		$this->page_group_access       = $data['group_access'];
		$this->page_rss                = (bool) $data['rss'];
		$this->page_show_child_feeds   = $data['show_child_feeds'];
		$this->page_items_per_page     = $data['items_per_page'];
		$this->page_items_sorting      = $data['items_sorting'];
		$this->page_items              = $data['items'];
		$this->page_thumb_img_width    = $data['thumb_img_width'];
		$this->page_thumb_img_height   = $data['thumb_img_height'];
		$this->page_append_info_before = $data['append_info_before'];
		$this->page_append_info_after  = $data['append_info_after'];

		# access
		$this->access = $data['access'];

                # set smarty vars
                $smarty->assign("page_sid",   $data['id']);
                $smarty->assign("page_alias", $data['alias']);
                $smarty->assign("page_title", $data['title']);
	}


	/**
	 * Функция собирает информация о родителе структурного элемента.
	 */
	private function update_tree_parent() {
		foreach($this->sitetree AS $k=>$v) {
			if($v['parent_id'] != 0) {
				$this->sitetree[$k]['parent'] = $this->sitetree[$v['parent_id']];
			}
		}
	}
}
