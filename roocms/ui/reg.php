<?php
/**
 * @package      RooCMS
 * @subpackage	 User Registration
 * @author       alex Roosso
 * @copyright    2010-2017 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.0
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * RooCMS - Russian free content managment system
 * Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/
 *
 *
 * RooCMS - Русская бесплатная система управления сайтом
 * Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
 *
 * Это программа является свободным программным обеспечением. Вы можете
 * распространять и/или модифицировать её согласно условиям Стандартной
 * Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 * Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
 *
 * Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 * ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 * ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 * Общественную Лицензию GNU для получения дополнительной информации.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 * с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('UI')) die('Access Denied');
//#########################################################


class REG {



	public function __construct() {

		global $structure, $roocms, $users;

		# breadcumb
		$structure->breadcumb[] = array('part'=>'reg', 'title'=>'Регистрация');

		# if users registred
		if($users->uid != 0) go("/index.php?part=ucp&act=ucp");
		
		# action
		switch($roocms->act) {
			case 'join':
				$this->join();
				break;

			case 'activation':
				$this->activation();
				break;

			case 'verification':
				$this->verification();
				break;
			
			default:
				$this->profile();
				break;
		}
	}


	/**
	 * Функция запроса анкеты будущего пользователя (формы регистрации)
	 */
	private function profile() {

		global $smarty, $tpl;


		$tpl->load_template("reg_profile");
	}


	/**
	 * Функция активации аккаунта и проверки электронной почты
	 */
	private function activation() {

		global $smarty, $tpl;


		$tpl->load_template("reg_activation");
	}


	/**
	 * Функция проверки регистрационных данных (анкеты пользователя) и регистрации
	 */
	private function join() {

		global $db, $config, $img, $smarty, $users, $tpl, $POST, $parse, $security, $site;

		if(isset($POST->join)) {

			# nickname
			if(!isset($POST->nickname) || trim($POST->nickname) == "") $POST->nickname = mb_ucfirst($POST->login);
			$POST->nickname = $users->check_new_nickname($POST->nickname);

			# login
			if(!isset($POST->login) || trim($POST->login) == "") {
				if(isset($POST->nickname) && trim($POST->nickname) != "") $POST->login = mb_strtolower($parse->text->transliterate($POST->nickname));
				else $parse->msg("У пользователя должен быть логин!", false);
			}
			else $POST->login = $parse->text->transliterate($POST->login);
			if(isset($POST->login) && trim($POST->login) != "" && $db->check_id($POST->login, USERS_TABLE, "login")) $parse->msg("Пользователь с таким логином уже существует", false);

			# email
			if(!isset($POST->email) || trim($POST->email) == "") $parse->msg("Обязательно указывать электронную почту для каждого пользователя", false);
			if(isset($POST->email) && trim($POST->email) != "" && !$parse->valid_email($POST->email)) $parse->msg("Некоректный адрес электронной почты", false);
			if(isset($POST->email) && trim($POST->email) != "" && $db->check_id($POST->email, USERS_TABLE, "email")) $parse->msg("Пользователь с таким адресом почты уже существует", false);

			if(!isset($_SESSION['error'])) {

				#password
				if(!isset($POST->password) || trim($POST->password) == "") $POST->password = $security->create_new_password();
				$salt = $security->create_new_salt();
				$password = $security->hashing_password($POST->password, $salt);

				# personal data
				if(!isset($POST->user_name)) 					$POST->user_name = "";
				if(!isset($POST->user_surname)) 				$POST->user_surname = "";
				if(!isset($POST->user_last_name)) 				$POST->user_last_name = "";

				if(isset($POST->user_birthdate) && $POST->user_birthdate != "") $POST->user_birthdate = $parse->date->rusint_to_unix($POST->user_birthdate);
				else 								$POST->user_birthdate = 0;

				if(!isset($POST->user_sex))					$POST->user_sex = "n";
				elseif($POST->user_sex == "m")					$POST->user_sex = "m";
				elseif($POST->user_sex == "f")					$POST->user_sex = "f";
				else								$POST->user_sex = "n";


				# activation code
				$activation['code'] = randcode(7);


				$db->query("INSERT INTO ".USERS_TABLE." (login, nickname, email, password, salt, date_create, date_update, last_visit, activation_code,
									 user_name, user_surname, user_last_name, user_birthdate, user_sex)
								 VALUES ('".$POST->login."', '".$POST->nickname."', '".$POST->email."', '".$password."', '".$salt."', '".time()."', '".time()."', '".time()."', '".$activation['code']."',
								 	 '".$POST->user_name."', '".$POST->user_surname."', '".$POST->user_last_name."', '".$POST->user_birthdate."', '".$POST->user_sex."')");
				$uid = $db->insert_id();


				# avatar
				$av = $img->upload_image("avatar", "", array($config->users_avatar_width, $config->users_avatar_height), array("filename"=>"av_".$uid, "watermark"=>false, "modify"=>false));
				if(isset($av[0])) $db->query("UPDATE ".USERS_TABLE." SET avatar='".$av[0]."' WHERE uid='".$uid."'");


				# activation link
				$activation['link'] = $site['domain']."/index.php?part=reg&act=activation&email=".$POST->email."&code=".$activation['code'];


				# Уведомление пользователю на электропочту
				$smarty->assign("login", $POST->login);
				$smarty->assign("nickname", $POST->nickname);
				$smarty->assign("email", $POST->email);
				$smarty->assign("password", $POST->password);
				$smarty->assign("activation", $activation);
				$smarty->assign("site", $site);
				$message = $tpl->load_template("email_new_registration", true);

				sendmail($POST->email, "Вы зарегистрировались на сайте ".$site['title'], $message);


				# уведомление
				$parse->msg("Поздравляем с успешной регистрацией. Вам осталось подтвердить адрес электронной почты и вы сможете пользоваться приемуществамми зарегистрированных пользователей.");

				# переход
				go("index.php?part=reg&act=activation&email=".$POST->email);
			}
			else goback();
		}
	}
}

/**
 * Init Class
 */
$reg = new REG;
?>