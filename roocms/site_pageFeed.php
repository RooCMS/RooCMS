<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2020 alexandr Belov aka alex Roosso. All rights reserved.
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
 * Class SitePageFeed
 */
class PageFeed {

	use FeedExtends;

	# vars
	private $item_id	= 0;
	private $items_per_page	= 10;


	/**
	 * Lets begin...
	 * Why does the gull die?
	 */
	public function __construct() {

		global $get, $db, $structure, $parse, $smarty;

		$feed           = [];
		$feed['title'] 	= $structure->page_title;
		$feed['alias'] 	= $structure->page_alias;
		$feed['id'] 	= $structure->page_id;

		# append information
		$feed['append_info_before'] = $parse->text->html($structure->page_append_info_before);
		$feed['append_info_after'] = $parse->text->html($structure->page_append_info_after);

		$smarty->assign("feed", $feed);

		if(isset($get->_id) && $db->check_id(round($get->_id), PAGES_FEED_TABLE, "id", "(date_end_publications = '0' || date_end_publications > '".time()."') AND status='1'")) {
			$this->item_id = round($get->_id);
			$this->load_item($this->item_id);
		}
		elseif(isset($get->_export) && $get->_export == "RSS" && $structure->page_rss) {
			$this->load_feed_rss();
		}
		else {
			$this->load_feed();
		}
	}


	/**
	 * Загружаем фид
	 */
	private function load_feed() {

		global $db, $config, $structure, $users, $tags, $rss, $parse, $img, $tpl, $smarty;

		# set limit on per page
		$this->items_per_page = ($structure->page_items_per_page > 0) ? $structure->page_items_per_page : $config->feed_items_per_page ;
		$db->limit =& $this->items_per_page;

		# cond request
		$cond = $this->feed_condition();

		# order request
		$order = $this->feed_order($structure->page_items_sorting);

		# calculate pages
		$db->pages_mysql(PAGES_FEED_TABLE, $cond);

		# get array pagination template array
		$pages = $this->construct_pagination();

		# RSS
		$rss->set_header_link();

		# Feed list
		$taglinks = [];
		$authors  = [];
		$feeds    = [];
		$q = $db->query("SELECT id, author_id, title, brief_item, date_publications, views FROM ".PAGES_FEED_TABLE." WHERE ".$cond." ORDER BY ".$order." LIMIT ".$db->from.",".$db->limit);
		while($row = $db->fetch_assoc($q)) {
			$row['datepub']    = $parse->date->unix_to_rus($row['date_publications'],true);
			$row['date']       = $parse->date->unix_to_rus_array($row['date_publications']);
			$row['brief_item'] = $parse->text->html($row['brief_item']);


			$row['image']      = $img->load_images("feeditemid=".$row['id']."", 0, 1);

			$row['tags']       = [];

			$taglinks[$row['id']] = "feeditemid=".$row['id'];

			$authors[] = $row['author_id'];

			$feeds[$row['id']] = $row;
		}

		# tags collect
		$feeds = $tags->collect_tags($feeds, $taglinks);

		# authors
		$fauthors = $users->get_userlist(-1,-1,-1,$authors);

		# smarty
		$smarty->assign("authors", $fauthors);
		$smarty->assign("feeds", $feeds);
		$smarty->assign("pages", $pages);
		$smarty->assign("rsslink", $rss->rss_link);

		$tpl->load_template("feed");
	}


