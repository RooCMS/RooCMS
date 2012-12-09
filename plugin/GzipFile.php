<?php
/*=========================================================
|	Title: RooCMS Plugin Utilites GZip JS|CSS
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
|	Build date:		0:39 04.03.2011
|	Last Build:		4:06 01.11.2012
|	Version file: 	1.00 build 2
=========================================================*/

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