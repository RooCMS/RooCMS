<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2020 alexandr Belov aka alex Roosso. All rights reserved.
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
 * Class Security
 */
class Security extends Shteirlitz {

	private $pass_leight = 8;
	private $salt_leight = 5;



	/**
	 * Hash user password
	 *
	 * @param string $password - unhash pass
	 * @param string $salt	   - user salt
	 *
	 * @return string - hash
	 */
	public function hash_password($password, $salt) {
		$hash = md5(md5($password).md5($salt));
		return $hash;
	}


	/**
	 * Function generates hash key to check current access.
	 * Temporary key is generated based on current user session.
	 *
	 * @param string $login		- user login
	 * @param string $password	- hash user password
	 * @param string $salt		- user salt
	 *
	 * @return string - security token
	 */
	public function hash_token($login, $password, $salt) {

		global $roocms;

		$token = md5(md5($roocms->usersession).md5($login).md5($password).md5($salt));
		return $token;
	}


	/**
	 * Function generates hash key to check user api express operations.
	 *
	 * @param int $uid - unique user id
	 */
	public function hash_uapi($uid) {

		//global $users;

		$hash = md5($uid);
	}


	/**
	 * Generate new paaword for user
	 *
	 * @return string - new password
	 */
	public function generate_password() {
		$password = randcode($this->pass_leight, "ABCDEFGHJKLMNPQRSTUVWXYZabcdefhjkmnprstvwxyz123456789");
		return $password;
	}


	/**
	 * Generate new salt for user
	 *
	 * @return string
	 */
	public function generate_salt() {
		$salt = randcode($this->salt_leight, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*(-)+{=}:?>~<,./[|]");
		return $salt;
	}


	/**
	 * Checks user data to make attempts to substitute
	 */
	protected function control_userdata() {

		global $roocms, $logger;

		$destroy = false;

		# check uid
		if($roocms->sess['uid'] != $this->uid) {
			$destroy = true;
		}

		# check login
		if($roocms->sess['login'] != $this->login) {
			$destroy = true;
		}

		# check title
		if($roocms->sess['title'] != $this->title) {
			$destroy = true;
		}

		# check nickname
		if($roocms->sess['nickname'] != $this->nickname) {
			$destroy = true;
		}

		# check token
		if($roocms->sess['token'] != $this->token) {
			$destroy = true;
		}

		if($destroy) {
			# destroy data
			$roocms->sess = [];
			session_destroy();

			# notice and stop
			$logger->error("Ваши данные изменились! Требуется пройти авторизацию.");
			go("/");
		}
	}
}
