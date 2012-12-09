<?php
/**
* @package      RooCMS
* @subpackage	Library
* @subpackage	Smarty initialise
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.0
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


/**
* Set const smarty folder
*/
define('SMARTY_DIR', _SMARTY.'/');

/**
* Require Smarty
*/
require_once _SMARTY."/Smarty.class.php";

/**
* Init Smarty
*
* @var Smarty
*/
$smarty = new Smarty();


?>