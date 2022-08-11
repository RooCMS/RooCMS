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
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################



/**
 * Class Modules
 */
class Modules {

	var $mods;



	/**
	 * Modules constructor.
	 */
	public function __construct() {
		settype($this->mods, "object");
	}


	/**
	 * Load module
	 *
	 * @param string $modulename - идентификатор модуля
	 *
	 * @return string $output     - Возвращает код модуля
	 */
	public function load(string $modulename) {

		global $parse, $smarty, $tpl;

		$output = "";

		if(!isset($this->mods->{$modulename})) {
			$modulename = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9\-_])'), array('','','',''), $modulename);

			if(is_file(_MODULE."/".$modulename.".php")) {

				require_once _MODULE."/".$modulename.".php";

				$moduletitle = "Module_".$modulename;

				# Load and use mod
				$this->mods->{$modulename} = new $moduletitle;
				$this->mods->{$modulename}->begin();

				# output
				$output = $this->mods->{$modulename}->out;
			}
			else {
				if(DEBUGMODE) {
					$output = "Модуль с названием - \"".$modulename."\" не найден";
				}
			}
		}
		else {
			$this->mods->{$modulename}->begin();
			$output = $this->mods->{$modulename}->out;
		}


		return $output;
	}
}


/**
 * Init Class
 */
$module = new Modules;

/**
 * assign in templates
 */
$smarty->assign("module", $module);
