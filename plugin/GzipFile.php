<?php
/**
* @package      RooCMS
* @subpackage	Plugin Utilites
* @subpackage	GZip alternate
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*	RooCMS - Russian free content managment system
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
*   along with this program.  If not, see <http://www.gnu.org/licenses/
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

# .htaccess
###########################################################
# RewriteRule ^(.*\.((js)|(css)))$ plugin/GzipFile.php?file=$1
# RewriteRule \.css$ plugin/GzipFile.php?file=$1
# RewriteRule \.js$ plugin/GzipFile.php?file=$1
###########################################################

ob_start("ob_gzhandler", 9);

if(isset($_GET['file']) && !empty($_GET['file'])) {
	if(file_exists("../".$_GET['file'])) {

		$exp = explode(".",$_GET['file']);
		$c = count($exp) - 1;

		if($exp[$c] == "js")		$filetype = "application/x-javascript";
		elseif($exp[$c] == "css")	$filetype = "text/css";
		else						$filetype = "plain/text";

		header('HTTP/1.1 200 OK');
		// header('Expires: ' . gmdate("D, d M Y H:i:s", date("U") + 31536000) . ' GMT');             	// Date in the past
		// header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); 	// always modified
		// header('Cache-control:  no-cache, private');           			// HTTP/1.1
		// header('Pragma: no-cache');                                   	// HTTP/1.0
		header('Content-type: '.$filetype);
		header("Content-disposition: attachment; filename=\"".$_GET['file']."\"");
		header('Content-transfer-encoding: binary\n');
		//header("Content-Length: ".filesize("../".$_GET['file'])."");
		header('Accept-Ranges: bytes');

		// read
		readfile("../".$_GET['file']);
	}
}

?>