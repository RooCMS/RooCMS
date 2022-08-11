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

			case 'syserrors':
				$this->syserrors();
				break;

			case 'clear_lowerrors':
				$this->clear_logerrors(ERRORSLOG);
				break;

			case 'clear_syserrors':
				$this->clear_logerrors(SYSERRLOG);
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
		$q = $db->query("SELECT l.id, l.uid, u.nickname, l.message, l.type_log, l.date_log, l.user_ip FROM ".LOG_TABLE." AS l 
							LEFT JOIN ".USERS_TABLE." AS u ON (u.uid = l.uid)
						ORDER BY l.date_log");
		while($data = $db->fetch_assoc($q)) {

			$data['date_log'] = $parse->date->unix_to_rus($data['date_log'], false, true, true);

			$datalog[] = $data;
		}

		# tpl
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
		$logger->info("Лог действий очищен");

		# go
		go(CP."?act=logs&part=logaction");
	}

	/**
	 * Show low errors file log
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

		# tpl
		$smarty->assign('error', $error);
		$content = $tpl->load_template("logs_lowerrors", true);
		$smarty->assign('content', $content);
	}


	/**
	 * Show sys errors file log
	 */
	private function syserrors() {

		global $tpl, $smarty;

		$data = file_read(SYSERRLOG);

		$error = [];
		$errors = explode("\r", $data);
		foreach($errors as $e) {
			if(trim($e) != "") {
				$error[] = $e;
			}
		}

		# tpl
		$smarty->assign('error', $error);
		$content = $tpl->load_template("logs_syserrors", true);
		$smarty->assign('content', $content);
	}


	/**
	 * Clear log file
	 *
	 * @param mixed $logfile - log file
	 */
	private function clear_logerrors($logfile) {

		global $files, $logger;

		# empty files
		$files->write_file($logfile, "");

		# log
		$logger->info("Лог ".basename($logfile)." очищен");

		# go
		go(CP."?act=logs&part=lowerrors");
	}
}

/**
 * Init Class
 */
$acp_logs = new ACP_Logs;
