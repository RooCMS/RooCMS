<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Main Page
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      2.0.2
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
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
if(!defined('RooCMS') || !defined('ACP')) die('Access Denied');
//#########################################################

$acp_index = new ACP_INDEX;

class ACP_INDEX {


	/* ####################################################
	 *	Run, Lola, run
	 */
	function __construct() {

		global $roocms, $tpl;

		switch($roocms->part) {
			case 'serverinfo':
				$this->serverinfo();
				break;

			case 'fileinfo':
				$this->fileinfo();
				break;

			case 'phpext':
				$this->phpext();
				break;

			case 'inivars':
				$this->inivars();
				break;

			case 'license':
				$this->license();
				break;

			default:
				$this->main();
				break;
		}

		# load template
		$tpl->load_template("index");
	}


	/* ####################################################
	 *	Main Screen
	 */
	function main() {

		global $tpl, $smarty;

		$warn = array();
		if(file_exists(ROOT."/install/index.php")) $warn[] = "Инсталятор RooCMS находится в корне сайта. В целях безопастности следует удалить инсталятор!";

		$info = array();
		$info['roocms'] = ROOCMS_VERSION;

		# draw
		$smarty->assign('part_title', 	'Сводка по сайту');
		$smarty->assign('warn',			$warn);
		$smarty->assign('info',			$info);

		$content = $tpl->load_template("index_main", true);

		$smarty->assign('content',		$content);
	}


	/* ####################################################
	 *	Информация о сервере
	 */
	private function serverinfo() {

		global $db, $tpl, $smarty;

		// Версия mySql ===========================================
		$q = $db->query("SHOW VARIABLES LIKE 'version'");
		$mysql = $db->fetch_row($q);
		$version['mysql']	= $mysql[1];
		//=========================================================


		$version['php'] 	= PHP_VERSION;						// Версия php
		$version['zend']	= zend_version();					// Версия Zend
		$version['apache'] 	= $_SERVER['SERVER_SOFTWARE'];		// Версия сервера
		$version['os']		= PHP_OS; 							// ОС
		$version['roocms']	= ROOCMS_VERSION;					// RooCMS

		$version['pid']		= PEAR_INSTALL_DIR; 				// Директория установки PEAR расширений
		$version['dip']		= DEFAULT_INCLUDE_PATH;				//
		$version['ped']		= PHP_EXTENSION_DIR;				// Директория php расширений
		$version['pcp']		= PHP_CONFIG_FILE_PATH;				//

		$version['sn']		= $_SERVER["SERVER_NAME"]; 			// Имя сервера
		$version['sa']		= $_SERVER["SERVER_ADDR"]; 			// Адрес сервера
		$version['sp']		= $_SERVER["SERVER_PROTOCOL"]; 		// Протокол сервера
		$version['ra']		= $_SERVER["REMOTE_ADDR"]; 			// Адрес клиента
		$version['docroot']	= $_SERVER["DOCUMENT_ROOT"]; 		// Путь к документам на сервере

		$version['ml']		= ini_get('memory_limit');			// Memory limit
		$version['mfs']		= ini_get('upload_max_filesize');	// Maximum file size
		$version['mps']		= ini_get('post_max_size');			// Maximum post size
		$version['met']		= ini_get('max_execution_time');	// Max execution time


		# draw
		$smarty->assign('part_title', 	'Информация о сервере');
		$smarty->assign('version',		$version);

		$content = $tpl->load_template("index_serverinfo", true);

		$smarty->assign('content',		$content);
	}


	/* ####################################################
	 *	Информация о файлах
	 */
	function fileinfo() {

		global $tpl, $smarty;

		require_once _LIB."/mimetype.php";

		$filetypes['mfs']		= ini_get('upload_max_filesize');	// Maximum file size
		$filetypes['mps']		= ini_get('post_max_size');			// Maximum post size
		$filetypes['images']	= $imagetype;						// Allow image types
		$filetypes['files']		= $filetype;						// Allow file types

		# draw
		$smarty->assign('part_title', 	'Информация о файлах');
		$smarty->assign('filetypes',	$filetypes);

		$content = $tpl->load_template("index_fileinfo", true);

		$smarty->assign('content',		$content);
	}


	/* ####################################################
	 *	Проверка и отображение установленных PHP расширений
	 */
	private function phpext() {

		global $config, $tpl, $smarty;

		$phpextfunc = array();
		foreach($config->phpextensions AS $k=>$v) {
			$phpextfunc[$v] = get_extension_funcs($v);
		}

		# draw
		$smarty->assign('part_title', 		'Установленные PHP расширения');
		$smarty->assign('phpextensions',	$config->phpextensions);
		$smarty->assign('phpextfunc',		$phpextfunc);

		$content = $tpl->load_template("index_phpext", true);

		$smarty->assign('content',			$content);
	}


	/* ####################################################
	 *	Значение PHP переменных
	 */
	private function inivars() {

		global $tpl, $smarty;

		$inivars = ini_get_all();

		# draw
		$smarty->assign('part_title', 	'Значение PHP переменных');
		$smarty->assign('inivars',		$inivars);

		$content = $tpl->load_template("index_inivars", true);

		$smarty->assign('content',		$content);
	}


	/* ####################################################
	 *	Лицензия
	 */
	private function license() {

		global $tpl, $smarty;

		require_once _LIB."/license.php";

		# draw
		$smarty->assign('part_title', 	'Лицензионное соглашение');
		$smarty->assign('license',		$license['ru']);

		$content = $tpl->load_template("index_license", true);

		$smarty->assign('content',		$content);
	}
}
?>