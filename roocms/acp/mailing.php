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
 * @subpackage   Admin Control Panel
 * @author       alex Roosso
 * @copyright    2010-2019 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.0
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
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

		// TODO: Расширить до нормальной службы расслки.
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
	 * Форма набора сообщения.
	 */
	private function message() {

		global $smarty, $tpl;

		$content = $tpl->load_template("mailing_message", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Sender
	 */
	private function send() {

		global $post, $parse, $users, $logger;

		if(isset($post->title) && isset($post->message)) {

			if(isset($post->force) && $post->force == 1) {
				# all
				$userlist = $users->get_userlist(1,0,-1, NULL, true);
			}
			else {
				# только подписчики
				$userlist = $users->get_userlist(1,0,1, NULL, true);
			}

			# html
			$post->message = $parse->text->html($post->message);

			$log = "";
			foreach($userlist AS $val) {

				# send
				sendmail($val['email'], $post->title, $post->message);

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