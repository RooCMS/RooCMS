<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      2.1.1
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

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('ACP')) die('Access Denied');
//#########################################################


class ACP_INDEX {


	/**
	 * Run, baby, run
	 */
	public function __construct() {

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

			case 'phpinfo':
				$this->showphpinfo();
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


	/**
	 * Главный экран
	 * Показывается общая информация
	 */
	private function main() {

		global $tpl, $smarty;

		$warn = array();
		if(file_exists(_SITEROOT."/install/index.php")) $warn[] = "Инсталятор RooCMS находится в корне сайта. В целях безопастности следует удалить инсталятор!";

        	$f = @file("http://version.roocms.com/index.php");

        	if($f && version_compare(ROOCMS_VERSION, $f[0], "<")) $warn[] = "Внимание! Вышла новая версия <b>RooCMS {$f[0]}</b>. Рекомендуем обновить ваш сайт до последней версии.
        			<br />Что бы скачать дистрибутив последней версии перейдите по <a href='http://www.roocms.com/index.php?page=download' target='_blank'>ссылке</a>";

		$info = array();
		$info['roocms'] = ROOCMS_VERSION;
		if($f) $info['last_stable'] = $f[0];


		# draw
		$smarty->assign('part_title', 	'Сводка по сайту');
		$smarty->assign('warn',		$warn);
		$smarty->assign('info',		$info);

		$content = $tpl->load_template("index_main", true);
		$smarty->assign('content',	$content);
	}


	/**
	 * Информация о сервере
	 */
	private function serverinfo() {

		global $db, $tpl, $smarty;

		# Версия mySql
		$q = $db->query("SHOW VARIABLES LIKE 'version'");
		$mysql = $db->fetch_row($q);
		$version['mysql']	= $mysql[1];

		$version['php'] 	= PHP_VERSION;				# Версия php
		$version['zend']	= zend_version();			# Версия Zend
		$version['apache'] 	= $_SERVER['SERVER_SOFTWARE'];		# Версия сервера  apache_get_version();
		$version['os']		= php_uname("s")." (".PHP_OS.")"; 	# ОС
		$version['uname']	= php_uname(); 				# UNAME
		$version['roocms']	= ROOCMS_VERSION;			# RooCMS

		$version['pid']		= PEAR_INSTALL_DIR; 			# Директория установки PEAR расширений
		$version['dip']		= DEFAULT_INCLUDE_PATH;
		$version['ped']		= PHP_EXTENSION_DIR;			# Директория php расширений
		$version['pcp']		= PHP_CONFIG_FILE_PATH;

		$version['sn']		= $_SERVER["SERVER_NAME"]; 		# Имя сервера
		$version['sa']		= $_SERVER["SERVER_ADDR"]; 		# Адрес сервера
		$version['sp']		= $_SERVER["SERVER_PROTOCOL"]; 		# Протокол сервера
		$version['ra']		= $_SERVER["REMOTE_ADDR"]; 		# Адрес клиента
		$version['docroot']	= _SITEROOT; 				# Путь к документам на сервере

		$version['ml']		= ini_get('memory_limit');		# Memory limit
		$version['mfs']		= ini_get('upload_max_filesize');	# Maximum file size
		$version['mps']		= ini_get('post_max_size');		# Maximum post size
		$version['met']		= ini_get('max_execution_time');	# Max execution time

		$version['apache_mods']	= apache_get_modules();			# Расширения Apache


		# draw
		$smarty->assign('part_title', 	'Информация о сервере');
		$smarty->assign('version',	$version);

		$content = $tpl->load_template("index_serverinfo", true);
		$smarty->assign('content',	$content);
	}


	/**
	 * Информация о допустимых файлах и расширениях
	 */
	private function fileinfo() {

		global $tpl, $smarty;

		require_once _LIB."/mimetype.php";

		$filetypes['mfs']		= ini_get('upload_max_filesize');	# Maximum file size
		$filetypes['mps']		= ini_get('post_max_size');		# Maximum post size
		$filetypes['images']		= $imagetype;				# Allow image types
		$filetypes['files']		= $filetype;				# Allow file types

		# draw
		$smarty->assign('part_title', 	'Информация о файлах');
		$smarty->assign('filetypes',	$filetypes);

		$content = $tpl->load_template("index_fileinfo", true);
		$smarty->assign('content',	$content);
	}


	/**
	 * Проверка и отображение установленных PHP расширений
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
		$smarty->assign('content',		$content);
	}


	/**
	 * Показывает phpinfo
	 */
	private function showphpinfo() {

		global $tpl, $smarty;

		ob_start();
			phpinfo();
			$output = ob_get_contents();
		ob_end_clean();

		preg_match_all('#(\<body[^\>]*\>)(.+?)(\<\/body\>)#is', $output, $out);

		$phpinfo = $out[2][0];

		# draw
		$smarty->assign('part_title', 	'PHP info');
		$smarty->assign('phpinfo', 	$phpinfo);

		$content = $tpl->load_template("index_phpinfo", true);
		$smarty->assign('content',	$content);
	}


	/**
	 * Значение PHP переменных
	 */
	private function inivars() {

		global $tpl, $smarty;

		$inivars = ini_get_all();

        	# draw
		$smarty->assign('part_title', 	'Значение PHP переменных');
		$smarty->assign('inivars',	$inivars);

		$content = $tpl->load_template("index_inivars", true);

		$smarty->assign('content',	$content);
	}


	/**
	 * Лицензия
	 */
	private function license() {

		global $tpl, $smarty;

		require_once _LIB."/license.php";

		# draw
		$smarty->assign('part_title', 	'Лицензионное соглашение');
		$smarty->assign('license',	$license['ru']);

		$content = $tpl->load_template("index_license", true);

		$smarty->assign('content',	$content);
	}
}

/**
 * Init Class
 */
$acp_index = new ACP_INDEX;
?>