<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Initialisation
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.13
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
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


//#########################################################
// Init Admin CP identification
//---------------------------------------------------------
if(!defined('ACP')) define('ACP', true);
//#########################################################


nocache();

require_once _ROOCMS."/acp/security_check.php";


/**
* check security
*/
if($security == true) {

	# запускаем меню админа
	require_once _ROOCMS."/acp/menu.php";


	if(trim($roocms->act) != "" && file_exists(_ROOCMS."/acp/".$roocms->act.".php")) {
		require_once _ROOCMS."/acp/".$roocms->act.".php";
	}
	else {
		require_once _ROOCMS."/acp/index.php";
	}
}
else {
	$smarty->assign("no_footer", true);
	require_once _ROOCMS."/acp/login.php";
}


?>