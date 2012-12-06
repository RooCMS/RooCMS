<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS MimeTypes
|	Author:	alex Roosso
|	Copyright: 2010-2011 (c) RooCMS. 
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
|	Build: 				7:34 08.11.2010
|	Last Build: 		6:25 11.10.2011
|	Version file:		1.00 
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


$mimetype = array();

// File ===================================================
$filetype 			= array();
$filetype[] 		= array('ext'	=> 'm3u',		'type'	=> 'audio/mpegurl',					'ico'	=> 'm3u.png');
$filetype[] 		= array('ext'	=> 'ttf',		'type'	=> 'application/octet-stream',		'ico'	=> 'ttf.png');
$filetype[] 		= array('ext'	=> 'zip',		'type'	=> 'application/x-zip-compressed',	'ico'	=> 'zip.png');					
$filetype[] 		= array('ext'	=> 'zip',		'type'	=> 'application/zip',				'ico'	=> 'zip.png');					
$filetype[] 		= array('ext'	=> 'tar.gz',	'type'	=> 'application/octetstream',		'ico'	=> 'tar.gz.png');
$filetype[] 		= array('ext'	=> 'rar',		'type'	=> 'application/octet-stream',		'ico'	=> 'rar.png');
$filetype[] 		= array('ext'	=> 'js',		'type'	=> 'application/x-javascript',		'ico'	=> 'js.png');
$filetype[] 		= array('ext'	=> 'html',		'type'	=> 'text/html',						'ico'	=> 'html.png');							
$filetype[] 		= array('ext'	=> 'htm',		'type'	=> 'text/html',						'ico'	=> 'htm.png');							
$filetype[] 		= array('ext'	=> 'css',		'type'	=> 'text/css',						'ico'	=> 'css.png');							
$filetype[] 		= array('ext'	=> 'xml',		'type'	=> 'text/xml',						'ico'	=> 'xml.png');							
$filetype[] 		= array('ext'	=> 'ini',		'type'	=> 'application/octet-stream',		'ico'	=> 'ini.png');							
$filetype[] 		= array('ext'	=> 'swf',		'type'	=> 'application/x-shockwave-flash',	'ico'	=> 'swf.png');							
$filetype[] 		= array('ext'	=> 'fla',		'type'	=> 'application/octet-stream',		'ico'	=> 'fla.png');							
$filetype[] 		= array('ext'	=> 'psd',		'type'	=> 'application/octet-stream',		'ico'	=> 'psd.png');							
$filetype[] 		= array('ext'	=> 'cdr',		'type'	=> 'application/octet-stream',		'ico'	=> 'cdr.png');							
$filetype[] 		= array('ext'	=> 'csv',		'type'	=> 'application/vnd.ms-excel',		'ico'	=> 'csv.png');							
$filetype[] 		= array('ext'	=> 'doc',		'type'	=> 'application/msword',			'ico'	=> 'doc.png');							
$filetype[] 		= array('ext'	=> 'xls',		'type'	=> 'application/vnd.ms-excel',		'ico'	=> 'xls.png');							
$filetype[] 		= array('ext'	=> 'docx',		'type'	=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',	'ico'	=> 'docx.png');							
$filetype[] 		= array('ext'	=> 'xlsx',		'type'	=> 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',			'ico'	=> 'xlsx.png');							


// Image ==================================================
$imagetype 			= array();
// $imagetype[] 		= array('ext'	=> 'ico',	'type'	=> 'image/x-icon',					'ico'	=> 'ico.png');
$imagetype[] 		= array('ext'	=> 'png',		'type'	=> 'image/png',						'ico'	=> 'png.png');
$imagetype[] 		= array('ext'	=> 'gif',		'type'	=> 'image/gif',						'ico'	=> 'gif.png');
$imagetype[] 		= array('ext'	=> 'jpg',		'type'	=> 'image/jpg',						'ico'	=> 'jpg.png');
$imagetype[] 		= array('ext'	=> 'jpg',		'type'	=> 'image/jpeg',					'ico'	=> 'jpeg.png');
$imagetype[] 		= array('ext'	=> 'jpg',		'type'	=> 'image/pjpeg',					'ico'	=> 'jpg.png');
$imagetype[] 		= array('ext'	=> 'bmp',		'type'	=> 'image/bmp',						'ico'	=> 'bmp.png');
						
$mimetype = array_merge($filetype, $imagetype);

//debug($mimetype);
					
?>