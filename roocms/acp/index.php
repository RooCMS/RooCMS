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


class ACP_Index {


	/**
	 * Run, baby, run
	 */
	public function __construct() {

		global $roocms, $debug, $smarty, $tpl;

		# subpart
		switch($roocms->part) {
			case 'serverinfo':
				$this->serverinfo();
				break;

			case 'fileinfo':
				$this->fileinfo();
				break;

			case 'phpext':
				$this->phpext();
				break;

			case 'phpinfo':
				$this->showphpinfo();
				break;

			case 'inivars':
				$this->inivars();
				break;

			case 'license':
				$this->license();
				break;

			default:
				$this->main();
				break;
		}

		# warning alerts
		$warning_subj = [];
		if(is_file(_SITEROOT."/install/index.php")) {
			$warning_subj[] = "Установщик RooCMS находится в корне сайта. Не волнуйтесь. Вход в него надежно защищен паролем. Но в целях безопастности, мы рекомендуем удалить папку /install/ с хостинга.";
		}

		if($debug->exist_errors) {
			$warning_subj[] = "Лог файл PHP ошибок содержит несколько записей!";
		}

		# smarty alerts
		$smarty->assign('warning_subj',	$warning_subj);

		# load template
		$tpl->load_template("index");
	}


	/**
	 * Main Screen
	 * Show dashboard information
	 */
	private function main() {

		global $site, $config, $tpl, $smarty;

		$info = [];
		$info['sitetitle']	= $config->site_title;
		$info['sitedomain']	= $site['domain'];
		$info['email']		= $config->cp_email;
		$info['sysemail']	= $site['sysemail'];
		$info['roocms']		= ROOCMS_VERSION;

		$warning_subj = [];

		if(get_http_response_code("https://version.roocms.com/index.php") == "200") {
			$f = file("https://version.roocms.com/index.php");

			if(!empty($f) && version_compare(ROOCMS_VERSION, $f[0], "<")) {
				$warning_subj[] = "Внимание! Вышла новая версия <b>RooCMS ".$f[0]."</b>. Рекомендуем обновить ваш сайт до последней версии.
				<br />Что бы скачать дистрибутив последней версии перейдите по <a href='http://www.roocms.com/index.php?page=download' target='_blank'>ссылке</a>";

				$info['last_stable'] = $f[0];
			}
		}

		# draw tpl
		$smarty->assign('info',		$info);
		$content = $tpl->load_template("index_main", true);
		$smarty->assign('content',	$content);
	}


