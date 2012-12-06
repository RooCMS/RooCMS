<?php
/*======================================================
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
|-------------------------------------------------------
|	Build date: 		12:10 01.12.2010
|	Last Build: 		3:00 17.10.2011
|	Version file:		1.00 build 11
======================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


// engine
require_once _CMS."/functions_portfolio.php";


$portfoliouser = new PortfolioUser;

class PortfolioUser {

	# classes
	protected $engine;



	function __construct() {
		
		global $db, $config, $tpl, $html, $parse, $GET, $var;
		
		
		// run engine
		$this->engine = new PortfolioEngine;
		
		
		// SEO meta
		$var['title'] 				= $config->portfolio_title." :: ".$var['title'];
		$config->meta_description 	.= " ".$this->engine->settings['meta_description'];
		$config->meta_keywords  	.= " ".$this->engine->settings['meta_keywords'];
		
		
		// Load template ========================
		$tpl->load_template("user_portfolio");
		//=======================================
		
		
		$html['head'] = $tpl->tpl->head($this->engine->settings);
		
		$this->engine->settings['birthdate'] = $parse->date->gregorian_to_rus($this->engine->settings['birth_date']);
		$html['info'] = $tpl->tpl->info($this->engine->settings);
		
		
		// category_tree
		$category = $this->engine->category_tree();
		for($c=0;$c<=count($category)-1;$c++) {
			if(!isset($GET->_category)) $GET->_category = 0;
			// output
			$html['category'][] = $tpl->tpl->category($category[$c], $this->engine->category_id);
		}
		
		// tag cloud
		$this->tags_cloud();

		
        if($this->engine->category_id != 0 && $this->engine->project_id == 0) {
			
			// SEO
			$config->meta_keywords  .= ", ".$this->engine->category_name;
			$var['title'] = $this->engine->category_name." :: ".$var['title'];
		
			// title
			$html['part_title'] = $tpl->tpl->part_title($this->engine->category_name);
			
			// output
			if($this->engine->category_type == "category")	{
				$this->show_category($this->engine->category_id);
				// SEO
				$config->meta_description .= " ".$this->engine->category_name;
			}
			elseif($this->engine->category_type == "part")	{
				$this->show_part($this->engine->category_id);
				// SEO
				$config->meta_description .= " ".$this->engine->category_name;
			}
        }
		elseif($this->engine->category_id == 0 && isset($GET->_tag) && $this->engine->project_id == 0) 
			$this->show_tags($GET->_tag);
		elseif($this->engine->project_id != 0) 
			$this->show_project($this->engine->project_id);
        else 
			$this->idx();
	}
	
    
	//*******************************************
	// default
	function idx() {
		
		global $tpl, $html, $parse;
        
        $html['content'] = $parse->text->br($tpl->tpl->about($this->engine->settings['about']));
	}
    
    
	//*******************************************
    // Show category works
    function show_category($category_id) {
        
		global $db, $tpl, $html, $var;

		
		// SEO page
		if($db->page > 1) $var['title'] .= " : Страница ".$db->page;
		
		
		// set limit works on page
		$db->limit =& $this->engine->settings['workonpage'];
		
		// calculate pages
		$db->pages_mysql(PORTFOLIO_PROJECT,"category_id='".$category_id."'");

		// draw nav pages
		if($db->pages >= 2) {
			$html['navpage'] = $tpl->tpl->navpage();
			for($p=1;$p<=$db->pages;$p++) {
				$html['navpage_el'][] = $tpl->tpl->navpage_el($category_id, $p);
			}
		}
		
		// sql query
        $q = $db->query("SELECT id, title, sub_title, link, poster, tags 
							FROM ".PORTFOLIO_PROJECT." 
							WHERE category_id='".$category_id."'
							ORDER BY sort ASC 
							LIMIT ".$db->from.",".$db->limit);
        while($project = $db->fetch_assoc($q)) {
			// output
			$html['content'][] = $tpl->tpl->brief_project($project);
        }
    }
	
	
	//*******************************************
	// Show project description
	function show_project($project_id) {
		
		global $db, $config, $tpl, $html, $parse, $var;
		
		
		// sql
		$q = $db->query("SELECT id, category_id, title, sub_title, link, poster, tags FROM ".PORTFOLIO_PROJECT." WHERE id='".$project_id."'");
		$project = $db->fetch_assoc($q);
		
		
		// SEO
		$var['title'] = "Проект: ".$project['title']." :: ".$var['title'];
		$config->meta_description .= " ".$project['title'].". ".$project['sub_title'];
		$config->meta_keywords .= ", ".$project['tags'];
		
		
		// sql steps
		$project['steps'] = "";
		$n = 1;
		$q = $db->query("SELECT id, step_picture, step_description, step FROM ".PORTFOLIO_PROJECT_STEPS." WHERE project_id='".$project_id."'");
		while($step = $db->fetch_assoc($q)) {
			
			$step['step_description'] = $parse->text->html($step['step_description']);
			$step['n'] = $n;
			
			$project['steps'] .= $tpl->tpl->project_step($step);
			
			$n++;
		}
		
		
		// output
		$html['content'] = $tpl->tpl->project($project);
	}
	
	//*******************************************
	// show tags
	function show_tags($tag) {
	
		global $roocms, $db, $config, $tpl, $html, $var;
		
		
		// SEO page
		$var['title'] = "Метка: ".$tag." :: ".$var['title'];
		$config->meta_description .= " ".$tag;
		$config->meta_keywords .= " tag, тег, метка, ".$tag;
		
		if($db->page > 1) $var['title'] .= " : Страница ".$db->page;
		
		
		// set limit works on page
		$db->limit =& $this->engine->settings['workonpage'];
		
		$q = $db->query("SELECT count(*) AS total FROM ".PORTFOLIO_PROJECT." 
							WHERE tags LIKE '%, ".$tag.",%' 
							   OR tags LIKE '".$tag.",%'
							   OR tags LIKE '%, ".$tag."'
							   OR tags='".$tag."'");
		$c = $db->fetch_assoc($q);
		
		if($c['total'] == 0) go(THIS_SCRIPT.".php");
		
		$html['part_title'] = $tpl->tpl->part_title("Метка: ".$tag);
		
		// set pages
		$db->pages_non_mysql($c['total']);
		
		// sql query
        $q = $db->query("SELECT id, title, sub_title, link, tags, poster
							FROM ".PORTFOLIO_PROJECT." 
							WHERE tags LIKE '%, ".$tag.",%' 
							   OR tags LIKE '".$tag.",%'
							   OR tags LIKE '%, ".$tag."'
							   OR tags='".$tag."'
							ORDER BY id DESC
							LIMIT ".$db->from.",".$db->limit);
		// draw nav pages
		if($db->pages >= 2) {
			$html['navpage'] = $tpl->tpl->navpage();
			for($p=1;$p<=$db->pages;$p++) {
				$html['navpage_el'][] = $tpl->tpl->navpage_el($tag, $p, "tag");
			}
		}
		
		// draw projects
        while($project = $db->fetch_assoc($q)) {

			// output
			$html['content'][] = $tpl->tpl->brief_project($project);
        }
	}
	
	
	//*******************************************
	// show part
	function show_part($category_id) {
		
		global $tpl, $html;
		
		$category = $this->engine->category_tree($this->engine->category_id);

		for($i=0;$i<=count($category)-1;$i++) {
			
			// output
			$html['content'][] = $tpl->tpl->part($category[$i]);
		}
	}
	
	
	//*******************************************
	// show tags cloud
	function tags_cloud() {
		
		global $tpl, $html;
		
		$tags = $this->engine->tags();
		if($tags) {
			foreach($tags AS $key=>$value) {
				$html['tag'][] = $tpl->tpl->tag($value['fontsize'], $value['key'], $value['ukey'], $value['value']);
			}
		}
	}

}

?>