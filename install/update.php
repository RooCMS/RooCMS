<?php
/**
* @package      RooCMS
* @subpackage	Updater
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2.1
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

$update = new Update;

class Update extends Requirement{

	# vars
	protected $allowed	= true;			# [bool]	flag for allowed to continue process
	protected $log		= array();		# [array]	array log process actions

	private $action		= "update";		# [string]	alias for identy process
	private $step		= 1;			# [int]		now use step
	private $nextstep	= 2;			# [int]		next use step
	private $steps		= 3;			# [int]		all step in operations
	private $page_title	= "";
	private $status		= "";
	private $noticetext	= "";			# [string]	attention text in head form



	/* ####################################################
	 *	Let's begin
	 */
	function __construct() {

		global $GET, $POST, $site, $parse, $tpl, $smarty;

		# init step
		if(isset($GET->_step) && round($GET->_step) > 0) $this->step =& $GET->_step;

		# seo
		$site['title'] = "Обновление RooCMS";

		# go
		switch($this->step) {

			case 1:
				$this->page_title = "Лицензионное соглашение";
				$this->status = "Внимательно прочитайте лицензионное соглашение<br />Помните, что нарушение авторских прав влечет за собой уголовную ответсвенность.";
				require_once _LIB."/license.php";
				$this->noticetext = $license['ru'];
				if($this->allowed && @$_REQUEST['submit']) {
					if(isset($POST->step) && $POST->step == 1) go(SCRIPT_NAME."?step=2");
					else goback();
				}
				break;

			case 2:
				$this->page_title = "Проверка требований RooCMS к хостингу";
				$this->status = "Проверяем версию PHP, MySQL, Apache<br />Проверяем наличие требуемых PHP и Apache расширений";
				$this->check_requirement();
				if($this->allowed && @$_REQUEST['submit']) {
					if(isset($POST->step) && $POST->step == 2) go(SCRIPT_NAME."?step=3");
					else goback();
				}
				break;

			case 3:
				$this->page_title = "Проверка и установка доступов к файлам RooCMS";
				$this->status = "Проверяем доступы и разрешения к важным файлам RooCMS<br />Установка доступов и разрешений для важных файлов RooCMS";
				$this->check_chmod();
				if($this->allowed && @$_REQUEST['submit']) {
					if(isset($POST->step) && $POST->step == 3) go(SCRIPT_NAME."?step=4");
					else goback();
				}
				break;

			default:
				$this->page_title = "Лицензионное соглашение";
				$this->status = "Внимательно прочитайте лицензионное соглашение<br />Помните, что нарушение авторских прав влечет за собой уголовную ответсвенность.";
				require_once _LIB."/license.php";
				$this->noticetext = $license['ru'];
				if($this->allowed && @$_REQUEST['submit']) {
					if(isset($POST->step) && $POST->step == 1) go(SCRIPT_NAME."?step=2");
					else goback();
				}
				break;
		}

		if($this->allowed && $this->step != $this->steps) $this->nextstep = $this->step + 1;

		# draw
		$smarty->assign("allowed",		$this->allowed);
		$smarty->assign("action",		$this->action);
		$smarty->assign("page_title", 	$this->page_title);
		$smarty->assign("status", 		$this->status);
		$smarty->assign("step", 		$this->step);
		$smarty->assign("nextstep", 	$this->nextstep);
		$smarty->assign("steps",		$this->steps);
		$smarty->assign("progress",		$parse->percent($this->step, $this->steps));

		$tpl->load_template("top");

		$smarty->assign("log", 			$this->log);
		$smarty->assign("noticetext", 	$this->noticetext);

		$tpl->load_template("body");
	}


	/* ####################################################
	 *
	 */
	private function step_1() {

	}
}

?>