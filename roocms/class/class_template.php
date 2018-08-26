<?php
/**
 *   RooCMS - Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
 *
 *   You should have received a copy of the GNU General Public License v3
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
* @package	RooCMS
* @subpackage	Engine RooCMS classes
* @author	alex Roosso
* @copyright	2010-2019 (c) RooCMS
* @link		http://www.roocms.com
* @version	4.8
* @since	$date$
* @license	http://www.gnu.org/licenses/gpl-3.0.html
*/


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class template
 */
class Template {

	# vars
	private $skinfolder	= "default";

	# other buffer
	private $css		= "";
	private $js		= "";
	private $skin		= "";

	# output buffer
	private $out 		= "";


	/**
	 * Инициализируем "шкурку"
	 *
	 * @param mixed $skin - указываем относительный путь к папке с "шкуркой" от папки _SKIN
	 */
	public function __construct($skin=false) {

		global $config, $site, $smarty;

		if(!$skin) {
			if(defined('ACP')) {
				$this->skinfolder = "acp";
			}
			elseif(defined('INSTALL')) {
				$this->skinfolder = "../install/skin";
			}
			else {
				$this->skinfolder = $site['skin'];
			}
		}
		else {
			$this->skinfolder = $skin;
		}

		# init settings smarty
		$this->set_smarty_options();

		# config vars
		$smarty->assign("config",  $config);

		# copyright text
		$smarty->assign("copyright",	"<a href=\"http://www.roocms.com/\">RooCMS</a> &copy; 2010-".date("Y"));
	}