	/**
	 * Load Feed Item
	 *
	 * @param int $id  - идентификатор элемента
	 */
	private function load_item($id) {

		global $db, $structure, $nav, $users, $parse, $tags, $files, $img, $tpl, $smarty, $site;

		# query data
		$q = $db->query("SELECT id, title, meta_title, meta_description, meta_keywords, author_id, full_item, views, date_publications, sort FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$item = $db->fetch_assoc($q);
		$item['datepub'] 	= $parse->date->unix_to_rus($item['date_publications'],true);
		$item['date']		= $parse->date->unix_to_rus_array($item['date_publications']);
		$item['full_item']	= $parse->text->html($item['full_item']);

		# tags
		$item['tags'] = $tags->read_tags("feeditemid=".$id);

		# add prev/next item
		$item = array_merge($item, $this->load_prevnext_item($item['id']));

		# author
		if($item['author_id'] != 0) {
			$item['author'] = $users->get_user_data($item['author_id']);
		}

		# load attached images
		$images = $img->load_images("feeditemid=".$id);
		$smarty->assign("images", $images);

		# load attached files
		$attachfile = $files->load_files("feeditemid=".$id);
		$smarty->assign("attachfile", $attachfile);

		# more items
		$more = $this->rand_items($item);

		# meta
		if(trim($item['meta_title']) != "") {
			$site['title'] = $item['meta_title']." - ".$site['title'];
		}
		else {
			$site['title'] = $item['title']." - ".$site['title'];
		}
		if(trim($item['meta_description']) != "") {
			$site['description']	= $item['meta_description'];
		}
		if(trim($item['meta_keywords']) != "") {
			$site['keywords']	= $item['meta_keywords'];
		}

		# breadcrumb
		$nav->breadcrumb[] = array('alias'=>$structure->page_alias, 'id'=>$item['id'], 'title'=>$item['title']);

		$smarty->assign("more", $more);
		$smarty->assign("item", $item);
		$tpl->load_template("feed_item");

		// cnt views
		$this->count_views($id);
	}


	/**
	 * загружаем RSS фид
	 */
	private function load_feed_rss() {

		global $db, $rss, $structure;

		# cond request
		$cond = $this->feed_condition();

		# order request
		$order = $this->feed_order($structure->page_items_sorting);

		$q = $db->query("SELECT id, title, brief_item, date_publications FROM ".PAGES_FEED_TABLE." WHERE ".$cond." ORDER BY ".$order." LIMIT ".$db->from.",".$db->limit);
		while($row = $db->fetch_assoc($q)) {
			# uri
			$newslink = SCRIPT_NAME."?page=".$structure->page_alias."&id=".$row['id'];

			# item
			$rss->create_item($newslink, $row['title'], $row['brief_item'], $newslink, $row['date_publications'], false, $structure->page_title);
			if($rss->lastbuilddate == 0) {
				$rss->set_lastbuilddate($row['date_publications']);
			}
		}
	}


	/**
	 * Считаем показы/просмотры элемента
	 *
	 * @param int $id - идентификатор элемента
	 */
	private function count_views($id) {

		global $db;

		if(!isset($_COOKIE[$id])) {

			$db->query("UPDATE ".PAGES_FEED_TABLE." SET views=views+1 WHERE id='".$id."'");

			// TODO: В дальнейшем время жизни будет опционально устанавливаться.
			$exp = time()+(60*60*24);
			setcookie($id, true, $exp);
		}
	}


	/**
	 * Функция собирает данные по предыдущему и следующим элементом
	 *
	 * @param int $id - Идентификатор текущего элемента
	 *
	 * @return array - массив данных с предыдущим и следующим элементами в ленте
	 */
	private function load_prevnext_item($id) {

		global $db, $structure, $img, $parse;

		# cond request
		$cond = $this->feed_condition();

		# order request
		$order = $this->feed_order($structure->page_items_sorting);

		# query
		$i = 0;
		$previndex = -1;
		$nextindex = -1;

		$data = [];
		$res = [];

		$q = $db->query("SELECT id, title, date_publications FROM ".PAGES_FEED_TABLE." WHERE ".$cond." ORDER BY ".$order."");
		while($row = $db->fetch_assoc($q)) {

			$row['datepub'] = $parse->date->unix_to_rus($row['date_publications']);
			$row['image']   = $img->load_images("feeditemid=".$row['id']."", 0, 1);

			$res[$i] = $row;

			if($row['id'] == $id) {
				$nextindex = $i - 1;
				if($nextindex >= 0) {
					$data['next'] = $res[$nextindex];
				}

				$previndex = $i + 1;
			}

			if($i == $previndex) {
				$data['prev'] = $res[$previndex];
				break;
			}

			$i++;
		}

		# return
		return $data;
	}


	/**
	 * Load random feed items
	 *
	 * @param array $i - array exceptions.
	 *
	 * @return array
	 */
	private function rand_items(array $i) {

		global $db, $img, $parse;

		# cond request
		$cond = $this->feed_condition();

		# add exceptions to condition
		$cond .= " AND (id !='".$i['id']."'";

		if(isset($i['prev']['id'])) {
			$cond .= " AND id !='".$i['prev']['id']."'";
		}

		if(isset($i['next']['id'])) {
			$cond .= " AND id !='".$i['next']['id']."'";
		}

		$cond .= " )";

		$data = [];
		$q = $db->query("SELECT id, title, date_publications FROM ".PAGES_FEED_TABLE." WHERE ".$cond." ORDER BY RAND() LIMIT 3"); // TODO: Избавиться от RAND!!!
		while($row = $db->fetch_assoc($q)) {
			$row['datepub'] = $parse->date->unix_to_rus($row['date_publications']);
			$row['image']   = $img->load_images("feeditemid=".$row['id']."", 0, 1);

			$data[] = $row;
		}

		return $data;
	}


	/**
	 * Функция возвращает массив идентификаторов лент, для условий запроса к БД, в случаях когда лента публикует элементы из дочерних лент.
	 *
	 * @param int    $sid  - structure id
	 * @param string $type - rule
	 *
	 * @return array - id's
	 */
	private function construct_child_feeds($sid, $type="default") {

		global $structure;

		$feeds = [];

		$tfeeds = $structure->load_tree($sid, 0, false);
		foreach((array)$tfeeds AS $v) {
			if($v['page_type'] == "feed") {

				$feeds[$v['id']] = $v['id'];

				# default rule
				if($type == "default" && $v['show_child_feeds'] != "none") {
					$addfeeds = $this->construct_child_feeds($v['id'],$v['show_child_feeds']);
					$feeds = array_merge($feeds, $addfeeds);
				}

				# force rule
				if($type == "forced") {
					$addfeeds = $this->construct_child_feeds($v['id'],$type);
					$feeds = array_merge($feeds, $addfeeds);
				}
			}
		}


		return $feeds;
	}


	/**
	 * Construct condition for query feed from DB
	 *
	 * @return string
	 */
	private function feed_condition() {

		global $db, $structure, $users;

		# query id's feeds begin
		$cond = " date_publications <= '".time()."' AND ( sid='".$structure->page_id."' ";

		$showchilds =& $structure->page_show_child_feeds;

		if($showchilds != "none") {
			$qfeeds = $this->construct_child_feeds($structure->page_id, $showchilds);
			foreach($qfeeds as $v) {
				# query id's feeds collect
				$cond .= " OR sid='".$v."' ";
			}
		}

		# query id's feeds final
		$cond .= " ) ";

		# access condition
		$accesscond = "";
		foreach($structure->sitetree AS $value) {
			if($value['access']) {
				$accesscond = $db->qcond_or($accesscond);
				$accesscond .= " sid='".$value['id']."' ";
			}
		}
		$accesscond = "(".$accesscond.")";

		if($users->title != "a") {
			$accesscond .= " AND (group_access='0' OR group_access='".$users->gid."' OR group_access LIKE '%,".$users->gid.",%' OR group_access LIKE '".$users->gid.",%' OR group_access LIKE '%,".$users->gid."')";
		}

		$cond .= " AND ".$accesscond." AND (date_end_publications = '0' || date_end_publications > '".time()."') AND status='1' ";

		# return
		return $cond;
	}


	/**
	 * Функция формирует массив данных для постраничной навигации, который будет использован в шаблонах
	 *
	 * @return array
	 */
	private function construct_pagination() {

		global $db, $site;

		$pages = [];
		# prev
		if($db->prev_page != 0) {
			$pages[]['prev'] =& $db->prev_page;
		}
		# pages
		for($p=1;$p<=$db->pages;$p++) {
			$pages[]['n'] = $p;
		}
		# next
		if($db->next_page > 1) {
			$pages[]['next'] =& $db->next_page;
		}

		# Указываем в титуле страницу
		# Это можно было бы оставить на усмотрение верстальщиков. Но использование одинаковых титулов на целом ряде страниц неполезно для SEO
		# (Есть небольшая вероятность, что этот момент будет исправлен и перенесен на усмотрение верстальщиков в шаблоны)
		if($db->page > 1) {
			$site['title'] .= " (Страница: ".$db->page.")";
		}

		return $pages;
	}
}