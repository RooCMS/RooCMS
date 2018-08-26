<?php
/**
 *   RooCMS - Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
 *
 *   You should have received a copy of the GNU General Public License v3
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
* @package      RooCMS
* @subpackage	User Control Panel
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.2
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

	private $userlist = [];



	/**
	 * Init
	 */
	public function __construct() {

		global $structure, $roocms, $users;

		# title
		$structure->page_title = "Личные сообщения";

		# get userlist
		$this->userlist = $users->get_userlist(1,0);

		# move
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

		# breadcumb
		$structure->breadcumb[] = array('part'=>'ucp', 'act'=>'pm', 'title'=>'Личные сообщения');
	}


	/**
	 * Функция списка личных сообщений
	 */
	private function show() {

		global $db, $users, $parse,  $tpl, $smarty;

		# список сообщений
		$pm = [];
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

		global $db, $structure, $get, $users, $parse, $tpl, $smarty;

		if(isset($get->_id) && $db->check_id($get->_id, USERS_PM_TABLE, "id", "to_uid='".$users->uid."'")) {

			# get pm
			$q = $db->query("SELECT title, date_create, message, from_uid FROM ".USERS_PM_TABLE." WHERE id='".$get->_id."'");
			$message = $db->fetch_assoc($q);
			$message['showmessage'] = $parse->text->br($message['message']);
			$message['date_send'] = $parse->date->unix_to_rus($message['date_create'], true);
			$message['from_name'] = $this->userlist[$message['from_uid']]['nickname'];

			# breadcumb
			$structure->breadcumb[] = array('act' => 'pm', 'part'=>'ucp', 'title'=>$message['title']);

			# now you see?
			$db->query("UPDATE ".USERS_PM_TABLE." SET see='1', date_read='".time()."' WHERE id='".$get->_id."'");

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

		global $db, $users, $post, $logger, $site, $smarty, $tpl;

		if(isset($post->send, $post->message) && $post->to_uid != 0 && $db->check_id($post->to_uid, USERS_TABLE, "uid", "status='1'") && $post->to_uid != $users->uid) {

			#title
			if(!isset($post->title)) {
				$post->title = "Без заголовка";
			}

			# send
			$db->query("INSERT INTO ".USERS_PM_TABLE." (title, to_uid, from_uid, message, date_create)
							VALUES ('".$post->title."', '".$post->to_uid."', '".$users->uid."', '".$post->message."', '".time()."')");

			# email notice
			$q = $db->query("SELECT nickname, email FROM ".USERS_TABLE." WHERE uid='".$post->to_uid."'");
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
			if(!isset($post->message)) {
				$logger->error("Вы попытались отправить пустое сообщение. К сожалению это невозможно.", false);
			}
			if($post->to_uid == $users->uid) {
				$logger->error("Переписываетесь сами с собой? Попробуйте с кем нибудь ещё.", false);
			}
			if(!$db->check_id($post->to_uid, USERS_TABLE, "uid", "status='1'")) {
				$logger->error("К сожалению пользователь, которому вы пытаетесь отправить сообщение больше не принимает корреспонденцию.", false);
			}
			goback();
		}
	}
}

/**
 * Init Class
 */
$ucppm = new UCP_PM;