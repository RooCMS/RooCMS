<?php
/*=========================================================
|	Title: RooCMS Plugin Utilites Codemirror Library
|	Author:	alex Roosso
|	Copyright: 2010-2014 (c) RooCMS. 
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
|	Build date:		15:10 23.07.2012
|	Last Build:		11:47 24.07.2012
|	Version file: 	1.00
=========================================================*/

# OUTPUT
header('HTTP/1.1 200 OK');
header("Content-type: application/x-javascript; charset=utf-8");
header('Content-transfer-encoding: binary\n');
header('Accept-Ranges: bytes');
ob_start("ob_gzhandler", 9);
?>

document.write('<link rel="stylesheet" href="/plugin/codemirror/lib/codemirror.css">');
document.write('<link rel="stylesheet" href="/plugin/codemirror/lib/util/dialog.css">');
document.write('<script src="/plugin/codemirror/lib/codemirror.js"></script>');
document.write('<script src="/plugin/codemirror/lib/util/foldcode.js"></script>');
document.write('<script src="/plugin/codemirror/lib/util/overlay.js"></script>');
document.write('<script src="/plugin/codemirror/lib/util/search.js"></script>');
document.write('<script src="/plugin/codemirror/lib/util/searchcursor.js"></script>');
document.write('<script src="/plugin/codemirror/lib/util/dialog.js"></script>');
document.write('<script src="/plugin/codemirror/lib/util/match-highlighter.js"></script>');
document.write('<script src="/plugin/codemirror/mode/xml/xml.js"></script>');
document.write('<script src="/plugin/codemirror/mode/javascript/javascript.js"></script>');
document.write('<script src="/plugin/codemirror/mode/css/css.js"></script>');
document.write('<script src="/plugin/codemirror/mode/clike/clike.js"></script>');
document.write('<script src="/plugin/codemirror/mode/php/php.js"></script>');
document.write('<script src="/plugin/codemirror/mode/htmlmixed/htmlmixed.js"></script>');
document.write('<script src="/plugin/codemirror/mode/smarty/smarty.js"></script>');
document.write('<script src="/plugin/codemirror/mode/mysql/mysql.js"></script>');
