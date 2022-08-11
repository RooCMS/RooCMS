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
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


class Post {

	/**
	 * needed fields
	 * @var array
	 */
	//public $needed = [];


	/**
	 * Validate captcha code in form
	 *
	 * @property string $captcha
	 *
	 * @return bool
	 */
	public function valid_captcha() {

		global $roocms, $config, $logger;

		# if admin not use captcha
		if(!$config->captcha_power) {
			return true;
		}

		# if captcha uncorrect
		if(!isset($roocms->sess['captcha'],$this->captcha) || strcmp($roocms->sess['captcha'], mb_strtoupper($this->captcha)) !== 0) {

			$logger->error("Ошибка ввода Captcha! Введенный код не совпадает с кодом на картинке.", false);
			//$logger->log("Ошибка ввода Captcha: code [".$roocms->sess['captcha']."] post [".$this->captcha."]", "error"); // This is timed code for debug captcha

			unset($_SESSION['captcha']);
			return false;
		}

		//$logger->log("Введена Captcha: code [".$roocms->sess['captcha']."] post [".$this->captcha."]", "error"); // This is timed code for debug captcha
		unset($_SESSION['captcha']);

		return true;
	}


	// TODO: Это закладка на обработку входящих запросов и проверку целостности данных.
	// Что будет полезно при проверке вводимых данных. Во избежание подлогов.
	/**
	 * Magic outside hogwarts is ALLOWED
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return null
	 */
	/*public function __set($name, $value) {

		global $logger;

		# debug log
		if(DEBUGMODE) {
			$trace = debug_backtrace();
			$pi = pathinfo($trace[0]['file']);

			$logger->log("Попытка получить неопределенное свойство : ".$name." = ".$value." ; Источник: ".$pi['filename']." строка ".$trace[0]['line']);
		}

		return null;
	}*/
}
