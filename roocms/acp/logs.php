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
if(!defined('RooCMS') || !defined('ACP')) {
	die('Access Denied');
}
//#########################################################


class ACP_Logs {

	/**
	 * ACP_Logs constructor.
	 */
	public function __construct() {

		global $roocms, $tpl;

		switch($roocms->part) {

			case 'clear_logaction':
				$this->clear_logaction();
				break;

			case 'lowerrors':
				$this->lowerrors();
				break;

			case 'clear_lowerrors':
				$this->clear_lowerrors();
				break;

			default: #logaction
				$this->logaction();
				break;
		}

		# tpl
		$tpl->load_template("logs");
	}


	/**
	 * Show log action
	 */
	private function logaction() {

		global $db, $parse, $tpl, $smarty;

		# get data log
		$datalog = [];
		$q = $db->query("SELECT l.id, l.uid, u.nickname, l.message, l.type_log, l.date_log FROM ".LOG_TABLE." AS l 
							LEFT JOIN ".USERS_TABLE." AS u ON (u.uid = l.uid)
						ORDER BY l.date_log ASC");
		while($data = $db->fetch_assoc($q)) {

			$data['date_log'] = $parse->date->unix_to_rus($data['date_log'], false, true, true);

			$datalog[] = $data;
		}

		#tpl
		$smarty->assign("datalog", $datalog);
		$content = $tpl->load_template("logs_logaction", true);
		$smarty->assign('content', $content);
	}


	/**
	 * Clear action log
	 */
	private function clear_logaction() {

		global $db, $logger;

		# empty files
		$db->query("TRUNCATE ".LOG_TABLE);

		# log
		$logger->info("Лог очищен");

		# go
		go(CP."?act=logs&part=logaction");
	}

	/**
	 * Show lowerrors
	 */
	private function lowerrors() {

		global $tpl, $smarty;

		$data = file_read(ERRORSLOG);

		$error = [];
		$errors = explode("\r", $data);
		foreach($errors as $e) {
			if(trim($e) != "") {
				$error[] = explode("|", $e);
			}
		}

		$smarty->assign('error', $error);
		$content = $tpl->load_template("logs_lowerrors", true);
		$smarty->assign('content', $content);
	}


	/**
	 * Clear php error log
	 */
	private function clear_lowerrors() {

		global $files, $logger;

		# empty files
		$files->write_file(ERRORSLOG, "");

		# log
		$logger->info("Лог некритических ошибок очищен");

		# go
		go(CP."?act=logs&part=lowerrors");
	}
}

/**
 * Init Class
 */
$acp_logs = new ACP_Logs;