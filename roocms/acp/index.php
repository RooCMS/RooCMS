<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyright    2010-2017 (c) RooCMS
* @link         http://www.roocms.com
* @version      2.2.1
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2017 alex Roosso aka alexandr Belov info@roocms.com
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
*   Copyright (C) 2010-2017 alex Roosso (александр Белов) info@roocms.com
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

		$warning_subj = array();
		if(file_exists(_SITEROOT."/install/index.php")) $warning_subj[] = "Инсталятор RooCMS находится в корне сайта. В целях безопастности следует удалить инсталятор!";

		$info = array();
		$info['roocms'] = ROOCMS_VERSION;

		if(get_http_response_code("http://version.roocms.com/index.php") == "200") {
			$f = file("http://version.roocms.com/index.php");

			if(!empty($f) && version_compare(ROOCMS_VERSION, $f[0], "<")) {
				$warning_subj[] = "Внимание! Вышла новая версия <b>RooCMS {$f[0]}</b>. Рекомендуем обновить ваш сайт до последней версии.
				<br />Что бы скачать дистрибутив последней версии перейдите по <a href='http://www.roocms.com/index.php?page=download' target='_blank'>ссылке</a>";

				$info['last_stable'] = $f[0];
			}
		}

		# draw
		$smarty->assign('warning_subj',	$warning_subj);
		$smarty->assign('info',		$info);

		$content = $tpl->load_template("index_main", true);
		$smarty->assign('content',	$content);
	}


	/**
	 * Информация о сервере
	 */
	private function serverinfo() {

		global $db, $tpl, $smarty;

		# Версия MYSQL
		$data1 = array();
		$q = $db->query("SHOW VARIABLES LIKE 'version'");
		$mysql = $db->fetch_row($q);
		$data1['mysql']		= $mysql[1];

		$data1['php'] 		= PHP_VERSION;				# Версия php
		$data1['zend']		= zend_version();			# Версия Zend
		$data1['apache'] 	= $_SERVER['SERVER_SOFTWARE'];		# Версия сервера  apache_get_version();
		$data1['os']		= php_uname("s")." (".PHP_OS.")"; 	# ОС
		$data1['uname']		= php_uname(); 				# UNAME
		$data1['roocms']	= ROOCMS_VERSION;			# RooCMS

		$data1['pid']		= PEAR_INSTALL_DIR; 			# Директория установки PEAR расширений
		$data1['dip']		= DEFAULT_INCLUDE_PATH;
		$data1['ped']		= PHP_EXTENSION_DIR;			# Директория php расширений
		$data1['pcp']		= PHP_CONFIG_FILE_PATH;

		$data1['sn']		= $_SERVER["SERVER_NAME"]; 		# Имя сервера
		$data1['sa']		= $_SERVER["SERVER_ADDR"]; 		# Адрес сервера
		$data1['sp']		= $_SERVER["SERVER_PROTOCOL"]; 		# Протокол сервера
		$data1['ra']		= $_SERVER["REMOTE_ADDR"]; 		# Адрес клиента
		$data1['docroot']	= _SITEROOT; 				# Путь к документам на сервере

		$data1['ml']		= ini_get('memory_limit');		# Memory limit
		$data1['mfs']		= ini_get('upload_max_filesize');	# Maximum file size
		$data1['mps']		= ini_get('post_max_size');		# Maximum post size
		$data1['met']		= ini_get('max_execution_time');	# Max execution time

		$data1['apache_mods']	= apache_get_modules();			# Расширения Apache


		$server_vars = array(
			'PHP_SELF',
			'argv',
			'argc',
			'GATEWAY_INTERFACE',
			'SERVER_ADDR',
			'SERVER_NAME',
			'SERVER_SOFTWARE',
			'SERVER_PROTOCOL',
			'REQUEST_METHOD',
			'REQUEST_TIME',
			'REQUEST_TIME_FLOAT',
			'QUERY_STRING',
			'DOCUMENT_ROOT',
			'HTTP_ACCEPT',
			'HTTP_ACCEPT_CHARSET',
			'HTTP_ACCEPT_ENCODING',
			'HTTP_ACCEPT_LANGUAGE',
			'HTTP_CONNECTION',
			'HTTP_HOST',
			'HTTP_REFERER',
			'HTTP_USER_AGENT',
			'HTTPS',
			'REMOTE_ADDR',
			'REMOTE_HOST',
			'REMOTE_PORT',
			'REMOTE_USER',
			'REDIRECT_REMOTE_USER',
			'SCRIPT_FILENAME',
			'SERVER_ADMIN',
			'SERVER_PORT',
			'SERVER_SIGNATURE',
			'PATH_TRANSLATED',
			'SCRIPT_NAME',
			'REQUEST_URI',
			'PHP_AUTH_DIGEST',
			'PHP_AUTH_USER',
			'PHP_AUTH_PW',
			'AUTH_TYPE',
			'PATH_INFO',
			'ORIG_PATH_INFO'
		) ;


		$data2 = array();
		foreach ($server_vars as $arg) {
			if (isset($_SERVER[$arg]))
				$data2[] = array("var"=>$arg, "value"=>$_SERVER[$arg]);
			else 	$data2[] = array("var"=>$arg, "value"=>"not found");
		}

		# draw
		$smarty->assign('data1',	$data1);
		$smarty->assign('data2',	$data2);

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