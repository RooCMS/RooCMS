<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2019 alexandr Belov aka alex Roosso. All rights reserved.
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
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class Users
 */
class Users extends Security {

	# user uniq data
	public	$uid		= 0;		# user id
	public	$login		= "";		# user login
	public	$nickname	= "";		# user nickname
	public	$avatar		= "";		# user avatar
	public	$email		= "";		# user nickname
	public	$title		= "u";		# user title
	public	$gid		= 0;		# user group id
	public	$gtitle		= "";		# user group title
	public	$token		= "";		# user security token

	# user ban status
	public  $ban		= 0;		# ban status
	public  $ban_reason	= "";		# ban reason
	public  $ban_expiried	= 0;		# ban date expiried (unixtimestamp)

	public	$userdata	= array('uid'=>0);

	# user global data
	private	$usersession	= "";		# user session
	private $userip		= "";		# user ip address
	private	$useragent	= "";		# user agent string
	private $referer	= "";		# user referer



	/**
	 * Work your magic
	 */
	public function __construct() {

		global $roocms;


		# get user data
		$this->usersession	&= $roocms->usersession;
		$this->userip		&= $roocms->userip;
		$this->useragent 	&= $roocms->useragent;
		$this->referer		&= $roocms->referer;


		# init user
		$this->init_user();


		if($this->uid != 0) {
			# control user data for security
			$this->control_userdata();

			# update users info
			$this->update_user_time_last_visit($this->uid);
		}
	}


