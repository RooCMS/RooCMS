<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS Template Class
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
|	Build: 			15:26 29.11.2010
|	Last Build: 	15:23 28.10.2011
|	Version file:	3.00 build 9
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


$tpl = new template;

class template {

	# template classes
	public	$tpl;
	
	# output buffer
	private $out 	= "";

	# other
	private $css	= "";
	private $js		= "";
	

	
	//###############################################################
	//#	Fucntion output template from /templates/*.tpl
	//#	@tpl - name of template
	//###############################################################
	public function load_template($tpl, $buffer = false, $path = _TEMPLATES) {
		
		global $var, $Debug;
		
		$template = "";

		// Если нет шаблона
		if(!file_exists($path."/".$tpl.".html") && !file_exists($path."/".$tpl.".php") && $Debug->debug == 1) {
			$Debug->debug_info .= "Не удалось найти шаблон: <br /><b>".$path."/".$tpl.".[html|php]</b><br />";
		}
		
		if(file_exists($path."/".$tpl.".html") || file_exists($path."/".$tpl.".php")) {
			// HTML
			if(file_exists($path."/".$tpl.".html")) {
				$f = file($path."/".$tpl.".html");
				
				// debug html comment
				if($Debug->debug == 1)	$template .= "\n<!-- Start template: {$tpl} -->\n";
				
				// 	Собираем шаблон
				foreach($f AS $k=>$v){ 
					$template .= "{$v}";
				}
				
				// debug html comment
				if($Debug->debug == 1)	$template .= "\n<!-- End template: {$tpl} -->\n";
			}
			
			//PHP
			if(file_exists($path."/".$tpl.".php")) {
				
				require_once $path."/".$tpl.".php";

				// Init class TPL ==========
				if(class_exists("tpl_items_".$tpl)) {
					$inittpl = "\$this->tpl = new tpl_items_{$tpl};";
					eval($inittpl);
				}
			
				// HTML ====================
				// debug html comment
				if($Debug->debug == 1)	$template .= "\n<!-- start template: {$tpl} -->\n";
				
				// Запускаем основной шаблон
				$template  	.= $this->tpl->tpl_page();
				
				// debug html comment
				if($Debug->debug == 1)	$template .= "\n<!-- end template: {$tpl} -->\n";
				
				
				// CSS =====================
				$this->load_css($tpl);

				// JS ======================
				$this->load_js($tpl);
			}
		}
		
		
		# Final
		if($buffer) return $template; 			// возвращаем буфер
		else 		$this->out .= $template; 	// помешаем в буфер основной шаблон
	}
	
	
	//###############################################################
	//#	Fucntion output css from template
	//###############################################################
	private function load_css($tpl="") {
	
		global $Debug;
	
		// debug css comment
		if($Debug->debug == 1 && trim($this->tpl->tpl_css()) != "")	
			$this->css .= "\n/* css template: {$tpl} */\n";
		
		// Запускаем стили для шаблона
			$this->css 	.= trim($this->tpl->tpl_css());
		
		// debug css comment
		if($Debug->debug == 1 && trim($this->tpl->tpl_css()) != "")	
			$this->css .= "\n/* end css template: {$tpl} */\n";
	
	}
	
		
	//###############################################################
	//#	Fucntion output JSs from template
	//###############################################################
	private function load_js($tpl="") {
	
		global $Debug;
		
		if($Debug->debug == 1 && trim($this->tpl->tpl_js()) != "")	
			$this->js .= "\n<!-- start js template: {$tpl} -->\n";
			
		// запускаем js для шаблона
		$this->js .= "\n".trim($this->tpl->tpl_js());

		
		// debug js comment
		if($Debug->debug == 1 && trim($this->tpl->tpl_js()) != "")	
			$this->js .= "\n<!-- end js template: {$tpl} -->\n";
	}
			

	//###############################################################
	//# 	Init tamplates vars
	//#	{*:variables}
	//###############################################################
	private function load_vars($code = "") {

		if($code == "") $code =& $this->out;
		
		//preg_match_all('/(?:\{([^}]\S+?):([^}]\S+?)\})/', $code, $block);
		preg_match_all('/(?:\{([\.\-_A-Za-z0-9]\S+?):([\.\-_A-Za-z0-9]\S+?)\})/', $code, $block);

		$u = array_unique($block[0]);
		$c = count($u);
		
		foreach($block[1] AS $key=>$value) {
			if(isset($u[$key])) {
				switch(mb_strtolower($value, 'utf8')) {
					case 'html':
						$this->load_html(mb_strtolower($block[2][$key], 'utf8'));
						break;
						
					case 'module':
						$this->load_module(mb_strtolower($block[2][$key], 'utf8'));
						break;
						
					default:
						$c--;
						break;
				}
			}
		}

		if($c != 0) $this->load_vars();
		
		if($code != "") return $code;
	}
			
