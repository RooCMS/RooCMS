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


class Logger {

	# stock
	private	$log = [];


	
	/**
	 * Logger constructor.
	 */
	public function __construct() {
		# register handler for logs
		register_shutdown_function(array($this,'save'));
	}

	/**
	 * Log error
	 *
	 * @param      $subj
	 * @param bool $save - on/off write error in db
	 */
	public function error($subj, bool $save=true) {
		$_SESSION['error'][] = $subj;
		if($save) {
			$this->log($subj, "error");
		}
	}


	/**
	 * Log info
	 *
	 * @param      $subj
	 * @param bool $save - on/off write notice in db
	 */
	public function info($subj, bool $save=true) {
		$_SESSION['info'][] = $subj;
		if($save) {
			$this->log($subj, "info");
		}
	}


	/**
	 * Add msg to log
	 *
	 * @param        $subj
	 * @param string $type
	 */
	public function log($subj, string $type="log") {

		# check type msg
		if($type != "info" && $type != "error") {
			$type="log";
		}

		$this->log[] = array("subj" => $subj, "type"=>$type);
	}


	/**
	 * Save log into database
	 */
	public function save() {

		global $db, $roocms, $parse;

		if(!empty($this->log)) {

			$dump = [];
			$uid = (isset($_SESSION['uid'])) ? $_SESSION['uid'] : 0 ;

			foreach($this->log AS $value) {
				$dump[] = "('".$uid."', '".$value["subj"]."', '".$value["type"]."', '".time()."', '".$roocms->userip."')";
			}

			# insert log msg in to db
			$db->query("INSERT INTO ".LOG_TABLE." (uid, message, type_log, date_log, user_ip) VALUES ".implode(", ", $dump));
		}

		# Close connection to DB (recommended)
		$db->close();
	}
}
