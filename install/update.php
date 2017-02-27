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
* @package      RooCMS
* @subpackage	Updater
* @author       alex Roosso
* @copyright    2010-2017 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2.3
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('INSTALL')) die('Access Denied');
//#########################################################


class Update extends Requirement{

	# vars
	protected $allowed	= true;			# [bool]	flag for allowed to continue process
	protected $log		= array();		# [array]	array log process actions

	private $action		= "update";		# [string]	alias for identy process
	private $step		= 1;			# [int]		now use step
	private $nextstep	= 2;			# [int]		next use step
	private $steps		= 4;			# [int]		all step in operations
	private $page_title	= "";
	private $status		= "";
	private $noticetext	= "";			# [string]	attention text in head form



	/**
	 * Вперед...
	 */
	public function Update() {

		global $GET, $POST, $site, $parse, $tpl, $smarty;

		# init step
		if(isset($GET->_step) && round($GET->_step) > 0) $this->step =& $GET->_step;

		# seo
		$site['title'] = "Обновление RooCMS";

		# переход
		switch($this->step) {

			case 1:
				$this->page_title = "Лицензионное соглашение";
				$this->status = "Внимательно прочитайте лицензионное соглашение<br />Помните, что нарушение авторских прав влечет за собой уголовную ответсвенность.";
				require_once _LIB."/license.php";
				$this->noticetext = $license['ru'];
				if($this->allowed && isset($POST->submit)) {
					if(isset($POST->step) && $POST->step == 1) go(SCRIPT_NAME."?step=2");
					else goback();
				}
				break;

			case 2:
				$this->page_title = "Проверка требований RooCMS к хостингу";
				$this->status = "Проверяем версию PHP, MySQL, Apache<br />Проверяем наличие требуемых PHP и Apache расширений";
				$this->check_requirement();
				if($this->allowed && isset($POST->submit)) {
					if(isset($POST->step) && $POST->step == 2) go(SCRIPT_NAME."?step=3");
					else goback();
				}
				break;

			case 3:
				$this->page_title = "Проверка и установка доступов к файлам RooCMS";
				$this->status = "Проверяем доступы и разрешения к важным файлам RooCMS<br />Установка доступов и разрешений для важных файлов RooCMS";
				$this->check_chmod();
				if($this->allowed && isset($POST->submit)) {
					if(isset($POST->step) && $POST->step == 3) go(SCRIPT_NAME."?step=4");
					else goback();
				}
				break;

			case 4:
				$this->page_title = "Обновление RooCMS";
				$this->status = "Обновление RooCMS<br />...";
				$this->step_4();
				break;

			default:
				$this->page_title = "Лицензионное соглашение";
				$this->status = "Внимательно прочитайте лицензионное соглашение<br />Помните, что нарушение авторских прав влечет за собой уголовную ответсвенность.";
				require_once _LIB."/license.php";
				$this->noticetext = $license['ru'];
				if($this->allowed && isset($POST->submit)) {
					if(isset($POST->step) && $POST->step == 1) go(SCRIPT_NAME."?step=2");
					else goback();
				}
				break;
		}

		if($this->allowed && $this->step != $this->steps) $this->nextstep = $this->step + 1;

		# draw
		$smarty->assign("allowed",	$this->allowed);
		$smarty->assign("action",	$this->action);
		$smarty->assign("page_title", 	$this->page_title);
		$smarty->assign("status", 	$this->status);
		$smarty->assign("step", 	$this->step);
		$smarty->assign("nextstep", 	$this->nextstep);
		$smarty->assign("steps",	$this->steps);
		$smarty->assign("progress",	$parse->percent($this->step, $this->steps));

		$tpl->load_template("top");

		$smarty->assign("log", 		$this->log);
		$smarty->assign("noticetext", 	$this->noticetext);

		$tpl->load_template("body");
	}


	/**
	 * Sorry
	 */
	private function step_4() {
		$this->log[] = array('', 'Извините.<br />Сегодня автоматические обновление невозможно.<br />В ближайшем будущем мы неприменно его реализуем, но сейчас, любое обновление придется делать вручную.', false, '');
		$this->log[] = array('', 'Ваша версия RooCMS: '.ROOCMS_VERSION.'', true, '');

		if(get_http_response_code("http://version.roocms.com/index.php") == "200") {
			$f = file("http://version.roocms.com/index.php");
			if(!empty($f)) $this->log[] = array('', 'Последняя версия: '.$f[0].'', true, '');
		}
	}
}

?>