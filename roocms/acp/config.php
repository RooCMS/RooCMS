<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS Config acp
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
|	Build date: 		21:05 02.12.2010
|	Last Build: 		3:39 20.10.2011
|	Version file:		1.00 build 6
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


$configacp = new Configacp;

class Configacp {

	# classes
	var $config;
	
	# type config
	private $types	= array();
	private $t_vars	= array();
	
	private $part	= "Global";
	

	function __construct() {
		
		global $db, $config, $tpl;
		
		
		// include config class
		$this->config = $config;
		
	
		// Load Template  ==============================
		$tpl->load_template("acp_config");
		//==============================================
		
		
		// Если есть запрос на обновление тогда обновляем
		if(@$_REQUEST['update_config'])
			$this->update_config();
		else {
			$this->view_config();
		}
	}
	

	// Показать настройки
	function view_config() {
		
		global $db, $tpl, $html, $parse, $GET;
		
		
		if(isset($GET->_part) && $db->check_id($GET->_part, CONFIG_PARTS, "part") == 1) $this->part = $GET->_part;
		
		// запрос разделов конфигурации из БД
		$q_1 = $db->query("SELECT part, title, type FROM ".CONFIG_PARTS." ORDER BY type ASC, sort ASC");
		while($part = $db->fetch_assoc($q_1)) {

			// запрашиваем из БД опции
			if($this->part == $part['part']) {
				$part['options'] = "";
				$q_2 = $db->query("SELECT id, title, description, options, options_type, variants, value FROM ".CONFIG_TABLE." WHERE part='".$part['part']."' ORDER BY sort ASC");
				while($option = $db->fetch_assoc($q_2)) {
					
					// parse
					$option['description'] = $parse->text->br($option['description']);
					$option['option'] = $this->init_field($option['options'], $option['options_type'], $option['value'], $option['variants']);
					
					// output
					$part['options'] .= $tpl->tpl->options($option);
				}
				
				$html['content'][] = $tpl->tpl->content($part);
			}
			
			if($part['type'] == "component")	$html['parts_component'][] 	= $tpl->tpl->parts($part, $this->part);
			if($part['type'] == "mod")			$html['parts_mod'][] 		= $tpl->tpl->parts($part, $this->part);
			if($part['type'] == "module")		$html['parts_module'][] 	= $tpl->tpl->parts($part, $this->part);
		}
	}
	
	// функция парсера опции для буфера
	function init_field($option_name, $option_type, $value, $variants) {
		
		global $tpl;
		
		# integer OR string OR email OR date
		if($option_type == "int" 		OR	$option_type == "string"	OR	$option_type == "email") {
			$out = $tpl->tpl->field_string($option_name, $value);
		}
		# text OR textarea
		elseif($option_type == "text"	OR 	$option_type == "textarea") {	
			$out = $tpl->tpl->field_textarea($option_name, $value);
		}
		# boolean
		elseif($option_type == "boolean" OR $option_type == "bool") {	
			$out = $tpl->tpl->field_boolean($option_name, $value);	
		}
		# date
		elseif($option_type == "date") {	
			$out = $tpl->tpl->field_date($option_name, $value);	
		}
		# select
		elseif($option_type == "select"	&& $variants != "") {
			$options = "";
			$vars = explode("\n",$variants);
			foreach($vars AS $k=>$v) {
				$vars = explode("|",trim($v));

				($vars[1] == $value) ? $s = "selected" : $s = "" ;

				$options .= $tpl->tpl->field_select_option($vars[0],$vars[1],$s);
			}
			
			$out = $tpl->tpl->field_select($option_name,$options);
		}
		
		return $out;
	}
	
	// обновить настройки
	function update_config() {
	
		global $db, $POST;
		
		// запрашиваем из БД типа опций
		$q = $db->query("SELECT options, options_type, variants FROM ".CONFIG_TABLE);
		while($row = $db->fetch_assoc($q)) {
			$this->types[$row['options']] = $row['options_type'];
			if($row['variants'] != "") {
				$vars = explode("\n",$row['variants']);
				foreach($vars AS $k=>$v) {
					$v = explode("|",trim($v));
					$this->t_vars[$row['options']][$v[1]] = trim($v[1]);
				}
			}
		}
		
		// Обновляем опции
		foreach($POST AS $key=>$value) {
			if($key != "update_config") {
				$check = false;
			
				# int OR integer
				if($this->types[$key] == "int" OR $this->types[$key] == "integer") {
					settype($value, "integer");
					$check = true;
				}
				# boolean OR bool
				elseif($this->types[$key] == "boolean" OR $this->types[$key] == "bool") {
					if($value == "true" OR $value == "false") {
						$check = true;
					}
					else $check = false;
				}
				# email
				elseif($this->types[$key] == "email") {
					if(valid_email($POST->$key)) 
						$check = true;
					else
						$check = false;
				}
				# date
				elseif($this->types[$key] == "date") {
					$date = explode("/",$POST->$key);
					settype($date[0], "integer");
					settype($date[1], "integer");
					settype($date[2], "integer");

					if(count($date) == 3) {
						if(mb_strlen($date[0], 'utf8') <= 2 && mb_strlen($date[1], 'utf8') <= 2 && mb_strlen($date[2], 'utf8') == 4 && checkdate($date[0], $date[1], $date[2])) {
							$check = true;
							$value = $date[0]."/".$date[1]."/".$date[2];
						}
						else $check = false;
					}
					else $check = false;
				}
				# string OR text
				elseif($this->types[$key] == "string" OR $this->types[$key] == "text" OR $this->types[$key] == "textarea") {
					$check = true;
				}
				# select
				elseif($this->types[$key] == "select") {
					if(isset($this->t_vars[$key][$value])) $check = true;
				}
				
				if($check) {
					$db->query("UPDATE ".CONFIG_TABLE." SET value='".$value."' WHERE options='".$key."'");
				}
			}
		}
		
		// notice
		$_SESSION['info'][] = "Настройки обновлены";
		
		// move
		goback();
	}
}

?>