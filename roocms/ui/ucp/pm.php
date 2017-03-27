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
* @subpackage	User Control Panel
* @author       alex Roosso
* @copyright    2010-2018 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.3
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('UI') || !defined('UCP')) {
	die('Access Denied');
}
//#########################################################


class UCP_PM {

	private $userlist = array();



	/**
	 * Init
	 */
	public function __construct() {

		global $structure, $roocms;

		# breadcumb
		$structure->breadcumb[] = array('part'=>'ucp', 'act'=>'pm', 'title'=>'Личные сообщения');

		# get userlist
		$this->get_userlist();


		switch($roocms->move) {
			case 'write':
				$this->write();
				break;

			case 'send':
				$this->send();
				break;

			case 'read':
				$this->read();
				break;

			default:
				$this->show();
				break;
		}
	}


	/**
	 * Функция списка личных сообщений
	 */
	private function show() {

		global $db, $users, $parse,  $tpl, $smarty;

		# список сообщений
		$pm = array();
		$q = $db->query("SELECT id, from_uid, title, see, date_create FROM ".USERS_PM_TABLE." WHERE to_uid='".$users->uid."' ORDER BY date_create DESC");
		while($row = $db->fetch_assoc($q)) {
			$row['date_send'] = $parse->date->unix_to_rus($row['date_create']);
			$row['from_name'] = $this->userlist[$row['from_uid']]['nickname'];
			$pm[] = $row;
		}

		# tpl
		$smarty->assign("pm", $pm);
		$smarty->assign("userlist", $this->userlist);
		$smarty->assign("userdata", $users->userdata);
		$tpl->load_template("ucp_pm");
	}


	/**
	 * Функция просмотра сообщения
	 */
	private function read() {

		global $db, $GET, $users, $parse, $tpl, $smarty;

		if(isset($GET->_id) && $db->check_id($GET->_id, USERS_PM_TABLE, "id", "to_uid='".$users->uid."'")) {

			# breadcumb
			//$structure->breadcumb[] = array('act' => 'pm', 'part'=>'read', 'title'=>'Читаем сообщение');

			# get pm
			$q = $db->query("SELECT title, date_create, message, from_uid FROM ".USERS_PM_TABLE." WHERE id='".$GET->_id."'");
			$message = $db->fetch_assoc($q);
			$message['showmessage'] = $parse->text->br($message['message']);
			$message['date_send'] = $parse->date->unix_to_rus($message['date_create'], true);
			$message['from_name'] = $this->userlist[$message['from_uid']]['nickname'];


			# now you see?
			$db->query("UPDATE ".USERS_PM_TABLE." SET see='1', date_read='".time()."' WHERE id='".$GET->_id."'");

			# tpl
			$smarty->assign("message", $message);
			$smarty->assign("userlist", $this->userlist);
			$smarty->assign("userdata", $users->userdata);
			$tpl->load_template("ucp_pm_read");
		}
		else go(SCRIPT_NAME."?part=ucp&act=pm");
	}


	/**
	 * Функция создания нового сообщения
	 */
	private function write() {

		global $structure, $users, $tpl, $smarty;

		# breadcumb
		$structure->breadcumb[] = array('part'=>'write', 'act' => 'pm', 'title'=>'Новое сообщение');

		# tpl
		$smarty->assign("userlist", $this->userlist);
		$smarty->assign("userdata", $users->userdata);
		$tpl->load_template("ucp_pm_write");
	}


	/**
	 * Функция отправки сообщения
	 */
	private function send() {

		global $db, $users, $POST, $logger, $site, $smarty, $tpl;

		if(isset($POST->send, $POST->message) && $POST->to_uid != 0 && $db->check_id($POST->to_uid, USERS_TABLE, "uid", "status='1'") && $POST->to_uid != $users->uid) {

			#title
			if(!isset($POST->title)) {
				$POST->title = "Без заголовка";
			}

			# send
			$db->query("INSERT INTO ".USERS_PM_TABLE." (title, to_uid, from_uid, message, date_create)
							VALUES ('".$POST->title."', '".$POST->to_uid."', '".$users->uid."', '".$POST->message."', '".time()."')");

			# email notice
			$q = $db->query("SELECT nickname, email FROM ".USERS_TABLE." WHERE uid='".$POST->to_uid."'");
			$u = $db->fetch_assoc($q);

			$smarty->assign("nickname", $u['nickname']);
			$smarty->assign("from", $users->nickname);
			$smarty->assign("site", $site);
			$message = $tpl->load_template("email_new_message", true);

			sendmail($u['email'], "Вы получили новое сообщение", $message);


			# notice
			$logger->info("Ваше сообщение отправлено", false);

			# go
			go(SCRIPT_NAME."?part=ucp&act=pm");
		}
		else {
			if(!isset($POST->message)) {
				$logger->error("Вы попытались отправить пустое сообщение. К сожалению это невозможно.", false);
			}
			if($POST->to_uid == $users->uid) {
				$logger->error("Переписываетесь сами с собой? Попробуйте с кем нибудь ещё.", false);
			}
			if(!$db->check_id($POST->to_uid, USERS_TABLE, "uid", "status='1'")) {
				$logger->error("К сожалению пользователь, которому вы пытаетесь отправить сообщение больше не принимает корреспонденцию.", false);
			}
			goback();
		}
	}


	/**
	 * Получаем список пользователей
	 *
	 * Данная функция будет в дальнейшем изменена!
	 * В целях снижения запросов к БД. Будет использоваться Кеш
	 */
	private function get_userlist() {

		global $db;

		# список пользователей
		$userlist = array();
		$q = $db->query("SELECT uid, nickname FROM ".USERS_TABLE." WHERE status='1' ORDER BY nickname ASC");
		while($row = $db->fetch_assoc($q)) {
			$userlist[$row['uid']] = $row;
		}

		$this->userlist = $userlist;
	}
}

/**
 * Init Class
 */
$ucppm = new UCP_PM;

?>