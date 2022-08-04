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
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class Users
 */
class Users extends Security {

	use UserGroups;
	use UserAvatar;

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

	# user data array
	public	$userdata	= array('uid'=>0, 'gid'=>0, 'title'=>'u');

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
		$this->usersession &= $roocms->usersession;
		$this->userip      &= $roocms->userip;
		$this->referer     &= $roocms->referer;

		# init useragent
		$this->get_useragent();

		# check useragent  for detect spider bot
		$roocms->check_spider_bot($this->useragent);

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

		if(isset($roocms->sess['login']) && $db->check_id($roocms->sess['login'], USERS_TABLE, "login", "status='1'") && isset($roocms->sess['token']) && mb_strlen($roocms->sess['token']) == 32) {

			# get data
			$q    = $db->query("SELECT u.uid, u.gid, u.login, u.nickname, u.avatar, u.email, u.mailing,
 							u.user_name, u.user_surname, u.user_last_name, u.user_birthdate, u.user_sex, u.user_slogan,
							u.title, u.password, u.salt, u.ban, u.ban_reason, u.ban_expiried, u.date_create, u.secret_key,
							g.title as gtitle
						FROM ".USERS_TABLE." AS u
						LEFT JOIN ".USERS_GROUP_TABLE." AS g ON (g.gid = u.gid)
						WHERE u.login='".$roocms->sess['login']."' AND u.status='1'");
			$data = $db->fetch_assoc($q);

			# uid
			$this->uid	= (int) $data['uid'];
			# gid
			$this->gid	= (int) $data['gid'];
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
				'uid'             => (int) $data['uid'],
				'gid'             => (int) $data['gid'],
				'gtitle'          => $data['gtitle'],
				'login'           => $data['login'],
				'nickname'        => $data['nickname'],
				'avatar'          => $data['avatar'],
				'email'           => $data['email'],
				'mailing'         => $data['mailing'],
				'title'           => $data['title'],
				'user_name'       => $data['user_name'],
				'user_surname'    => $data['user_surname'],
				'user_last_name'  => $data['user_last_name'],
				'user_birthdate'  => $parse->date->jd_to_rus($data['user_birthdate']),
				'user_birthdaten' => $parse->date->jd_to_rusint($data['user_birthdate']),
				'user_sex'        => $data['user_sex'],
				'user_slogan'     => $parse->text->br($data['user_slogan']),
				'ban'             => $data['ban'],
				'ban_reason'      => $data['ban_reason'],
				'ban_expiried'    => $parse->date->unix_to_rus($data['ban_expiried']),
				'secret_key'      => $data['secret_key'],
				'date_create'     => $parse->date->unix_to_rus($data['date_create'])
			);


			# security token
			$this->token = $this->get_token($roocms->sess['login'], $data['password'], $data['salt']);
		}
	}


	/**
	 * Обновляем простую информацию пользователя, вроде времени последнего визита на сайт.
	 *
	 * @param int $uid - уникальные идентификатор пользователя
	 */
	private function update_user_time_last_visit(int $uid) {

		global $db, $roocms;

		# update time last visited
		$db->query("UPDATE ".USERS_TABLE." SET last_visit='".time()."', user_ip='".$roocms->userip."' WHERE uid='".$uid."' AND status='1'");
	}


	/**
	 * Get user data
	 *
	 * @param int $uid - user id
	 *
	 * @return array user data
	 */
	public function get_user_data(int $uid) {

		global $db, $parse;

		# default
		$row = array(
			'uid' => 0,
		);

		# check and get data
		if($db->check_id($uid, USERS_TABLE, "uid")) {
			$q = $db->query("SELECT uid, nickname, email, user_sex, user_slogan, avatar, user_birthdate, status, ban, ban_expiried, ban_reason, secret_key FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$row = $db->fetch_assoc($q);

			$row['slogan'] = $parse->text->br($row['user_slogan']);
			$row['user_birthdate'] = $parse->date->jd_to_rus($row['user_birthdate']);
		}

		return $row;
	}


	/**
	 * Get users list
	 *
	 * @param int   $status  - current user status: 1 active user, 0 unactive user, -1 all user
	 * @param int   $ban     - ban status: 0 without ban, 1 with ban, -1 all
	 * @param int   $mailing - subscrib status: 0 no, 1 yes, -1 all
	 * @param array $users   - array id's users
	 *
	 * @return array
	 */
	public function get_userlist(int $status=-1, int $ban=-1, int $mailing=-1, array $users=[]) {

		global $db, $parse;

		# condition
		$cond = "";

		$arcond = array("status"=>$status, "ban"=>$ban, "mailing"=>$mailing);

		foreach($arcond AS $k=>$v) {

			if($v == 0 || $v == 1) {
				$cond = $db->qcond_and($cond);
				$cond .= " ".$k."='".$v."' ";
			}
		}

		if(!empty($users)) {

			$cond = $db->qcond_and($cond);

			$uids = [];
			foreach($users AS $k=>$v) {
				$uids[] = " uid='".$v."' ";
			}

			$cond .= " ( ".implode(" OR ", $uids)." ) ";
		}

		# condition formating
		if($cond != "") {
			$cond = "WHERE".$cond;
		}

		# Get user list
		$userlist = [];
		$q = $db->query("SELECT uid, nickname, email, user_sex, user_slogan, avatar, user_birthdate, status, ban, ban_expiried, ban_reason, secret_key FROM ".USERS_TABLE." ".$cond." ORDER BY nickname");
		while($row = $db->fetch_assoc($q)) {
			$row['slogan'] = $parse->text->br($row['user_slogan']);

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
	 * @param string $table   - Таблица для проверки
	 *
	 * @return bool $res - true - если значение не уникально, false - если значение уникально
	 */
	public function check_field(string $field, string $name, string $without="", string $table=USERS_TABLE) {

		global $db;

		$res = false;

		if(trim($without) != trim($name)) {
			$w = $field."!='".trim($without)."'";

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
	public function valid_user_email(string $email) {

		global $db, $logger, $parse;

		if(trim($email) != "") {
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
	public function uniq_nickname(string $nickname) {

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
			$post->user_birthdate = $parse->date->rusint_to_jd($post->user_birthdate);
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
	 * Get useragent string from user
	 */
	public function  get_useragent() {

		if(!empty($_SERVER['HTTP_USER_AGENT'])) {
			$this->useragent = mb_strtolower($_SERVER['HTTP_USER_AGENT']);
		}
	}
}
