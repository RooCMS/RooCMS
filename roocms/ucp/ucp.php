<?php
/**
* @package      RooCMS
* @subpackage	User Control Panel
* @author       alex Roosso
* @copyright    2010-2015 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.2
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2015 alex Roosso aka alexandr Belov info@roocms.com
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
*   Copyright (C) 2010-2015 alex Roosso (александр Белов) info@roocms.com
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
if(!defined('RooCMS') || !defined('UCP')) die('Access Denied');
//#########################################################


class UCP_CP {


	/**
	 * Init
	 */
	public function UCP_CP() {

		global $structure, $roocms;

		# breadcumb
		$structure->breadcumb[] = array('act' => 'ucp', 'title'=>'Личный кабинет');

		switch($roocms->part) {
			case 'edit_info':
				$this->edit_info();
				break;

			case 'update_info':
				$this->update_info();
				break;

			default:
				$this->cp();
				break;
		}
	}


	/**
	 * Функция главной страницы личного кабинета пользователя
	 */
	private function cp() {

		global $users, $tpl, $smarty;

		# tpl
		$smarty->assign("userdata", $users->userdata);
		$tpl->load_template("ucp");
	}


	/**
	 * Функция редактирования личных данных
	 */
	private function edit_info() {

		global $structure, $users, $tpl, $smarty;

		# breadcumb
		$structure->breadcumb[] = array('act' => 'ucp', 'part'=>'edit_info', 'title'=>'Изменяем личные данные');

		# tpl
		$smarty->assign("userdata", $users->userdata);
		$tpl->load_template("ucp_edit_info");
	}


	/**
	 * Функция обновляет личные данные пользователя
	 */
	private function update_info() {

		global $db, $config, $POST, $img, $parse, $users, $site, $tpl, $smarty;

		$query = "";

		# login
		if(isset($POST->login) && trim($POST->login) != "")
			if(!$users->check_field("login", $POST->login, $users->userdata['login']))
				$parse->msg("Ваш логин не был изменен. Возможно использование такого логина невозможно, попробуйте выбрать другой логин", false);
			else
				$query .= "login='".$POST->login."', ";

		else
			$parse->msg("Вы не указали логин.", false);

		# nickname
		if(isset($POST->nickname) && trim($POST->nickname) != "")
			if(!$users->check_field("nickname", $POST->nickname, $users->userdata['nickname']))
				$parse->msg("Такой псевдоним уже имеется у одного из пользователей. Пожалуйста, выберите другой псевдоним.", false);
			else
				$query .= "nickname='".$POST->nickname."', ";

		else
			$parse->msg("Вы не указали псевдоним.", false);

		# email
		if(isset($POST->email) && trim($POST->email) != "")
			if(!$users->check_field("email", $POST->email, $users->userdata['email']))
				$parse->msg("Указанный email уже существует в Базе Данных!", false);
			else
				$query .= "email='".$POST->email."', ";

		# personal data
		if(isset($POST->user_name)) 						$query .= " user_name='".$POST->user_name."',";
		else									$query .= " user_name='',";
		if(isset($POST->user_surname)) 						$query .= " user_surname='".$POST->user_surname."',";
		else									$query .= " user_surname='',";
		if(isset($POST->user_last_name)) 					$query .= " user_last_name='".$POST->user_last_name."',";
		else									$query .= " user_last_name='',";
		if(isset($POST->user_birthdate) && $POST->user_birthdate != "") 	$query .= " user_birthdate='".$parse->date->rusint_to_unix($POST->user_birthdate)."',";
		else 									$query .= " user_birthdate='0',";
		if(!isset($POST->user_sex))						$query .= " user_sex='n',";
		elseif($POST->user_sex == "m")						$query .= " user_sex='m',";
		elseif($POST->user_sex == "f")						$query .= " user_sex='f',";
		else									$query .= " user_sex='n',";

		# avatar
		if(isset($POST->delete_avatar)) {
			$users->delete_avatar($users->uid);
			$query .= "avatar='', ";
		}

		$av = $img->upload_image("avatar", "", array($config->users_avatar_width, $config->users_avatar_height), array("filename"=>"av_".$users->uid, "watermark"=>false, "modify"=>false));
		if(isset($av[0])) {
			if($users->avatar != "" && $users->avatar != $av[0]) unlink(_UPLOADIMAGES."/".$users->avatar);
			$query .= "avatar='".$av[0]."', ";
		}

		# update
		if(!isset($_SESSION['error'])) {
			# password
			if(isset($POST->password) && trim($POST->password) != "") {
				$salt = $users->create_new_salt();
				$password = $users->hashing_password($POST->password, $salt);

				$query .= "password='".$password."', salt='".$salt."', ";
			}

			$db->query("UPDATE ".USERS_TABLE." SET ".$query." date_update='".time()."' WHERE uid='".$users->userdata['uid']."'");

			# notice
			$parse->msg("Ваши данные успешно обновлены.");

			# Уведомление пользователю на электропочту
			$smarty->assign("login", $POST->login);
			$smarty->assign("nickname", $POST->nickname);
			$smarty->assign("email", $POST->email);
			$smarty->assign("password", $POST->password);
			$smarty->assign("site", $site);
			$message = $tpl->load_template("email_update_userinfo", true);

			sendmail($POST->email, "Ваши данные на \"".$site['title']."\" были обновлены", $message);

			# go out
			go("index.php?act=ucp");
		}
		else goback();
	}
}

/**
 * Init Class
 */
$ucpcp = new UCP_CP;

?>