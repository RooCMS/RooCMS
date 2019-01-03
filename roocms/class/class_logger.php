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
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


class Logger {

	# сток
	private	$log = [];


	
	/**
	 * Logger constructor.
	 */
	public function __construct() {
		# регистрируем обработчик записи логов
		register_shutdown_function(array($this,'save'));
	}

	/**
	 * Записываем ошибку
	 *
	 * @param      $subj
	 * @param bool $save - флаг указывающий записывать ли ошибку в лог
	 */
	public function error($subj, $save=true) {
		$_SESSION['error'][] = $subj;
		if($save) {
			$this->log($subj, "error");
		}
	}


	/**
	 * Записываем информационное сообщение
	 *
	 * @param      $subj
	 * @param bool $save - флаг указывающий записывать ли уведомление в лог
	 */
	public function info($subj, $save=true) {
		$_SESSION['info'][] = $subj;
		if($save) {
			$this->log($subj, "info");
		}
	}


	/**
	 * Добавляем запись в лог
	 *
	 * @param        $subj
	 * @param string $type
	 */
	public function log($subj, $type="log") {

		# обезопасим на всякий случай
		if($type != "info" && $type != "error") {
			$type="log";
		}

		$this->log[] = array("subj" => $subj, "type"=>$type);
	}


	/**
	 * Save log into database
	 */
	public function save() {

		global $db, $parse;

		if(!empty($this->log)) {

			$dump = "";
			$uid = (isset($_SESSION['uid'])) ? $_SESSION['uid'] : 0 ;

			foreach($this->log AS $value) {
				$dump = $parse->text->comma($dump);
				$dump .= "('".$uid."', '".$value["subj"]."', '".$value["type"]."', '".time()."')";
			}

			$db->query("INSERT INTO ".LOG_TABLE." (uid, message, type_log, date_log) VALUES ".$dump);
		}

		# Close connection to DB (recommended)
		$db->close();
	}
}
