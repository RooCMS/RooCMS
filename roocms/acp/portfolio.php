<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS mod Portfolio
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
|	Build date: 		17:58 04.12.2010
|	Last Biuld: 		3:00 17.10.2011
|	Version file:		1.00 build 12
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


require_once _CMS."/functions_portfolio.php";


$portfolioacp = new Portfolioacp;
class Portfolioacp {
	
	# classes
	protected $engine;
	
	

	//*******************************************
	// Start
	function __construct() {
		
		global $db, $tpl, $GET, $roocms;
		
		
		// run engine
		$this->engine = new PortfolioEngine;
	

		// Load Template  ==============================
		$tpl->load_template("acp_portfolio");
		//==============================================
		
		
		switch($roocms->part) {
			
			/* Список работ */
			case 'works':
				$this->projects();
				break;
			
			/* Функции добавления */
			case 'addwork':
				$this->add_project();
				break;
				
			case 'addcat':
				$this->add_category();
				break;
				
			/* Функции редактирования */
			case 'edit_project':
				if($this->engine->project_id != 0) 
					$this->edit_project($this->engine->project_id);
				else 
					$this->projects();
				break;
				
			case 'editcat':
				if($this->engine->category_id != 0)
					$this->edit_category();
				else 
					$this->projects();
				break;
				
			/* Функции обновления */
			
			case 'sortwork':
				$this->sortwork();
				break;
				
			case 'update_project':
				$this->update_project();
				break;
				
			case 'updatecat':
				$this->update_category();
				break;
			
			/* Функция удаления */
			case 'delcat':
				if($this->engine->category_id != 0) 	
					$this->del_category($this->engine->category_id);
				else 
					$this->projects();
				break;
				
			case 'del_project':
				if($this->engine->project_id != 0) 
					$this->del_project($this->engine->project_id);
				else 
					$this->projects();
				break;
				
			default:
				$this->projects();
				break;
				
		}
	}
	
	
	//*******************************************
	// Load works
	private function projects() {
	
		global $db, $tpl, $html;
		
		
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================

		
		// set limit works on page
		$db->limit = 10;
		
		// calculate pages
		$db->pages_mysql(PORTFOLIO_PROJECT,"category_id='".$this->engine->category_id."'");
		
		// draw nav pages
		if($db->pages >= 2) {
			$html['navpage'] = $tpl->tpl->navpage();
			for($p=1;$p<=$db->pages;$p++) {
				$html['navpage_el'][] = $tpl->tpl->navpage_el($this->engine->category_id, $p);
			}
		}
		
		// Форма добавления проектов
		if($this->engine->category_id != 0 && $this->engine->check_type($this->engine->category_id) == "category") {
			// tags ui
			$tags = $this->engine->tags();
			if($tags) foreach($tags AS $key=>$value) {$html['tags'][] = $tpl->tpl->tags($value['key']);}
			// form
			$html['form_addwork'] = $tpl->tpl->form_addwork();
		}
		else $html['form_addwork'] = $tpl->tpl->form_not_addwork();

		// Load projects 
		$allprojects = 0;
		$q = $db->query("SELECT id, title, sub_title, sort, poster
							FROM ".PORTFOLIO_PROJECT." 
							WHERE category_id='".$this->engine->category_id."' 
							ORDER BY sort ASC 
							LIMIT ".$db->from.",".$db->limit);
		while($project = $db->fetch_assoc($q)) {

			$allprojects++;
			
			$html['project'][]	= $tpl->tpl->project($project);
		}

		
		for($i=0;$i<=count($category)-1;$i++) {
			$html['category'][]				= $tpl->tpl->category($category[$i]);
			$html['select_pcategory'][]		= $tpl->tpl->select_pcategory($category[$i]['cat_id'], $category[$i]['cat_name'], $category[$i]['padding'], $this->engine->settings['indention']);
			$html['select_pwcategory'][]	= $tpl->tpl->select_pwcategory($category[$i]['cat_id'], $category[$i]['cat_name'], $category[$i]['type'], $category[$i]['padding'], $this->engine->settings['indention']);
		}


		$html['content']	= $tpl->tpl->allprojects($allprojects);
	}
	
	
	//*******************************************
	// Добавляет работы в каталог
	private function add_project() {
		
		global $db, $POST, $gd;

		if(@$_REQUEST['add_project']) {
			if(isset($POST->title) && isset($POST->sub_title) && isset($POST->category)) {
				
				// Категория для загрузки
				if($db->check_id(round($POST->category), PORTFOLIO_CATEGORY) == 1 && $this->engine->check_type($POST->category) == "category") {
				
					$category = $POST->category;
					
					
					// subtitle
					if(!isset($POST->sub_title)) $POST->sub_title = "";
					// link
					if(!isset($POST->link)) $POST->link = "";
					// tags
					if(!isset($POST->tags)) $POST->tags = "";
					$POST->tags = mb_strtolower($POST->tags, 'utf8');
					$tag = explode(",",$POST->tags);
					foreach($tag as $key=>$value) {
						if($key == 0) $POST->tags = trim($value);
						else $POST->tags .= ", ".trim($value);
					}
					
					
					$poster = $gd->post_upload('poster', 'prj_poster', false, false);
					if($poster) {
					
						// insert sql work
						$db->query("INSERT INTO ".PORTFOLIO_PROJECT." (category_id, title, sub_title, link, tags, poster)
															 VALUES ('".$category."', '".$POST->title."', '".$POST->sub_title."', '".$POST->link."', '".$POST->tags."', '".$poster."')");
						
						// получаем ид только что добавленно работы
						$project_id = $db->insert_id();									 
					
					
						// Подгружаем этапы работы
						$pics 	= $gd->post_upload('step_picture', 'prj_step_pic', false, false, _UPLOAD, false);
						foreach ($POST->step AS $key=>$value) {
							if(isset($POST->step_description[$key])) {
								$db->query("INSERT INTO ".PORTFOLIO_PROJECT_STEPS." (project_id, step, step_picture, step_description)
																			 VALUES ('".$project_id."', '".$POST->step[$key]."', '".$pics[$key]."', '".$POST->step_description[$key]."')");
							}
							else {
								if(!empty($pics[$key]) && file_exists(_UPLOAD."/".$pics[$key]))	
									unlink(_UPLOAD."/".$pics[$key]);
								$_SESSION['error'][] = "Этап ".$POST->step[$key]." не удалось добавить. Нет описания.";
							}
						}
					
					
						// Обновляем количество работ в категории
						$this->count_projects($category);
						
						// notice
						$_SESSION['info'][] = "Работа добавлена в портфолио";
					}
					else $_SESSION['error'][] = "Не удалось добавить работу, поскольку Вы не загрузили обложку проекта.";
				}
				else $_SESSION['error'][] = "Не удалось добавить работу! Вы неверено указали категорию. Добавлять работы можно только в категории.";
			}
			else {
				# error
				$_SESSION['error'][] = "Не удалось добавить работу";
				if(!isset($POST->title))		$_SESSION['error'][] = "Не указано название работы!";
				if(!isset($POST->sub_title))	$_SESSION['error'][] = "Не указано краткое описание работы!";
				if(!isset($POST->category))		$_SESSION['error'][] = "Не указана категория для добавления!";
			}
		}
		
		// move
		goback();
	}
	
	
	//*******************************************
	// Добавляет новую категорию в портфолио
	private function add_category() {
		
		global $db, $POST;
		
		if(@$_REQUEST['new_category']) {
			if(isset($POST->cat_name)) {
				
				// type
				$type = "category";
				if(isset($POST->type) && $POST->type == "part") $type = "part";
				
				
				// sql insert
				$db->query("INSERT INTO ".PORTFOLIO_CATEGORY." (parent_id, name, type)
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
	// редактор выбранной работы
	private function edit_project($project_id) {
		
		global $db, $tpl, $html, $GET;
		
		// Load category tree ==============
		$category = $this->engine->category_tree();
		//==================================

		$q = $db->query("SELECT id, category_id, sort, title, sub_title, description, link, tags, poster FROM ".PORTFOLIO_PROJECT." WHERE id='".$project_id."'");
		$project = $db->fetch_assoc($q);
		
		$GET->_category = $project['category_id'];
		for($i=0;$i<=count($category)-1;$i++) {
			$html['category'][]				= $tpl->tpl->category($category[$i]);
			$html['select_pcategory'][]		= $tpl->tpl->select_pcategory($category[$i]['cat_id'], $category[$i]['cat_name'], $category[$i]['padding'], $this->engine->settings['indention']);
			$html['select_pwcategory'][]	= $tpl->tpl->select_pwcategory($category[$i]['cat_id'], $category[$i]['cat_name'], $category[$i]['type'], $category[$i]['padding'], $this->engine->settings['indention']);
		}
		
		
		// tags ui
		$tags = $this->engine->tags();
		if($tags) foreach($tags AS $key=>$value) {$html['tags'][] = $tpl->tpl->tags($value['key']);}
		
		
		// load steps
		$project['steps'] = "";
		$qs = $db->query("SELECT id, step, step_picture, step_description FROM ".PORTFOLIO_PROJECT_STEPS." WHERE project_id='".$project_id."' ORDER BY step ASC");
		while($step = $db->fetch_assoc($qs)) {
			$project['steps'] .= $tpl->tpl->step($step);
		}
		
		
		// init template
		$html['content'] = $tpl->tpl->editproject($project);
	}
	
	
	//*******************************************
	// редактируем категорию
	private function edit_category() {
		
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
	// Удаляет выбранную категорию из портфолио
	private function del_category($category_id) {
	
		global $db;
		
		$q = $db->query("SELECT count(*) FROM ".PORTFOLIO_PROJECT." WHERE category_id='".$category_id."'");
		$c = $db->fetch_row($q);
		if($c[0] == 0) {
			// удаляем катгорию
			$db->query("DELETE FROM ".PORTFOLIO_CATEGORY." WHERE id='".$category_id."'");
		
			// notice
			$_SESSION['info'][] = "Категория удалена";
		}
		else $_SESSION['error'][] = "Невозможно удалить категорию: В категории имеются работы";

		// move
		go(THIS_SCRIPT.".php?act=portfolio&part=works");
	}
	
	
	//*******************************************
	// Удаляет выбранную работу из портфолио
	private function del_project($project_id) {
	
		global $db, $GET;
		
		// запрос данных
		$q = $db->query("SELECT category_id, poster FROM ".PORTFOLIO_PROJECT." WHERE id='".$project_id."'");
		$project = $db->fetch_assoc($q);
		
		// 1. Удаляем постер
		if(file_exists(_UPLOAD."/".$project['poster'])) unlink(_UPLOAD."/".$project['poster']);
		
		// 2. Удаляем изображения этапов
		$q = $db->query("SELECT step_picture FROM ".PORTFOLIO_PROJECT_STEPS." WHERE project_id='".$project_id."' AND step_picture!=''");
		while($row = $db->fetch_assoc($q)) {
			if(file_exists(_UPLOAD."/".$row['step_picture'])) unlink(_UPLOAD."/".$row['step_picture']);
		}
		
		// 3. удаляем этапы
		$db->query("DELETE FROM ".PORTFOLIO_PROJECT_STEPS." WHERE project_id='".$project_id."'");
		
		// 4. удаляем проект
		$db->query("DELETE FROM ".PORTFOLIO_PROJECT." WHERE id='".$project_id."'");
		
		
		// Обновляем количество работ в категории
		$this->count_projects($project['category_id']);
		
		
		// notice
		$_SESSION['info'][] = "Работа удалена";
		
		// move
		goback();
	}
	
	
	//*******************************************
	// обновляем работу
	private function update_project() {
		
		global $db, $POST, $gd;
		
		if(@$_REQUEST['update_project']) {
			if(isset($POST->title) && isset($POST->sub_title) && isset($POST->id)) {	
				if($db->check_id(round($POST->id), PORTFOLIO_PROJECT) == 1 && isset($POST->category) && $db->check_id($POST->category, PORTFOLIO_CATEGORY) && $this->engine->check_type($POST->category) == "category") {
					
					// link
					if(!isset($POST->link)) $POST->link = "";
					// tags

					if(!isset($POST->tags)) $POST->tags = "";
					$POST->tags = mb_strtolower($POST->tags, 'utf8');
					$tag = explode(",",$POST->tags);
					foreach($tag as $key=>$value) {
						if($key == 0) $POST->tags = trim($value);
						else $POST->tags .= ", ".trim($value);
					}
					
					
					// update poster
					$upposter = "";
					$poster = $gd->post_upload("poster", "prj_poster", false, false);
					if($poster) {
						// удаляем старый постер
						$q = $db->query("SELECT poster FROM ".PORTFOLIO_PROJECT." WHERE id=".$POST->id."");
						$p = $db->fetch_assoc($q);
						if(!empty($p['poster']) && file_exists(_UPLOAD."/".$p['poster'])) 
							unlink(_UPLOAD."/".$p['poster']);
						
						// присваиваем значение нового постера
						$upposter = "poster='".$poster."',";
					}
					
					
					// update sql project
					$db->query("UPDATE ".PORTFOLIO_PROJECT." 
									SET
										title='".$POST->title."',
										sub_title='".$POST->sub_title."',
										link='".$POST->link."',
										tags='".$POST->tags."',
										{$upposter}
										category_id='".$POST->category."'
									WHERE 
										id='".$POST->id."'");
										
										
					// обновляем этапы проекта
					if(isset($POST->step_id)) {
						$pics = $gd->post_upload("step_picture","prj_step_pic", false, false, _UPLOAD, false);
						foreach($POST->step_id AS $key=>$value) {
							if(isset($POST->step_description[$key])) {
								
								if(!isset($POST->step[$key])) $POST->step[$key] = 0;
								
								$pic = "";
								
								// Удаляем картинку у этапа по галочке или в случае полного удаления этапа
								if(isset($POST->del_step_picture[$key]) || isset($POST->del_step[$key])) {
									$qp = $db->query("SELECT step_picture FROM ".PORTFOLIO_PROJECT_STEPS." WHERE id='".$POST->step_id[$key]."'");
									$dp = $db->fetch_assoc($qp);
									
									if($dp['step_picture'] != "" && file_exists(_UPLOAD."/".$dp['step_picture'])) 
										unlink(_UPLOAD."/".$dp['step_picture']);
										
									$pic = "step_picture='',";
										
									// на случай если полу file не было пустым
									if(!empty($pics[$key]) && file_exists(_UPLOAD."/".$pics[$key])) {
										unlink(_UPLOAD."/".$pics[$key]);
										$pics[$key] = "";
									}
								}

								// Обвновляем картинку (если не стояло галки "удалить картинку" или "удалить этап")
								if(!empty($pics[$key])) {
									$qp = $db->query("SELECT step_picture FROM ".PORTFOLIO_PROJECT_STEPS." WHERE id='".$POST->step_id[$key]."'");
									$dp = $db->fetch_assoc($qp);
									
									if($dp['step_picture'] != "" && file_exists(_UPLOAD."/".$dp['step_picture'])) 
										unlink(_UPLOAD."/".$dp['step_picture']);
									
									$pic = "step_picture='".$pics[$key]."',";
								}
								
								
								if(!isset($POST->del_step[$key])) {
									# update step
									$q = $db->query("UPDATE ".PORTFOLIO_PROJECT_STEPS." 
														SET 
															step='".$POST->step[$key]."',
															{$pic}
															step_description='".$POST->step_description[$key]."'
														WHERE
															id='".$POST->step_id[$key]."'");
															
									# notice
									$_SESSION['info'][] = "Этап ".$POST->step[$key]." успешно обновлен";
								}
								else {	
									# delete step
									$db->query("DELETE FROM ".PORTFOLIO_PROJECT_STEPS." WHERE id='".$POST->step_id[$key]."'");
									
									# notice
									$_SESSION['info'][] = "Этап ".$POST->step[$key]." удален";
								}
							}
							else {
								$_SESSION['error'][] = "Не удалось обновить этап ".$POST->step[$key]." потому что не было указано описание";
								if(isset($pics[$key]) && file_exists(_UPLOAD."/".$pics[$key])) 
									unlink(_UPLOAD."/".$pics[$key]);
							}
						}
					}
					
					
					// Подгружаем новые этапы работы
					if(isset($POST->new_step_description)) {
						$newpics 	= $gd->post_upload('new_step_picture', 'prj_step_pic', false, false, _UPLOAD, false);
						foreach ($POST->new_step AS $key=>$value) {
							if(isset($POST->new_step_description[$key])) {
								$db->query("INSERT INTO ".PORTFOLIO_PROJECT_STEPS." (project_id, step, step_picture, step_description)
																			 VALUES ('".$POST->id."', '".$POST->new_step[$key]."', '".$newpics[$key]."', '".$POST->new_step_description[$key]."')");
							}
							else {
								if(!empty($newpics[$key]) && file_exists(_UPLOAD."/".$newpics[$key]))	
									unlink(_UPLOAD."/".$newpics[$key]);
								$_SESSION['error'][] = "Этап ".$POST->new_step[$key]." не удалось добавить. Нет описания.";
							}
						}
					}


					// Обновляем количество работ
					$this->count_projects($POST->category);
					if($POST->category != $POST->category_id)	$this->count_projects($POST->category_id);
					
					
					// notice
					$_SESSION['info'][] = "Работа обновлена";
				}
				else {
					// error
					$_SESSION['error'][] = "Не удалось обновить работу";
					if(!isset($POST->category))		$_SESSION['error'][] = "Не удалось определить идентификатор!";
					if(!isset($POST->category) || $POST->category == 0)	$_SESSION['error'][] = "Не указана категория!";
					if(isset($POST->category) && $this->engine->check_type($POST->category) == "part")	
						$_SESSION['error'][] = "Невозможно перенести работу в раздел. Выберите категорию.";
				}
			}
			else {
				// error
				$_SESSION['error'][] = "Не удалось обновить работу";
				if(!isset($POST->title))		$_SESSION['error'][] = "Не указано название работы!";
				if(!isset($POST->sub_title))	$_SESSION['error'][] = "Не указано краткое описание работы!";
				if(!isset($POST->category))		$_SESSION['error'][] = "Не удалось определить идентификатор!";
			}
		}
		
		// move
		goback();
	}
	
	
	//*******************************************
	// Обвновление категории
	private function update_category() {
	
		global $db, $POST;
		
		if(@$_REQUEST['update_cat']) {
			if(isset($POST->thisid) && $POST->thisid == $this->engine->category_id && isset($POST->title)) {
				if($this->engine->check_subcat($POST->thisid,$POST->parent_category)) {
					
					// type
					$type = "category";
					
					$q = $db->query("SELECT count(*) FROM ".PORTFOLIO_PROJECT." WHERE category_id='".$POST->thisid."'");
					$c = $db->fetch_row($q);
					
					if(isset($POST->type) && $POST->type == "part" && $c[0] == 0) $type = "part";
					if(isset($POST->type) && $POST->type == "part" && $c[0] != 0) 
						$_SESSION['info'][] = "Не удалось изменить катгорию на раздел, потому что в Категории имеются работы. Переместите работы в другую категорию, прежде чем изменять её тип на раздел";
					
					// sql update
					$db->query("UPDATE ".PORTFOLIO_CATEGORY."
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
	// Сортируем работы
	private function sortwork() {
		
		global $db, $POST;
		
		if(@$_REQUEST['update_sort']) {
			if(isset($POST->sort)) {
				foreach($POST->sort AS $key=>$value) {
					
					settype($value, "integer");
					
					// update sql
					$db->query("UPDATE ".PORTFOLIO_PROJECT." 
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
	function count_projects($category_id) {
	
		global $db;
		
		$q = $db->query("SELECT count(*) FROM ".PORTFOLIO_PROJECT." WHERE category_id='".$category_id."'");
		$c = $db->fetch_row($q);
		
		$db->query("UPDATE ".PORTFOLIO_CATEGORY." SET projects='".$c[0]."' WHERE id='".$category_id."'");
		//$_SESSION['info'][] = "Счетчик обновлен ".$category_id;
	}
}

?>