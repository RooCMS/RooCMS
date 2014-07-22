<?php
/**
 * @package      RooCMS
 * @subpackage	 Plugins
 * @subpackage	 Files
 * @author       alex Roosso
 * @copyright    2010-2015 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.1a
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 *   RooCMS - Russian free content managment system
 *   Copyright (C) 2010-2014 alex Roosso aka alexandr Belov info@roocms.com
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 *
 *
 *   RooCMS - Русская бесплатная система управления сайтом
 *   Copyright (C) 2010-2014 alex Roosso (александр Белов) info@roocms.com
 *
 *   Это программа является свободным программным обеспечением. Вы можете
 *   распространять и/или модифицировать её согласно условиям Стандартной
 *   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 *   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
 *
 *   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 *   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 *   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 *   Общественную Лицензию GNU для получения дополнительной информации.
 *
 *   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 *   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */

/**
 * Инициализируем RooCMS
 */
define('MULTIUPLOAD', true);
define('_SITEROOT', str_ireplace(DIRECTORY_SEPARATOR."plugin", "", dirname(__FILE__)));
require_once _SITEROOT."/roocms/init.php";

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################

# Security check
$security = false;
require_once _ROOCMS."/acp/security_check.php";

# Заливаем картинги во временную папку...
if (!empty($_FILES) && $security) {

	global $img;

	# get file info
	$pi = pathinfo($_FILES['Filedata']['name']);

	# транслит
	$pi['filename'] = $parse->text->transliterate($pi['filename'], "lower");

	# чистим имя файла от абракадабры...
	$filename = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9\-_])'), array(' ','-','_',''), $pi['filename'])."_".randcode(5).".".$pi['extension'];

	$targetFile = rtrim(_CACHEIMAGE,'/') . '/' . $filename;

	# Validate the file type
	static $allow_exts = array();
	if(empty($allow_exts))
		$allow_exts = $img->get_allow_exts();

	if (in_array($pi['extension'],$allow_exts)) {
		move_uploaded_file($_FILES['Filedata']['tmp_name'], $targetFile);
		echo $filename;
	} else {
		echo 'Invalid file type.';
	}
}

?>