<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS mod Images Gallery
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
|	Build date: 		8:11 07.03.2011
|	Last Biuld: 		3:01 17.10.2011
|	Version file:		1.00 build 3
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


require_once _CMS."/functions_gallery.php";


$galleryacp = new Galleryacp;
class Galleryacp {
	
	# classes
	protected $engine;
	
	

	//*******************************************
	// Start
	function __construct() {
		
		global $roocms, $db, $tpl, $GET;
		
		
		// run engine
		$this->engine = new GalleryEngine;
	

		// Load Template  ==============================
		$tpl->load_template("acp_gallery");
		//==============================================
		
		
		switch($roocms->part) {
			
			/* Функции добавления */
			case 'addcat':
				$this->addcat();
				break;
				
			case 'addimage':
				$this->addimage();
				break;
				
			/* Функции редактирования */
			case 'editcat':
				if($this->engine->category_id != 0)
					$this->editcat();
				else 
					$this->idx();
				break;
				
			case 'editimage':
				if($this->engine->image_id != 0)
					$this->editimage($this->engine->image_id);
				else 
					$this->idx();
				break;
				
			/* Функции обновления */
			case 'updatecat':
				$this->updatecat();
				break;
				
			case 'updateimage':
				$this->updateimage();
				break;
				
			case 'sortimage':
				$this->sortimage();
				break;
			
			/* Функция удаления */
			case 'delcat':
				if($this->engine->category_id != 0) 	
					$this->delcat($this->engine->category_id);
				else 
					$this->idx();
				break;
				
			case 'delimage':
				if($this->engine->image_id != 0) 	
					$this->delimage($this->engine->image_id);
				else 
					$this->idx();
				break;

			default:
				$this->idx();
				break;
				
		}
	}
	
	
	//*******************************************
	// Load images
	private function idx() {
	
		global $db, $tpl, $html;
		
		
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================

		
		// set limit works on page
		$db->limit = 50;
		
		// calculate pages
		$db->pages_mysql(GALLERY_ITEMS,"category_id='".$this->engine->category_id."'");
		
		// draw nav pages
		if($db->pages >= 2) {
			$html['navpage'] = $tpl->tpl->navpage();
			for($p=1;$p<=$db->pages;$p++) {
				$html['navpage_el'][] = $tpl->tpl->navpage_el($this->engine->category_id, $p);
			}
		}
		
		// Форма добавления изображений
		if($this->engine->category_id != 0 && $this->engine->check_type($this->engine->category_id) == "category") {
			// form
			$uploadmaxfilesize = ini_get('upload_max_filesize');
			$html['form_addimage'] = $tpl->tpl->form_addimage($uploadmaxfilesize);
		}
		else $html['form_addimage'] = $tpl->tpl->form_not_addimage();

		// Load projects 
		$allprojects = 0;
		
		$q = $db->query("SELECT id, thumb_img, original_img, sort, description
							FROM ".GALLERY_ITEMS." 
							WHERE category_id='".$this->engine->category_id."'
							ORDER BY sort ASC, id DESC
							LIMIT ".$db->from.",".$db->limit);
		while($row = $db->fetch_assoc($q)) {
			
			$html['project'][] = $tpl->tpl->project($row);
			$allprojects++;
		}


		
		for($i=0;$i<=count($category)-1;$i++) {
			$html['category'][]				= $tpl->tpl->category($category[$i]);
			$html['select_pcategory'][]		= $tpl->tpl->select_pcategory($category[$i]['cat_id'], $category[$i]['cat_name'], $category[$i]['padding'], $this->engine->settings['indention']);
			$html['select_pwcategory'][]	= $tpl->tpl->select_pwcategory($category[$i]['cat_id'], $category[$i]['cat_name'], $category[$i]['type'], $category[$i]['padding'], $this->engine->settings['indention']);
		}


		$html['content']	= $tpl->tpl->images($allprojects);
	}
	
	
	//*******************************************
	// Добавляет новую категорию в галерею
	private function addcat() {
		
		global $db, $POST;
		
		if(@$_REQUEST['new_category']) {
			if(isset($POST->cat_name)) {
				
				// type
				$type = "category";
				if(isset($POST->type) && $POST->type == "part") $type = "part";
				
				
				// sql insert
				$db->query("INSERT INTO ".GALLERY_CATEGORY." (parent_id, name, type)
														VALUES ('".round($POST->cat_parent)."', '".$POST->cat_name."', '".$type."')");
														
				# notice
				if($type == "part") $_SESSION['info'][] = "Раздел добавлен";
				else $_SESSION['info'][] = "Категория добавлена";
			}
			else {
				# error
				if(!isset($POST->cat_name)) $_SESSION['error'][] = "Не указано название категории!";
			}
		}
		
		// move
		goback();
	}
	
	
	//*******************************************
	// Добавляем изображения в категорию
	private function addimage() {
		
		global $db, $POST, $gd;
		
		// грузим фото и создаем превью.
		$image = $gd->post_upload("image", "gallery", true, true, _UPLOAD."/gallery/");
		
		// Если загрузка прошла успено
		if($image) {
			foreach($image as $value) {
				// написать проверку миниатюры.
				$db->query("INSERT INTO ".GALLERY_ITEMS." (category_id, original_img, thumb_img)
														   VALUES ('".$POST->category."', '".$value."', 'thumb_".$value."')");
			}
			
			// count images
			$this->count_projects($POST->category);
			
			# notice
			$_SESSION['info'][] = "Изображения успешно загружены.";
		}
		else {
			# error
			$_SESSION['error'][] = "Не удалось добавить изображения.";
		}
		
		// move
		goback();
	}
	
	
	//*******************************************
	// редактируем категорию
	private function editcat() {
		
		global $db, $tpl, $html, $GET;
		
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================
		
		for($i=0;$i<=count($category)-1;$i++) {
			$html['category'][]			= $tpl->tpl->category($category[$i]);
			$html['select_pcategory'][]	= $tpl->tpl->select_pcategory($category[$i]['cat_id'], 
																	  $category[$i]['cat_name'], 
																	  $category[$i]['padding'], 
																	  $this->engine->settings['indention'], 
																	  $this->engine->category_info['parent_id']);
		}
		
		// init html
		$html['content'] = $tpl->tpl->editcat($this->engine->category_info);
	}
	
	
	//*******************************************
	// Редактириуем изображение и описание к нему
	private function editimage($image_id) {
	
		global $db, $tpl, $html, $GET;
		
		// запрашиваем данные из БД
		$q = $db->query("SELECT id, category_id, description, thumb_img, original_img FROM ".GALLERY_ITEMS." WHERE id='".$image_id."'");
		$image = $db->fetch_assoc($q);
		
		
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================
		
		// вычеслим для удобства ширину и высоту оригинала, что бы красиво его показать
		$size_original = getimagesize(_UPLOAD."/gallery/".$image['original_img']);
		$image['width'] = $size_original[0];
		$image['pwidth'] = $size_original[0] + 30;
		$image['height'] = $size_original[1];
		$image['pheight'] = $size_original[1] + 30;
		
		$GET->_category = $image['category_id'];
		for($i=0;$i<=count($category)-1;$i++) {
			$html['category'][]				= $tpl->tpl->category($category[$i]);
			$html['select_pcategory'][]		= $tpl->tpl->select_pcategory($category[$i]['cat_id'], $category[$i]['cat_name'], $category[$i]['padding'], $this->engine->settings['indention']);
			$html['select_pwcategory'][]	= $tpl->tpl->select_pwcategory($category[$i]['cat_id'], $category[$i]['cat_name'], $category[$i]['type'], $category[$i]['padding'], $this->engine->settings['indention']);
		}
		
		$html['content'] = $tpl->tpl->editimage($image);
	}
	
	
	//*******************************************
	// Удаляет выбранную категорию из галереи
	private function delcat($category_id) {
	
		global $db;
		
		$q = $db->query("SELECT count(*) FROM ".GALLERY_ITEMS." WHERE category_id='".$category_id."'");
		$c = $db->fetch_row($q);
		if($c[0] == 0) {
			// удаляем катгорию
			$db->query("DELETE FROM ".GALLERY_CATEGORY." WHERE id='".$category_id."'");
		
			// notice
			$_SESSION['info'][] = "Категория удалена";
		}
		else $_SESSION['error'][] = "Невозможно удалить категорию: В категории имеются изображения";

		// move
		go(THIS_SCRIPT.".php?act=gallery");
	}
	
	
	//*******************************************
	// Удаляем изображение
	private function delimage($image_id) {
	
		global $db;
		
		// запрос для обновления счетчика
		$q = $db->query("SELECT category_id FROM ".GALLERY_ITEMS." WHERE id='".$image_id."'");
		$category = $db->fetch_assoc($q);
		
		// Удаляем изображение
		$q = $db->query("SELECT id, original_img, thumb_img FROM ".GALLERY_ITEMS." WHERE id='".$image_id."'");
		while($row = $db->fetch_assoc($q)) {
			if(file_exists(_UPLOAD."/gallery/".$row['original_img'])) {
				unlink(_UPLOAD."/gallery/".$row['original_img']);
				$_SESSION['info'][] = "Файл ".$row['original_img']." удален";
			}
			if(file_exists(_UPLOAD."/gallery/".$row['thumb_img'])) {
				unlink(_UPLOAD."/gallery/".$row['thumb_img']);
				$_SESSION['info'][] = "Файл ".$row['thumb_img']." удален";
			}
		}
		
		// чистим БД
		$db->query("DELETE FROM ".GALLERY_ITEMS." WHERE id='".$image_id."'");
		
		// Обновляем количество работ в категории
		$this->count_projects($category['category_id']);
		
		// notice
		$_SESSION['info'][] = "Изображение удалено";
		
		// move
		goback();
	}
	
	
	//*******************************************
	// Обвновление категории
	private function updatecat() {
	
		global $db, $POST;
		
		if(@$_REQUEST['update_cat']) {
			if(isset($POST->thisid) && $POST->thisid == $this->engine->category_id && isset($POST->title)) {
				if($this->engine->check_subcat($POST->thisid,$POST->parent_category)) {
					
					// type
					$type = "category";
					
					$q = $db->query("SELECT count(*) FROM ".GALLERY_ITEMS." WHERE category_id='".$POST->thisid."'");
					$c = $db->fetch_row($q);
					
					if(isset($POST->type) && $POST->type == "part" && $c[0] == 0) $type = "part";
					if(isset($POST->type) && $POST->type == "part" && $c[0] != 0) 
						$_SESSION['info'][] = "Не удалось изменить катгорию на раздел, потому что в Категории имеются изображения. Переместите изображения в другую категорию, прежде чем изменять её тип на раздел";
					
					// sql update
					$db->query("UPDATE ".GALLERY_CATEGORY."
									SET
										parent_id='".$POST->parent_category."',
										name='".$POST->title."',
										type='".$type."'
									WHERE
										id='".$POST->thisid."'");
										
					// Обновляем количество работ в категории
					$this->count_projects($POST->thisid);
					$this->count_projects($POST->parent_category);
					
					# notice
					$_SESSION['info'][] = "Категория успешно обновлена!";
				}
				else $_SESSION['error'][] = "Вы ошиблись указывая родительскую категорию! Не удалось обновить данные.";
			}
			else {
				# Error
				$_SESSION['error'][] = "Не удалось обновить категорию!";
				if(!isset($POST->title)) $_SESSION['error'][] = "Вы оставили пустым название категории!";
			}
		}
		
		// move
		goback();
	}
	
	
	//*******************************************
	// Обновляем фотографию
	private function updateimage() {
	
		global $db, $POST, $gd;
		
		if(@$_REQUEST['update_image']) {
			if(isset($POST->category) && isset($POST->id) && $db->check_id($POST->category, GALLERY_CATEGORY) && $db->check_id($POST->id, GALLERY_ITEMS)) {
				if(!isset($POST->description)) $POST->description = "";
				
				$db->query("UPDATE ".GALLERY_ITEMS." 
								SET 
									category_id='".$POST->category."',
									description='".$POST->description."'
								WHERE
									id='".$POST->id."'");
									
				// грузим фото и создаем превью.
				$image = $gd->post_upload("image", "gallery", true, true, _UPLOAD."/gallery/");

				if($image) {
					$q = $db->query("SELECT original_img, thumb_img FROM ".GALLERY_ITEMS." WHERE id='".$POST->id."'");
					$row = $db->fetch_assoc($q);
					if(file_exists(_UPLOAD."/gallery/".$row['original_img'])) {
						unlink(_UPLOAD."/gallery/".$row['original_img']);
						$_SESSION['info'][] = "Файл ".$row['original_img']." удален";
					}
					if(file_exists(_UPLOAD."/gallery/".$row['thumb_img'])) {
						unlink(_UPLOAD."/gallery/".$row['thumb_img']);
						$_SESSION['info'][] = "Файл ".$row['thumb_img']." удален";
					}
					
					$db->query("UPDATE ".GALLERY_ITEMS." 
									SET 
										category_id='".$POST->category."', 
										original_img='".$image."',
										thumb_img='thumb_".$image."'
									WHERE 
										id='".$POST->id."'");
										
					$_SESSION['info'][] = "Новое изображение успешно загружено";
				}
				
				
				// Обновляем количество работ в категории
				$this->count_projects($POST->category);
				if($POST->category != $POST->prev_cat) $this->count_projects($POST->prev_cat);
				
				
				$_SESSION['info'][] = "Изображение успешно обновлено.";
			}
			else {
				$_SESSION['error'][] = "Произошла ошибка в запросе. Данные обновить не удалось.";
			}
		}
		
		// move
		goback();
	}
	
	
	//*******************************************
	// Сортируем фотографии
	private function sortimage() {
	
		global $db, $POST;
		
		if(@$_REQUEST['update_sort']) {
			if(isset($POST->sort)) {
				foreach($POST->sort AS $key=>$value) {
					
					// update sql
					$db->query("UPDATE ".GALLERY_ITEMS." 
									SET	sort='".$value."'
									WHERE id='".$key."'");
				}
				
				// notice
				$_SESSION['info'][] = "Порядок изменен";
			}
		}
		
		// move
		goback();
	}
	
	
	//*******************************************
	// Обновляем число работ в категории
	private function count_projects($category_id) {
	
		global $db;
		
		$q = $db->query("SELECT count(*) FROM ".GALLERY_ITEMS." WHERE category_id='".$category_id."'");
		$c = $db->fetch_row($q);
		
		$db->query("UPDATE ".GALLERY_CATEGORY." SET images='".$c[0]."' WHERE id='".$category_id."'");
		//$_SESSION['info'][] = "Счетчик обновлен ".$category_id;
	}
}

?>