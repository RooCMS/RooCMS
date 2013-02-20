<?php
/**
* @package      RooCMS
* @subpackage	Installer or Updater
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.00 build 1
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

define('INSTALL', true);
require_once $_SERVER['DOCUMENT_ROOT']."/roocms/init.php";



//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


nocache();


if(trim($adm['login']) != "" && trim($adm['passw']) != "") {

	require_once _ROOCMS."/acp/security_check.php";

	if($security == true) {

		require_once "check_requirement.php";

		if(!empty($db_info['user']) && !empty($db_info['pass']) && !empty($db_info['base'])) {
			require_once "update.php";
		}
		else {
			$site['title'] = "Установка RooCMS";
			require_once "install.php";
		}
	}
	else {
		$smarty->assign("no_footer", true);
		require_once _ROOCMS."/acp/login.php";
	}
}
else {
	$site['title'] = "Установка RooCMS";

	require_once "check_requirement.php";
	require_once "install.php";
}

# draw page
$tpl->out();

?>
