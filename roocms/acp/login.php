<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyright    2010-2016 (c) RooCMS
* @link         http://www.roocms.com
* @version      2.0.5
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
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
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
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

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || (!defined('ACP') && !defined('INSTALL'))) die('Access Denied');
//#########################################################


class ACP_LOGIN {

	/**
	 * Проверяем введенные данные
	 */
	public function ACP_LOGIN() {

		global $db, $POST, $security, $smarty, $tpl, $site;


		$smarty->assign("site", $site);


		# check
		if(isset($POST->login) && $db->check_id($POST->login, USERS_TABLE, "login", "status='1' AND title='a'") && isset($POST->password)) {

			$q = $db->query("SELECT uid, login, nickname, title, password, salt FROM ".USERS_TABLE." WHERE login='".$POST->login."' AND status='1' AND title='a'");
			$data = $db->fetch_assoc($q);

			$dbpass = $security->hashing_password($POST->password, $data['salt']);

			if($dbpass == $data['password']) {

				# @include session security_check hash

				$_SESSION['uid'] 	= $data['uid'];
				$_SESSION['login'] 	= $data['login'];
				$_SESSION['title'] 	= $data['title'];
				$_SESSION['nickname'] 	= $data['nickname'];
				$_SESSION['token'] 	= $security->hashing_token($data['login'], $dbpass, $data['salt']);

				$smarty->assign("error_login", "");

				goback();
			}
			else {
				# неверный логин или пароль
				$this->incorrect_entering("Неверный логин или пароль.");
			}
		}


		# load template
		$tpl->load_template("login");
	}


	/**
	 * @param $msg - сообщение об ошибке передаваемое в шаблон
	 */
	private function incorrect_entering($msg) {

		global $smarty;

		session_destroy();

		sleep(3);
		$smarty->assign("error_login", $msg);
	}
}

/**
 * Init Class
 */
$acplogin = new ACP_LOGIN;

?>