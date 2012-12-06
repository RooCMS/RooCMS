<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS mod News
|	Author:	alex Roosso
|	Copyright: 2010-2011 (c) RooCMS. 
|	Web: http://www.roocms.com
|	All rights reserved.
|----------------------------------------------------------
|	This program is free software; you can redistribute it and/or modify
|	it under the terms of the GNU General Public License as published by
|	the Free Software Foundation; either version 2 of the License, or
|	(at your option) any later version.
|	
|	Данное программное обеспечение является свободным и распространяется
|	по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
|	При любом использовании данного ПО вы должны соблюдать все условия
|	лицензии.
|----------------------------------------------------------
|	Build date: 		1:05 03.12.2010
|	Last Build: 		4:48 27.10.2011
|	Version file: 		1.00 build 9
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


require_once _CMS."/functions_news.php";


$newsuser = new NewsUser;
class NewsUser {

	# clasess
	protected 	$engine;

	
	
	function __construct() {
		
		global $config, $tpl, $GET, $var, $NewsCategoryId;
		
		
		// Передаем параметры если заданы вручную
		//	ИД Категории
		if(isset($NewsCategoryId) && $NewsCategoryId > 0 && !isset($GET->_category)) $GET->_category = $NewsCategoryId;

		
		// init engine
		$this->engine = new NewsEngine;
		
		
		// SEO meta
		//$var['title'] = " :: ".$var['title']." - новости";
		$config->meta_keywords .= ", новости";
		

		// Load Template  ==============================
		$tpl->load_template("user_news");
		//==============================================
		
		
		if($this->engine->news_id != 0)
			$this->show_news_item();
		elseif($this->engine->news_id == 0 && $this->engine->category_id != 0)
			$this->show_news_category();
		else
			$this->show_main();
	}
	
	
	//	Выводим галвную страницу раздела
	function show_main() {
	
		global $roocms, $db, $config, $tpl, $html, $parse, $rss, $var;
		
		
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================
		// draw category select
		foreach($category AS $key=>$value) {
			$html['category'][]	= $tpl->tpl->category($value);
		}
		
		
		// SEO meta
		$var['title'] = "Новости - ".$var['title'];
		
		// header rss link
		$rss->set_header_link();
		
		// rss link
		$html['rsslink'] = $tpl->tpl->rsslink($rss->rss_link);
		
		
		// set limit works on page
		$db->limit =& $this->engine->settings['newsonpage'];
		// Query last news
		$q = $db->query("SELECT n.id, n.category_id, n.date, n.title, n.brief_news, n.images, n.files, n.date_update, (SELECT thumb_img FROM ".NEWS_IMAGE." WHERE news_id=n.id ORDER BY id ASC LIMIT 1) AS image_news, c.name AS catname
							FROM ".NEWS_ITEM." AS n
							LEFT JOIN ".NEWS_CATEGORY." AS c ON (c.id = n.category_id)
							WHERE n.date <= '".$this->engine->udate."'
							ORDER BY n.date DESC, n.id DESC 
							LIMIT ".$db->from.",".$db->limit);
		while($news = $db->fetch_assoc($q)) {
			// RSS
			if($roocms->rss) {
				// uri
				$newslink = THIS_SCRIPT.".php?news=".$news['id'];
				
				// item
				$rss->create_item($newslink, $news['title'], $news['brief_news'], $newslink, $news['date_update'], false, $news['catname']);
				if($rss->lastbuilddate == 0) $rss->set_lastbuilddate($news['date_update']);
			}
			// HTML
			else {
				// parse
				$news['brief_news'] = $parse->text->html($news['brief_news']);
				$news['rdate']		= $parse->date->unix_to_rus($news['date']);
				
				// output
				$html['content'][]	= $tpl->tpl->last_news($news);
			}
		}
	}
	
	
	// выводим ленту
	function show_news_category() {
	
		global $roocms, $db, $config, $tpl, $html, $parse, $rss, $var;
		
		
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================
		// draw category select
		foreach($category AS $key=>$value) {
			$html['category'][]	= $tpl->tpl->category($value, $this->engine->category_id);
		}
		

		// set limit works on page
		$db->limit =& $this->engine->settings['newsonpage'];
		
		// calculate pages
		$db->pages_mysql(NEWS_ITEM, "date <= '".$this->engine->udate."' AND category_id = '".$this->engine->category_id."'");

		// draw nav pages
		if($db->pages >= 2) {
			$html['navpage'] = $tpl->tpl->navpage();
			# prev
			if($db->prev_page != 0) $html['navpage_el'][] = $tpl->tpl->navpage_prev_el($db->prev_page, $this->engine->category_id);
			# pages
			for($p=1;$p<=$db->pages;$p++) {
				$html['navpage_el'][] = $tpl->tpl->navpage_el($p, $this->engine->category_id);
			}
			# next
			if($db->next_page != 0) $html['navpage_el'][] = $tpl->tpl->navpage_next_el($db->next_page, $this->engine->category_id);
		}
		

		// SEO meta
		$var['title'] = $this->engine->category_name." ".$var['title'];
		$config->meta_description 	.= " ".$this->engine->category_info['meta_description'];
		$config->meta_keywords		.= ", ".$this->engine->category_info['meta_keywords'];
		if(empty($this->engine->category_info['meta_description'])) $config->meta_description .= " ".$this->engine->category_name;
		if($db->page > 1) $var['title'] .= " : Страница ".$db->page;

		
		// header rss link
		$rss->set_header_link();
		
		// rss link
		$html['rsslink'] = $tpl->tpl->rsslink($rss->rss_link);
		
		// Query News from category
		$q = $db->query("SELECT n.id, n.date, n.title, n.brief_news, n.images, n.files, n.date_update, (SELECT thumb_img FROM ".NEWS_IMAGE." WHERE news_id=n.id ORDER BY id ASC LIMIT 1) AS image_news
							FROM ".NEWS_ITEM." AS n
							WHERE n.date <= '".$this->engine->udate."' AND n.category_id = '".$this->engine->category_id."'
							ORDER BY n.date DESC, n.id DESC 
							LIMIT ".$db->from.",".$db->limit);
		while($news = $db->fetch_assoc($q)) {
			// RSS
			if($roocms->rss) {
				// uri
				$newslink = THIS_SCRIPT.".php?news=".$news['id'];

				// item
				$rss->create_item($newslink, $news['title'], $news['brief_news'], $newslink, $news['date_update'], false, $this->engine->category_name);
				if($rss->lastbuilddate == 0) $rss->set_lastbuilddate($news['date_update']);
			}
			// HTML
			else {
				// parse
				$news['brief_news'] = $parse->text->html($news['brief_news']);
				$news['rdate']		= $parse->date->unix_to_rus($news['date']);
				
				// output
				$html['content'][]	= $tpl->tpl->news($news);
			}
		}
	}
	
	
	// Выводим новость
	function show_news_item() {
	
		global $roocms, $db, $config, $tpl, $html, $parse, $var, $Debug;
		
	
		// Load news item
		$q = $db->query("SELECT i.id, i.category_id, c.name AS catname, i.date, i.title, i.full_news, i.brief_news, i.images, i.files, i.meta_description, i.meta_keywords, i.date_update FROM ".NEWS_ITEM." AS i
							LEFT JOIN ".NEWS_CATEGORY." AS c ON (c.id = i.category_id)
							WHERE i.id='".$this->engine->news_id."' AND i.date<='".$this->engine->udate."'");
		$news = $db->fetch_assoc($q);
		
		// check date flags
		if(empty($news)) {
			go(THIS_SCRIPT.".php");
			break;
		}
			
		
		// SEO
		$var['title'] 	= $news['title']." :: ".$news['catname']." ".$var['title'];
		$config->meta_description 	.= " ".$news['meta_description'];
		$config->meta_keywords		.= ", ".$news['meta_keywords'];
		if(empty($news['meta_description']))	$config->meta_description = $news['brief_news']." (".$news['catname'].")";
		
		if($roocms->spiderbot) $roocms->ifmodifedsince($news['date_update']);
	
	
		// parse
		$news['brief_news'] = $parse->text->anchors($parse->text->html($news['brief_news']));
		$news['full_news']	= $parse->text->anchors($parse->text->html($news['full_news']));
		$news['rdate']		= $parse->date->unix_to_rus($news['date']);
		
		
		//	Изображения
		if($news['images'] > 0) {
			$image = $this->engine->load_news_image($news['id']);
			
			$news['images'] = "";
			if($image) {
				foreach($image as $key=>$value) {
					$image[$key]['description'] = $parse->text->br($image[$key]['description']);
					
					// if bad thumbnail
					if(!file_exists(_UPLOAD."/".$image[$key]['thumb_img'])) $image[$key]['thumb_img'] = "no_thumb_image.png";
					
					$news['images'] .= $tpl->tpl->image($image[$key]);
				}
			}
		}
		else $news['images'] = "";
		
		
		//	Файлы
		if($news['files'] > 0) {
			$files = $this->engine->load_news_file($news['id']);
			
			$news['files'] = "";
			if($files) {
				foreach($files as $key=>$value) {
					$news['files'] .= $tpl->tpl->file($files[$key]);
				}
			}
		}
		else $news['files'] = "";
		
		
		// Ссылки вперед/назад
		$newslist = array();
		$q = $db->query("SELECT id, title FROM ".NEWS_ITEM." WHERE category_id='".$news['category_id']."' AND date<='".$this->engine->udate."' ORDER BY date DESC, id DESC");
		while($r = $db->fetch_assoc($q)) {
			$newslist[] = $r;
		}
		
		foreach($newslist AS $key=>$value) {
			if($value['id'] == $this->engine->news_id) {
				$n = $key - 1; $p = $key + 1;
				if(array_key_exists($p, $newslist)) $html['prev_news'] = $tpl->tpl->prev_news($newslist[$p]);
				if(array_key_exists($n, $newslist)) $html['next_news'] = $tpl->tpl->next_news($newslist[$n]);
			}
		}
		
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================
		// draw category select
		for($i=0;$i<=count($category)-1;$i++) {
			$html['category'][]	= $tpl->tpl->category($category[$i], $news['category_id']);
		}
		
		
		// output
		$html['content']	= $tpl->tpl->news_item($news);

	}
}


?>