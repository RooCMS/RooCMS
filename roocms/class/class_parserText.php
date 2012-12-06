<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS extends Parser Class 
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
|	Build: 				5:11 06.12.2010
|	Last Build: 		1:54 02.10.2011
|	Version file:		1.00 build 24
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


class ParserText {

	//************************************************
	// parser BBCODE 
	function bbcode($text) {
		
		# [blackquote]
		$text = str_ireplace("[blockquote]", "<blockquote>", $text, $tag_bq_o);
		$text = str_ireplace("[/blockquote]", "</blockquote>", $text, $tag_bq_c);
		
		# [b]
		$text = str_ireplace("[b]", "<b>", $text, $tag_b_o);
		$text = str_ireplace("[/b]", "</b>", $text, $tag_b_c);
		# [i]
		$text = str_ireplace("[i]", "<i>", $text, $tag_i_o);
		$text = str_ireplace("[/i]", "</i>", $text, $tag_i_c);
		# [u]
		$text = str_ireplace("[u]", "<u>", $text, $tag_u_o);
		$text = str_ireplace("[/u]", "</u>", $text, $tag_u_c);
		
		#font

		#link
		
		
		$text = $this->br($text);
		
		return $text;
	}
	
	
	//************************************************
	// parse HTML	
	function html($text) {
		
 		$text = strtr($text, array(
			'&lt;'		=> '<', 		//	< [lt]
			'&gt;'		=> '>', 		//	> [rt]
			'&#123;'	=> '{', 		//	{
			'&#125;'	=> '}', 		//	}
			'&quot;'	=> '"', 		//	" [quot]
			'&amp;'		=> '&',			//	& [amp]
			'&#36;'		=> '$'
		));
		
		return $text;
	}
	
	
	//************************************************
	// parser BR 
	function br($text) {
		//$text = str_replace("\n", "<br />", $text);
		$text = nl2br($text);
		return $text;
	}
	
	
	//************************************************
	// parser anchor in text 
	function anchors($text) {
		// Извлекаем имя хоста из URL
		// preg_match("/^(http:\/\/)?([^\/]+)/i",
			// "http://www.php.net/index", $matches);
		// $host = $matches[2];

		// извлекаем две последние части имени хоста
		// preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
		// echo "domain name is: {$matches[0]}\n";
	
		if (preg_match_all("#(^|\s|\()((http(s?)://)|(www\.))(\w+[^\s\)\<]+)#i", $text, $matches)) {

			for($i=0;$i<sizeof($matches['0']);$i++) {
				
				$period = '';
				
				if (preg_match("|\.$|", $matches['6'][$i])) {
					
					$period = '.';
					$matches['6'][$i] = substr($matches['6'][$i], 0, -1);
				}
		
				$text = str_ireplace($matches['0'][$i],
									 $matches['1'][$i].'<a href="http'.
									 $matches['4'][$i].'://'.
									 $matches['5'][$i].
									 $matches['6'][$i].'" target="_blank" rel="nofollow">http'.
									 $matches['4'][$i].'://'.
									 $matches['5'][$i].
									 $matches['6'][$i].'</a>'.
									 $period, $text);
			}
		}

		return $text;
	}
	
	
	//************************************************
	// parser numbers 
	function only_numbers($n) {
		return preg_replace("/[^0-9\-()]+/","",$n);
	}
}

?>