<?php
/**
 * @package      RooCMS
 * @subpackage	 Plugin Utilites
 * @subpackage	 Codemirror Library
 * @author       alex Roosso
 * @copyright    2010-2014 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      2.0
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
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
*   Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
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

$mode = "";
if(isset($_GET['mode']) && trim($_GET['mode']) != "") $mode = $_GET['mode'];

# OUTPUT
header('HTTP/1.1 200 OK');
header("Content-type: application/x-javascript; charset=utf-8");
header('Content-transfer-encoding: binary\n');
header('Accept-Ranges: bytes');
ob_start("ob_gzhandler", 9);

/* Hint */ /* LINT */ /* HTML CSS TEXT XML */ /* Load mode */
//document.write('<link rel="stylesheet" href="/plugin/codemirror/addon/fold/foldgutter.css">');	// HTML XML
//document.write('<script src="/plugin/codemirror/addon/edit/matchtags.js"></script>');			// HTML XML
//document.write('<script src="/plugin/codemirror/addon/edit/closetag.js"></script>');			// HTML XML
//document.write('<script src="/plugin/codemirror/addon/fold/xml-fold.js"></script>');			// HTML XML
//document.write('<script src="/plugin/codemirror/addon/edit/continuelist.js"></script>');		// TEXT
//document.write('<script src="/plugin/codemirror/addon/edit/trailingspace.js"></script>'); 		// CSS
//document.write('<script src="/plugin/codemirror/addon/fold/foldcode.js"></script>');                  // HTML XML
//document.write('<script src="/plugin/codemirror/addon/fold/foldgutter.js"></script>');		// HTML XML

//document.write('<script src="/plugin/codemirror/mode/smarty/smarty.min.js"></script>');
//document.write('<script src="/plugin/codemirror/mode/smartymixed/smartymixed.js"></script>');
//document.write('<script src="/plugin/codemirror/mode/sql/sql.js"></script>');
?>

document.write('<link rel="stylesheet" href="/plugin/codemirror/lib/codemirror.min.css">');
document.write('<link rel="stylesheet" href="/plugin/codemirror/addon/dialog/dialog.min.css">');
document.write('<link rel="stylesheet" href="/plugin/codemirror/addon/display/fullscreen.min.css">');

document.write('<script src="/plugin/codemirror/lib/codemirror.min.js"></script>');				// Engine
document.write('<script src="/plugin/codemirror/addon/dialog/dialog.min.js"></script>');			// ALL
document.write('<script src="/plugin/codemirror/addon/search/search.min.js"></script>');
document.write('<script src="/plugin/codemirror/addon/search/searchcursor.min.js"></script>');
document.write('<script src="/plugin/codemirror/addon/search/match-highlighter.min.js"></script>');
document.write('<script src="/plugin/codemirror/addon/edit/matchbrackets.min.js"></script>');
document.write('<script src="/plugin/codemirror/addon/edit/closebrackets.min.js"></script>');

document.write('<script src="/plugin/codemirror/addon/mode/overlay.min.js"></script>');
document.write('<script src="/plugin/codemirror/addon/mode/multiplex.min.js"></script>');
document.write('<script src="/plugin/codemirror/addon/display/fullscreen.min.js"></script>');		// Util

/* mode */
document.write('<script src="/plugin/codemirror/mode/htmlmixed/htmlmixed.min.js"></script>');
document.write('<script src="/plugin/codemirror/mode/xml/xml.min.js"></script>');
document.write('<script src="/plugin/codemirror/mode/javascript/javascript.min.js"></script>');
document.write('<script src="/plugin/codemirror/mode/css/css.min.js"></script>');
document.write('<script src="/plugin/codemirror/mode/clike/clike.min.js"></script>');
document.write('<script src="/plugin/codemirror/mode/php/php.min.js"></script>');
document.write('<script src="/plugin/codemirror/mode/htmlembedded/htmlembedded.min.js"></script>');

<?php if($mode == "php") { ?>

<?php } ?>
