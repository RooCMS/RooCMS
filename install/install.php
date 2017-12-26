<?php
/**
 *   RooCMS - Russian Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
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

/**
* @package      RooCMS
* @subpackage	Installer
* @author       alex Roosso
* @copyright    2010-2018 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.6.2
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('INSTALL')) {
	die('Access Denied');
}
//#########################################################


class Install extends Requirement {

	# vars
	protected $allowed	= true;		# [bool]	flag for allowed to continue process
	protected $log		= array();	# [array]	array log process actions

	private $action		= "install";	# [string]	alias for identy process
	private $step		= 1;		# [int]		now use step
	private $nextstep	= 2;		# [int]		next use step
	private $steps		= 8;		# [int]		all step in operations
	private $page_title	= "";
	private $status		= "";
	private $noticetext	= "";		# [string]	attention text in head form



	/**
	 * Доктор, начнем операцию...
	 */
	public function __construct() {

		global $get, $site, $parse, $tpl, $smarty;

		# init step
		if(isset($get->_step) && round($get->_step) > 0) {
			$this->step =& $get->_step;
		}

		# переход
		switch($this->step) {

			case 2:
				$this->page_title = "Проверка требований RooCMS к хостингу";
				$this->status = "Проверяем версию PHP, MySQL, Apache<br />Проверяем наличие требуемых PHP и Apache расширений";
				$this->check_requirement();
				if($this->check_submit()) {
					if($this->check_step(2)) {
						go(SCRIPT_NAME."?step=3");
					}
					else {
						goback();
					}
				}
				break;

			case 3:
				$this->page_title = "Проверка и установка доступов к файлам RooCMS";
				$this->status = "Проверяем доступы и разрешения к важным файлам RooCMS<br />Установка доступов и разрешений для важных файлов RooCMS";
				$this->check_chmod();
				if($this->check_submit()) {
					if($this->check_step(3)) {
						go(SCRIPT_NAME."?step=4");
					}
					else {
						goback();
					}
				}
				break;

			case 4:
				$this->page_title = "Настройка простых параметров сайта";
				$this->status = "Устанвливаем домен и название сайта<br />Указываем электронную почту администратора сайта";
				$this->step_4();
				break;

			case 5:
				$this->page_title = "Настройка соеденения с БД";
				$this->status = "Устанвливаем соедение с базой данных<br />Записываем данные для соеденения с БД";
				$this->step_5();
				break;

			case 6:
				$this->page_title = "Установка БД";
				$this->status = "Устанавливаем схему БД<br />Импортируем таблицы и записи БД";
				$this->step_6();
				break;

			case 7:
				$this->page_title = "Установка логина и пароля администратора";
				$this->status = "Устанавливаем логин и пароль администратора<br />После установки логина и пароля вас переадресует в Панель Управления сайтом.";
				$this->step_7();
				break;

			case 8:
				$this->page_title = "Завешаем установку";
				$this->status = "Установка RooCMS успешно завершена<br />Спасибо, что выбрали RooCMS для своего проекта.";
				$this->step_8();
				break;

			default:
				$this->page_title = "Лицензионное соглашение";
				$this->status = "Внимательно прочитайте лицензионное соглашение<br />Помните, что нарушение авторских прав влечет за собой уголовную ответсвенность.";
				require_once _LIB."/license.php";
				$this->noticetext = $license['ru'];
				if($this->check_submit()) {
					if($this->check_step(1)) {
						go(SCRIPT_NAME."?step=2");
					}
					else {
						goback();
					}
				}
				break;
		}

		if($this->allowed && $this->step != $this->steps) {
			$this->nextstep = $this->step + 1;
		}

		# draw
		$smarty->assign("allowed",	$this->allowed);
		$smarty->assign("action",	$this->action);
		$smarty->assign("page_title", 	$this->page_title);
		$smarty->assign("status", 	$this->status);
		$smarty->assign("step", 	$this->step);
		$smarty->assign("nextstep", 	$this->nextstep);
		$smarty->assign("steps",	$this->steps);
		$smarty->assign("progress",	$parse->percent($this->step, $this->steps));

		$tpl->load_template("top");

		$smarty->assign("log", 		$this->log);
		$smarty->assign("noticetext", 	$this->noticetext);

		$tpl->load_template("body");
	}


	/**
	 * Простые настройки
	 */
	private function step_4() {

		global $POST, $parse, $logger, $files, $site;

		if($this->check_submit() && $this->check_step(4)) {

			# Проверяем введен ли заголовок сайта
			$this->check_data_post("site_title", "Неверно указано название сайта");

			# Проверяем указан ли домен сайта
			$this->check_data_post("site_domain", "Неверно указан адрес сайта");

			# Проверяем указан ли почтовый ящик администратора сайта
			if(!isset($POST->site_sysemail) || !$parse->valid_email($POST->site_sysemail)) {
				$this->allowed = false;
				$logger->error("Неверно указан адрес электронной почты администратора");
			}

			if($this->allowed) {
				$conffile = _ROOCMS."/config/config.php";

				$context = file_read($conffile);

				$context = str_ireplace('$site[\'title\'] = "'.$site['title'].'";','$site[\'title\'] = "'.$POST->site_title.'";',$context);
				$context = str_ireplace('$site[\'domain\'] = "'.$site['domain'].'";','$site[\'domain\'] = "'.$POST->site_domain.'";',$context);
				$context = str_ireplace('$site[\'sysemail\'] = "'.$site['sysemail'].'";','$site[\'sysemail\'] = "'.$POST->site_sysemail.'";',$context);

				$files->write_file($conffile, $context);


				# запоминаем название сайта для БД
				$_SESSION['site_title'] = $parse->text->html($POST->site_title);

				# уведомление
				$logger->info("Данные успешно записаны:", false);
				$logger->info("Название сайта - ".$parse->text->html($POST->site_title, false));
				$logger->info("Адрес сайта - ".$POST->site_domain, false);
				$logger->info("E-mail администратора - ".$POST->site_sysemail, false);

				# переход next step
				go(SCRIPT_NAME."?step=5");
			}
			else goback();
		}

		$servname = explode(".", $_SERVER['SERVER_NAME']);
		$server_name = (count($servname) == 2) ? "www.".$_SERVER['SERVER_NAME']: $_SERVER['SERVER_NAME'] ;

		$this->log[] = array('Название сайта', '<input type="text" class="form-control" name="site_title" required placeholder="RooCMS">', true, 'Укажите название сайта.');
		$this->log[] = array('Адрес сайта', '<input type="text" class="form-control" name="site_domain" required value="http://'.$server_name.'">', true, 'Укажите интернет адрес вашего сайта');
		$this->log[] = array('E-Mail Администратора', '<input type="text" class="form-control" name="site_sysemail" placeholder="Ваш@Почтовый.ящик" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,6}$" required>', true, 'Укажите адрес электронной почты администратора сайта.');


		# переход next step
		if(isset($site) && trim($site['title']) != "" && trim($site['domain']) != "" && trim($site['sysemail']) != "") {
			go(SCRIPT_NAME."?step=5");
		}
	}


	/**
	 * Настраиваем соеденение с БД
	 */
	private function step_5() {

		global $db, $db_info, $POST, $parse, $logger, $files;

		if($this->check_submit() && $this->check_step(5)) {

			# Проверяем указан ли хост БД
			$this->check_data_post("db_info_host", "Не указано соеденение с сервером БД");

			# Проверяем указано ли название БД
			$this->check_data_post("db_info_base", "Не указано название БД");

			# Проверяем указан ли пользователь для соеденения с БД
			$this->check_data_post("db_info_user", "Не указан пользователь БД");

			# Проверяем указан ли пароль от БД
			$this->check_data_post("db_info_pass", "Не указан пароль пользователя БД");

			# Префикс таблич
			if(!isset($POST->db_info_prefix)) {
				$POST->db_info_prefix = "";
			}

			if($this->allowed) {

				# check mysql connect
				$POST->db_info_pass = $parse->text->html($POST->db_info_pass);
				if(!$db->check_connect($POST->db_info_host, $POST->db_info_user, $POST->db_info_pass, $POST->db_info_base)) {
					$this->allowed = false;
				}

				if($this->allowed) {

					$_SESSION['db_info_host'] = $POST->db_info_host;
					$_SESSION['db_info_user'] = $POST->db_info_user;
					$_SESSION['db_info_pass'] = $POST->db_info_pass;
					$_SESSION['db_info_base'] = $POST->db_info_base;

					$conffile = _ROOCMS."/config/config.php";

					$context = file_read($conffile);

					$context = str_ireplace('$db_info[\'prefix\'] = "'.$db_info['prefix'].'";','$db_info[\'prefix\'] = "'.$POST->db_info_prefix.'";',$context);

					$files->write_file($conffile, $context);

					# уведомление
					$logger->info("Данные для соеденения с БД успешно записаны", false);

					# переход next step
					go(SCRIPT_NAME."?step=6");
				}
				else {
					$logger->error("Указаны неверные параметры для соеденения с БД", false);
					goback();
				}
			}
		}

		if(!$db->db_connect) {
			$this->log[] = array('Адрес сервера БД', '<input type="text" class="form-control" name="db_info_host" required placeholder="localhost" value="localhost">', true, 'Укажите адрес сервера на котором расположена БД');
			$this->log[] = array('Название БД', '<input type="text" class="form-control" name="db_info_base" required>', true, 'Укажите название БД.');
			$this->log[] = array('Имя пользователя БД', '<input type="text" class="form-control" name="db_info_user" required>', true, 'Укажите имя пользователя с правами для подключения к БД.');
			$this->log[] = array('Пароль пользователя БД', '<input type="text" class="form-control" name="db_info_pass" required>', true, 'Укажите пароль пользователя для соеденения с БД');
			$this->log[] = array('Префикс таблиц БД', '<input type="text" class="form-control" name="db_info_prefix" required placeholder="roocms_" value="roocms_">', true, 'Укажите префикс для таблиц БД.');
		}
		else go(SCRIPT_NAME."?step=6");
	}


	/**
	 * Импортируем данные в БД
	 */
	private function step_6() {

		global $db, $db_info, $roocms, $POST, $parse, $logger, $files, $site;

		$roocms->sess['db_info_pass'] = $parse->text->html($roocms->sess['db_info_pass']);

		if($this->check_submit() && $this->check_step(6)) {

			$conffile = _ROOCMS."/config/config.php";

			$context = file_read($conffile);

			$context = str_ireplace('$db_info[\'host\'] = "'.$db_info['host'].'";','$db_info[\'host\'] = "'.$roocms->sess['db_info_host'].'";',$context);
			$context = str_ireplace('$db_info[\'base\'] = "'.$db_info['base'].'";','$db_info[\'base\'] = "'.$roocms->sess['db_info_base'].'";',$context);
			$context = str_ireplace('$db_info[\'user\'] = "'.$db_info['user'].'";','$db_info[\'user\'] = "'.$roocms->sess['db_info_user'].'";',$context);
			$context = str_ireplace('$db_info[\'pass\'] = "'.$db_info['pass'].'";','$db_info[\'pass\'] = "'.$roocms->sess['db_info_pass'].'";',$context);

			$files->write_file($conffile, $context);

			# уведомление
			$logger->info("Данные занесены в БД успешно!", false);

			# переход next step
			go(SCRIPT_NAME."?step=7");
		}


		# check mysql connect
		if(!$db->check_connect($roocms->sess['db_info_host'], $roocms->sess['db_info_user'], $roocms->sess['db_info_pass'], $roocms->sess['db_info_base'])) {
			$this->allowed = false;
		}

		if($this->allowed) {
			require_once _LIB."/mysql_schema.php";

			$mysqli = new mysqli($roocms->sess['db_info_host'], $roocms->sess['db_info_user'], $roocms->sess['db_info_pass'], $roocms->sess['db_info_base']);

			foreach($sql AS $k=>$v) {
				$mysqli->query($v);

				if($mysqli->errno == 0) {
					$this->log[] = array('Операция', $k, true, '');
				}
				else {
					$this->log[] = array('Операция', $k, false, '# '.$mysqli->errno.'<br />- '.$mysqli->error);
					$this->allowed = false;
				}
			}
		}
	}


	/**
	 * Установка логина и пароля администратора
	 */
	private function step_7() {

		global $db, $parse, $logger, $POST;

		if($db->check_id(1, USERS_TABLE, "uid")) {
			go(SCRIPT_NAME."?step=8");
		}

		if($this->check_submit() && $this->check_step(7)) {

			if(!isset($POST->adm_login)) {
				$this->allowed = false;
				$logger->error("Неверно указан логин администратора");
			}

			if(!isset($POST->adm_passw)) {
				$this->allowed = false;
				$logger->error("Неверно указан пароль администратора");
			}

			if($this->allowed) {
				$_SESSION['adm_login'] = $parse->text->transliterate($POST->adm_login);
				$_SESSION['adm_passw'] = $POST->adm_passw;

				# переход
				go(SCRIPT_NAME."?step=8");
			}
			else goback();
		}

		$this->log[] = array('Логин администратора', '<input type="text" class="form-control" name="adm_login" required>', true, 'Укажите логин администратора для доступа к Панели Управления сайтом.');
		$this->log[] = array('Пароль администратора', '<input type="text" class="form-control" name="adm_passw" required>', true, 'Введите пароль администратора для доступа к Панели Управления сайтом.');
	}


	/**
	 * Завершение установки RooCMS
	 */
	private function step_8() {

		global $db, $security, $roocms, $logger, $site;

		if(!isset($roocms->sess['adm_login']) || trim($roocms->sess['adm_login']) == "" || !isset($roocms->sess['adm_passw']) || trim($roocms->sess['adm_passw']) == "") {
			$logger->error("Сбой при записи логина и пароля администратора сайта");
			go(SCRIPT_NAME."?step=7");
		}


		# write admin acc
		$salt = $security->create_new_salt();
		$upass = $security->hashing_password($roocms->sess['adm_passw'], $salt);

		$db->query("INSERT INTO ".USERS_TABLE." (login, nickname, email, title, password, salt, date_create, date_update, last_visit, status)
						 VALUES ('".$roocms->sess['adm_login']."', '".$roocms->sess['adm_login']."', '".$site['sysemail']."', 'a', '".$upass."', '".$salt."', '".time()."', '".time()."', '".time()."', '1')");

		# auto auth
		$_SESSION['uid'] 	= 1;
		$_SESSION['login'] 	= $roocms->sess['adm_login'];
		$_SESSION['title'] 	= "a";
		$_SESSION['nickname'] 	= $roocms->sess['adm_login'];
		$_SESSION['token'] 	= $security->hashing_token($roocms->sess['adm_login'], $upass, $salt);


		# CONGRULATIONS
		$this->log[] = array('', '<div class="text-center">Поздравляем.<br />Вы успешно завершили установку RooCMS.<br />Текущая версия: '.ROOCMS_VERSION.'</div>', true, '');
		$this->log[] = array('', '<div class="text-center">Не забудьте удалить папку /install/ в целях повышения безопастности вашего сайта.</div>', false, '');

		$confperms = array('path' => _ROOCMS.'/config/config.php', 'chmod' => '0644');

		@chmod($confperms['path'], $confperms['chmod']);
		if(!@chmod($confperms['path'], $confperms['chmod'])) {
			$this->log[] = array("", "Не удалось изменить доступ к файлу ".$confperms." вам потребуется установить доступ вручную через FTP. Установить доступ <b>0644</b>", false, "");
		}

		$servname = explode(".", $_SERVER['SERVER_NAME']);
		$server_name = (count($servname) == 2) ? "www.".$_SERVER['SERVER_NAME']: $_SERVER['SERVER_NAME'] ;
		$hostname = (count($servname) == 2) ? $servname[0] : $servname[1] ;


		$this->log[] = array('', '<div class="alert alert-info" style="margin-top: 10px;"><b class="label label-primary">Внимание!</b>
						<br />Отредактируйте файл <code>.htaccess</code> расположенный в корне сайта
						<br />Для этого откройте его любым текстовым редактором
						<br />Выделите стрки с 6 по 13 включительно (они закоментированны знаком <code>#</code>) и замените их на эти:
						<br />
						<pre>
&lt;IfModule mod_rewrite.c&gt;
	RewriteEngine on
	RewriteBase /
	RewriteCond %{HTTP_HOST} ^'.$hostname.'
	RewriteRule (.*) http://'.$server_name.'/$1 [R=301,L]
	RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /index\.php\ HTTP/
	RewriteRule ^index\.php$ http://'.$server_name.'/ [R=301,L]
&lt;/IfModule&gt;</pre>
						Это важно для поисковой оптимизации, но вовсе не обязательно.</div>', false, '');
	}


	/**
	 * Check data $POST->step
	 *
	 * @param int $n - step
	 *
	 * @return bool
	 */
	private function check_step($n) {

		global $POST;

		if(isset($POST->step) && $POST->step == $n) {
			return true;
		}
		else {
			return false;
		}
	}


	/**
	 * Check used $POST->submit
	 *
	 * @return bool
	 */
	private function check_submit() {

		global $POST;

		if($this->allowed && isset($POST->submit)) {
			return true;
		}
		else {
			return false;
		}
	}


	/**
	 * Проверяем вводимые данные в прцоессе установки
	 *
	 * @param string $field - проверяемое поле
	 * @param string $ermsg - сообщение об ошибке
	 */
	private function check_data_post($field, $ermsg) {

		global $POST, $logger;

		if(!isset($POST->{$field})) {
			$this->allowed = false;
			$logger->error($ermsg);
		}
	}
}