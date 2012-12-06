<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS Login page
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
|	Build date: 		18:51 14.09.2010
|	Last Build: 		8:14 17.10.2011
|	Version file:		1.00 build 4
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


// Load template ===========================
$tpl->load_template("acp_login");
//==========================================


// init tpl vars
$html['error_login'] = $tpl->tpl->error_login("");


// Проверка запроса
if(@$_REQUEST['go']) {

	if(isset($POST->login) && $POST->login == $adm['login']
	&& isset($POST->passw) && $POST->passw == $adm['passw']) {
		// @include session security_check hash
		$_SESSION['acp'] 	= md5(md5($POST->login).md5($POST->passw));
		go("acp.php");
	}
	else {
		// неверный логин или пароль
		sleep(5);
		$html['error_login'] = $tpl->tpl->error_login("Неверный логин или пароль", "error");
	}
}


?>