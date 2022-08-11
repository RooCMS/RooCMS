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
 * Class Navigation
 */
class Navigation {

	# navigation tree
	public $navtree    = [];

	# bread crumb
	public $breadcrumb = [];



	/**
	 * Navigation constructor.
	 */
	public function __construct() {

		global $smarty;

		# Constrct navigation tree
		$this->construct_nav();

		# tpl navigation
		$smarty->assign("navtree", $this->navtree);
	}


	/**
	 * Constrct navigation tree
	 */
	private function construct_nav() {

		global $structure;

		if(count($structure->sitetree) > 1) {
			foreach($structure->load_tree(1) AS $k=>$v) {
				if($v['nav'] == 1)  {
					$v['sublevel'] = 0;

					if($v['parent_id'] != 1) {
						$this->navtree[$v['parent_id']]['sublevel'] = 1;
					}

					$this->navtree[$k] = $v;
				}
			}
		}
	}

	/**
	 * Construct bread crumb navigation
	 *
	 * @param int $sid - current page id
	 */
	public function construct_breadcrumb(int $sid = 1) {

		global $structure;

		if($sid != 1) {
			$v = $structure->sitetree[$sid];
			$this->breadcrumb[] = array('id'    => $v['id'],
						   'alias'  => $v['alias'],
						   'act'    => "",
						   'part'   => "",
						   'title'  => $v['title'],
						   'parent' => $v['parent']);

			if($v['parent_id'] != 0) {
				$this->construct_breadcrumb($v['parent_id']);
			}
		}
	}
}
