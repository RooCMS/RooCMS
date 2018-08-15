<?php
/**
 * RooCMS - Russian free content managment system
 * Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved
 * Contacts: <info@roocms.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/
 *
 *
 * RooCMS - Русская бесплатная система управления сайтом
 * Copyright © 2010-2018 александр Белов  (alex Roosso). Все права защищены
 * Для связи: info@roocms.com
 *
 * Это программа является свободным программным обеспечением. Вы можете
 * распространять и/или модифицировать её согласно условиям Стандартной
 * Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 * Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
 *
 * Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 * ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 * ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 * Общественную Лицензию GNU для получения дополнительной информации.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 * с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */

/**
 * @package      RooCMS
 * @subpackage	 Extended function for install/update script RooCMS
 * @author       alex Roosso
 * @copyright    2010-2019 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.0
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


class ExtIUFunction extends Requirement {


	/**
	 * This function check steps in algorythm and set next step
	 */
	protected function set_nextstep() {
		if($this->allowed && $this->step != $this->steps) {
			$this->nextstep = $this->step + 1;
		}
	}


	/**
	 * Check data $post->step
	 *
	 * @param int $n - step
	 *
	 * @return bool
	 */
	protected function check_step($n) {

		global $post;

		return isset($post->step) && $post->step == $n;

	}


	/**
	 * Check used $post->submit
	 *
	 * @return bool
	 */
	protected function check_submit() {

		global $post;

		return $this->allowed && isset($post->submit);

	}
}