	//###############################################################
	//# 	HTML construct
	//#	{html:variables}
	//###############################################################
	private function load_html($param) {
		
		global $html, $Debug;

		$buffer = "";		
		if(isset($html[$param])) {
			if(is_array($html[$param])) {
				foreach($html[$param] AS $key=>$value) {
					$buffer .= $value;
				}
			}
			else $buffer = $html[$param];
		}
		elseif($Debug->debug == 1) {
			$this->error .= "Не удалось найти переменную: <br /><b>{html:".$param."}</b><br />";
		}
		
		$this->out = str_replace("{html:".$param."}", $buffer, $this->out);
	}
	
	
	//###############################################################
	//# 	Module construct
	//#	{module:variables}
	//###############################################################
	private function load_module($param) {

		global $tpl, $html, $parse, $module, $Debug;

		if(file_exists(_CMS."/modules/".$param.".php")) {
			require_once _CMS."/modules/".$param.".php";
			if(isset($module[$param])) $output = $module[$param];
			else $output = "";
		}
		else {
			if($Debug->debug == 1) $this->error .= "Не удалось найти модуль: <br /><b>{".$param."}</b><br />";
			$output = "";
		}

		// output
		$this->out = str_replace("{module:{$param}}", $output, $this->out);
	}
	

	//###############################################################
	//# 	Load Meta Tags
	//###############################################################
	private function load_meta() {
	
		global $config, $parse, $rss, $var;
		
		$this->info_popup();
		
		// init Meta
		$this->out = str_ireplace("{title}",		$var['title'],				$this->out);
		$this->out = str_ireplace("{charset}",		CHARSET,					$this->out);
		$this->out = str_ireplace("{keywords}",		$config->meta_keywords ,	$this->out);
		$this->out = str_ireplace("{description}",	$config->meta_description,	$this->out);
		$this->out = str_ireplace("{domain}",		$config->baseurl,			$this->out);
		$this->out = str_ireplace("{copyright}",	'Сделано на <a href="http://www.roocms.com/" target="_blank" title="RooCMS">RooCMS</a>  &copy; 2010-2011<br />Версия '.VERSION,	$this->out);
		$this->out = str_ireplace("{info}",			$parse->info,				$this->out);
		$this->out = str_ireplace("{error}",		$parse->error,				$this->out);
		
		//###########################################################
		//#	FUCK YOU BILL GATES	|	FUCK YOU INTERNET EXPLORER
		//###########################################################
		if($config->fuckie && $parse->browser('ie', 8)) {
			$this->out = str_ireplace("{FUCKIE}",	$this->load_template("fuck_IE",true),	$this->out);
		}
		else $this->out = str_ireplace("{FUCKIE}",	"",	 $this->out);

		
		// init CSS
		if(trim($this->css) != "") $this->css = "<style type=\"text/css\" id=\"template_css\" media=\"screen\">\n".$this->css."\n</style>\n";
		$this->out = str_ireplace("{CSS}",		$this->css,	$this->out);

		// init RSS link
		//	собаку мы используем только по одному случаю -> rss класс объявляется только в случае когда явно вызван. В остальных его нет.
		if(@$rss->rss_link != "") $this->js .= "\n\n <!-- RSS --> \n <link rel=\"alternate\" type=\"application/rss+xml\" title=\"".$var['title']." - RSS Лента\" href=\"".$rss->rss_link."\" />";
	
		// init JavaScript
		$this->out = str_ireplace("{JSCRIPT}",	$this->js,	$this->out);
	}
	
	
	//###############################################################
	//# 	Parse error & info
	//###############################################################
	private function info_popup() {
	
		global $parse;
	
		if($parse->error != "") {
			$parse->error = "<div id=\"error\">".$parse->error."</div>";
			$this->js .= "<script type=\"text/javascript\" src=\"inc/jquery.notice.js\"></script>";
		}
		
		if($parse->info != "") {
			$parse->info = "<div id=\"info\">".$parse->info."</div>";
			$this->js .= "<script type=\"text/javascript\" src=\"inc/jquery.notice.js\"></script>";
		}
	
	}
	
	
	//###############################################################
	//# 	Out html
	//###############################################################
	public function out() {

		global $roocms, $rss, $var;
		
		if($roocms->rss && $rss->check_rss()) $this->out = $rss->out();
		else {
			
			// инициализируем переменные
			$this->load_vars();
			
			// Load meta_tags 
			$this->load_meta();

			
			$this->out = str_ireplace("{THIS}",			THIS_SCRIPT.".php",			$this->out);
			//$this->out = str_ireplace("{RURI}",			$_SERVER['REQUEST_URI'],	$this->out);
			$this->out = str_ireplace("{build}",		BUILD,						$this->out);
			
			
			if(!$roocms->ajax) {
				$this->out = str_ireplace('href="#',	'href="'.$_SERVER['REQUEST_URI'].'#',	$this->out);
			}
		}
	
		// Выводим
		return $this->out;
	}
}

?>