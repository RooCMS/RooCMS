<?php
/**
* @package		RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	Template Class
* @author		alex Roosso
* @copyright	2010-2014 (c) RooCMS
* @link			http://www.roocms.com
* @version		4.0.24
* @since		$date$
* @license		http://www.gnu.org/licenses/gpl-2.0.html
*
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

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


/**
* Запускаем шаблонизатор
*
* @var template
*/
$tpl = new template;

class template {

	# vars
	private $skinfolder = "default";	# [string]	skin templates folder

	# other buffer
	private $css		= "";			# [text]	CSS buffer
	private $js			= "";			# [text]	JavaScript buffer

	# output buffer
	private $out 		= "";			# [text]	output buffer



	/**
	* Инициализируем "шкурку"
	*
	* @param mixed $skin - указываем относительный путь к папке с "шкуркой" от папки _SKIN
	*/
	public function template($skin=false) {

		global $site;

		if(!$skin) {
			if(defined('ACP')) $this->skinfolder = "acp";
			elseif(defined('INSTALL')) $this->skinfolder = "../install/skin";
			else $this->skinfolder = $site['skin'];
		}
		else $this->skinfolder = $skin;

		# init settings smarty
		$this->set_smarty();
	}


	/**
	* set Smarty settings
	*
	*/
	private function set_smarty() {

		global $debug, $config, $smarty;

		# set tempplates options
        $smarty->template_dir 	= _SKIN."/".$this->skinfolder."/";
        $smarty->compile_id 	=& $this->skinfolder;
        $smarty->compile_dir  	= _CACHESKIN;
        $smarty->cache_dir    	= _CACHE;

		# set other options
        $smarty->caching = false;
		if(isset($config->tpl_recompile_force))	$smarty->force_compile = $config->tpl_recompile_force;
		if(isset($config->if_modified_since)) 	$smarty->cache_modified_check = $config->if_modified_since;
		//$smarty->config_fix_newlines = false;

		# debug mode for smarty
		$smarty->debugging =& $debug->debug;


		# assign skin folders templates
		$smarty->assign("SKIN",str_replace(_ROOT, "", _SKIN)."/".$this->skinfolder);
	}



	/* ####################################################
	 *		Load template
     */
	public function load_template($tpl, $return=false) {

		global $smarty, $debug;

		$path	= _SKIN."/".$this->skinfolder;
		$out 	= "";

		# Если нет шаблона
		if(!file_exists($path."/".$tpl.".tpl") && $debug->debug == 1) {
			$debug->debug_info .= "Не удалось найти шаблон: <br /><b>".$path."/".$tpl.".tpl</b><br />";
		}

		if(file_exists($path."/".$tpl.".tpl")) {
			# load html
			if($debug->debug && $tpl != "header") $out .= "\r\n<!-- begin template: {$tpl} -->\r\n";

			$out .= $smarty->fetch($tpl.".tpl");
			//$out .= $smarty->display($tpl.".html");


			if($debug->debug) $out .= "\r\n<!-- end template: {$tpl} -->\r\n";
		}

		if($return) return $out;
		else $this->out .= $out;
	}


	/**
	* Parse error & info
	*
	*/
	private function info_popup() {

		global $parse;

		if(trim($parse->error) != "" && trim($parse->info) == "") {
			$this->js .= "<script type=\"text/javascript\" src=\"inc/jquery.notice.js\"></script>";
		}

		if(trim($parse->info) != "") {
			if(trim($parse->error) != "") {
				$parse->info .= $parse->error;
				$parse->error = "";
			}

			$this->js .= "<script type=\"text/javascript\" src=\"inc/jquery.notice.js\"></script>";
		}

	}


	/**
	* Parse OUTPUT for eval blocks
	*
	*/
	function init_blocks() {

		global $blocks;

		//preg_match_all('/(?:\{([\.\-_A-Za-z0-9]\S+?):([\.\-_A-Za-z0-9]\S+?)\})/', $this->out, $block);
		preg_match_all('(\{\$blocks-\>load\(([a-zA-Z0-9_"\';&-]*?)\)\})', $this->out, $block);

		$u = array_unique($block[1]);
		foreach($u as $k=>$v) {
			$v = str_ireplace('"', '', $v);
			$buf = $blocks->load($v);
			$this->out = str_ireplace("{\$blocks->load({$v})}", $buf, $this->out);
		}
	}


	/**
	* Выводим скомпилированный HTML на экран
	*
	*/
	public function out() {

		global $roocms, $config, $db, $rss, $site, $structure, $smarty, $parse, $debug;

		# header & footer
		if(!$roocms->ajax && !$roocms->rss) {

			# check notice
			$this->info_popup();

            # noindex for robots
            $robots = (!defined('ACP')) ? "index, follow, all" : "no-index,no-follow,all" ;
            if(!defined('ACP')) if($structure->page_noindex == 1) $robots = "no-index,no-follow,all";


            # global site title
            if(isset($config->global_site_title)) $site['title'] .= " &bull; ".$config->site_title;

			# assign tpl vars
			$smarty->assign("site",			$site);
			$smarty->assign("charset",		CHARSET);
			$smarty->assign("build",		BUILD);
			$smarty->assign("jscript",		$this->js);
			$smarty->assign("robots",		$robots);

			$smarty->assign("fuckie",		"");
			$smarty->assign("error",		$parse->error);
			$smarty->assign("info",			$parse->info);

			$smarty->assign("rsslink",		$rss->rss_link);


			# copyright text
			$smarty->assign("copyright",		"<a href=\"http://www.roocms.com/\">RooCMS</a> &copy; 2010-".date("Y"));


			$head = $this->load_template("header", true);

			# debug_info in footer
			if(isset($roocms->sess['acp'])) {
				$smarty->assign("debug", 		$debug->debug);
				$smarty->assign("devmode", 		$debug->dev_mode);
				$smarty->assign("db_querys", 	$db->cnt_querys);
				$smarty->assign("debug_timer",  $debug->endTimer());
			}

			$foot = $this->load_template("footer", true);

			$this->out = $head.$this->out.$foot;

			# blocks
			if(!defined('ACP')) $this->init_blocks();
		}


		# output
		echo (!$roocms->rss) ? $this->out : $rss->out() ;


		# Close connection to DB (recommended)
		$db->close();
	}
}

?>
