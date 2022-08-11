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

	# settings
	private $result_per_page = 10;



	/**
	 * UI_Search constructor.
	 */
	public function __construct() {

		global $db, $post, $get;

		# settings
		$db->limit =& $this->result_per_page;

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
	private function search(string $searchstring) {

		global $db, $structure, $nav, $tags, $users, $parse, $img, $tpl, $smarty;

		# title
		$structure->page_title = "Поиск : ".$searchstring;

		# breadcrumb
		$nav->breadcrumb[] = array('part'=>'search', 'title'=>'Поиск: '.$searchstring);

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

		# access condition
		$accesscond = "";
		if($users->title != "a") {
			$accesscond = " AND (f.group_access='0' OR f.group_access='".$users->gid."' OR f.group_access LIKE '%,".$users->gid.",%' OR f.group_access LIKE '".$users->gid.",%' OR f.group_access LIKE '%,".$users->gid."')";
		}

		# calculate pages
		$db->pages_mysql(PAGES_FEED_TABLE, str_ireplace("f.", "", "f.date_publications <= '".time()."' AND ".$cond." AND (".$condsid.") ".$accesscond." AND (f.date_end_publications = '0' || f.date_end_publications > '".time()."') AND f.status='1'"));

		# get array pagination template array
		$pages = $db->construct_pagination();

		$taglinks = [];
		$authors  = [];
		$result   = [];
		$q = $db->query("SELECT f.id, f.author_id, f.title, f.brief_item, s.title AS feed_title, s.alias, f.date_publications, f.views 
					FROM ".PAGES_FEED_TABLE." AS f 
					LEFT JOIN ".STRUCTURE_TABLE." AS s ON (s.id = f.sid) 
					WHERE (".$cond.") AND (".$condsid.") ".$accesscond." AND (f.date_end_publications = '0' || f.date_end_publications > '".time()."') AND f.status='1' AND f.date_publications <= '".time()."' 
					ORDER BY f.date_publications DESC, f.views DESC
					LIMIT ".$db->from.",".$db->limit);
		while($row = $db->fetch_assoc($q)) {
			$row['datepub']    = $parse->date->unix_to_rus($row['date_publications'],true);
			$row['date']       = $parse->date->unix_to_rus_array($row['date_publications']);
			$row['brief_item'] = $parse->text->html($row['brief_item']);

			$row['image']      = $img->load_images("feeditemid=".$row['id']."", 0, 1);

			$row['tags']       = [];

			$taglinks[$row['id']] = "feeditemid=".$row['id'];

			$authors[] = $row['author_id'];

			$result[$row['id']] = $row;
		}

		# tags collect
		$result = $tags->collect_tags($result, $taglinks);

		# authors
		$fauthors = $users->get_userlist(-1,-1,-1,$authors);

		# template
		$smarty->assign("authors", $fauthors);
		$smarty->assign("searchstring", $searchstring);
		$smarty->assign("result", $result);
		$smarty->assign("pages", $pages);

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
				$cond .= " f.sid='".$val['id']."' ";
			}
		}

		return $cond;
	}
}

/**
 * Init Class
 */
$uisearch = new UI_Search;
