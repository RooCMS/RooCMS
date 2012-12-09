<?php
/**
* @package		RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	Global general class
* @author		alex Roosso
* @copyright	2010-2014 (c) RooCMS
* @link			http://www.roocms.com
* @version		1.1.6
* @since		$date$
* @license		http://www.gnu.org/licenses/gpl-2.0.html
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################



$roocms = new Globals;
$config =& $roocms->config;

class Globals {

	# clasess
	var		$config;						# [obj]	object global configuration

	# global vars
	public	$act			= "";			# [string]	param $_GET['act'] for init actions
	public	$part			= "";			# [string]	param $_GET['part'] for init partition

	public 	$sess			= array();		# [array]	parsing $_SESSION

	# options
	public	$ajax			= false;		# [bool]	flag ajax output
	public	$rss			= false;		# [bool]	flag rss output
	public	$modifiedsince	= false;		# [bool]	flag for answer IF MODIFIED SINCE
	// public	$noscript	= false;		# [bool]	flag noscript identification

	# userdata
	public  $referer		= "";			# [string]	user referer
	public  $userip			= "";			# [string]	user ip address
	public	$useragent		= "";			# [string]	user agent string
	public	$browser		= "";			# [string]	user browser
	public	$spiderbot		= false;		# [bool]	if this search spider bot



	/**
	* Lets begin
	*
	*/
	function __construct() {

		global $GET;

		# Инициируем конфигурацию
		$this->init_configuration();

		# 	init referer
		$this->referer 	= mb_strtolower(getenv("HTTP_REFERER"));

		#	init userip
		if(getenv('HTTP_X_FORWARDED_FOR'))
			$this->userip = getenv('HTTP_X_FORWARDED_FOR');
		else
			$this->userip = getenv('REMOTE_ADDR');

		# init useragent
		$this->useragent = mb_strtolower($_SERVER['HTTP_USER_AGENT']);

		# Обрабатываем useragent для spider bot
		$this->check_spider_bot();
	}


	/**
	* Инициируем конфигурацию
	*
	*/
	protected function init_configuration() {

		global $db, $site, $debug;

		# делаем объектом
		settype($this->config, "object");

		# заносим опции в объект
		$q = $db->query("SELECT option_name, option_type, value FROM ".CONFIG_TABLE."");
		while($row = $db->fetch_assoc($q)) {

			# safe secure name script from cp
			if($row['option_name'] == "cp_script") {
				(defined('ACP') || defined('INSTALL')) ? define('CP', $row['value']) : $row['value'] = "access_denied" ;
			}

			switch($row['option_type']) {
				case 'boolean':
				case 'bool':
					$conf = "\$this->config->{$row['option_name']} = ".$row['value'].";";
					break;

				case 'int':
				case 'integer':
					settype($row['value'], "integer");
					$conf = "\$this->config->{$row['option_name']} = ".$row['value'].";";
					break;

				case 'string':
					$conf = "\$this->config->{$row['option_name']} = \"{$row['value']}\";";
					break;

				default:
					$conf = "\$this->config->{$row['option_name']} = \"{$row['value']}\";";
					break;
			}

			# init classd
			eval($conf);
		}

        # Устанавливаем title
        if(isset($this->config->site_title) && trim($this->config->site_title) != "" && trim($site['title']) == "") $site['title'] =& $this->config->site_title;

		# Устанавливаем заголовок ответа на запрос об изменении документа от поисковых машин.
		$this->modifiedsince =& $this->config->if_modified_since;

		# Добавляем в объект конфигурации список имеющихся php расширений
		$this->config->phpextensions =& $debug->phpextensions;
	}


	/**
	* Check useragent for search spider bot machine
	*
	*/
	protected function check_spider_bot() {

		require_once _LIB."/spiders.php";

		foreach($spider AS $key=>$value) {
			$check = mb_strpos($this->useragent, $value, 0, 'utf8');
			if($check !== false) $this->spiderbot = true;
		}
	}


	/**
	* Устанавливаем заголовок последней модификации для ускорения индексации сайта.
	*
	* @param mixed $lastmodifed - дата последнего редактирования контента.
	*/
	protected function ifmodifedsince($lastmodifed) {

		Header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastmodifed)." GMT");

		if($this->modifiedsince) {

			$ifmodsince = false;

			if (isset($_ENV['HTTP_IF_MODIFIED_SINCE']))		$ifmodsince = strtotime(mb_substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))	$ifmodsince = strtotime(mb_substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
			if ($ifmodsince && $ifmodsince >= $lastmodifed) {
				header($_SERVER['SERVER_PROTOCOL']." 304 Not Modified");
				exit;
			}
		}
	}
}

?>