<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
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

		global $structure, $nav, $roocms, $users;

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

		# breadcrumb
		$nav->breadcrumb[] = array('part'=>'ucp', 'act'=>'pm', 'title'=>'Личные сообщения');
	}


	/**
	 * Функция списка личных сообщений
	 */
	private function show() {

		global $db, $users, $parse,  $tpl, $smarty;

		# pm list
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
		$tpl->load_template("ucp_pm");
	}


	/**
	 * Функция просмотра сообщения
	 */
	private function read() {

		global $db, $structure, $nav, $get, $users, $parse, $tpl, $smarty;

		if(isset($get->_id) && $db->check_id($get->_id, USERS_PM_TABLE, "id", "to_uid='".$users->uid."'")) {

			# get pm
			$q = $db->query("SELECT id, title, date_create, message, from_uid FROM ".USERS_PM_TABLE." WHERE id='".$get->_id."'");
			$message = $db->fetch_assoc($q);
			$message['showmessage'] = $parse->text->br($message['message']);
			$message['date_send'] = $parse->date->unix_to_rus($message['date_create'], true);
			$message['from_name'] = $this->userlist[$message['from_uid']]['nickname'];

			# breadcrumb
			$nav->breadcrumb[] = array('act' => 'pm', 'part'=>'ucp', 'title'=>$message['title']);

			# title
			$structure->page_title .= ": #".$message['id']." &quot;".$message['title']."&quot;";

			# now you see?
			$db->query("UPDATE ".USERS_PM_TABLE." SET see='1', date_read='".time()."' WHERE id='".$get->_id."'");

			# tpl
			$smarty->assign("message", $message);
			$smarty->assign("userlist", $this->userlist);
			$tpl->load_template("ucp_pm_read");
		}
		else go(SCRIPT_NAME."?part=ucp&act=pm");
	}


	/**
	 * Функция создания нового сообщения
	 */
	private function write() {

		global $nav, $tpl, $smarty;

		# breadcrumb
		$nav->breadcrumb[] = array('part'=>'write', 'act' => 'pm', 'title'=>'Новое сообщение');

		# tpl
		$smarty->assign("userlist", $this->userlist);
		$tpl->load_template("ucp_pm_write");
	}


	/**
	 * Функция отправки сообщения
	 */
	private function send() {

		global $db, $post, $logger, $users, $mailer, $site, $smarty, $tpl;

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
			$message = $tpl->load_template("mail/new_message", true);

			$mailer->send($u['email'], "Вы получили новое сообщение", $message);


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