	/**
	 * Получаем персональные данные пользователя
	 */
	private function init_user() {

		global $db, $roocms, $parse;

		if(isset($roocms->sess['login']) && trim($roocms->sess['login']) != "" && $db->check_id($roocms->sess['login'], USERS_TABLE, "login", "status='1'") && isset($roocms->sess['token']) && strlen($roocms->sess['token']) == 32) {

			# get data
			$q    = $db->query("SELECT u.uid, u.gid, u.login, u.nickname, u.avatar, u.email, u.mailing,
 							u.user_name, u.user_surname, u.user_last_name, u.user_birthdate, u.user_sex, u.user_slogan,
							u.title, u.password, u.salt, u.ban, u.ban_reason, u.ban_expiried,
							g.title as gtitle
						FROM ".USERS_TABLE." AS u
						LEFT JOIN ".USERS_GROUP_TABLE." AS g ON (g.gid = u.gid)
						WHERE u.login='".$roocms->sess['login']."' AND u.status='1'");
			$data = $db->fetch_assoc($q);

			# uid
			$this->uid	= $data['uid'];
			# gid
			$this->gid	= $data['gid'];
			# gtitle
			$this->gtitle	= $data['gtitle'];
			# login
			$this->login	= $data['login'];
			# title
			$this->title	= $data['title'];
			# nickname
			$this->nickname	= $data['nickname'];
			# avatar
			$this->avatar	= $data['avatar'];
			# email
			$this->email	= $data['email'];
			# ban
			$this->ban		= $data['ban'];
			$this->ban_reason	= $data['ban_reason'];
			$this->ban_expiried	= $data['ban_expiried'];

			# array userdata
			$this->userdata = array(
				'uid'			=> $data['uid'],
				'gid'			=> $data['gid'],
				'gtitle'		=> $data['gtitle'],
				'login'			=> $data['login'],
				'nickname'		=> $data['nickname'],
				'avatar'		=> $data['avatar'],
				'email'			=> $data['email'],
				'mailing'		=> $data['mailing'],
				'title'			=> $data['title'],
				'user_name'		=> $data['user_name'],
				'user_surname'		=> $data['user_surname'],
				'user_last_name'	=> $data['user_last_name'],
				'user_birthdate'	=> $parse->date->unix_to_rus($data['user_birthdate']),
				'user_birthdaten'	=> date("d.m.Y", $data['user_birthdate']),
				'user_sex'		=> $data['user_sex'],
				'user_slogan'		=> $parse->text->br($data['user_slogan']),
				'ban'			=> $data['ban'],
				'ban_reason'		=> $data['ban_reason'],
				'ban_expiried'		=> $parse->date->unix_to_rus($data['ban_expiried'])
			);


			# security token
			$this->token	= $this->hashing_token($roocms->sess['login'], $data['password'], $data['salt']);
		}
	}


	/**
	 * Обновляем простую информацию пользователя, вроде времени последнего визита на сайт.
	 *
	 * @param int $uid - уникальные идентификатор пользователя
	 */
	private function update_user_time_last_visit($uid) {

		global $db, $roocms;

		# update time last visited
		$db->query("UPDATE ".USERS_TABLE." SET last_visit='".time()."', user_ip='".$roocms->userip."' WHERE uid='".$uid."' AND status='1'");
	}


	/**
	 * Функция получения данных о пользователе.
	 *
	 * @param int $uid идентификатор пользователя
	 *
	 * @return array user data
	 */
	public function get_user_data($uid) {

		global $db, $parse;

		# default
		$row = array(
			'uid' => 0,
		);

		# check and get data
		if($db->check_id($uid, USERS_TABLE, "uid")) {
			$q = $db->query("SELECT uid, nickname, user_slogan, avatar, user_sex FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$row = $db->fetch_assoc($q);
			$row['slogan'] = $parse->text->br($row['user_slogan']);
		}

		return $row;
	}


	/**
	 * Функция получения списка пользователей.
	 *
	 * @param int   $status  - Текущий статус пользователя: 1 включенные, 0 отключенные, -1 все
	 * @param int   $ban     - Текущий бан пользователя: 0 без бана, 1 с баном, -1 все
	 * @param int   $mailing - Является пользователь подписчиком рассылки: 0 нет, 1 да, -1 все
	 * @param array $users   - массив с идентификаторами запрашиваемых пользователей.
	 * @param bool  $email   - Флаг запрашивать ли почтовые адреса пользователей
	 *
	 * @return array
	 */
	public function get_userlist($status=-1, $ban=-1, $mailing=-1, $users=[], $email=false) {

		global $db;

		# condition
		$cond = "";

		$arcond = array("status"=>$status, "ban"=>$ban, "mailing"=>$mailing);

		foreach($arcond AS $k=>$v) {

			if($v == 0 || $v == 1) {

				if($cond != "") {
					$cond .= " AND ";
				}

				$cond .= " ".$k."='".$v."' ";
			}
		}

		if(!empty($users)) {

			if($cond != "") {
				$cond .= " AND ";
			}

			$cond .= " ( ";

			$i = 0;
			foreach($users AS $k=>$v) {
				if($i != 0) {
					$cond .= " OR ";
				}

				$cond .= " uid='".$v."'";

				$i++;
			}

			$cond .= " ) ";
		}

		# condition formating
		if($cond != "") {
			$cond = "WHERE".$cond;
		}

		# email
		$query = "";
		if($email) {
			$query = ", email";
		}

		# получаем список пользователей
		$userlist = [];
		$q = $db->query("SELECT uid, nickname, user_slogan, avatar, user_sex".$query." FROM ".USERS_TABLE." ".$cond." ORDER BY nickname ASC");
		while($row = $db->fetch_assoc($q)) {
			$userlist[$row['uid']] = $row;
		}

		return $userlist;
	}


	/**
	 * Проверяем поля на уникальность
	 *
	 * ВНИМАНИЕ! Не расчитывайте на эту функцию, она временная.
	 *
	 * @param string $field   - поле
	 * @param string $name    - значение поля
	 * @param string $without - Выражение исключения для mysql запроса
	 * @param string $table	  - Таблица для проверки
	 *
	 * @return bool $res - true - если значение не уникально, false - если значение уникально
	 */
	public function check_field($field, $name, $without="", $table=USERS_TABLE) {

		global $db;

		$res = false;

		if(trim($without) != trim($name)) {

			$without = trim($without);
			$w = $field."!='".$without."'";

			if(!$db->check_id($name, $table, $field, $w)) {
				$res = true;
			}
		}
		else {
			$res = true;
		}

		return $res;
	}


	/**
	 * Функция проверки почты пользователя.
	 * Проверяем на дубли и корректность.
	 *
	 * @param string $email - адрес электронной почты пользователя.
	 */
	public function valid_user_email($email) {

		global $db, $logger, $parse;

		if(isset($email) && trim($email) != "") {
			if(!$parse->valid_email($email)) {
				$logger->error("Некорректный адрес электронной почты", false);
			}

			if($db->check_id($email, USERS_TABLE, "email")) {
				$logger->error("Пользователь с таким адресом почты уже есть в нашей базе данных", false);
			}
		}
		else {
			$logger->error("Электронная почта обязательная для каждого пользователя", false);
		}
	}


	/**
	 * Функция проверяет Никнейм на уникальность.
	 * В случае повторения добавляет к никнейму несколько цифр.
	 *
	 * ВНИМАНИЕ! Не расчитывайте на эту функцию. Она временная.
	 *
	 * @param string $nickname - Никнейм
	 *
	 * @return string
	 */
	public function uniq_nickname($nickname) {

		global $db, $logger;

		static $nick = NULL;

		if(!isset($nick)) {
			$nick = $nickname;
		}

		# Проверяем на уникальность
		if($db->check_id($nickname, USERS_TABLE, "nickname")) {
			$nickname = $this->uniq_nickname($nickname.randcode(2,"0123456789"));
			$notice = "Псевдоним ".$nick." недоступен. Был присвоен псевдоним ".$nickname;
		}

		# уведомление если оно есть
		if(isset($notice)) {
			$logger->info($notice, false);
		}

		return $nickname;
	}


	/**
	 * Проверяем персональные данные пользователя при попытки их создания и обновления
	 */
	public function correct_personal_data() {

		global $post, $parse;

		# user name/surname/last_name
		if(!isset($post->user_name)) {
			$post->user_name = "";
		}
		if(!isset($post->user_surname)) {
			$post->user_surname = "";
		}
		if(!isset($post->user_last_name)) {
			$post->user_last_name = "";
		}

		# user birthdate
		if(isset($post->user_birthdate) && $post->user_birthdate != "") {
			$post->user_birthdate = $parse->date->rusint_to_unix($post->user_birthdate);
		}
		else {
			$post->user_birthdate = 0;
		}

		#check user sex
		switch($post->user_sex) {
			case 'm':
				$post->user_sex = "m";
				break;
			case 'f':
				$post->user_sex = "f";
				break;
			default:
				$post->user_sex = "n";
				break;
		}

		# mailing
		if(!isset($post->mailing) || round($post->mailing) != 1) {
			$post->mailing = 0;
		}

		# check slogan
		if(!isset($post->user_slogan)) {
			$post->user_slogan = "";
		}

		$post->user_slogan = $parse->text->clearhtml($post->user_slogan);
	}


	/**
	 * Функция проверяет никнейм пользователя указанный во время создания учетной записи
	 */
	public function check_create_nickname() {

		global $post;

		# Если никнейм не ввведен, делаем никнем из логина
		if(!isset($post->nickname) && isset($post->login)) {
			$post->nickname = mb_ucfirst($post->nickname);
		}

		# теперь проверяем на никальность
		if(isset($post->nickname)) {
			$post->nickname = $this->uniq_nickname($post->nickname);
		}
	}


	/**
	 * Функция проверяет логин пользователя указанный во время создания учетной записи
	 */
	public function check_create_login() {

		global $db, $post, $parse, $logger;

		if(!isset($post->login)) {
			if(isset($post->nickname)) {
				$post->login = mb_strtolower($parse->text->transliterate($post->nickname));
			}
			else {
				$logger->error("У пользователя должен быть логин!", false);
			}
		}
		else {
			$post->login = mb_strtolower($parse->text->transliterate($post->login));
		}

		if(isset($post->login) && $db->check_id($post->login, USERS_TABLE, "login")) {
			$logger->error("Логин ".$post->login." недоступен.", false);
		}
	}


	/**
	 * Функция загружает вновь созданному пользователю аватар.
	 *
	 * @param int    $uid    - уникальный идентификатор пользователя.
	 * @param string $avatar - текущий аватар пользователя
	 *
	 * @return string - имя файла аватарки
	 */
	public function upload_avatar($uid, $avatar="") {

		global $db, $config, $img;

		$av = $img->upload_image("avatar", "", array($config->users_avatar_width, $config->users_avatar_height), false, false, false, "av_".$uid);
		if(isset($av[0])) {
			if($avatar != "" && $avatar != $av[0]) {
				$img->erase_image(_UPLOADIMAGES."/".$avatar);
			}
			$avatar = $av[0];
		}

		return $avatar;
	}


	/**
	 * Функция удаляет пользовательский аватар.
	 *
	 * @param int $uid - Уникальный идентификатор пользователя
	 */
	public function delete_avatar($uid) {

		global $db;

		if($db->check_id($uid, USERS_TABLE, "uid", "avatar!=''") && ($this->uid == $uid || $this->title == "a")) {

			$q = $db->query("SELECT avatar FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$data = $db->fetch_assoc($q);

			if(is_file(_UPLOADIMAGES."/".$data['avatar'])) {
				unlink(_UPLOADIMAGES."/".$data['avatar']);
				$db->query("UPDATE ".USERS_TABLE." SET avatar='' WHERE uid='".$uid."'");
			}
		}
	}
}