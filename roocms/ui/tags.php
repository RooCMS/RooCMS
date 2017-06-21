<?php
/**
 * RooCMS - Russian free content managment system
 * Copyright © 2010-2017 alexandr Belov aka alex Roosso. All rights reserved
 * Contacts: <info@roocms.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/
 *
 *
 * RooCMS - Русская бесплатная система управления сайтом
 * Copyright © 2010-2017 александр Белов  (alex Roosso). Все права защищены
 * Для связи: info@roocms.com
 *
 * Это программа является свободным программным обеспечением. Вы можете
 * распространять и/или модифицировать её согласно условиям Стандартной
 * Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 * Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
 *
 * Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 * ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 * ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 * Общественную Лицензию GNU для получения дополнительной информации.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 * с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */

/**
 * @package      RooCMS
 * @subpackage   Frontend
 * @author       alex Roosso
 * @copyright    2010-2018 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      0.2
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
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

		global $GET;

		# init tag
		if(isset($GET->_tag)) {
			$this->init_tag($GET->_tag, "title");
		}
		elseif(isset($GET->_tagid)) {
			$this->init_tag($GET->_tagid);
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

		global $db, $structure, $smarty;

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

			# breadcumb
			$structure->breadcumb[] = array('part'=>'tags', 'title'=>'Тег: '.$data['title']);

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

		global $db, $structure, $parse, $img, $tags, $tpl, $smarty;

		# data linked
		$links = array();
		$q = $db->query("SELECT linkedto FROM ".TAGS_LINK_TABLE." WHERE tag_id='".$this->id."'");
		while($data = $db->fetch_assoc($q)) {
			$id = explode("=", $data['linkedto']);
			$links[] = $id[1];
		}

		# cond
		$cond = "(";
		foreach($links AS $value) {
			if($cond != "(") {
				$cond .= "OR ";
			}
			$cond .= " id='".$value."' ";
		}
		$cond .= ")";

		$scond = "(";
		foreach($structure->sitetree AS $i=>$val) {
			if($val['access']) {
				if(trim($scond) != "(") $scond .= " OR ";
				$scond .= " sid='".$val['id']."' ";
			}
		}
		$scond .= ")";

		# calculate pages
		$db->pages_mysql(PAGES_FEED_TABLE, "date_publications <= '".time()."' AND ".$cond." AND ".$scond." AND (date_end_publications = '0' || date_end_publications > '".time()."') AND status='1'");

		# get array pagination template array
		$pages = $this->construct_pagination();

		# Feed list
		$taglinks = array();
		$feeds    = array();
		$cond = str_ireplace("id=", "fi.id=", $cond);
		$scond = str_ireplace("sid=", "fi.sid=", $scond);
		$q = $db->query("SELECT fi.id, fi.sid, s.alias, s.title AS feed_title, fi.title, fi.brief_item, fi.full_item, fi.date_publications, fi.views 
					FROM ".PAGES_FEED_TABLE." AS fi
					LEFT JOIN ".STRUCTURE_TABLE." AS s ON (s.id = fi.sid)
					WHERE fi.date_publications <= '".time()."' AND ".$cond." AND ".$scond." AND (fi.date_end_publications = '0' || fi.date_end_publications > '".time()."') AND fi.status='1'
					ORDER BY fi.date_publications DESC, fi.date_create DESC, fi.date_update DESC 
					LIMIT ".$db->from.",".$db->limit);
		while($row = $db->fetch_assoc($q)) {

			if(trim($row['brief_item']) == "") {
				$row['brief_item'] = $row['full_item'];
			}

			$row['datepub']    = $parse->date->unix_to_rus($row['date_publications'],true);
			$row['date']       = $parse->date->unix_to_rus_array($row['date_publications']);
			$row['brief_item'] = $parse->text->html($row['brief_item']);

			$row['image']      = $img->load_images("feeditemid=".$row['id']."", 0, 1);

			$row['tags']       = array();


			$taglinks[$row['id']] = "feeditemid=".$row['id'];
			$feeds[$row['id']] = $row;
		}

		# tags collect
		if(!empty($taglinks)) {
			$alltags = $tags->read_tags($taglinks);
			foreach((array)$alltags AS $value) {
				$lid = explode("=",$value['linkedto']);
				$feeds[$lid[1]]['tags'][] = array("tag_id"=>$value['tag_id'], "title"=>$value['title']);
			}
		}

		# smarty
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

		$pages = array();
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

?>