<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('INSTALL')) {
	die('Access Denied');
}
//#########################################################


class Requirement {

	# var
	protected $allowed;
	protected $log;


	/**
	 * Check hosting settings
	 */
	protected function check_requirement() {
		$this->check_php_version();
		//$this->check_php_sapi();
		$this->check_php_extensions();
		$this->check_php_ini();
	}


	/**
	 * Checl PHP Version
	 */
	private function check_php_version() {
		$php 	= PHP_VERSION;

		if(version_compare($php, '5.6', "<")) {
			$this->log[] = array("Версия PHP", $php, false, "Версия PHP не подходит для использования RooCMS. Оптимальная версия 5.6 и выше. Мы рекомендуем PHP 7.2");
			$this->allowed = false;
		}
		else {
			$this->log[] = array("Версия PHP", $php, true, "");
		}

		if(ini_get('safe_mode') == '1' || mb_strtolower(ini_get('safe_mode')) == 'on') {
			$this->log[] = array("Безопасный режим PHP", "Вкл", false, "Ваш PHP включен в безопасном режиме.");
		}
		else {
			$this->log[] = array("Безопасный режим PHP", "Выкл", true, "");
		}
	}


	/**
	 * Check PHP Mode
	 */
	/*private function check_php_sapi() {

		$sapi_type = php_sapi_name();

		if (substr($sapi_type, 0, 3) == 'cgi') {
			$this->log[] = array("PHP инсталирован на сервере", "как CGI", false, "RooCMS не будет выполнятся корректно. Для коректной работы требуется, что бы PHP был установлен как модуль Apache");
		}
		else {
			$this->log[] = array("PHP инсталирован на сервере", "как модуль", true, "");
		}
	}*/


	/**
	 * Check PHP Ext
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
	 * Check setting...
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
}
