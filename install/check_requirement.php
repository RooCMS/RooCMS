<?php
/**
 *   RooCMS - Russian Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
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
 *   RooCMS - Бесплатная система управления контентом с открытым исходным кодом
 *   Copyright © 2010-2018 александр Белов (alex Roosso). Все права защищены.
 *   Для связи: <info@roocms.com>
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

/**
 * @package      RooCMS
 * @subpackage	 Checked requirement for stable work RooCMS
 * @author       alex Roosso
 * @copyright    2010-2019 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.1.5
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('INSTALL')) {
	die('Access Denied');
}
//#########################################################


class Requirement {

	# vars
	protected $allowed 	= true;		# [bool] 	flag for checked requirement for stable work RooCMS
	protected $log		= array();	# [array]



	/**
	* Проверяем настройки хостинга
	*/
	protected function check_requirement() {
		$this->check_php_version();
		$this->check_php_sapi();
		$this->check_php_extensions();
		$this->check_php_ini();
	}



	/**
	* Проверяем версию PHP
	*/
	private function check_php_version() {
		$php 	= PHP_VERSION;

		if(version_compare($php, '5.2', "<")) {
			$this->log[] = array("Версия PHP", $php, false, "Версия PHP не подходит для использования RooCMS. Оптимальная версия 5.2 или 5.3");
			$this->allowed = false;
		}
		else {
			$this->log[] = array("Версия PHP", $php, true, "");
		}

		if(ini_get('safe_mode') == '1' || mb_strtolower(ini_get('safe_mode')) == 'on') {
			$this->log[] = array("Безопасный режим PHP", "Вкл", false, "Ваш PHP включен в безопасном режиме. Это может повлечь за собой некорректную работу некоторых алгоритмов.");
		}
		else {
			$this->log[] = array("Безопасный режим PHP", "Выкл", true, "");
		}
	}


	/**
	* Проверяем что бы PHP не был установлен как CGI
	*/
	private function check_php_sapi() {

		$sapi_type = php_sapi_name();

		if (substr($sapi_type, 0, 3) == 'cgi') {
			$this->log[] = array("PHP инсталирован на сервере", "как CGI", false, "RooCMS не будет выполнятся корректно. Для коректной работы требуется, что бы PHP был установлен как модуль Apache");
		}
		else {
			$this->log[] = array("PHP инсталирован на сервере", "как модуль", true, "");
		}
	}


	/**
	* Проверяем наличие расширений PHP
	*/
	private function check_php_extensions() {
		$rextensions	= array("Core", "standard", "mysqli", "session", "mbstring", "calendar", "date", "pcre", "xml", "SimpleXML", "gd");
		$extensions = get_loaded_extensions();

		foreach($rextensions AS $v) {
			if(!in_array($v,$extensions)) {
				$this->log[] = array("Расширение: ".$v, "Отсутствует", false, "Без данного расширения работа RooCMS будет нестабильной!");
				$this->allowed = false;
			}
			else {
				$this->log[] = array("Расширение: ".$v, "Установлено", true, "");
			}
		}
	}


	/**
	* Проверяем настройки PHP
	*/
	private function check_php_ini() {
		if(ini_get('register_globals') == '1' || mb_strtolower(ini_get('register_globals')) == 'on') {
			$this->log[] = array("Режим REGISTR_GLOBALS", "Вкл", false, "У вас включен режим REGISTR_GLOBALS. Это может угрожать безопастности RooCMS.");
			$this->allowed = false;
		}
		else {
			$this->log[] = array("Режим REGISTR_GLOBALS", "Выкл", true, "");
		}

		if(ini_get('magic_quotes_gpc') == '1' || mb_strtolower(ini_get('magic_quotes_gpc')) == 'on') {
			$this->log[] = array("Режим MAGIC_QUOTES_GPC", "Вкл", false, "У вас включен режим экранирования спецсимволов. Данный режим вызывает конфликты при работе с RooCMS.");
		}
		else {
			$this->log[] = array("Режим MAGIC_QUOTES_GPC", "Выкл", true, "");
		}

		if(!preg_match('//u', '')) {
			$this->log[] = array("Поддержка PCRE UTF-8", "Выкл", false, "Регулярные выражения не поддерживают UTF-8");
			$this->allowed = false;
		}
		else {
			$this->log[] = array("Поддержка PCRE UTF-8", "Вкл", true, "");
		}
	}


	/**
	* Устанавливаем права на важные директории и файлы
	*/
	protected function check_chmod() {

		global $files;

		$protect = array();
		require_once _LIB."/files_protected.php";

		foreach($protect AS $v) {
			$perms = $files->show_fileperms($v['path']);

			$roocmspath = str_replace(_SITEROOT, "", $v['path']);
			if($perms != $v['chmod']) {
				@chmod($v['path'], $v['chmod']);
				if(@chmod($v['path'], $v['chmod'])) {
					$this->log[] = array("Директория/Файл ".$roocmspath, $v['chmod'], true, "");
				}
				else {
					$this->log[] = array("Директория/Файл ".$roocmspath, $perms, false, "Неверные права доступа к директории/файлу. Рекомендуемые права ".$v['chmod'].". Для повышения безопастности установите права вручную через ваш FTP доступ");
				}
			}
			else {
				$this->log[] = array("Директория/Файл ".$roocmspath, $perms, true, "");
			}
		}

		clearstatcache();
	}

}