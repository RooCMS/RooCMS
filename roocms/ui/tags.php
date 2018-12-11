<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2019 alexandr Belov aka alex Roosso. All rights reserved.
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
 * Class UI_Tags
 */
class UI_Tags {

	# tag
	private $id  = 0;
	private $tag = "";

	# settings
	private $tags_per_page	= 10;



	/**
	 * UI_Tags constructor.
	 */
	public function __construct() {

		global $get;

		# init tag
		if(isset($get->_tag)) {
			$this->init_tag($get->_tag, "title");
		}
		elseif(isset($get->_tagid)) {
			$this->init_tag($get->_tagid);
		}

		# safe
		if($this->id == 0) {
			goback();
		}

		# show
		$this->show_tagged_items();
	}


	/**
	 * Инициализируем запрошенный тег
	 *
	 * @param        $tag
	 * @param string $type
	 */
	private function init_tag($tag, $type="id") {

		global $db, $structure, $nav, $smarty;

		$tag = urldecode($tag);

		if($db->check_id($tag, TAGS_TABLE, $type)) {

			$q = $db->query("SELECT id, title FROM ".TAGS_TABLE." WHERE {$type}='{$tag}'");
			$data = $db->fetch_assoc($q);

			# init
			$this->id  = $data['id'];
			$this->tag = $data['title'];

			# settings
			$db->limit =& $this->tags_per_page;

			# title
			$structure->page_title = "Тег : ".$data['title'];

			# breadcrumb
			$nav->breadcrumb[] = array('part'=>'tags', 'title'=>'Тег: '.$data['title']);

			# smarty
			$smarty->assign("tag", $data);
		}
		else {
			goback();
		}
	}


	/**
	 * Подготавливаем помеченные тегом объекты к публикации.
	 * Данная функция временная.
	 */
	private function show_tagged_items() {

		global $db, $structure, $parse, $img, $tags, $users, $tpl, $smarty;

		# data linked
		$links = [];
		$q = $db->query("SELECT linkedto FROM ".TAGS_LINK_TABLE." WHERE tag_id='".$this->id."'");
		while($data = $db->fetch_assoc($q)) {
			$id = explode("=", $data['linkedto']);
			$links[] = $id[1];
		}

		# abort mission
		if(empty($links)) {
			goback();
		}

		# cond
		$cond = "";
		foreach($links AS $value) {
			$cond = $db->qcond_or($cond);
			$cond .= " id='".$value."' ";
		}
		$cond = "(".$cond.")";

		$scond = "";
		foreach($structure->sitetree AS $value) {
			if($value['access']) {
				$scond = $db->qcond_or($scond);
				$scond .= " sid='".$value['id']."' ";
			}
		}
		$scond = "(".$scond.")";

		# calculate pages
		$db->pages_mysql(PAGES_FEED_TABLE, "date_publications <= '".time()."' AND ".$cond." AND ".$scond." AND (date_end_publications = '0' || date_end_publications > '".time()."') AND status='1'");

		# get array pagination template array
		$pages = $this->construct_pagination();

		# Feed list
		$taglinks = [];
		$authors  = [];
		$feeds    = [];
		$cond = str_ireplace("id=", "fi.id=", $cond);
		$scond = str_ireplace("sid=", "fi.sid=", $scond);
		$q = $db->query("SELECT fi.id, fi.sid, fi.author_id, s.alias, s.title AS feed_title, fi.title, fi.brief_item, fi.date_publications, fi.views 
					FROM ".PAGES_FEED_TABLE." AS fi
					LEFT JOIN ".STRUCTURE_TABLE." AS s ON (s.id = fi.sid)
					WHERE fi.date_publications <= '".time()."' AND ".$cond." AND ".$scond." AND (fi.date_end_publications = '0' || fi.date_end_publications > '".time()."') AND fi.status='1'
					ORDER BY fi.date_publications DESC, fi.date_create DESC, fi.date_update DESC 
					LIMIT ".$db->from.",".$db->limit);
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

		$tpl->load_template("tags");
	}


	/**
	 * Функция формирует массив данных для постраничной навигации, который будет использован в шаблонах
	 *
	 * @return array
	 */
	private function construct_pagination() {

		global $db, $structure;

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
			$structure->page_title .= " (Страница: ".$db->page.")";
		}

		return $pages;
	}
}

$uitags = new UI_Tags;
