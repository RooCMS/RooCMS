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
if(!defined('RooCMS') || !defined('INSTALL')) {
	die('Access Denied');
}
//#########################################################


class Install extends IU_Extends {

	# vars
	protected $allowed	= true;		# [bool]	flag for allowed to continue process
	protected $log		= [];		# [array]	array log process actions

	private $action		= "install";	# [string]	alias for identy process
	protected $step		= 1;		# [int]		now use step
	protected $nextstep	= 2;		# [int]		next use step
	protected $steps	= 7;		# [int]		all step in operations
	protected $page_title	= "";
	protected $status	= "";
	protected $noticetext	= "";		# [string]	attention text in head form


	/**
	 * Start
	 */
	public function __construct() {

		global $parse, $tpl, $smarty;

		# init step
		$this->init_step();

		# go
		switch($this->step) {

			case 2:
				$this->step_2();
				break;

			case 3:
				$this->page_title = "Настройка простых параметров сайта";
				$this->status = "Устанвливаем домен и название сайта<br />Указываем электронную почту администратора сайта";
				$this->step_3();
				break;

			case 4:
				$this->page_title = "Настройка соеденения с БД";
				$this->status = "Устанвливаем соедение с базой данных<br />Записываем данные для соеденения с БД";
				$this->step_4();
				break;

			case 5:
				$this->page_title = "Установка БД";
				$this->status = "Устанавливаем схему БД<br />Импортируем таблицы и записи БД";
				$this->step_5();
				break;

			case 6:
				$this->page_title = "Установка логина и пароля администратора";
				$this->status = "Устанавливаем логин и пароль администратора<br />После установки логина и пароля вас переадресует в Панель Управления сайтом.";
				$this->step_6();
				break;

			case 7:
				$this->page_title = "Завешаем установку";
				$this->status = "Установка RooCMS успешно завершена<br />Спасибо, что выбрали RooCMS для своего проекта.";
				$this->step_7();
				break;

			default:
				$this->step_1();
				break;
		}

		# nextstep
		$this->set_nextstep();

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
	 * Easy Settings
	 */
	private function step_3() {

		global $post, $parse, $logger, $files, $site;

		if($this->check_submit() && $this->check_step(3)) {

			# Make sure Site Title is indicated.
			$this->check_data_post("site_title", "Неверно указано название сайта");

			# Check if domain is specified
			$this->check_data_post("site_domain", "Неверно указан адрес сайта");

			# Check that admin email is specified.
			if(!isset($post->site_sysemail) || !$parse->valid_email($post->site_sysemail)) {
				$this->allowed = false;
				$logger->error("Неверно указан адрес электронной почты администратора");
			}

			if($this->allowed) {
				$conffile = _ROOCMS."/config/config.php";

				# open
				$context = file_read($conffile);

				$context = str_ireplace('$site[\'title\'] = "'.$site['title'].'";','$site[\'title\'] = "'.$post->site_title.'";',$context);
				$context = str_ireplace('$site[\'domain\'] = "'.$site['domain'].'";','$site[\'domain\'] = "'.$post->site_domain.'";',$context);
				$context = str_ireplace('$site[\'sysemail\'] = "'.$site['sysemail'].'";','$site[\'sysemail\'] = "'.$post->site_sysemail.'";',$context);

				# write
				$files->write_file($conffile, $context);

				# Remember site title for database
				$_SESSION['site_title'] = $parse->text->html($post->site_title);

				# notice
				$logger->info("Данные успешно записаны:", false);
				$logger->info("Название сайта - ".$parse->text->html($post->site_title));
				$logger->info("Адрес сайта - ".$post->site_domain, false);
				$logger->info("E-mail администратора - ".$post->site_sysemail, false);

				# go next step
				go(SCRIPT_NAME."?step=4");
			}
			else {
				goback();
			}
		}


		$servname = explode(".", $_SERVER['HTTP_HOST']);
		$server_name = (count($servname) == 2) ? "www.".$_SERVER['HTTP_HOST'] : $_SERVER['HTTP_HOST'] ;

		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$path = '/'.implode('/', explode('/', ltrim($path, '/'), -1));

		if($path != "/install") {
			$server_name .= str_ireplace("/install", "", $path)."/";
		}


		$this->log[] = array('Название сайта', '<input type="text" class="form-control" name="site_title" required placeholder="RooCMS">', true, 'Укажите название сайта.');
		$this->log[] = array('Адрес сайта', '<input type="text" class="form-control" name="site_domain" required value="'.$server_name.'">', true, 'Укажите интернет адрес вашего сайта');
		$this->log[] = array('E-Mail Администратора', '<input type="text" class="form-control" name="site_sysemail" placeholder="Ваш@Почтовый.ящик" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,6}$" required>', true, 'Укажите адрес электронной почты администратора сайта.');


		# go next step
		if(isset($site) && trim($site['title']) != "" && trim($site['domain']) != "" && trim($site['sysemail']) != "") {
			go(SCRIPT_NAME."?step=4");
		}
	}


	/**
	 * Setting up database connection
	 */
	private function step_4() {

		global $db, $db_info, $post, $parse, $logger, $files;

		if($this->check_submit() && $this->check_step(4)) {

			# Check that host has been specified for database
			$this->check_data_post("db_info_host", "Не указано соеденение с сервером БД");

			# Check that name of database is specified
			$this->check_data_post("db_info_base", "Не указано название БД");

			# Make sure that database user is specified.
			$this->check_data_post("db_info_user", "Не указан пользователь БД");

			# Make sure that database password is specified.
			$this->check_data_post("db_info_pass", "Не указан пароль пользователя БД");

			# prefix table
			if(!isset($post->db_info_prefix)) {
				$post->db_info_prefix = "";
			}

			# Check mysql connect
			$post->db_info_pass = $parse->text->html($post->db_info_pass);
			if(!$db->check_connect($post->db_info_host, $post->db_info_user, $post->db_info_pass, $post->db_info_base)) {
				$logger->error("Указаны неверные параметры для соеденения с БД", false);
				$this->allowed = false;
				goback();
			}

			if($this->allowed) {

				$_SESSION['db_info_host'] = $post->db_info_host;
				$_SESSION['db_info_user'] = $post->db_info_user;
				$_SESSION['db_info_pass'] = $post->db_info_pass;
				$_SESSION['db_info_base'] = $post->db_info_base;

				$conffile = _ROOCMS."/config/config.php";

				$context = file_read($conffile);

				$context = str_ireplace('$db_info[\'prefix\'] = "'.$db_info['prefix'].'";','$db_info[\'prefix\'] = "'.$post->db_info_prefix.'";',$context);

				$files->write_file($conffile, $context);

				# notice
				$logger->info("Данные для соеденения с БД успешно записаны", false);

				# go next step
				go(SCRIPT_NAME."?step=5");
			}
		}

		if(!$db->db_connect) {
			$this->log[] = array('Адрес сервера БД', '<input type="text" class="form-control" name="db_info_host" required placeholder="localhost" value="localhost">', true, 'Укажите адрес сервера на котором расположена БД');
			$this->log[] = array('Название БД', '<input type="text" class="form-control" name="db_info_base" required>', true, 'Укажите название БД.');
			$this->log[] = array('Имя пользователя БД', '<input type="text" class="form-control" name="db_info_user" required>', true, 'Укажите имя пользователя с правами для подключения к БД.');
			$this->log[] = array('Пароль пользователя БД', '<input type="text" class="form-control" name="db_info_pass" minlength="3" required>', true, 'Укажите пароль пользователя для соеденения с БД');
			$this->log[] = array('Префикс таблиц БД', '<input type="text" class="form-control" name="db_info_prefix" required placeholder="roocms_" value="roocms_">', true, 'Укажите префикс для таблиц БД.');
		}
		else {
			go(SCRIPT_NAME."?step=5");
		}
	}


	/**
	 * Import schema and data to DB
	 */
	private function step_5() {

		global $db, $db_info, $roocms, $post, $parse, $logger, $files, $site;

		$roocms->sess['db_info_pass'] = $parse->text->html($roocms->sess['db_info_pass']);

		if($this->check_submit() && $this->check_step(5)) {

			$conffile = _ROOCMS."/config/config.php";

			$context = file_read($conffile);

			$context = str_ireplace('$db_info[\'host\'] = "'.$db_info['host'].'";','$db_info[\'host\'] = "'.$roocms->sess['db_info_host'].'";',$context);
			$context = str_ireplace('$db_info[\'base\'] = "'.$db_info['base'].'";','$db_info[\'base\'] = "'.$roocms->sess['db_info_base'].'";',$context);
			$context = str_ireplace('$db_info[\'user\'] = "'.$db_info['user'].'";','$db_info[\'user\'] = "'.$roocms->sess['db_info_user'].'";',$context);
			$context = str_ireplace('$db_info[\'pass\'] = "'.$db_info['pass'].'";','$db_info[\'pass\'] = "'.$roocms->sess['db_info_pass'].'";',$context);

			$files->write_file($conffile, $context);

			# notice
			$logger->info("Данные занесены в БД успешно!", false);

			# go next step
			go(SCRIPT_NAME."?step=6");
		}


		# check mysql connect
		if(!$db->check_connect($roocms->sess['db_info_host'], $roocms->sess['db_info_user'], $roocms->sess['db_info_pass'], $roocms->sess['db_info_base'])) {
			$this->allowed = false;
		}

		if($this->allowed) {
			$sql = [];
			require_once "db_mysql_schema.php";

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
	 * Set up administrator login and password
	 */
	private function step_6() {

		global $db, $parse, $logger, $post;

		# check superadmin data in db
		if($db->check_id(1, USERS_TABLE, "uid")) {
			go(SCRIPT_NAME."?step=7");
		}

		if($this->check_submit() && $this->check_step(6)) {

			if(!isset($post->adm_login)) {
				$this->allowed = false;
				$logger->error("Неверно указан логин администратора");
			}

			if(!isset($post->adm_passw)) {
				$this->allowed = false;
				$logger->error("Неверно указан пароль администратора");
			}

			if($this->allowed) {
				$_SESSION['adm_login'] = $parse->text->transliterate($post->adm_login);
				$_SESSION['adm_passw'] = $post->adm_passw;

				# go
				go(SCRIPT_NAME."?step=7");
			}
			else {
				goback();
			}
		}

		$this->log[] = array('Логин администратора', '<input type="text" class="form-control" name="adm_login" required>', true, 'Укажите логин администратора для доступа к Панели Управления сайтом.');
		$this->log[] = array('Пароль администратора', '<input type="text" class="form-control" name="adm_passw" required>', true, 'Введите пароль администратора для доступа к Панели Управления сайтом.');
	}


	/**
	 * End of installation
	 */
	private function step_7() {

		global $db, $security, $roocms, $logger, $site;

		if(!isset($roocms->sess['adm_login']) || trim($roocms->sess['adm_login']) == "" || !isset($roocms->sess['adm_passw']) || trim($roocms->sess['adm_passw']) == "") {
			$logger->error("Сбой при записи логина и пароля администратора сайта");
			go(SCRIPT_NAME."?step=6");
		}


		# write admin acc
		$salt = $security->generate_salt();
		$sk   = randcode(16);
		$upass = $security->hash_password($roocms->sess['adm_passw'], $salt);

		$db->query("INSERT INTO ".USERS_TABLE." (login, nickname, email, title, password, salt, date_create, date_update, last_visit, status, secret_key)
						 VALUES ('".$roocms->sess['adm_login']."', '".$roocms->sess['adm_login']."', '".$site['sysemail']."', 'a', '".$upass."', '".$salt."', '".time()."', '".time()."', '".time()."', '1', '".$sk."')");

		# auto auth
		$_SESSION['uid'] 	= 1;
		$_SESSION['login'] 	= $roocms->sess['adm_login'];
		$_SESSION['title'] 	= "a";
		$_SESSION['nickname'] 	= $roocms->sess['adm_login'];
		$_SESSION['token'] 	= $security->get_token($roocms->sess['adm_login'], $upass, $salt);


		# CONGRULATIONS
		$this->log[] = array('', '<div class="text-center">Поздравляем.<br />Вы успешно завершили установку RooCMS.<br />Текущая версия: '.ROOCMS_VERSION.'</div>', true, '');
		$this->log[] = array('', '<div class="text-center">Не забудьте удалить папку /install/ в целях повышения безопастности вашего сайта.</div>', false, '');

		$servname = explode(".", $_SERVER['SERVER_NAME']);
		$server_name = (count($servname) == 2) ? "www.".$_SERVER['SERVER_NAME']: $_SERVER['SERVER_NAME'] ;
		$hostname = (count($servname) == 2) ? $servname[0] : $servname[1] ;

		# .htaccess
		if(APACHE) {
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
	}


	/**
	 * Check data
	 *
	 * @param string $field - check field
	 * @param string $ermsg - error message
	 */
	private function check_data_post(string $field, string $ermsg) {

		global $post, $logger;

		if(!isset($post->{$field})) {
			$this->allowed = false;
			$logger->error($ermsg);
		}
	}
}
