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
if(!defined('RooCMS') || !defined('UI')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class UI_Mailing
 */
class UI_Mailing {


	/**
	 * UI_Mailing constructor.
	 */
	public function __construct() {

		global $roocms, $structure, $nav;

		# title
		$structure->page_title = "Рассылка";

		# breadcrumb
		$nav->breadcrumb[] = array('part'=>'mailing', 'title'=>'Рассылка');

		# action
		switch($roocms->act) {
			default:
				$this->letter();
				break;
		}
	}


	/**
	 * Show letter
	 */
	private function letter() {

		global $db, $get, $parse, $smarty, $tpl;

		$letter = [];

		# check access
		if(isset($get->_id, $get->_secret) && $db->check_id($get->_id, MAILING_LINK_TABLE, "id", "secret_key='".$get->_secret."'")) {

			# get letter
			$q = $db->query("SELECT ml.message_id, ml.uid, ml.email, m.title, m.message, m.date_create, u.nickname
						FROM ".MAILING_LINK_TABLE." AS ml
						LEFT JOIN ".MAILING_TABLE." AS m ON (ml.message_id = m.id)
						LEFT JOIN ".USERS_TABLE." AS u ON (ml.uid = u.uid)
						WHERE ml.id='".$get->_id."' AND ml.secret_key='".$get->_secret."'");
			$letter = $db->fetch_assoc($q);

			$letter['message'] = $parse->text->html($letter['message']);
			$letter['date']    = $parse->date->unix_to_rus($letter['date_create']);
		}

		# tpl
		$smarty->assign("letter", $letter);
		$tpl->load_template("mailing_letter");
	}
}

/**
 * Init Class
 */
$uimailing = new UI_Mailing;
