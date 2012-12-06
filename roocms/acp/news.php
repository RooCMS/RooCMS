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
|	Build date: 		1:05 02.12.2010
|	Last Build: 		20:04 21.10.2011
|	Version file:		1.00 build 9
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


require_once _CMS."/functions_news.php";


$newsacp = new Newsacp;
class Newsacp {

	# classes
	protected	$engine;
	protected	$limit_news_per_page = 10;
	
	protected	$fileprefix = "news";
	

	
	function __construct() {
	
		global $roocms, $tpl, $html, $parse, $GET;
		
		// init engine
		$this->engine = new NewsEngine;
		
		
		// Load Template  ===============================
		$tpl->load_template("acp_news");
		//===============================================
		
		
		// выводим текущее время
		$current_time = $parse->date->unix_to_rus($this->engine->udate, true, false);
		$html['current_time'] = $tpl->tpl->current_time($current_time);
		
		
		switch($roocms->part) {
			/* функции добавления */
			case 'addnews':
				$this->addnews();
				break;
				
			case 'addcategory':
				$this->addcategory();
				break;
				
			/* Функции редактирования */
			case 'editnews':
				if($this->engine->news_id != 0)
					$this->editnews($this->engine->news_id);
				else
					$this->idx();
				break;
				
			case 'editcat':
				if($this->engine->category_id != 0)
					$this->editcat();
				else 
					$this->idx();
				break;
				
			case 'sortcategory':
				$this->sortcategory();
				break;
			
				
			/* функции обновления */
			case 'updatenews':
				$this->updatenews();
				break;
				
			case 'updatecat':
				$this->updatecat();
				break;
				
			case 'updatesortcategory':
				$this->updatesortcategory();
				break;
				
			/* функции удаления */
			case 'delnews':
				if($this->engine->news_id != 0)
					$this->delnews($this->engine->news_id);
				else
					$this->idx();
				break;
			
			case 'delimage':
				if($GET->_image != 0)
					$this->delimage();
				else
					$this->idx();
				break;
				
			case 'delfile':
				if($GET->_file != 0)
					$this->delfile();
				else
					$this->idx();
				break;
				
			case 'delcat':
				if($this->engine->category_id != 0)
					$this->delcat($this->engine->category_id);
				else
					$this->idx();
				break;
				
				
			default:
				$this->idx();
				break;
		}
	}
	

