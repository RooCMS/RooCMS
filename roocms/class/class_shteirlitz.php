<?php
/**
 *   RooCMS - Russian free content managment system
 *   Copyright © 2010-2017 alexandr Belov aka alex Roosso. All rights reserved.
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
 *   RooCMS - Русская бесплатная система управления контентом
 *   Copyright © 2010-2017 александр Белов (alex Roosso). Все права защищены.
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
 * @package	RooCMS
 * @subpackage	Engine RooCMS classes
 * @author	alex Roosso
 * @copyright	2010-2017 (c) RooCMS
 * @link	http://www.roocms.com
 * @version	1.0
 * @license	http://www.gnu.org/licenses/gpl-3.0.html
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################

class Shteirlitz {

	/**
	 * Шифруем
	 *
	 * @param        $str
	 * @param string $salt  - соль
	 * @param string $passw - пароль
	 *
	 * @return string
	 */
	public function encode($str, $passw="", $salt="") {
		return base64_encode($this->code($str, $passw, $salt));
	}


	/**
	 * Расшифровываем
	 *
	 * @param        $str
	 * @param string $salt  - соль
	 * @param string $passw - пароль
	 *
	 * @return data
	 */
	public function decode($str, $passw="", $salt="") {
		return $this->code(base64_decode($str), $passw, $salt);
	}


	/**
	 * Кодируем XOP
	 *
	 * @param        $str
	 * @param string $salt		- соль
	 * @param string $passw		- пароль
	 *
	 * @return data
	 */
	private function code($str, $passw="", $salt="") {

		$len = strlen($str);
		$n = $len > 100 ? 8 : 2;

		$gamma = '';
		while(strlen($gamma) < $len ) {
			$gamma .= substr(pack('H*', sha1($passw.$gamma.$salt)), 0, $n);
		}

		return $str^$gamma;
	}
}