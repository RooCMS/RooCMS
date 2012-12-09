<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Frontend Main page
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      2.1.1
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
* Инициализируем админ панель
*/
define('ACP', true);
require_once $_SERVER['DOCUMENT_ROOT']."/roocms/init.php";
require_once _ACP;


/**
* Генерим HTML
*
* @var template
* @return frontend html
*/
$tpl->out();


?>