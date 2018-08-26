<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 * Contacts: <info@roocms.com>
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
 * @package      RooCMS
 * @subpackage   Frontend
 * @author       alex Roosso
 * @copyright    2010-2019 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      0.2.2
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('UI')) {
	die('Access Denied');
}
//#########################################################



/**
 * Class UI_Search
 */
class UI_Search {

	# vars
	private $minleight = 3; // TODO: В конфиг



	/**
	 * UI_Search constructor.
	 */
	public function __construct() {

		global $post, $get;

		# POST
		if(isset($post->search) && mb_strlen($post->search) >= $this->minleight) {
			go(SCRIPT_NAME."?part=search&search=".$post->search);
		}

		# GET
		if(isset($get->_search) && mb_strlen($get->_search) >= $this->minleight) {
			$this->search($get->_search);
		}
		else {
			goback();
		}
	}


	/**
	 * Функция вывода результатов поиска
	 *
	 * @param string $searchstring - Searchstring
	 */
	private function search($searchstring) {

		global $structure, $db, $tags, $parse, $img, $tpl, $smarty;

		# title
		$structure->page_title = "Поиск : ".$searchstring;

		# breadcumb
		$structure->breadcumb[] = array('part'=>'search', 'title'=>'Поиск: '.$searchstring);

		# search
		$srch = explode(" ", $searchstring);

		# cond
		$condsid = $this->condition();

		# feeds
		$cond = "";
		foreach($srch AS $val) {
			if(trim($cond) != "") $cond .= " AND ";
			$cond .= " (f.brief_item LIKE '%".$val."%' OR f.full_item LIKE '%".$val."%') ";
		}

		$taglinks = [];
		$result   = [];
		$q = $db->query("SELECT f.id, f.title, f.brief_item, s.title AS feed_title, s.alias, f.date_publications, f.views FROM ".PAGES_FEED_TABLE." AS f LEFT JOIN ".STRUCTURE_TABLE." AS s ON (s.id = f.sid) WHERE (".$cond.") AND (".$condsid.") AND (f.date_end_publications = '0' || f.date_end_publications > '".time()."') AND f.status='1' AND date_publications <= '".time()."' ORDER BY f.date_publications DESC, f.views DESC");
		while($row = $db->fetch_assoc($q)) {
			if(trim($row['brief_item']) == "") {
				$row['brief_item'] = $row['full_item'];
			}

			$row['datepub']    = $parse->date->unix_to_rus($row['date_publications'],true);
			$row['date']       = $parse->date->unix_to_rus_array($row['date_publications']);
			$row['brief_item'] = $parse->text->html($row['brief_item']);

			$row['image']      = $img->load_images("feeditemid=".$row['id']."", 0, 1);

			$row['tags']       = [];

			$taglinks[$row['id']] = "feeditemid=".$row['id'];

			$result[$row['id']] = $row;
		}

		# tags collect
		$result = $tags->collect_tags($result, $taglinks);

		# template
		$smarty->assign("searchstring", $searchstring);
		$smarty->assign("result", $result);
		$tpl->load_template("search");
	}


	/**
	 * Формируем список структурным идентификаторов для формирования запроса.
	 *
	 * @return string
	 */
	private function condition() {

		global $structure;

		$cond = "";
		foreach($structure->sitetree AS $i=>$val) {
			if($val['access']) {
				if(trim($cond) != "") $cond .= " OR ";
				$cond .= " s.id='".$val['id']."' ";
			}
		}

		return $cond;
	}
}

/**
 * Init Class
 */
$uisearch = new UI_Search;