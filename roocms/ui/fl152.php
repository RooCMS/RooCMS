<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2019 alexandr Belov aka alex Roosso. All rights reserved.
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
 * Class UI_FL152
 */
class UI_FL152 {

	public function __construct() {

		global $structure, $config;

		# title
		$structure->page_title = "Соглашение об условиях передачи информации";

		# breadcumb
		$structure->breadcumb[] = array('part'=>'fl152', 'title'=>'Соглашение об условиях передачи информации');

		# goout
		if(!$config->fl152_use) {
			go(SCRIPT_NAME."?part=ucp&act=ucp");
		}

		# show agreement fl 152
		$this->fl152();
	}


	/**
	 * Функция выводит пользовательское соглашение о передачи информации согласно ФЗ РФ 152
	 */
	public function fl152() {

		global $config, $parse, $smarty, $tpl;

		# parse
		$agreement = $parse->text->html($config->fl152_agreement);

		# tpl
		$smarty->assign("agreement", $agreement);
		$tpl->load_template("fl152");
	}
}

/**
 * Init Class
 */
$uifl152 = new UI_FL152;