	/**
	 * Server information
	 */
	private function serverinfo() {

		global $db, $debug, $tpl, $smarty;

		# MySQL version
		$data1 = [];
		$q = $db->query("SHOW VARIABLES LIKE 'version'");
		$mysql = $db->fetch_row($q);
		$data1['mysql']		= $mysql[1];

		$data1['php'] 		= PHP_VERSION;				# php version
		$data1['zend']		= zend_version();			# Zend version
		$data1['ws'] 		= $_SERVER['SERVER_SOFTWARE'];		# Server sowfware version apache_get_version();
		$data1['os']		= php_uname("s")." (".PHP_OS.")"; 	# OS
		$data1['uname']		= php_uname(); 				# UNAME
		$data1['roocms']	= ROOCMS_VERSION;			# RooCMS

		$data1['pid']		= PEAR_INSTALL_DIR; 			# PEAR extends dir
		$data1['dip']		= DEFAULT_INCLUDE_PATH;
		$data1['ped']		= PHP_EXTENSION_DIR;			# PHP extends dir
		$data1['pcp']		= PHP_CONFIG_FILE_PATH;

		$data1['sn']		= $_SERVER["SERVER_NAME"];
		$data1['sa']		= $_SERVER["SERVER_ADDR"];
		$data1['sp']		= $_SERVER["SERVER_PROTOCOL"];
		$data1['ra']		= $_SERVER["REMOTE_ADDR"];
		$data1['docroot']	= _SITEROOT;

		$data1['ml']		= ini_get('memory_limit');		# Memory limit
		$data1['mfs']		= ini_get('upload_max_filesize');	# Maximum file size
		$data1['mps']		= ini_get('post_max_size');		# Maximum post size
		$data1['met']		= ini_get('max_execution_time');	# Max execution time

		if(array_search("apache2handler", $debug->phpextensions)) {
			$data1['apache_mods']	= apache_get_modules();		# Apache extends
			$data1['apache_ver']	= apache_get_version();		# Apache version
		}

		$server_vars = array(
			'PHP_SELF',
			'argv',
			'argc',
			'GATEWAY_INTERFACE',
			'SERVER_ADDR',
			'SERVER_NAME',
			'SERVER_SOFTWARE',
			'SERVER_PROTOCOL',
			'REQUEST_METHOD',
			'REQUEST_TIME',
			'REQUEST_TIME_FLOAT',
			'QUERY_STRING',
			'DOCUMENT_ROOT',
			'HTTP_ACCEPT',
			'HTTP_ACCEPT_CHARSET',
			'HTTP_ACCEPT_ENCODING',
			'HTTP_ACCEPT_LANGUAGE',
			'HTTP_CONNECTION',
			'HTTP_HOST',
			'HTTP_REFERER',
			'HTTP_USER_AGENT',
			'HTTPS',
			'REMOTE_ADDR',
			'REMOTE_HOST',
			'REMOTE_PORT',
			'REMOTE_USER',
			'REDIRECT_REMOTE_USER',
			'SCRIPT_FILENAME',
			'SERVER_ADMIN',
			'SERVER_PORT',
			'SERVER_SIGNATURE',
			'PATH_TRANSLATED',
			'SCRIPT_NAME',
			'REQUEST_URI',
			'PHP_AUTH_DIGEST',
			'PHP_AUTH_USER',
			'PHP_AUTH_PW',
			'AUTH_TYPE',
			'PATH_INFO',
			'ORIG_PATH_INFO'
		) ;


		$data2 = [];
		foreach ($server_vars as $arg) {
			if (isset($_SERVER[$arg])) {
				if(is_array($_SERVER[$arg])) {
					$_SERVER[$arg] = json_encode($_SERVER[$arg]);
				}
				$data2[] = array("var"=>$arg, "value"=>$_SERVER[$arg]);
			}
			else {
				$data2[] = array("var"=>$arg, "value"=>"not found");
			}
		}

		# draw tpl
		$smarty->assign('data1',	$data1);
		$smarty->assign('data2',	$data2);

		$content = $tpl->load_template("index_serverinfo", true);
		$smarty->assign('content',	$content);
	}


	/**
	 * Format attached infromation
	 */
	private function fileinfo() {

		global $tpl, $smarty;

		$imagetype = []; $filetype = [];
		require_once _LIB."/mimetype.php";

		$filetypes = [];
		$filetypes['mfs']		= ini_get('upload_max_filesize');	# Maximum file size
		$filetypes['mps']		= ini_get('post_max_size');		# Maximum post size
		$filetypes['images']		= $imagetype;				# Allow image types
		$filetypes['files']		= $filetype;				# Allow file types

		# draw
		$smarty->assign('filetypes',	$filetypes);

		$content = $tpl->load_template("index_fileinfo", true);
		$smarty->assign('content',	$content);
	}


	/**
	 * Check & show php extends
	 */
	private function phpext() {

		global $config, $tpl, $smarty;

		$phpextfunc = [];
		foreach($config->phpextensions AS $v) {
			$phpextfunc[$v] = get_extension_funcs($v);
		}

		# draw tpl
		$smarty->assign('phpextensions',	$config->phpextensions);
		$smarty->assign('phpextfunc',		$phpextfunc);

		$content = $tpl->load_template("index_phpext", true);
		$smarty->assign('content',		$content);
	}


	/**
	 * phpinfo
	 */
	private function showphpinfo() {

		global $tpl, $smarty;

		ob_start();
			phpinfo();
			$output = ob_get_contents();
		ob_end_clean();

		preg_match_all('#(\<body[^\>]*\>)(.+?)(\<\/body\>)#is', $output, $out);

		$phpinfo = $out[2][0];

		# draw tpl
		$smarty->assign('phpinfo', 	$phpinfo);

		$content = $tpl->load_template("index_phpinfo", true);
		$smarty->assign('content',	$content);
	}


	/**
	 * PHP variables
	 */
	private function inivars() {

		global $tpl, $smarty;

		$inivars = ini_get_all();

        	# draw tpl
		$smarty->assign('inivars',	$inivars);

		$content = $tpl->load_template("index_inivars", true);

		$smarty->assign('content',	$content);
	}


	/**
	 * License text
	 */
	private function license() {

		global $tpl, $smarty;

		require_once _LIB."/license.php";

		# draw tpl
		$smarty->assign('license',	$license['ru']);

		$content = $tpl->load_template("index_license", true);

		$smarty->assign('content',	$content);
	}
}

/**
 * Init Class
 */
$acp_index = new ACP_Index;
