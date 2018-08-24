<?php
/**
 * RooCMS - Open Source Free Content Managment System
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
 * RooCMS - Бесплатная система управления сайтом
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
 * @version      1.1
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


class IU_Extends extends Requirement {

	# var
	protected $step;
	protected $nextstep;

	protected $page_title;
	protected $status;
	protected $noticetext;


	/**
	 * Step 1 : Licence
	 */
	protected function step_1() {

		$this->page_title = "Лицензионное соглашение";
		$this->status = "Внимательно прочитайте лицензионное соглашение<br />Помните, что нарушение авторских прав влечет за собой уголовную ответсвенность.";

		require_once _LIB."/license.php";

		$this->noticetext = $license['ru'];

		if($this->check_submit()) {
			if($this->check_step(1)) {
				go(SCRIPT_NAME."?step=2");
			}
			else {
				goback();
			}
		}
	}


	/**
	 * Step 2 : Check requirement
	 */
	protected function step_2() {

		$this->page_title = "Проверка требований RooCMS к хостингу";
		$this->status = "Проверяем версию PHP, MySQL, Apache<br />Проверяем наличие требуемых PHP и Apache расширений";

		$this->check_requirement();

		if($this->check_submit()) {
			if($this->check_step(2)) {
				go(SCRIPT_NAME."?step=3");
			}
			else {
				goback();
			}
		}
	}


	/**
	 * Step 3 : Check chmod
	 */
	protected function step_3() {

		$this->page_title = "Проверка и установка доступов к файлам RooCMS";
		$this->status = "Проверяем доступы и разрешения к важным файлам RooCMS<br />Установка доступов и разрешений для важных файлов RooCMS";

		$this->check_chmod();

		if($this->check_submit()) {
			if($this->check_step(3)) {
				go(SCRIPT_NAME."?step=4");
			}
			else {
				goback();
			}
		}
	}


	/**
	 * Init var $this->>step
	 */
	protected function init_step() {

		global $get;

		if(isset($get->_step) && round($get->_step) > 0) {
			$this->step =& $get->_step;
		}
	}


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