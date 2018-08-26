<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
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

	/**
	 * Загружаем модуль
	 *
	 * @param  string $modulename - идентификатор модуля
	 *
	 * @return string $output     - Возвращает код модуля
	 */
	public function load($modulename) {

		global $parse, $smarty, $tpl;

                $output = "";

		$modulename = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9\-_])'), array('','','',''), $modulename);

		if(file_exists(_MODULE."/".$modulename.".php")) {

			ob_start();
				require_once _MODULE."/".$modulename.".php";
				$output = ob_get_contents();
			ob_end_clean();

		}
		else {
			if(DEBUGMODE) {
				$output = "Модуль с названием - \"".$modulename."\" не найден";
			}
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