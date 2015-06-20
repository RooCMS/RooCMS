<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @author       alex Roosso
* @copyright    2010-2015 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2014 alex Roosso aka alexandr Belov info@roocms.com
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
*   Copyright (C) 2010-2014 alex Roosso (александр Белов) info@roocms.com
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
class Users {

	# user uniq data
	public	$uid		= 0;		# [int]		user id
	public	$login		= "";		# [string]	user login
	public	$nickname	= "";		# [string]	user nickname
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


		# check uniq user data
		$this->get_private_userdata();


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
	private function get_private_userdata() {

		global $db, $roocms, $security;

		if(isset($roocms->sess['login']) && trim($roocms->sess['login']) != "" && $db->check_id($roocms->sess['login'], USERS_TABLE, "login", "status='1'") && isset($roocms->sess['token']) && strlen($roocms->sess['token']) == 32) {

			# get data
			$q    = $db->query("SELECT uid, login, nickname, password, salt FROM ".USERS_TABLE." WHERE login='".$roocms->sess['login']."' AND status='1'");
			$data = $db->fetch_assoc($q);

			# uid
			$this->uid	= $data['uid'];

			# login
			$this->login	= $data['login'];

			# nickname
			$this->nickname	= $data['nickname'];

			# security token
			$this->token	= $security->hashing_token($roocms->sess['login'], $data['password'], $data['salt']);
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
	 * Паранои много не бывает.
	 * Проверяем данные авторизации, не было ли попыток совершения подмены данных
	 */
	private function check_userdata() {

		global $roocms;

		$destroy = false;

		# check uid
		if($roocms->sess['uid'] != $this->uid) $destroy = true;

		# check login
		if($roocms->sess['login'] != $this->login) $destroy = true;

		# check nickname
		if($roocms->sess['nickname'] != $this->nickname) $destroy = true;

		# check token
		if($roocms->sess['token'] != $this->token) $destroy = true;

		if($destroy) {
			$roocms->sess = array();
			session_destroy();

			# notice
			die("ВНИМАНИЕ! Зарегестрированна попытка подмены данных!");
		}
	}

}
?>