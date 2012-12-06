<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS Global Class
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
|	Build: 			2:29 28.11.2010
|	Last Build:		3:18 28.10.2011
|	Version file:	1.00 build 28
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


$roocms = new Globals;
$config = $roocms->config;

class Globals {
	
	# clasess
	var		$config;		// Object config
	
	# global vars
	public	$act			= "";
	public	$part			= "";
	
	public 	$sess			= array();

	# options
	public	$ajax			= true;
	public	$rss			= false;
	public	$modifedsince	= false;
	
	# userdata
	public  $referer		= "";
	public  $userip			= "";
	public	$useragent		= "";
	public	$browser		= "";
	public	$spiderbot		= false;
	

	
	function __construct() {
		
		global $GET;
		
		// init configuration 
		$this->init_configuration();
		

		// 	init referer
		$this->referer 	= getenv("HTTP_REFERER");

		//	init userip
		if(getenv('HTTP_X_FORWARDED_FOR'))
			$this->userip = getenv('HTTP_X_FORWARDED_FOR');
		else 
			$this->userip = getenv('REMOTE_ADDR');
			
		// init useragent
		$this->useragent = mb_strtolower($_SERVER['HTTP_USER_AGENT'], 'utf8');
		
		
		// Обрабатываем useragent для spider bot
		$this->check_spider_bot();
	}
	
	
	//*****************************************************
	//	Инициируем конфигурацию
	protected function init_configuration() {
		
		global $db, $var;
		
		// делаем объектом
		settype($this->config, "object");
		
		// заносим опции в объект
		$q = $db->query("SELECT options, options_type, value FROM ".CONFIG_TABLE."");
		while($row = $db->fetch_assoc($q)) {
			if($row['options_type'] == "boolean" OR $row['options_type'] == "bool")
				$conf = "\$this->config->{$row['options']} = ".$row['value'].";";
			elseif($row['options_type'] == "int" OR $row['options_type'] == "integer") {
				settype($row['value'], "integer");
				$conf = "\$this->config->{$row['options']} = ".$row['value'].";";
			}
			elseif($row['options_type'] == "string")
				$conf = "\$this->config->{$row['options']} = \"{$row['value']}\";";
			else
				$conf = "\$this->config->{$row['options']} = \"{$row['value']}\";";
				

			// init classd
			eval($conf);
		}

		// init domain
		if(empty($this->config->baseurl)) $this->config->baseurl = $var['domain'];
		
		// set modifedsince answer
		$this->modifedsince =& $this->config->if_modifed_since;
	}
	
	
	//*****************************************************
	//	Check useragent for search spider bot machine
	protected function check_spider_bot() {
	
		require_once _LIB."/spiders.php";
		
		foreach($spiderbot AS $key=>$value) {
			$check = mb_strpos($this->useragent, $value, 0, 'utf8');
			if($check !== false) $this->spiderbot = true;
		}
	}
	
	
	//*****************************************************
	//	set headers last modifed and if-modified-since
	protected function ifmodifedsince($lastmodifed) {
		
		Header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastmodifed)." GMT");
		
		if($this->modifedsince) {
			
			$ifmodsince = false; 

			if (isset($_ENV['HTTP_IF_MODIFIED_SINCE']))		$ifmodsince = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));  
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))	$ifmodsince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5)); 
			if ($ifmodsince && $ifmodsince >= $lastmodifed) {
				header($_SERVER['SERVER_PROTOCOL']." 304 Not Modified");     
				exit; 
			}
		}
	}

}

?>