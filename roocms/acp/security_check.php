<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Secuirity check
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1
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


// @param boolean
$security		= false;

if(trim($adm['login']) != "" && trim($adm['passw']) != "") {
	// @return md5 hash
	$session_check 	= md5(md5($adm['login']).md5($adm['passw']));

	if(isset($roocms->sess['acp']) && $roocms->sess['acp'] == $session_check) {
		$security 	= true;
	}
}

?>