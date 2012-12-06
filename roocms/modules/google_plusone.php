<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS module Google PlusOne Button
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
|	Build date: 		6:10 17.10.2011
|	Last build: 		6:10 17.10.2011
|	Version file:		1.00 alpha
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


global $config;

if($config->google_plusone_on) {

	// button size
	switch($config->google_plusone_size) {
		case 'small':
			$config->google_plusone_size = ' size="small"';
			break;
			
		case 'meduim':
			$config->google_plusone_size = ' size="meduim"';
			break;
			
		case 'tall':
			$config->google_plusone_size = ' size="tall"';
			break;
			
		default:
			$config->google_plusone_size = "";
			break;
	}
	
	// on/off count
	if(!$config->google_plusone_count) $config->google_plusone_count = ' count="false"'; else $config->google_plusone_count = '';

	
	$module['google_plusone'] = $tpl->load_template("module_google_plusone", true);
}

?>