	/**
	* set Smarty settings
	*
	*/
	private function set_smarty_options() {

		global $config, $parse, $smarty;

		# set tempplates options
                $smarty->template_dir 	= _SKIN."/".$this->skinfolder."/";
                $smarty->compile_id 	=& $this->skinfolder;
                $smarty->compile_dir  	= _CACHESKIN;
                $smarty->cache_dir    	= _CACHE;

		# set other options
                $smarty->caching 	= 0;
                $smarty->cache_lifetime = 60;
		if(isset($config->tpl_recompile_force))	{
			$smarty->force_compile 		= $config->tpl_recompile_force;
		}
		if(isset($config->if_modified_since)) {
			$smarty->cache_modified_check 	= $config->if_modified_since;
		}

		# filters
		if($parse->uri_chpu) {
			$smarty->autoload_filters = array('output' => array('correct4pu'));
		}

		//$smarty->config_fix_newlines = false;
		//$smarty->compile_check = false;

		//$smarty->clearAllCache();

		# debug mode for smarty
		//$smarty->debugging = DEBUGMODE;

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
	 * @return string|null
	 */
	public function load_template($tpl, $return=false) {

		global $smarty;

		$path	= _SKIN."/".$this->skinfolder;
		$out 	= "";

		if($this->tpl_exists($path, $tpl)) {
			# load html
			if(DEBUGMODE && $tpl != "header") {
				$out .= "\r\n<!-- begin template: {$tpl} -->\r\n";
			}

			$out .= $smarty->fetch($tpl.".tpl");

			if(DEBUGMODE && $tpl != "footer") {
				$out .= "\r\n<!-- end template: {$tpl} -->\r\n";
			}
		}

		if($return) {
			return $out;
		}
		else {
			$this->out .= $out;
		}
	}


	/**
	 * Функция проверяет наличие шаблона
	 *
	 * @param string $path - path to folder
	 * @param string $tpl  template name
	 *
	 * @return bool
	 */
	private function tpl_exists($path, $tpl) {

		global $debug;

		if(file_exists($path."/".$tpl.".tpl")) {
			return true;
		}
		else {
			if(DEBUGMODE) {
				# show message in debugmode
				$debug->debug_info .= "Не удалось найти шаблон: <br /><b>".$path."/".$tpl.".tpl</b><br />";
			}

			return false;
		}
	}


	/**
	 * Функция подключения шаблона для загрузки картинок
	 * Определяет какой из шаблонов требуется подключать и в какую переменную
	 *
	 * @param string $smarty_variable - указываем переменную для смарти шаблонов.
	 * @param string $tpl             - На случай если вам потребуется использовать собственный шаблон
	 * @param bool   $tplreturn       - Возврат скомпилорованного шаблона в переменную. По-умолчанию включено.
	 */
	public function load_image_upload_tpl($smarty_variable, $tpl="images_upload", $tplreturn=true) {

		global $smarty;

		require _LIB."/mimetype.php";
		$smarty->assign("allow_images_type", $imagetype);

		$smarty_tpl = $this->load_template("{$tpl}", $tplreturn);
		$smarty->assign("{$smarty_variable}", $smarty_tpl);
	}


	/**
	 * Функция подключения шаблона для загрузки файлов
	 * Определяет какой из шаблонов требуется подключать и в какую переменную
	 *
	 * @param string $smarty_variable - указываем переменную для смарти шаблонов.
	 * @param string $tpl             - На случай если вам потребуется использовать собственный шаблон
	 * @param bool   $tplreturn       - Возврат скомпилорованного шаблона в переменную. По-умолчанию включено.
	 */
	public function load_files_upload_tpl($smarty_variable, $tpl="files_upload", $tplreturn=true) {

		global $smarty;

		require _LIB."/mimetype.php";
		$smarty->assign("allow_files_type", $filetype);

		$smarty_tpl = $this->load_template("{$tpl}", $tplreturn);
		$smarty->assign("{$smarty_variable}", $smarty_tpl);
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
		foreach($b as $v) {
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
		foreach($m as $v) {
			$v = str_ireplace('"', '', $v);
			$buf = $module->load($v);
			$this->out = str_ireplace("{\$module->load({$v})}", $buf, $this->out);
		}
	}


	/**
	 * Функция компилирует вывод параметров в head
	 *
	 * @return string|null tpl
	 */
	private function init_head() {

		global $config, $site, $structure, $parse, $debug, $rss, $smarty;

		# check notice
		$this->info_popup();

		# global site title
		if(!defined('INSTALL') && isset($config->global_site_title)) {
			$site['title'] .= " &bull; ".$config->site_title;
		}

		# get actual version included js and styles in templates (only Developer or Debug mode)
		$build = (DEBUGMODE || DEVMODE) ? "?v=".str_ireplace(".","",ROOCMS_VERSION)."-".time() : "" ;

		# assign tpl vars
		$smarty->assign("site",	   $site);
		$smarty->assign("charset", CHARSET);
		$smarty->assign("build",   $build);
		$smarty->assign("css",     $this->css);
		$smarty->assign("jscript", $this->js);
		$smarty->assign("error",   $parse->error);
		$smarty->assign("info",	   $parse->info);

		if(!defined('ACP')) {
			# rss link
			$smarty->assign("rsslink",	$rss->rss_link);

			# meta noindex
			$smarty->assign("noindex",	$structure->page_noindex);

			# breadcumb
			$structure->construct_breadcumb($structure->page_id);
			krsort($structure->breadcumb);

			$smarty->assign("breadcumb",	$structure->breadcumb);

			$breadcumb = $this->load_template("breadcumb", true);
			$smarty->assign("breadcumb",	$breadcumb);
		}

		# head
		$output = $this->load_template("header", true);

		return $output;
	}


	/**
	 * Функция компилирует вывод параметров в footer
	 *
	 * @return string|null tpl
	 */
	private function init_footer() {

		global $db, $debug, $smarty;

		# debug_info in footer
		$smarty->assign("db_querys", 		$db->cnt_querys);

		$debug->end_productivity();
		$smarty->assign("debug_timer",		$debug->productivity_time);
		$smarty->assign("debug_memory",		$debug->productivity_memory);
		$smarty->assign("debug_memusage",	$debug->memory_peak_usage);

		# foot
		$output = $this->load_template("footer", true);

		return $output;
	}


	/**
	* Выводим скомпилированный HTML на экран
	*
	*/
	public function out() {

		global $roocms, $rss;

		# html output
		if(!$roocms->rss) {

			# template with header and footer
			if(!$roocms->ajax) {
				$this->out = $this->init_head().$this->out.$this->init_footer();
			}

			# blocks & module in UI
			if(!defined('ACP')) {
				$this->init_blocks();
				$this->init_modules();
			}

			# output
			echo $this->out;
		}
		else {
			# rss output
			echo $rss->out();
		}

		# secure
		unset($_GET);
	}
}