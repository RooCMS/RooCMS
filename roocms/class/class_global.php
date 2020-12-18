<?php
/**
 *   RooCMS - Open Source Free Content Managment System
 *   Copyright © 2010-2020 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 *
 *
 *   RooCMS - Бесплатная система управления контентом с открытым исходным кодом
 *   Copyright © 2010-2018 александр Белов (alex Roosso). Все права защищены.
 *   Для связи: <info@roocms.com>
 *
 *   Это программа является свободным программным обеспечением. Вы можете
 *   распространять и/или модифицировать её согласно условиям Стандартной
 *   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 *   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
 *
 *   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 *   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 *   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 *   Общественную Лицензию GNU для получения дополнительной информации.
 *
 *   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 *   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class RooCMS_Globals
 */
class RooCMS_Global {

	use DebugLog;

	# clasess
	public	$config;		 # [obj]	object global configuration

	# global vars
	public	$part		= "";	 # [string]	param $_GET['part'] for init partition
	public	$act		= "";	 # [string]	param $_GET['act'] for init actions
	public	$move		= "";	 # [string]	param $_GET['move'] for init move

	public 	$sess		= [];	 # [array]	parsing $_SESSION

	# options
	public	$ajax		= false; # [bool]	flag ajax output
	public	$rss		= false; # [bool]	flag rss output
	public	$modifiedsince	= false; # [bool]	flag for answer IF MODIFIED SINCE
	//public $noscript	= false; # [bool]	flag noscript identification

	# userdata
	public	$usersession	= "";	 # [string]	user ssession
	public  $userip		= "";	 # [string]	user ip address
	public  $referer	= "";	 # [string]	user referer

	# spiderbot
	public	$spiderbot	= false; # [bool]	if this search spider bot



	/**
	* Lets begin
	*
	*/
	public function __construct() {

		# init configuration
		$this->init_configuration();

		# read session id
		$this->usersession = session_id();

		# init referer
		$this->referer 	= mb_strtolower(getenv("HTTP_REFERER"));

		# init userip
		if(getenv('HTTP_X_FORWARDED_FOR')) {
			$this->userip = getenv('HTTP_X_FORWARDED_FOR');
		}
		else {
			$this->userip = getenv('REMOTE_ADDR');
		}
	}


	/**
	* init configuration
	*
	*/
	protected function init_configuration() {

		global $db, $site, $debug;

		# set data type object fot $this->config
		settype($this->config, "object");

		$this->get_default_config();

		if($db->db_connect) {
			# get data config options from data base
			$q = $db->query("SELECT option_name, option_type, value FROM ".CONFIG_TABLE."");
			while($row = $db->fetch_assoc($q)) {

				switch($row['option_type']) {
					case 'boolean':
						$this->config->{$row['option_name']} = ($row['value'] === "true");
						break;

					case 'int':
						settype($row['value'], "integer");
						$this->config->{$row['option_name']} = (int) $row['value'];
						break;

					case 'string':
						$this->config->{$row['option_name']} = (string) $row['value'];
						break;

					/*case 'html': #default
						$this->config->{$row['option_name']} = $row['value'];
						break;*/

					default:
						$this->config->{$row['option_name']} = $row['value'];
						break;
				}
			}
		}

		# http(s) protocol
		$this->handler_https();

		# safe secure name script from cp
		if(defined('ACP') || defined('INSTALL')) {
			define('CP', $this->config->cp_script);
		}

		# Set page title
		if(trim($this->config->site_title) != "" && trim($site['title']) == "") {
			$site['title'] =& $this->config->site_title;
		}

		# Set header for searchbot
		$this->modifiedsince =& $this->config->if_modified_since;

		# extend config object- add phpextension
		$this->config->phpextensions =& $debug->phpextensions;
	}


	/**
	 * Check useragent for search spider bot machine
	 *
	 * @param $useragent
	 */
	public function check_spider_bot($useragent) {

		require_once _LIB."/spiders.php";

		foreach($spider AS $value) {
			$check = mb_strpos($useragent, $value, 0, 'utf8');

			if($check !== false) {
				$this->spiderbot = true;
			}
		}
	}


	/**
	 * Set secure protocol (if active)
	 */
	protected function handler_https() {

		global $site;

		$site['protocol'] = (isset($_SERVER["HTTPS"]) || $this->config->global_https) ? "https" : "http" ;

		# Set headers for security protocols
		if($this->config->global_https) {
			header("Strict-Transport-Security: max-age=15552000; preload");
		}
		else {
			header("Strict-Transport-Security: max-age=0; preload");
		}
	}


	/**
	* Set date title last modification for search bot.
	*
	* @param mixed $lastmodifed - date last modify content
	*/
	protected function ifmodifedsince($lastmodifed) {

		# set header
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastmodifed)." GMT");

		if($this->modifiedsince) {

			$ifmodsince = false;

			if (isset($_ENV['HTTP_IF_MODIFIED_SINCE'])) {
				$ifmodsince = strtotime(mb_substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
			}

			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
				$ifmodsince = strtotime(mb_substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
			}

			if ($ifmodsince !== false && $ifmodsince >= $lastmodifed) {
				header($_SERVER['SERVER_PROTOCOL']." 304 Not Modified");
				exit;
			}
		}
	}


	/**
	 * Get default config options if not connecting to DB
	 */
	private function get_default_config() {

		$this->config->site_title            = "";
		$this->config->global_site_title     = false;
		$this->config->global_https          = false;
		$this->config->gd_thumb_image_width  = 267;
		$this->config->gd_thumb_image_height = 150;
		$this->config->gd_image_maxwidth     = 1600;
		$this->config->gd_image_maxheight    = 1600;
		$this->config->gd_thumb_type_gen     = "cover";
		$this->config->gd_thumb_bgcolor      = "#ffffff";
		$this->config->gd_thumb_jpg_quality  = 90;
		$this->config->gd_use_watermark      = "no";
		$this->config->tpl_recompile_force   = false;
		$this->config->meta_description      = "";
		$this->config->meta_keywords         = "";
		$this->config->cp_script             = "acp.php";
	}
}