	//*******************************************	
	// по умолчанию показывается список новостей и форма добавления новости
	private function idx() {
	
		global $db, $tpl, $html, $parse;
		
		
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================
		// draw category select
		foreach($category AS $key=>$value) {
			$html['category'][]				= $tpl->tpl->category($value);
			$html['select_pcategory'][]		= $tpl->tpl->select_pcategory($value['cat_id'], 
																		  $value['cat_name'], 
																		  $value['padding'], 
																		  $this->engine->settings['indention']);
		}
		
		
		// stat
		$html['total_cats']		= $this->engine->total_cats;
		$html['total_news']		= $this->engine->total_news;
		$html['total_vnews']	= $this->engine->total_vnews;
		
		
		// parse date
		$newdate = $parse->date->unix_to_gregorian($this->engine->udate);
		$new = explode("/", $newdate);
		
		$new['day']		= $parse->field_select_day($new[1]);
		$new['month']	= $parse->field_select_month($new[0]);
		$new['year']	= $parse->field_select_year($new[2]);
		
		
		// set limit works on page
		$db->limit = $this->limit_news_per_page;
		
		
		if($this->engine->category_id > 0) {
		
			// calculate pages
			$db->pages_mysql(NEWS_ITEM, "category_id='".$this->engine->category_id."'");
			
			// draw nav pages
			if($db->pages >= 2) {
				$html['navpage'] = $tpl->tpl->navpage();
				for($p=1;$p<=$db->pages;$p++) {
					$html['navpage_el'][] = $tpl->tpl->navpage_el($p, $this->engine->category_id);
				}
			}
		
			// init {html:addnews}
			$html['addnews'] 	= $tpl->tpl->addnews($new);
		
			// запрашиваем новости из ленты
			$q = $db->query("SELECT id, category_id, date_create, date_update, date, title, brief_news, full_news, images, files 
								FROM ".NEWS_ITEM."
								WHERE category_id = '".$this->engine->category_id."'
								ORDER BY date DESC, id DESC 
								LIMIT ".$db->from.",".$db->limit);
			while($news = $db->fetch_assoc($q)) {
				// parse
				$news['brief_news'] 	= $parse->text->html($news['brief_news']);
				$news['full_news'] 		= $parse->text->html($news['full_news']);
				$news['rdate']			= $parse->date->unix_to_rus($news['date']);
				$news['date_create']	= $parse->date->unix_to_rus($news['date_create']);
				$news['date_update']	= $parse->date->unix_to_rus($news['date_update']);
				
				// init {html:newslist}
				$html['newslist'][] = $tpl->tpl->newslist($news);
			}
		}
		else {
			$html['newslist'] = $html['addnews'] = $tpl->tpl->choosecategory();
		}
		
		
		// init {html:add_category}
		$html['addcategory'] = $tpl->tpl->addcategory();
		
		
		// init {html:content}
		$html['content'] = $tpl->tpl->idx();

	}
	
	
	//*******************************************
	// Добавляем новую новость 
	private function addnews() {
		
		global $db, $parse, $POST, $gd, $files;
		
		// Если заявлен постинг новой новости
		if(@$_REQUEST['add_news']) {
			if(isset($POST->title) && isset($POST->brief_news) && isset($POST->full_news)) {
			
				// parse date
				$date = $parse->date->gregorian_to_unix($POST->month."/".$POST->day."/".$POST->year);
			
			
				// check empty meta data
				if(!isset($POST->meta_description)) $POST->meta_description = "";
				if(!isset($POST->meta_keywords))	$POST->meta_keywords	= "";

			
				// insert sql news
				$db->query("INSERT INTO ".NEWS_ITEM." (title, category_id, date_create, date_update, date, brief_news, full_news, meta_description, meta_keywords)
												VALUE ('".$POST->title."', '".$POST->category."', '".time()."', '".time()."', '".$date."', '".$POST->brief_news."', '".$POST->full_news."', '".$POST->meta_description."', '".$POST->meta_keywords."')");
				
				
				// узнаем ИД только что вставленной новости
				$news_id = $db->insert_id();
				
				
				// грузим фото и создаем превью.
				$image = $gd->post_upload("image", $this->fileprefix);
				
				// Если загрузка прошла успено
				if($image) $this->add_image($news_id, $image);
				
				
 				//Грузим файлы
				$attach = $files->upload("file", $this->fileprefix);
				
				//Если файлы успешно загружены
				if($attach) {
					if($attach) $this->add_attach($news_id, $attach);
				} 
				
				// считаем атачи
				$this->count_attach($news_id);
				

				// считаем количество новостей в категории
				$this->count_news($POST->category);
				
				
				# notice
				$_SESSION['info'][] = "Новость успешно добавлена";
			}
			else {
				# error
				$_SESSION['error'][] = "Не удалось добавить новость:";
				if(!isset($POST->title))		$_SESSION['error'][] = "Не указан заголовок новости!";
				if(!isset($POST->brief_news))	$_SESSION['error'][] = "Вы ненаписали краткое представление новости!";
				if(!isset($POST->full_news))	$_SESSION['error'][] = "Вы ничего не написали в качестве новости!";
			}
		}
		
		// move
		goback();
	}
	
	
	//*******************************************
	// добавляем категорию
	private function addcategory() {
	
		global $db, $POST;
		
		if(@$_REQUEST['add_category']) {
			if(isset($POST->title)) {
			
				
				// check empty meta data
				if(!isset($POST->meta_description)) $POST->meta_description = "";
				if(!isset($POST->meta_keywords))	$POST->meta_keywords	= "";
			
			
				// insert sql category
				$db->query("INSERT INTO ".NEWS_CATEGORY." (parent_id, name, meta_description, meta_keywords) 
												   VALUES ('".$POST->cat_parent."', '".$POST->title."', '".$POST->meta_description."', '".$POST->meta_keywords."')");
												   
				# notice
				$_SESSION['info'][] = "Категория добавлена";
			}
			else {
				# error
				$_SESSION['error'][] = "Не удалось добавить категорию:";
				if(!isset($POST->title))		$_SESSION['error'][] = "Не указано название категории!";
			}
		}
		
		// move
		goback();
	}
	
	
	//*******************************************
	// редатируем новость
	private function editnews($news_id) {
	
		global $db, $tpl, $html, $parse, $GET, $imagetype, $filetype;
		
		
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================
		
		
		// запрашиваем новость из БД
		$q = $db->query("SELECT id, category_id, title, date_create, date_update, date, brief_news, full_news, meta_description, meta_keywords FROM ".NEWS_ITEM." WHERE id='".$news_id."'");
		$news = $db->fetch_assoc($q);
		
		
		//set init category
		$this->engine->category_id = $news['category_id'];
		
		
		// парсим даты в русский формат
		$news['rdate'] 			= $parse->date->unix_to_rus($news['date']);
		$news['date_create'] 	= $parse->date->unix_to_rus($news['date_create']);
		$news['date_update'] 	= $parse->date->unix_to_rus($news['date_update']);
		
		
		// парсим дату для отображения в форме
		$gdate 	= $parse->date->unix_to_gregorian($news['date']);
		$date 	= explode("/",$gdate);
		$news['day']	=	$parse->field_select_day($date[1]);
		$news['month']	=	$parse->field_select_month($date[0]);
		$news['year']	=	$parse->field_select_year($date[2]);
		
		
		// проверяем есть ли картинки
		$images = $this->engine->load_news_image($news['id']);
		foreach($images AS $key=>$value) {
			$html['images'][] = $tpl->tpl->images($images[$key]);
		}
		
		// проверяем есть ли файлы
		$files = $this->engine->load_news_file($news['id']);
		foreach($files AS $key=>$value) {
			$html['files'][] = $tpl->tpl->files($files[$key]);
		}
		
		
		$GET->_category = $news['category_id'];
		foreach($category AS $key=>$value) {
			$html['category'][]			= $tpl->tpl->category($value);
			$html['select_pcategory'][]	= $tpl->tpl->select_pcategory($value['cat_id'], 
																	  $value['cat_name'], 
																	  $value['padding'], 
																	  $this->engine->settings['indention']);
		}
		
		
		// init {html:content}
		$html['content'] = $tpl->tpl->editnews($news);
	}
	
	
	//*******************************************
	// Редактируем категорию
	private function editcat() {
	
		global $db, $tpl, $html;
	
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================
		// construct tree
		foreach($category AS $key=>$value) {
			$html['category'][]			= $tpl->tpl->category($value);
			$html['select_pcategory'][]	= $tpl->tpl->select_pcategory($value['cat_id'], 
																	  $value['cat_name'], 
																	  $value['padding'], 
																	  $this->engine->settings['indention'],
																	  $this->engine->category_info['parent_id']);
		}
		
		// init html
		$html['content'] = $tpl->tpl->editcat($this->engine->category_info);
	}
	
	
	//*******************************************
	//	Сортируем категории
	private function sortcategory() {
		
		global $db, $tpl, $html;
		
		
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================
		// draw category select
		foreach($category AS $key=>$value) {
			$html['category'][]		= $tpl->tpl->category($value);
			$html['scategory'][] 	= $tpl->tpl->scategory($value);
		}
		
		$html['content'] = $tpl->tpl->sortcategory();
	}
	
	
	//*******************************************
	// обновление новости
	private function updatenews() {
		
		global $db, $parse, $POST, $gd, $files;
		
		if(@$_REQUEST['update_news']) {
			if(isset($POST->title) && isset($POST->brief_news) && isset($POST->full_news) && isset($POST->id) && isset($POST->category) && $db->check_id($POST->category, NEWS_CATEGORY) ) {
				if($db->check_id($POST->id, NEWS_ITEM) == 1) {
				
					// parse date
					$date = $parse->date->gregorian_to_unix($POST->month."/".$POST->day."/".$POST->year);

					
					// check empty meta data
					if(!isset($POST->meta_description)) $POST->meta_description = "";
					if(!isset($POST->meta_keywords))	$POST->meta_keywords	= "";
					
					
					// update sql news
					$db->query("UPDATE ".NEWS_ITEM." 
									SET
										category_id='".$POST->category."',
										title='".$POST->title."',
										date_update='".time()."',
										date='".$date."',
										brief_news='".$POST->brief_news."',
										full_news='".$POST->full_news."',
										meta_description='".$POST->meta_description."',
										meta_keywords='".$POST->meta_keywords."'
									WHERE 
										id='".$POST->id."'");
					
					
					// чистим описание картинок в БД
					$db->query("UPDATE ".NEWS_IMAGE." 
									SET		description=''
									WHERE	news_id='".$POST->id."'");
									
					// чистим описание файлов в БД
					$db->query("UPDATE ".NEWS_FILES." 
									SET		description=''
									WHERE	news_id='".$POST->id."'");

					
					// Правим описание картинок
					if(isset($POST->editimage)) {
						foreach($POST->editimage AS $key=>$value) {
							$db->query("UPDATE ".NEWS_IMAGE."
											SET		description='".$value."'
											WHERE	id='".$key."'");
						}
					}
					
					// Правим описание файлов
					if(isset($POST->editfile)) {
						foreach($POST->editfile AS $key=>$value) {
							$db->query("UPDATE ".NEWS_FILES."
											SET		description='".$value."'
											WHERE	id='".$key."'");
						}
					}
					
					
					// грузим фото и создаем превью.
					$image = $gd->post_upload("image", $this->fileprefix);
					// Если загрузка катинок прошла успешно вносим их в базу
					if($image) $this->add_image($POST->id, $image);
					
					
					//Грузим файлы
					$attach = $files->upload("file", $this->fileprefix);
					//Если файлы успешно загружены
					if($attach) $this->add_attach($POST->id, $attach);
					
					
					// считаем атачи
					$this->count_attach($POST->id);
					
					
					// считаем количество новостей в категории
					$this->count_news($POST->category);
					
					
					// notice
					$_SESSION['info'][] = "Новость обновлена";
				}
			}
			else {
				# error
				$_SESSION['error'][] = "Не удалось обновить новость:";
				if(!isset($POST->title))		$_SESSION['error'][] = "Пустое название новости!";
				if(!isset($POST->brief_news))	$_SESSION['error'][] = "Путстое краткое представление новости!";
				if(!isset($POST->full_news))	$_SESSION['error'][] = "Пустая новость!";
			}
		}
		
		// move
		goback();
	}
	
	
	//*******************************************
	// обновляем категорию
	private function updatecat() {
	
		global $db, $POST;
		
		if(@$_REQUEST['update_cat']) {
			if(isset($POST->thisid) && $POST->thisid == $this->engine->category_id && isset($POST->title)) {
				if($this->engine->check_subcat($POST->thisid,$POST->parent_category)) {
					
					// check empty meta data
					if(!isset($POST->meta_description)) $POST->meta_description = "";
					if(!isset($POST->meta_keywords))	$POST->meta_keywords	= "";
					
					// sql update
					$db->query("UPDATE ".NEWS_CATEGORY."
									SET
										parent_id='".$POST->parent_category."',
										name='".$POST->title."',
										meta_description='".$POST->meta_description."',
										meta_keywords='".$POST->meta_keywords."'
									WHERE
										id='".$POST->thisid."'");
									
					// считаем количество новостей в категории
					$this->count_news($POST->thisid);
					
					# notice
					$_SESSION['info'][] = "Категория успешно обновлена!";
				}
				else {
					# Error
					$_SESSION['error'][] = "Вы неверное указали родительскую категорию. Изменения сохранить не удалось.";
				}
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
	//	Обновляем порядок вывода категория
	private function updatesortcategory() {
	
		global $db, $POST;
		
		if(@$_REQUEST['update_sort_category'] && isset($POST->id) && is_array($POST->id)) {
			
			// Load category tree ==============
			$category = $this->engine->category_tree();
			//==================================
			$cats = array();
			foreach($category AS $key=>$value) {
				$cats[$value['cat_id']] = $value['sort'];
			}
			
			foreach($POST->id AS $key=>$value) {
				if(array_key_exists($key, $cats) && $value != $cats[$key]) {
					$db->query("UPDATE ".NEWS_CATEGORY." SET sort='".$value."' WHERE id='".$key."'");
					# notice
					$_SESSION['info'][] = "Порядок для категории id=".$key." на ".$value." изменен";
				}
			}
		}
		
		// move
		goback();
	}
	
	//*******************************************
	// удаляем новость
	private function delnews($news_id) {
		
		global $db;
		
		$q = $db->query("SELECT category_id FROM ".NEWS_ITEM." WHERE id='".$news_id."'");
		$news = $db->fetch_assoc($q);

		// удаляем картинки
		$q = $db->query("SELECT id, original_img, thumb_img FROM ".NEWS_IMAGE." WHERE news_id='".$news_id."'");
		while($row = $db->fetch_assoc($q)) {
			if(file_exists(_UPLOAD."/".$row['original_img']))	unlink(_UPLOAD."/".$row['original_img']);
			if(file_exists(_UPLOAD."/".$row['thumb_img']))		unlink(_UPLOAD."/".$row['thumb_img']);
		}
		
		// удаляем файлы
		$q = $db->query("SELECT id, filename FROM ".NEWS_FILES." WHERE news_id='".$news_id."'");
		while($row = $db->fetch_assoc($q)) {
			if(file_exists(_UPLOADFILES."/".$row['filename']))	unlink(_UPLOADFILES."/".$row['filename']);
		}
		
		// удаляем записи картинок из БД
		$db->query("DELETE FROM ".NEWS_IMAGE." WHERE news_id='".$news_id."'");
		// удаляем записи файлов из БД
		$db->query("DELETE FROM ".NEWS_FILES." WHERE news_id='".$news_id."'");
		// удаляем новость из БД
		$db->query("DELETE FROM ".NEWS_ITEM." WHERE id='".$news_id."'");

		// Считаем новости в категории
		$this->count_news($news['category_id']);
		
		// notice
		$_SESSION['info'][] = "Новость удалена";
		
			
		// move
		goback();
	}
	
	
	//*******************************************
	// удаляем картинку
	private function delimage() {
		
		global $db, $GET;
		
		
		if($db->check_id($GET->_image, NEWS_IMAGE) == 1) {
			// удаляем картинку
			$q = $db->query("SELECT id, news_id, original_img, thumb_img FROM ".NEWS_IMAGE." WHERE id='".$GET->_image."'");
			while($row = $db->fetch_assoc($q)) {
				$news_id = $row['news_id'];
				
				if(file_exists(_UPLOAD."/".$row['original_img']))	unlink(_UPLOAD."/".$row['original_img']);
				if(file_exists(_UPLOAD."/".$row['thumb_img']))		unlink(_UPLOAD."/".$row['thumb_img']);
			}
			
			// удаляем запись картинки из БД
			$db->query("DELETE FROM ".NEWS_IMAGE." WHERE id='".$GET->_image."'");
			
			// считаем атачи
			$this->count_attach($news_id);
			
			// notice
			$_SESSION['info'][] = "Картинка удалена";
		}
		else {
			# error
			$_SESSION['error'][] = "Не удалось обнаружить запись о картинке в БД, что бы её удалить!";
		}
		
		// move
		goback();
	}
	
	
	//*******************************************
	// удаляем файл
	private function delfile() {
		
		global $db, $GET;
		
		
		if($db->check_id($GET->_file, NEWS_FILES) == 1) {
			// удаляем файл
			$q = $db->query("SELECT id, news_id, filename FROM ".NEWS_FILES." WHERE id='".$GET->_file."'");
			while($row = $db->fetch_assoc($q)) {
				$news_id = $row['news_id'];
			
				if(file_exists(_UPLOADFILES."/".$row['filename']))	unlink(_UPLOADFILES."/".$row['filename']);
			}
			
			// удаляем запись картинки из БД
			$db->query("DELETE FROM ".NEWS_FILES." WHERE id='".$GET->_file."'");
			
			// считаем атачи
			$this->count_attach($news_id);
			
			// notice
			$_SESSION['info'][] = "Файл удален";
		}
		else {
			# error
			$_SESSION['error'][] = "Не удалось обнаружить запись о файле в БД, что бы его удалить!";
		}
		
		// move
		goback();
	}
	
	
	//*******************************************
	// Удаляет выбранную категорию из портфолио
	private function delcat($category_id) {
	
		global $db;
		
		$q = $db->query("SELECT count(*) FROM ".NEWS_ITEM." WHERE category_id='".$category_id."'");
		$c = $db->fetch_row($q);
		if($c[0] == 0) {
			// удаляем катгорию
			$db->query("DELETE FROM ".NEWS_CATEGORY." WHERE id='".$category_id."'");
		
			// notice
			$_SESSION['info'][] = "Категория удалена";
		}
		else $_SESSION['error'][] = "Невозможно удалить категорию: В категории имеются новости";

		// move
		go(THIS_SCRIPT.".php?act=news");
	}
	
	
	//*******************************************
	// Добавляем новые картинки к новостям
	private function add_image($news_id, $images) {
		
		global $db;
	
		foreach($images as $value) {
			if($value) {
				// дописать проверку миниматюры
				$db->query("INSERT INTO ".NEWS_IMAGE." (news_id, original_img, thumb_img)
												VALUES ('".$news_id."', '".$value."', 'thumb_".$value."')");
				
				// notice
				$_SESSION['info'][] = "Файл ".$value." успешно загружен";
			}
		}
	}
	
	
	//*******************************************
	// Добавляем новые файлы к новостям
	private function add_attach($news_id, $attach) {
		
		global $db;

		foreach($attach as $value) {
			if($value) {
				$db->query("INSERT INTO ".NEWS_FILES." (news_id, filename, ext, description)
												VALUES ('".$news_id."', '".$value['name']."', '".$value['ext']."', '".$value['real_name']."')");
				
				// notice
				$_SESSION['info'][] = "Файл ".$value['real_name']." успешно загружен";
			}
		}
	}
	
	
	//*******************************************
	//	Функция подсчета картинок и файлов у новости
	private function count_attach($news_id) {
		
		global $db;
		
		// считаем картинки
		$q = $db->query("SELECT count(*) FROM ".NEWS_IMAGE." WHERE news_id='".$news_id."'");
		$i = $db->fetch_row($q);
		
		// считаем файлы
		$q = $db->query("SELECT count(*) FROM ".NEWS_FILES." WHERE news_id='".$news_id."'");
		$f = $db->fetch_row($q);

		// обновляем данные
		$db->query("UPDATE ".NEWS_ITEM." SET images='".$i[0]."', files='".$f[0]."' WHERE id='".$news_id."'");
	}
	
	
	//*******************************************
	// Обновляем число новостей в категории
	function count_news($category_id) {
	
		global $db;
		
		$q = $db->query("SELECT count(*) FROM ".NEWS_ITEM." WHERE category_id='".$category_id."'");
		$c = $db->fetch_row($q);
		
		$db->query("UPDATE ".NEWS_CATEGORY." SET items='".$c[0]."' WHERE id='".$category_id."'");
		//$_SESSION['info'][] = "Счетчик обновлен ".$category_id;
	}
}

?>