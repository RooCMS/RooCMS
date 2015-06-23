<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @author       alex Roosso
* @copyright    2010-2015 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.2
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
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
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
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
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


/**
 * Class Users
 */
class Users extends Security {

	# user uniq data
	public	$uid		= 0;		# [int]		user id
	public	$login		= "";		# [string]	user login
	public	$nickname	= "";		# [string]	user nickname
	public	$title		= "u";		# [enum]	user title
	public	$gid		= 0;		# [int]		user group id
	public	$token		= "";		# [string]	user security token

	# user global data
	private	$usersession	= "";		# [string]	user ssession
	private $userip		= "";		# [string]	user ip address
	private	$useragent	= "";		# [string]	user agent string
	private $referer	= "";		# [string]	user referer



	/**
	 * Work your magic
	 */
	function Users() {

		global $roocms;


		# get user data
		$this->usersession	&= $roocms->usersession;
		$this->userip		&= $roocms->userip;
		$this->useragent 	&= $roocms->useragent;
		$this->referer		&= $roocms->referer;


		# init user
		$this->init_user();


		if($this->uid != 0) {
			# check user data for security
			$this->check_userdata();

			# update users info
			$this->update_info_user($this->uid);
		}
	}


	/**
	 * Получаем персональные данные пользователя
	 */
	private function init_user() {

		global $db, $roocms;

		if(isset($roocms->sess['login']) && trim($roocms->sess['login']) != "" && $db->check_id($roocms->sess['login'], USERS_TABLE, "login", "status='1'") && isset($roocms->sess['token']) && strlen($roocms->sess['token']) == 32) {

			# get data
			$q    = $db->query("SELECT uid, gid, login, nickname, title, password, salt FROM ".USERS_TABLE." WHERE login='".$roocms->sess['login']."' AND status='1'");
			$data = $db->fetch_assoc($q);

			# uid
			$this->uid	= $data['uid'];

			# gid
			$this->gid	= $data['gid'];

			# login
			$this->login	= $data['login'];

			# title
			$this->title	= $data['title'];

			# nickname
			$this->nickname	= $data['nickname'];

			# security token
			$this->token	= $this->hashing_token($roocms->sess['login'], $data['password'], $data['salt']);
		}
	}


	/**
	 * Обновляем простую информацию пользователя, вроде времени последнего визита на сайт.
	 *
	 * @param int $uid - уникальные идентификатор пользователя
	 */
	private function update_info_user($uid) {

		global $db;

		# update time last visited
		$db->query("UPDATE ".USERS_TABLE." SET last_visit='".time()."' WHERE uid='".$uid."' AND status='1'");
	}


	/**
	 * Функция получения данных о пользователе.
	 *
	 * @param int $uid идентификатор пользователя
	 */
	public function get_user_data($uid) {

		global $db;
	}
}
?>