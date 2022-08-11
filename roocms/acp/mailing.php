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
if(!defined('RooCMS') || !defined('ACP')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class ACP_Mailing
 */
class ACP_Mailing {

	/**
	 * ACP_Mailing constructor.
	 */
	public function __construct() {

		global $roocms, $tpl;

		# action
		switch($roocms->part) {

			case 'archive_list':
				$this->archive_list();
				break;

			case 'archive_letter':
				$this->archive_letter();
				break;

			case 'send':
				$this->send();
				break;

			default:
				$this->create_message();
				break;
		}

		# output
		$tpl->load_template("mailing");
	}


	/**
	 * Form message
	 */
	private function create_message() {

		global $users, $users, $smarty, $tpl;

		# list groups
		$groups = $users->get_usergroups();

		# tpl
		$smarty->assign("groups", $groups);
		$content = $tpl->load_template("mailing_create_message", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Sender
	 */
	private function send() {

		global $post, $parse, $logger, $users, $mailer;

		if(isset($post->title) && isset($post->message)) {

			$userscond = [];
			if(isset($post->gids)) {
				$ulist = $users->get_groupuids($post->gids);
				foreach($ulist AS $u) {
					$userscond[] = $u['uid'];
				}
			}

			if(isset($post->force) && $post->force == 1) {
				# all
				$userlist = $users->get_userlist(1,0,-1, $userscond);
			}
			else {
				# only subscribers
				$userlist = $users->get_userlist(1,0,1, $userscond);
			}

			# html
			$post->message = $parse->text->html($post->message);

			# send
			if(count($userlist) != 0) {
				$mailer->spread($userlist, $post->title, $post->message);
			}
			else {
				$logger->error("Сообщение не отправлено! Не обнаружены подписчики подходящие под заданные критерии.", false);
			}

		}
		else {
			$logger->error("Необхходимо заполнить все поля, что бы произвести рассылку.", false);
		}

		goback();
	}


	/**
	 * Archive messages list
	 */
	private function archive_list() {

		global $db, $parse, $smarty, $tpl;

		$list = [];
		$q = $db->query("SELECT m.id, m.author_id, m.date_create, m.title, u.nickname 
					FROM ".MAILING_TABLE." AS m 
					LEFT JOIN ".USERS_TABLE." AS u ON (u.uid = m.author_id)
					ORDER BY m.id DESC");
		while($data = $db->fetch_assoc($q)) {
			$data['date_create'] = $parse->date->unix_to_rus($data['date_create'], true, true, true);
			$list[] = $data;
		}

		# tpl
		$smarty->assign("list", $list);
		$content = $tpl->load_template("mailing_archive_list", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Archive message view
	 */
	private function archive_letter() {

		global $db, $get, $parse, $smarty, $tpl;

		if(isset($get->_id) && $db->check_id($get->_id, MAILING_TABLE)) {

			$id = $get->_id;

			# get letter data
			$q = $db->query("SELECT m.id, m.author_id, m.date_create, m.title, m.message, u.nickname 
					FROM ".MAILING_TABLE." AS m 
					LEFT JOIN ".USERS_TABLE." AS u ON (u.uid = m.author_id)
					WHERE m.id='".$id."'");
			$letter = $db->fetch_assoc($q);

			$letter['message'] = $parse->text->html($letter['message']);
			$letter['date_create'] = $parse->date->unix_to_rus($letter['date_create'], true, true, true);

			# get recipients
			$recipients = [];
			$q = $db->query("SELECT r.uid, r.email, u.nickname, u.email as actual_email
					FROM ".MAILING_LINK_TABLE." AS r
					LEFT JOIN ".USERS_TABLE." AS u ON (r.uid = u.uid)
					WHERE r.message_id='".$id."'");
			while($data = $db->fetch_assoc($q)) {
				$recipients[] = $data;
			}

			# tpl
			$smarty->assign("letter", $letter);
			$smarty->assign("recipients", $recipients);
			$content = $tpl->load_template("mailing_archive_letter", true);
			$smarty->assign("content", $content);
		}
		else {
			goback();
		}
	}
}

/**
 * Init class
 */
$acp_mailing = new ACP_Mailing;
