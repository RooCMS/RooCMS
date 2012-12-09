<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Login
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.8
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || (!defined('ACP') && !defined('INSTALL'))) die('Access Denied');
//#########################################################


$smarty->assign("error_login", "");


//#########################################################
// Проверка запроса
if(@$_REQUEST['go']) {

	if(isset($POST->login) && $POST->login == $adm['login']
	&& isset($POST->passw) && $POST->passw == $adm['passw']) {
		# @include session security_check hash
		$_SESSION['acp'] 	= md5(md5($POST->login).md5($POST->passw));

		//go(CP);
		goback();
	}
	else {
		# неверный логин или пароль
		sleep(3);
		$smarty->assign("error_login", "Неверный логин или пароль");
	}
}

# load template
$tpl->load_template("login");

?>