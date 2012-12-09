<?php
/**
* @package      RooCMS
* @subpackage	Checked requirement for stable work RooCMS
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.01 build 1
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
if(!defined('RooCMS') || !defined('INSTALL')) die('Access Denied');
//#########################################################


class Requirement {

	# var
	protected 	$allowed 	= true;		# [bool] 	flag for checked requirement for stable work RooCMS
	protected	$log		= array();	# [array]



	/**
	* Проверяем настройки хостинга
	*
	*/
	protected function check_requirement() {
		$this->check_php_version();
		$this->check_php_sapi();
		$this->check_php_extensions();
		$this->check_php_ini();
	}



	/**
	* Проверяем версию PHP
	*
	*/
	private function check_php_version() {
		$php 	= PHP_VERSION;

		if(version_compare($php, '5.2', "<")) {
			$this->log[] = array("Версия PHP", $php, false, "Версия PHP не подходит для использования RooCMS. Оптимальная версия 5.2 или 5.3");
			$this->allowed = false;
		}
		else $this->log[] = array("Версия PHP", $php, true, "");

		if(@ini_get('safe_mode') == '1' || mb_strtolower(@ini_get('safe_mode')) == 'on') {
			$this->log[] = array("Безопасный режим PHP", "Вкл", false, "Ваш PHP включен в безопасном режиме. Это может повлечь за собой некоректную работу некоторых алгоритмов.");
		}
		else $this->log[] = array("Безопасный режим PHP", "Выкл", true, "");
	}


	/**
	* Проверяем что бы PHP не был установлен как CGI
	*
	*/
	private function check_php_sapi() {

		$sapi_type = php_sapi_name();

		if (substr($sapi_type, 0, 3) == 'cgi') {
			$this->log[] = array("PHP инсталирован на сервере", "как CGI", false, "RooCMS не будет выполнятся корректно. Для коректной работы требуется, что бы PHP был установлен как модуль Apache");
		} else {
			$this->log[] = array("PHP инсталирован на сервере", "как модуль", true, "");
		}
	}


	/**
	* Проверяем наличие расширений PHP
	*
	*/
	private function check_php_extensions() {
		$rextensions	= array("Core",
								"calendar",
								"date",
								"pcre",
								"session",
								"xml",
								"gd",
								"mbstring",
								"standard",
								"SimpleXML",
								"apache2handler",
								"mysql");
		$extensions = get_loaded_extensions();

		foreach($rextensions AS $k=>$v) {
			if(!in_array($v,$extensions)) {
				$this->log[] = array("Расширение: ".$v, "Отсутствует", false, "Без данного расширения работа RooCMS будет нестабильной!");
				$this->allowed = false;
			}
			else $this->log[] = array("Расширение: ".$v, "Установлено", true, "");
		}
	}


	/**
	* Проверяем настройки PHP
	*
	*/
	private function check_php_ini() {
		if(@ini_get('register_globals') == '1' || mb_strtolower(@ini_get('register_globals')) == 'on') {
			$this->log[] = array("Режим REGISTR_GLOBALS", "Вкл", false, "У вас включен режим REGISTR_GLOBALS. Это может угрожать безопастности RooCMS.");
			$this->allowed = false;
		}
		else $this->log[] = array("Режим REGISTR_GLOBALS", "Выкл", true, "");

		if(@ini_get('magic_quotes_gpc') == '1' || mb_strtolower(@ini_get('magic_quotes_gpc')) == 'on') {
			$this->log[] = array("Режим MAGIC_QUOTES_GPC", "Вкл", false, "У вас включен режим экранирования спецсимволов. Данный режим вызывает конфликты при работе с RooCMS.");
			//$this->requirement = false;
		}
		else $this->log[] = array("Режим MAGIC_QUOTES_GPC", "Выкл", true, "");

		if(@!preg_match('//u', '')) {
			$this->log[] = array("Поддержка PCRE UTF-8", "Выкл", false, "Регулярные выражения не поддерживают UTF-8");
			$this->allowed = false;
		}
		else $this->log[] = array("Поддержка PCRE UTF-8", "Вкл", true, "");
	}


	/**
	* Устанавливаем права на важные директории и файлы
	*
	*/
	protected function check_chmod() {

		global $files;

		require_once _LIB."/files_protected.php";

		foreach($protectfolder AS $k=>$v) {
			$perms = $files->show_fileperms($v['path']);

			$roocmspath = str_replace(_ROOT, "", $v['path']);
			if($perms != $v['chmod']) {
				if(chmod($v['path'], $v['chmod'])) $this->log[] = array("Директория ".$roocmspath, $v['chmod'], true, "");
				else $this->log[] = array("Директория ".$roocmspath, $perms, false, "Неверные права доступа к директории. Рекомендуемые права ".$v['chmod'].". Для повышения безопастности установите права вручную через ваш FTP доступ");
			}
			else $this->log[] = array("Директория ".$roocmspath, $perms, true, "");
		}

		foreach($protectfiles AS $k=>$v) {
			$perms = $files->show_fileperms($v['path']);

			$roocmspath = str_replace(_ROOT, "", $v['path']);
			if($perms != $v['chmod']) {
				if(chmod($v['path'], $v['chmod'])) $this->log[] = array("Файл ".$roocmspath, $v['chmod'], true, "");
				else $this->log[] = array("Файл ".$roocmspath, $perms, false, "Неверные права доступа к директории. Рекомендуемые права ".$v['chmod'].". Для повышения безопастности установите права вручную через ваш FTP доступ");
			}
			else $this->log[] = array("Файл ".$roocmspath, $perms, true, "");
		}

		clearstatcache();
	}

}

?>