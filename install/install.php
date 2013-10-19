<?php
/**
* @package      RooCMS
* @subpackage	Installer
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2.4
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
*   along with this program.  If not, see <http://www.gnu.org/licenses/
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
if(!defined('RooCMS') || !defined('INSTALL')) die('Access Denied');
//#########################################################

$install = new Install;

class Install extends Requirement{

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



	/* ####################################################
	 *	Let's begin
	 */
	function __construct() {

		global $GET, $POST, $site, $parse, $tpl, $smarty;

		# init step
		if(isset($GET->_step) && round($GET->_step) > 0) $this->step =& $GET->_step;

		# seo
		$site['title'] = "Установка RooCMS";

		# переход
		switch($this->step) {

			case 1:
				$this->page_title = "Лицензионное соглашение";
				$this->status = "Внимательно прочитайте лицензионное соглашение<br />Помните, что нарушение авторских прав влечет за собой уголовную ответсвенность.";
				require_once _LIB."/license.php";
				$this->noticetext = $license['ru'];
				if($this->allowed && @$_REQUEST['submit']) {
					if(isset($POST->step) && $POST->step == 1) go(SCRIPT_NAME."?step=2");
					else goback();
				}
				break;

			case 2:
				$this->page_title = "Проверка требований RooCMS к хостингу";
				$this->status = "Проверяем версию PHP, MySQL, Apache<br />Проверяем наличие требуемых PHP и Apache расширений";
				$this->check_requirement();
				if($this->allowed && @$_REQUEST['submit']) {
					if(isset($POST->step) && $POST->step == 2) go(SCRIPT_NAME."?step=3");
					else goback();
				}
				break;

			case 3:
				$this->page_title = "Проверка и установка доступов к файлам RooCMS";
				$this->status = "Проверяем доступы и разрешения к важным файлам RooCMS<br />Установка доступов и разрешений для важных файлов RooCMS";
				$this->check_chmod();
				if($this->allowed && @$_REQUEST['submit']) {
					if(isset($POST->step) && $POST->step == 3) go(SCRIPT_NAME."?step=4");
					else goback();
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
				if($this->allowed && @$_REQUEST['submit']) {
					if(isset($POST->step) && $POST->step == 1) go(SCRIPT_NAME."?step=2");
					else goback();
				}
				break;
		}

		if($this->allowed && $this->step != $this->steps) $this->nextstep = $this->step + 1;

		# draw
		$smarty->assign("allowed",		$this->allowed);
		$smarty->assign("action",		$this->action);
		$smarty->assign("page_title", 	$this->page_title);
		$smarty->assign("status", 		$this->status);
		$smarty->assign("step", 		$this->step);
		$smarty->assign("nextstep", 	$this->nextstep);
		$smarty->assign("steps",		$this->steps);
		$smarty->assign("progress",		$parse->percent($this->step, $this->steps));

		$tpl->load_template("top");

		$smarty->assign("log", 			$this->log);
		$smarty->assign("noticetext", 	$this->noticetext);

		$tpl->load_template("body");
	}


	/* ####################################################
	 *	Простые настройки
	 */
	private function step_4() {

		global $site, $POST, $parse;

		if($this->allowed && @$_REQUEST['submit'] && isset($POST->step) && $POST->step == 4) {
			if(!isset($POST->site_title) || trim($POST->site_title) == "") {
				$this->allowed = false;
				$parse->msg("Неверно указано название сайта", false);
			}
			if(!isset($POST->site_domain) || trim($POST->site_domain) == "") {
				$this->allowed = false;
				$parse->msg("Неверно указан адрес сайта", false);
			}
			if(!isset($POST->site_sysemail) || !$parse->valid_email($POST->site_sysemail)) {
				$this->allowed = false;
				$parse->msg("Неверно указан адрес электронной почты администратора", false);
			}

			if($this->allowed) {
				$cf = _ROOCMS."/config/config.php";

				$f = file($cf);

				$context = "";
				for($i=0;$i<=count($f)-1;$i++) {
					$context .= $f[$i];
				}

				$context = str_ireplace('$site[\'title\'] = "";','$site[\'title\'] = "'.$parse->text->html($POST->site_title).'";',$context);
				$context = str_ireplace('$site[\'domain\'] = "";','$site[\'domain\'] = "'.$POST->site_domain.'";',$context);
				$context = str_ireplace('$site[\'sysemail\'] = "";','$site[\'sysemail\'] = "'.$POST->site_sysemail.'";',$context);

				$ecf = fopen($cf, "w+");
				if (is_writable($cf)) {
					fwrite($ecf, $context);
				}
				fclose($ecf);

				# уведомление
				$parse->msg("Данные успешно записаны:", true);
				$parse->msg("Название сайта - ".$parse->text->html($POST->site_title), true);
				$parse->msg("Адрес сайта - ".$POST->site_domain, true);
				$parse->msg("E-mail администратора - ".$POST->site_sysemail, true);

				# переход next step
				go(SCRIPT_NAME."?step=5");
			}
			else goback();
		}

		$this->log[] = array('Название сайта', '<input type="text" class="form-control" name="site_title" required placeholder="RooCMS">', true, 'Укажите название сайта.');
		$this->log[] = array('Адрес сайта', '<input type="text" class="form-control" name="site_domain" required value="http://'.$_SERVER['SERVER_NAME'].'">', true, 'Укажите интернет адрес вашего сайта');
		$this->log[] = array('E-Mail Администратора', '<input type="text" class="form-control" name="site_sysemail" placeholder="Ваш@Почтовый.ящик" pattern="^\s*\w+\.*\w*@\w+\.\w+\s*" required>', true, 'Укажите адрес электронной почты администратора сайта.');


		# переход next step
		if(trim($site['title']) != "" && trim($site['domain']) != "" && trim($site['sysemail']) != "") go(SCRIPT_NAME."?step=5");
	}


	/* ####################################################
	 *	Настраиваем соеденение с БД
	 */
	private function step_5() {

		global $db, $db_info, $POST, $parse;

		if($this->allowed && @$_REQUEST['submit'] && isset($POST->step) && $POST->step == 5) {
			if(!isset($POST->db_info_host) || trim($POST->db_info_host) == "") {
				$this->allowed = false;
				$parse->msg("Не указано соеденение с сервером БД", false);
			}
			if(!isset($POST->db_info_base) || trim($POST->db_info_base) == "") {
				$this->allowed = false;
				$parse->msg("Не указано название БД", false);
			}
			if(!isset($POST->db_info_user) || trim($POST->db_info_user) == "") {
				$this->allowed = false;
				$parse->msg("Не указан пользователь БД", false);
			}
			if(!isset($POST->db_info_pass) || trim($POST->db_info_pass) == "") {
				$this->allowed = false;
				$parse->msg("Не указан пароль пользователя БД", false);
			}
			if(!isset($POST->db_info_prefix) || trim($POST->db_info_prefix) == "") {
				$this->allowed = false;
				$parse->msg("Не указан префикс БД", false);
			}

			if($this->allowed) {

				# check mysql connect
				$POST->db_info_pass = $parse->text->html($POST->db_info_pass);
				if(!$db->check_connect($POST->db_info_host, $POST->db_info_user, $POST->db_info_pass, $POST->db_info_base)) $this->allowed = false;

				if($this->allowed) {

					$_SESSION['db_info_host'] = $POST->db_info_host;
					$_SESSION['db_info_user'] = $POST->db_info_user;
					$_SESSION['db_info_pass'] = $POST->db_info_pass;
					$_SESSION['db_info_base'] = $POST->db_info_base;

					$cf = _ROOCMS."/config/config.php";

					$f = file($cf);

					$context = "";
					for($i=0;$i<=count($f)-1;$i++) {
						$context .= $f[$i];
					}

					/*if(trim($db_info['host']) == "")	$context = str_ireplace('$db_info[\'host\'] = "";','$db_info[\'host\'] = "'.$POST->db_info_host.'";',$context);
					else								$context = str_ireplace('$db_info[\'host\'] = "'.$db_info['host'].'";','$db_info[\'host\'] = "'.$POST->db_info_host.'";',$context);
					if(trim($db_info['base']) == "")	$context = str_ireplace('$db_info[\'base\'] = "";','$db_info[\'base\'] = "'.$POST->db_info_base.'";',$context);
					else								$context = str_ireplace('$db_info[\'base\'] = "'.$db_info['base'].'";','$db_info[\'base\'] = "'.$POST->db_info_base.'";',$context);
					if(trim($db_info['user']) == "")	$context = str_ireplace('$db_info[\'user\'] = "";','$db_info[\'user\'] = "'.$POST->db_info_user.'";',$context);
					else								$context = str_ireplace('$db_info[\'user\'] = "'.$db_info['user'].'";','$db_info[\'user\'] = "'.$POST->db_info_user.'";',$context);
					if(trim($db_info['pass']) == "")	$context = str_ireplace('$db_info[\'pass\'] = "";','$db_info[\'pass\'] = "'.$POST->db_info_pass.'";',$context);
					else								$context = str_ireplace('$db_info[\'pass\'] = "'.$db_info['pass'].'";','$db_info[\'pass\'] = "'.$POST->db_info_pass.'";',$context);*/
					if(trim($db_info['prefix']) == "")	$context = str_ireplace('$db_info[\'prefix\'] = "";','$db_info[\'prefix\'] = "'.$POST->db_info_prefix.'";',$context);
					else								$context = str_ireplace('$db_info[\'prefix\'] = "'.$db_info['prefix'].'";','$db_info[\'prefix\'] = "'.$POST->db_info_prefix.'";',$context);

					$ecf = fopen($cf, "w+");
					if (is_writable($cf)) {
						fwrite($ecf, $context);
					}
					fclose($ecf);

					# уведомление
					$parse->msg("Данные для соеденения с БД успешно записаны", true);

					# переход next step
					go(SCRIPT_NAME."?step=6");
				}
				else {
					$parse->msg("Указаны неверные параметры для соеденения с БД", false);
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


	/* ####################################################
	 *	Импортируем данные в БД
	 */
	private function step_6() {

		global $db, $db_info, $roocms, $POST, $parse, $site;

		$roocms->sess['db_info_pass'] = $parse->text->html($roocms->sess['db_info_pass']);

		if($this->allowed && @$_REQUEST['submit'] && isset($POST->step) && $POST->step == 6) {
			$cf = _ROOCMS."/config/config.php";

			$f = file($cf);

			$context = "";
			for($i=0;$i<=count($f)-1;$i++) {
				$context .= $f[$i];
			}

			if(trim($db_info['host']) == "")	$context = str_ireplace('$db_info[\'host\'] = "";','$db_info[\'host\'] = "'.$roocms->sess['db_info_host'].'";',$context);
			else								$context = str_ireplace('$db_info[\'host\'] = "'.$db_info['host'].'";','$db_info[\'host\'] = "'.$roocms->sess['db_info_host'].'";',$context);
			if(trim($db_info['base']) == "")	$context = str_ireplace('$db_info[\'base\'] = "";','$db_info[\'base\'] = "'.$roocms->sess['db_info_base'].'";',$context);
			else								$context = str_ireplace('$db_info[\'base\'] = "'.$db_info['base'].'";','$db_info[\'base\'] = "'.$roocms->sess['db_info_base'].'";',$context);
			if(trim($db_info['user']) == "")	$context = str_ireplace('$db_info[\'user\'] = "";','$db_info[\'user\'] = "'.$roocms->sess['db_info_user'].'";',$context);
			else								$context = str_ireplace('$db_info[\'user\'] = "'.$db_info['user'].'";','$db_info[\'user\'] = "'.$roocms->sess['db_info_user'].'";',$context);
			if(trim($db_info['pass']) == "")	$context = str_ireplace('$db_info[\'pass\'] = "";','$db_info[\'pass\'] = "'.$roocms->sess['db_info_pass'].'";',$context);
			else								$context = str_ireplace('$db_info[\'pass\'] = "'.$db_info['pass'].'";','$db_info[\'pass\'] = "'.$roocms->sess['db_info_pass'].'";',$context);

			$ecf = fopen($cf, "w+");
			if (is_writable($cf)) {
				fwrite($ecf, $context);
			}
			fclose($ecf);

			# уведомление
			$parse->msg("Данные занесены в БД успешно", true);

			# переход next step
			go(SCRIPT_NAME."?step=7");
		}


		# check mysql connect
		if(!$db->check_connect($roocms->sess['db_info_host'], $roocms->sess['db_info_user'], $roocms->sess['db_info_pass'], $roocms->sess['db_info_base'])) $this->allowed = false;

		if($this->allowed) {
			require_once _LIB."/mysql_schema.php";

			foreach($sql AS $k=>$v) {
				mysql_query($v);

				if(mysql_errno() == 0) $this->log[] = array('Операция', $k, true, '');
				else {
					$this->log[] = array('Операция', $k, false, '# '.mysql_errno().'<br />- '.mysql_error());
					$this->allowed = false;
				}
			}
		}
	}


	/* ####################################################
	 *	Установка логина и пароля администратора
	 */
	private function step_7() {

		global $parse, $adm, $POST;

		if(trim($adm['login']) != "" && trim($adm['passw']) != "") go(SCRIPT_NAME."?step=8");

		if($this->allowed && @$_REQUEST['submit'] && isset($POST->step) && $POST->step == 7) {
			if(!isset($POST->adm_login) || trim($POST->adm_login) == "") {
				$this->allowed = false;
				$parse->msg("Неверно указан логин администратора", false);
			}
			if(!isset($POST->adm_passw) || trim($POST->adm_passw) == "") {
				$this->allowed = false;
				$parse->msg("Неверно указан пароль администратора", false);
			}

			if($this->allowed) {
				$_SESSION['adm_login'] = $POST->adm_login;
				$_SESSION['adm_passw'] = $POST->adm_passw;

				# переход
				go(SCRIPT_NAME."?step=8");
			}
			else goback();
		}

		if(trim($adm['login']) == "") $this->log[] = array('Логин администратора', '<input type="text" class="form-control" name="adm_login" required>', true, 'Укажите логин администратора для доступа к Панели Управления сайтом.');
		if(trim($adm['passw']) == "") $this->log[] = array('Пароль администратора', '<input type="text" class="form-control" name="adm_passw" required>', true, 'Введите пароль администратора для доступа к Панели Управления сайтом.');
	}


	/* ####################################################
	 *	Завершение установки RooCMS
	 */
	private function step_8() {

		global $roocms, $adm, $parse;

		if(!isset($roocms->sess['adm_login']) || trim($roocms->sess['adm_login']) == "" || !isset($roocms->sess['adm_passw']) || trim($roocms->sess['adm_passw']) == "") {
			$parse->msg("Сбой при записи логина и пароля администратора сайта", false);
			go(SCRIPT_NAME."?step=7");
		}

		$cf = _ROOCMS."/config/config.php";

		$f = file($cf);

		$context = "";
		for($i=0;$i<=count($f)-1;$i++) {
			$context .= $f[$i];
		}

		if(trim($adm['login']) == "")	$context = str_ireplace('$adm[\'login\'] = "";','$adm[\'login\'] = "'.$roocms->sess['adm_login'].'";',$context);
		else							$context = str_ireplace('$adm[\'login\'] = "'.$adm['login'].'";','$adm[\'login\'] = "'.$roocms->sess['adm_login'].'";',$context);
		if(trim($adm['passw']) == "")	$context = str_ireplace('$adm[\'passw\'] = "";','$adm[\'passw\'] = "'.$roocms->sess['adm_passw'].'";',$context);
		else							$context = str_ireplace('$adm[\'passw\'] = "'.$adm['passw'].'";','$adm[\'passw\'] = "'.$roocms->sess['adm_passw'].'";',$context);

		$ecf = fopen($cf, "w+");
		if (is_writable($cf)) {
			fwrite($ecf, $context);
		}
		else {
			$parse->msg("Сбой при записи логина и пароля администратора сайта", false);
			go(SCRIPT_NAME."?step=7");
		}
		fclose($ecf);

		$this->log[] = array('', '<center>Поздравляем.<br />Вы успешно завершили установку RooCMS.<br />Текущая версия: '.ROOCMS_VERSION.'</center>', true, '');
		$this->log[] = array('', '<center>Не забудьте удалить папку /install/ в целях безопастности вашего сайта.</center>', false, '');
	}
}

?>