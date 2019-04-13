<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2020 alexandr Belov aka alex Roosso. All rights reserved.
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

			case 'send':
				$this->send();
				break;

			default:
				$this->message();
				break;
		}

		# output
		$tpl->load_template("mailing");
	}


	/**
	 * Form message
	 */
	private function message() {

		global $users, $users, $smarty, $tpl;

		# list groups
		$groups = $users->get_usergroups();

		# tpl
		$smarty->assign("groups", $groups);
		$content = $tpl->load_template("mailing_message", true);
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

			$log = "";
			foreach($userlist AS $val) {

				# send
				$mailer->send($val['email'], $post->title, $post->message);

				# log
				$log .= " ".$val['email'];
			}

			$logger->info("Отправлено сообщение по адресам: ".$log);
		}
		else {
			$logger->error("Необхходимо заполнить все поля, что бы произвести рассылку.", false);
		}

		goback();
	}
}

/**
 * Init class
 */
$acp_mailing = new ACP_Mailing;
