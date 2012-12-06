<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS
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
|	Build date: 		3:08 13.09.2010
|	Last Build: 		0:12 19.03.2011
|	Version file:		1.00
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


// @param boolean
$security		= false;

// @return md5 hash
$session_check 	= md5(md5($adm['login']).md5($adm['passw']));

if(isset($roocms->sess['acp']) && $roocms->sess['acp'] == $session_check) {
	$security 	= true;
}

?>