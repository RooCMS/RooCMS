<?php
/**
* @package	RooCMS
* @subpackage	Engine RooCMS classes
* @author	alex Roosso
* @copyright	2010-2015 (c) RooCMS
* @link		http://www.roocms.com
* @version	4.5.2
* @since	$date$
* @license	http://www.gnu.org/licenses/gpl-3.0.html
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

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


/**
 * Class template
 */
class template {

	# vars
	private $skinfolder	= "default";	# [string]	skin templates folder

	# other buffer
	private $css		= "";		# [text]	CSS buffer
	private $js		= "";		# [text]	JavaScript buffer
	private $skin		= "";		# [string]	skin

	# output buffer
	private $out 		= "";		# [text]	output buffer



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
		$this->set_smarty_options();
	}


	/**
	* set Smarty settings
	*
	*/
	private function set_smarty_options() {

		global $config, $smarty;

		# set tempplates options
                $smarty->template_dir 	= _SKIN."/".$this->skinfolder."/";
                $smarty->compile_id 	=& $this->skinfolder;
                $smarty->compile_dir  	= _CACHESKIN;
                $smarty->cache_dir    	= _CACHE;

		# set other options
                $smarty->caching 	= 0;
                $smarty->cache_lifetime = 60;
		if(isset($config->tpl_recompile_force))	$smarty->force_compile = $config->tpl_recompile_force;
		if(isset($config->if_modified_since)) 	$smarty->cache_modified_check = $config->if_modified_since;
		//$smarty->config_fix_newlines = false;
		//$smarty->compile_check = false;

		//$smarty->clearAllCache();

		# debug mode for smarty
		$smarty->debugging = DEBUGMODE;

		# set skin variable
        	$this->skin =  str_replace(_SITEROOT, "", _SKIN)."/".$this->skinfolder;

		# assign skin folders templates
		$smarty->assign("SKIN", $this->skin);
	}


	/**
	 * Загружаем шаблон
	 *
	 * @param string  $tpl    - Имя шаблона.
	 * @param boolean $return - Включенный флаг возвращает загруженный через return
	 *
	 * @return string
	 */
	public function load_template($tpl, $return=false) {

		global $smarty, $debug;

		$path	= _SKIN."/".$this->skinfolder;
		$out 	= "";

		# Если нет шаблона
		if(!file_exists($path."/".$tpl.".tpl") && DEBUGMODE) {
			$debug->debug_info .= "Не удалось найти шаблон: <br /><b>".$path."/".$tpl.".tpl</b><br />";
		}

		if(file_exists($path."/".$tpl.".tpl")) {
			# load html
			if(DEBUGMODE && $tpl != "header") $out .= "\r\n<!-- begin template: {$tpl} -->\r\n";

			$out .= $smarty->fetch($tpl.".tpl");
			//$out .= $smarty->display($tpl.".html");


			if(DEBUGMODE && $tpl != "footer") $out .= "\r\n<!-- end template: {$tpl} -->\r\n";
		}

		if($return) return $out;
		else $this->out .= $out;
	}


	/**
	 * Функция подключения шаблона для загрузки картинок
	 * Определяет какой из шаблонов требуется подключать и в какую переменную
	 *
	 * @param        $smarty_variable - указываем переменную для смарти шаблонов.
	 * @param string $tpl - На случай если вам потребуется использовать собственный шаблон
	 * @param bool   $tplreturn - Возврат скомпилорованного шаблона в переменную. По-умолчанию включено.
	 */
	public function load_image_upload_tpl($smarty_variable, $tpl="images_upload", $tplreturn=true) {

		global $smarty;

		require _LIB."/mimetype.php";
		$smarty->assign("allow_images_type", $imagetype);

		$code  = "\$$smarty_variable = \$this->load_template(\"$tpl\", $tplreturn);";
		$code .= "\$smarty->assign(\"$smarty_variable\", \$$smarty_variable);";

		eval ($code);
	}


	/**
	 * Функция подключения шаблона для загрузки файлов
	 * Определяет какой из шаблонов требуется подключать и в какую переменную
	 *
	 * @param        $smarty_variable - указываем переменную для смарти шаблонов.
	 * @param string $tpl - На случай если вам потребуется использовать собственный шаблон
	 * @param bool   $tplreturn - Возврат скомпилорованного шаблона в переменную. По-умолчанию включено.
	 */
	public function load_files_upload_tpl($smarty_variable, $tpl="files_upload", $tplreturn=true) {

		global $smarty;

		require _LIB."/mimetype.php";
		$smarty->assign("allow_files_type", $filetype);

		$code  = "\$$smarty_variable = \$this->load_template(\"$tpl\", $tplreturn);";
		$code .= "\$smarty->assign(\"$smarty_variable\", \$$smarty_variable);";

		eval ($code);
	}


	/**
	* Parse error & info
	*
	*/
	private function info_popup() {

		global $parse;

		if(trim($parse->info) != "") {
			if(trim($parse->error) != "") {
				$parse->info .= $parse->error;
				$parse->error = "";
			}
		}

	}


	/**
	* Parse OUTPUT for eval blocks
	*
	*/
	private function init_blocks() {

		global $blocks;

		preg_match_all('(\{\$blocks-\>load\(([a-zA-Z0-9_"\';&-]*?)\)\})', $this->out, $block);

		$b = array_unique($block[1]);
		foreach($b as $k=>$v) {
			$v = str_ireplace('"', '', $v);
			$buf = $blocks->load($v);
			$this->out = str_ireplace("{\$blocks->load({$v})}", $buf, $this->out);
		}
	}


	/**
	 * Parse OUTPUT for eval module
	 *
	 */
	private function init_modules() {

		global $module;

		preg_match_all('(\{\$module-\>load\(([a-zA-Z0-9_"\';&-]*?)\)\})', $this->out, $mod);

		$m = array_unique($mod[1]);
		foreach($m as $k=>$v) {
			$v = str_ireplace('"', '', $v);
			$buf = $module->load($v);
			$this->out = str_ireplace("{\$module->load({$v})}", $buf, $this->out);
		}
	}


	/**
	* Выводим скомпилированный HTML на экран
	*
	*/
	public function out() {

		global $roocms, $config, $db, $site, $smarty, $parse, $debug;
		if(!defined('ACP')) global $rss, $structure;

		# header & footer
		if(!$roocms->ajax && !$roocms->rss) {

                        # check notice
                        $this->info_popup();

                        # noindex for robots
                        $robots = (!defined('ACP')) ? "index, follow, all" : "no-index,no-follow,all" ;
                        if(!defined('ACP')) if($structure->page_noindex == 1) $robots = "no-index,no-follow,all";

                        # global site title
                        if(!defined('INSTALL') && $config->global_site_title) $site['title'] .= " &bull; ".$config->site_title;

                        # jquery-core (check brwoser version)
                        $jquerycore = ($parse->browser("ie",8)) ? "jquery-coreie.min.js" : "jquery-core.min.js" ;

                        # get actual version included js and styles in templates (only Developer or Debug mode)
                        $build = (DEBUGMODE or DEVMODE) ? "?v=".str_ireplace(".","",ROOCMS_VERSION)."-".time() : "" ;


			# assign tpl vars
			$smarty->assign("site",		$site);
			$smarty->assign("charset",	CHARSET);
			$smarty->assign("jquerycore",	$jquerycore);
			$smarty->assign("build",	$build);
			$smarty->assign("jscript",	$this->js);
			$smarty->assign("robots",	$robots);

			$smarty->assign("fuckie",	"");
			$smarty->assign("error",	$parse->error);
			$smarty->assign("info",		$parse->info);

			if(!defined('ACP')) {
				# rss link
				$smarty->assign("rsslink",	$rss->rss_link);

				# breadcumb
				$smarty->assign("breadcumb",	$structure->breadcumb);
				$breadcumb = $this->load_template("breadcumb", true);
				$smarty->assign("breadcumb",	$breadcumb);
			}

			# copyright text
			$smarty->assign("copyright",	"<a href=\"http://www.roocms.com/\">RooCMS</a> &copy; 2010-".date("Y"));


			# head
			$head = $this->load_template("header", true);

			# debug_info in footer
			if(isset($roocms->sess['token'])) {
				$smarty->assign("debug", 		DEBUGMODE);
				$smarty->assign("devmode", 		DEVMODE);
				$smarty->assign("db_querys", 		$db->cnt_querys);

				$debug->end_productivity();
				$smarty->assign("debug_timer",		$debug->productivity_time);
				$smarty->assign("debug_memory",		$debug->productivity_memory);
				$smarty->assign("debug_memusage",	$debug->memory_peak_usage);
			}

			# foot
			$foot = $this->load_template("footer", true);


			# output buffer
			$this->out = $head.$this->out.$foot;

			# blocks & module
			if(!defined('ACP')) $this->init_blocks();
			if(!defined('ACP')) $this->init_modules();
		}


		# output
		echo (!$roocms->rss) ? $this->out : $rss->out() ;

		# secure
		unset($_GET);


		# Close connection to DB (recommended)
		$db->close();
	}
}

?>