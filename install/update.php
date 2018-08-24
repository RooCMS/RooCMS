<?php
/**
 *   RooCMS - Open Source Free Content Managment System
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
* @subpackage	Updater
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2.6
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


class Update extends IU_Extends {

	# vars
	protected $allowed	= true;		# [bool]	flag for allowed to continue process
	protected $log		= [];		# [array]	array log process actions

	private $action		= "update";	# [string]	alias for identy process
	protected $step		= 1;		# [int]		now use step
	protected $nextstep	= 2;		# [int]		next use step
	protected $steps	= 4;		# [int]		all step in operations
	private $page_title	= "";
	private $status		= "";
	private $noticetext	= "";		# [string]	attention text in head form



	/**
	 * Вперед...
	 */
	public function Update() {

		global $get, $post, $site, $parse, $tpl, $smarty;

		# init step
		$this->init_step();

		# seo
		$site['title'] = "Обновление RooCMS";

		# переход
		switch($this->step) {

			case 2:
				$this->page_title = "Проверка требований RooCMS к хостингу";
				$this->status = "Проверяем версию PHP, MySQL, Apache<br />Проверяем наличие требуемых PHP и Apache расширений";
				$this->check_requirement();
				if($this->allowed && isset($post->submit)) {
					if(isset($post->step) && $post->step == 2) {
						go(SCRIPT_NAME."?step=3");
					}
					else {
						goback();
					}
				}
				break;

			case 3:
				$this->page_title = "Проверка и установка доступов к файлам RooCMS";
				$this->status = "Проверяем доступы и разрешения к важным файлам RooCMS<br />Установка доступов и разрешений для важных файлов RooCMS";
				$this->check_chmod();
				if($this->allowed && isset($post->submit)) {
					if(isset($post->step) && $post->step == 3) {
						go(SCRIPT_NAME."?step=4");
					}
					else {
						goback();
					}
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
				if($this->allowed && isset($post->submit)) {
					if(isset($post->step) && $post->step == 1) {
						go(SCRIPT_NAME."?step=2");
					}
					else {
						goback();
					}
				}
				break;
		}

		# nextstep
		$this->set_nextstep();

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
			if(!empty($f)) {
				$this->log[] = array('', 'Последняя версия: '.$f[0].'', true, '');
			}
		}
	}
}