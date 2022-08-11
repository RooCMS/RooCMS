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
 * Class UI_User_Agreement
 */
class UI_User_Agreement {

	public function __construct() {

		global $config, $structure, $nav;

		# title
		$structure->page_title = "Соглашение об условиях передачи информации";

		# breadcrumb
		$nav->breadcrumb[] = array('part'=>'uagreement', 'title'=>'Соглашение об условиях передачи информации');

		# goout
		if(!$config->uagreement_use) {
			go(SCRIPT_NAME."?part=ucp&act=ucp");
		}

		# show agreement
		$this->show();
	}


	/**
	 * Show user agreement
	 */
	public function show() {

		global $config, $parse, $smarty, $tpl;

		# parse
		$agreement = $parse->text->html($config->uagreement_text);

		# tpl
		$smarty->assign("agreement", $agreement);
		$tpl->load_template("user_agreement");
	}
}

/**
 * Init Class
 */
$uiuagreement = new UI_User_Agreement;
