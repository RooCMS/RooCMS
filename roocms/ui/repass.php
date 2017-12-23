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
 * @subpackage	 User Registration
 * @author       alex Roosso
 * @copyright    2010-2018 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.0.2
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('UI')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class UI_RePass
 */
class UI_RePass {



	public function __construct() {

		global $structure, $roocms, $users, $POST;

		# title
		$structure->page_title = "Восстановление пароля";

		# breadcumb
		$structure->breadcumb[] = array('part'=>'repass', 'title'=>'Восстановление пароля');

		# if users registred
		if($users->uid != 0) {
			go(SCRIPT_NAME."?part=ucp&act=ucp");
		}

		# action
		switch($roocms->act) {
			case 'reminder':
				if(isset($POST->reminder)) {
					$this->reminder();
				}
				break;

			case 'confirm':
				$this->confirm();
				break;

			case 'verification':
				$this->verification();
				break;

			default:
				$this->form();
				break;
		}
	}


	/**
	 * Функция с формой восстановления пароля
	 */
	private function form() {

		global $tpl;


		$tpl->load_template("repass_form");
	}


	/**
	 * Функция подтверждения запроса на смену пароля
	 */
	private function confirm() {

		global $GET, $parse, $smarty, $tpl;

		$email = (isset($GET->_email) && $parse->valid_email($GET->_email)) ? $GET->_email : "" ;
		$code  = (isset($GET->_code)) ? $GET->_code : "" ;

		# tpl
		$smarty->assign("email", $email);
		$smarty->assign("code",  $code);
		$tpl->load_template("repass_confirm");
	}


	/**
	 * Функция восстановления пароля
	 */
	private function reminder() {

		global $db, $roocms, $site, $POST, $users, $parse, $logger, $smarty, $tpl;

		# log
		$logger->log("Запрос на восстановление пароля для почтового ящика: ".$POST->email." с IP:".$roocms->userip);

		# check
		if(isset($POST->email) && $parse->valid_email($POST->email) && $db->check_id($POST->email, USERS_TABLE, "email")) {

			$confirm = array();
			$confirm['code'] = randcode(10);

			# set secret key
			$db->query("UPDATE ".USERS_TABLE." SET secret_key='".$confirm['code']."' WHERE email='".$POST->email."'");

			# userdata
			$q = $db->query("SELECT nickname FROM ".USERS_TABLE." WHERE email='".$POST->email."'");
			$userdata = $db->fetch_assoc($q);

			# confirm link
			$confirm['link'] = $site['domain'].SCRIPT_NAME."?part=repass&act=confirm&email=".$POST->email."&code=".$confirm['code'];


			# Уведомление пользователю на электропочту
			$smarty->assign("nickname", $userdata['nickname']);
			$smarty->assign("confirm", $confirm);
			$smarty->assign("site", $site);
			$message = $tpl->load_template("email_confirm_repass", true);

			sendmail($POST->email, "Запрос на восстановление пароля для сайта: ".$site['title'], $message);


			# уведомление
			$logger->info("Инструкции для восстановления пароля, отправлены Вам на электронную почту", false);

			# переход
			go(SCRIPT_NAME."?part=repass&act=confirm&email=".$POST->email);
		}
		else {
			# bad result
			$logger->error("Невозможно выполнить запрос на восстановление пароля. Мы не нашли данных о Вашей учетной записи.", false);
			goback();
		}
	}



	/**
	 * Функция подтверждения запроса на смену пароля.
	 */
	private function verification() {

		global $db, $parse, $logger, $POST, $site, $security, $smarty, $tpl;

		if(isset($POST->email, $POST->code) && $parse->valid_email($POST->email) && $db->check_id($POST->email, USERS_TABLE, "email", "secret_key='".$POST->code."'")) {

			# new password
			$salt = $security->create_new_salt();
			$pass = randcode(10);
			$password = $security->hashing_password($pass, $salt);

			# userdata
			$q = $db->query("SELECT login, nickname FROM ".USERS_TABLE." WHERE email='".$POST->email."'");
			$userdata = $db->fetch_assoc($q);

			# update
			$db->query("UPDATE ".USERS_TABLE." SET salt='".$salt."', password='".$password."', secret_key='', last_visit='".time()."' WHERE email='".$POST->email."'");


			# Уведомление пользователю на электропочту
			$smarty->assign("userdata", $userdata);
			$smarty->assign("pass", $pass);
			$smarty->assign("site", $site);
			$message = $tpl->load_template("email_send_repass", true);

			sendmail($POST->email, "Ваш новый пароль для сайта: ".$site['title'], $message);


			# log
			$logger->info("Новый пароль создан. Проверьте Ваш почтовый ящик.");
			go("/");
		}
		else {
			# bad result
			$logger->error("Не удалось сгенерировать новый пароль. Предоставленные данные неверны.");
			goback();
		}

	}
}

/**
 * Init Class
 */
$uirepass = new UI_RePass;