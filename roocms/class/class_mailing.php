<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2020 alexandr Belov aka alex Roosso.
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
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class Mailing
 */
class Mailing {

	# variable for headers
	private $site_title  = "";
	private $site_domain = "";

	# variable for message
	private $from        = "";

	# headers
	private $headers     = "";



	/**
	 * Mailing constructor.
	 *
	 * Set up default variables
	 */
	public function __construct() {

		global $config, $site;

		# set site title
		$this->site_title = $site['title'];

		# set site domain
		$this->site_domain = str_ireplace(array('http://','https://','www.'), '', $site['domain']);

		# set sender backmail
		$this->from = ($config->global_email != "") ? $config->global_email : "roocms@".$this->site_domain;
	}


	/**
	 * Send mail message
	 *
	 * @param string $mailto   - recipient's email
	 * @param string $theme    - title message
	 * @param string $message  - message
	 * @param string $from     - sender's return email
	 */
	public function send($mailto, $theme, $message, $from="") {

		global $parse;

		# valid back address
		if($parse->valid_email($from)) {
			$this->from = $from;
		}

		# parse message
		$message = str_ireplace(array('\\r','\\n'), array('', '\n'), $message);

		# set headers
		$this->set_headers();

		# send email message
		if($parse->valid_email($mailto)) {
			mb_send_mail($mailto, $theme, $message, $this->headers);
		}
	}


	/**
	 * Spread mail to users
	 *
	 * @param array  $usersdata	- data users array from get_userlist
	 * @param string $theme		- subject message
	 * @param string $message	- message
	 */
	public function spread(array $usersdata, $theme, $message) {

		global $db, $users, $security, $logger, $site, $smarty, $tpl;

		# write message
		$db->query("INSERT INTO ".MAILING_TABLE." (author_id, title, message, date_create) VALUES ('".$users->uid."', '".$theme."', '".$message."', '".time()."')");
		$message_id = $db->insert_id();

		foreach($usersdata as $val) {

			# generated keys
			$secret_key = randcode(16);

			# save into db
			$db->query("INSERT INTO ".MAILING_LINK_TABLE." (message_id, uid, email, secret_key) VALUES ('".$message_id."', '".$val['uid']."', '".$val['email']."', '".$secret_key."')");

			# added spread footer
			$smarty->assign("theme",      $theme);
			$smarty->assign("message_id", $message_id);
			$smarty->assign("secret_key", $secret_key);
			$smarty->assign("site",       $site);
			$smarty->assign("userdata",   $val);

			$spread_footer = $tpl->load_template("mail/spread_header", true);
			$spread_footer = $tpl->load_template("mail/spread_footer", true);

			$letter = $spread_header.$message.$spread_footer;

			# send to mail
			$this->send($val['email'], $theme, $letter);
		}

		$logger->info("Почтовая рассылка #".$message_id." отправлена пользователям");
	}


	/**
	 * Set headers
	 */
	private function set_headers() {

		# headers
		$this->headers  = "MIME-Version: 1.0\n";
		$this->headers .= "From: ".$this->site_title." <{$this->from}>\n".EMAIL_MESSAGE_PARAMETERS."\n";
		$this->headers .= "X-Sender: <no-reply@".$this->site_domain.">\n";
		$this->headers .= "X-Mailer: RooCMS from ".$this->site_domain."\n";
		$this->headers .= "Return-Path: <no-replay@".$this->site_domain.">";
	}
}
