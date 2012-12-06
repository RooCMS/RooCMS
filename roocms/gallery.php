<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS mod Gallery
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
|	Build date: 		2:16 27.09.2011
|	Last Build: 		3:00 17.10.2011
|	Version file: 		1.00 build 2
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


require_once _CMS."/functions_gallery.php";


$galleryuser = new GalleryUser;
class GalleryUser {

	# clasess
	protected 	$engine;
	
	
	// let's dance
	function __construct() {
	
		global $config, $tpl, $var;
		
		# init engine
		$this->engine = new GalleryEngine;
		
		//SEO
		$var['title'] .= " - галлерея";
		$config->meta_keywords .= ", галлерея, изображения, картинки";
		
		
		// Load Template  ==============================
		$tpl->load_template("user_gallery");
		//==============================================
		
		
		// check request
		if($this->engine->image_id != 0) 
			$this->show_image($this->engine->image_id);
		elseif($this->engine->category_id != 0 && $this->engine->category_type == "category") 
			$this->show_category($this->engine->category_id);
		else 
			$this->show_main_content();
	}
	
	
	// по-умолчанию показываем последние изображения
	function show_main_content() {
	
		global $db, $tpl, $html;
	
	
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================
		// draw category select
		for($i=0;$i<=count($category)-1;$i++) {
			$html['category'][]	= $tpl->tpl->category($category[$i], $this->engine->category_id);
		}
		
		
		// last images
		$q = $db->query("SELECT i.id, i.category_id, i.thumb_img, i.original_img, i.description, c.name AS category_name
								FROM ".GALLERY_ITEMS." AS i
								LEFT JOIN ".GALLERY_CATEGORY." AS c  ON (i.category_id = c.id)
								ORDER BY i.id DESC LIMIT 0,".$this->engine->settings['imageonpage']);
		while($row = $db->fetch_assoc($q)) {
			$html['images'][] = $tpl->tpl->show_images($row);
		}
		
		$html['content'] = $tpl->tpl->main();
	}
	
	
	// просмотр категории
	function show_category($category_id) {
		
		global $db, $tpl, $html, $var;
		
		
		//SEO
		$var['title'] = $this->engine->category_name." :: ".$var['title'];
		
		
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================
		// draw category select
		for($i=0;$i<=count($category)-1;$i++) {
			$html['category'][]	= $tpl->tpl->category($category[$i], $this->engine->category_id);
		}
		
		
		// set limit works on page
		$db->limit =& $this->engine->settings['imageonpage'];
		
		// calculate pages
		$db->pages_mysql(GALLERY_ITEMS,"category_id='".$category_id."'");

		// draw nav pages
		if($db->pages >= 2) {
			$html['navpage'] = $tpl->tpl->navpage();
			for($p=1;$p<=$db->pages;$p++) {
				$html['navpage_el'][] = $tpl->tpl->navpage_el($category_id, $p);
			}
		}
		
		
		// images
		$q = $db->query("SELECT id, category_id, thumb_img, original_img, description
								FROM ".GALLERY_ITEMS."
								WHERE category_id='".$category_id."'
								ORDER BY sort ASC, id DESC 
								LIMIT ".$db->from.",".$db->limit);
		while($row = $db->fetch_assoc($q)) {
			$html['images'][] = $tpl->tpl->show_images($row);
		}
		
		
		$html['content'] = $tpl->tpl->show_category($this->engine->category_info);
	}
}

?>