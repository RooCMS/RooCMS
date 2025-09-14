<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      https://www.roocms.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program. If not, see https://www.gnu.org/licenses/
 */

//#########################################################
//	Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}
//#########################################################



class AuthService {

	private Db $db;
	private Auth $auth;
	private Settings $settings;
	private Mailer $mailer;

	private int $recovery_code_length = 6;
	private int $max_recovery_attempts = 3;


	
	/**
	 * Constructor
	 * @param Db $db
	 * @param Auth $auth
	 * @param Settings $settings
	 * @param Mailer $mailer
	 */
	public function __construct(Db $db, Auth $auth, Settings $settings, Mailer $mailer) {
		$this->db = $db;
		$this->auth = $auth;
		$this->settings = $settings;
		$this->mailer = $mailer;
	}


	/**
	 * Register a new user
	 * @param string $login
	 * @param string $email
	 * @param string $password
	 * @return array
	 */
	public function register(string $login, string $email, string $password): array {
		$existing_login = $this->db->select()
			->from(TABLE_USERS)
			->where('login', $login)
			->limit(1)
			->first();
		if($existing_login) {
			throw new DomainException('Login already exists', 409);
		}

		$existing_email = $this->db->select()
			->from(TABLE_USERS)
			->where('email', $email)
			->limit(1)
			->first();
		if($existing_email) {
			throw new DomainException('Email already exists', 409);
		}

		$time = time();
		$data = [
			'login' => $login,
			'email' => $email,
			'password' => $this->auth->hash_password($password),
			'is_active' => '1',
			'is_verified' => '0',
			'is_banned' => '0',
			'ban_expired' => $time,
			'ban_reason' => '',
			'created_at' => $time,
			'updated_at' => $time,
			'last_activity' => $time
		];


		// Create user and base profile atomically
		$user_id = (int)$this->db->transaction(function() use ($data, $time) {
			$this->db->insert(TABLE_USERS)->data($data)->execute();
			$inserted_user_id = (int)$this->db->insert_id();
			if(!$inserted_user_id) {
				throw new RuntimeException('Registration failed', 500);
			}

			// Base profile (minimal defaults)
			$this->db->insert(TABLE_USER_PROFILES)->data([
				'user_id' => $inserted_user_id,
				'nickname' => null,
				'first_name' => null,
				'last_name' => null,
				'gender' => null,
				'avatar' => null,
				'bio' => null,
				'birthday' => null,
				'website' => null,
				'is_public' => 0,
				'created_at' => $time,
				'updated_at' => $time,
			])->execute();

			return $inserted_user_id;
		});

		$access_token = $this->auth->generate_token();
		$refresh_token = $this->auth->generate_token(64);
		$this->auth->store_token($access_token, $refresh_token, (int)$user_id);

		try {

			$site_name = $this->settings->get_by_key('site_name') ?? 'RooCMS';
			$site_domain = $this->settings->get_by_key('site_domain') ?? _DOMAIN;
			$site_url = 'https://' . $site_domain;

			$subject = 'Welcome to ' . $site_name . '!';

			$this->mailer->send_with_template([
				'to' => $email,
				'subject' => $subject,
				'template' => 'welcome',
				'data' => [
					'user_name' => $login,
					'user_email' => $email,
					'site_name' => $site_name,
					'site_url' => $site_url,
					'login_url' => $site_url
				],
			]);
		} catch (Exception $e) {
			// ignore mail errors
		}

		$user_data = [
			'user_id' => (int)$user_id,
			'login' => $login,
			'email' => $email,
			'is_verified' => false,
			'role' => 'u' // default role
		];
		

		return [
			'access_token' => $access_token,
			'refresh_token' => $refresh_token,
			'token_type' => 'Bearer',
			'expires_in' => $this->auth->token_expires,
			'refresh_expires_in' => $this->auth->refresh_token_expires,
			'user' => $user_data
		];
	}


	/**
	 * Login a user
	 * @param string $login
	 * @param string $password
	 * @return array
	 */
	public function login(string $login, string $password): array {
		$user = $this->db->select()
			->from(TABLE_USERS)
			->where('login', $login)
			->limit(1)
			->first();

		if(!$user) {
			throw new DomainException('Invalid credentials', 401);
		}

		if($user['is_active'] != '1') {
			throw new DomainException('Account is not active', 403);
		}

		if($user['is_banned'] == '1' && (int)$user['ban_expired'] > time()) {
			throw new DomainException('Account is banned', 403);
		}

		if(!$this->auth->verify_password($password, $user['password'])) {
			throw new DomainException('Invalid credentials', 401);
		}

		$access_token = $this->auth->generate_token();
		$refresh_token = $this->auth->generate_token(64);

		$this->auth->store_token($access_token, $refresh_token, (int)($user['id']));

		$this->db->update(TABLE_USERS)
			->data(['last_activity' => time()])
			->where('id', $user['id'] ?? $user['user_id'])
			->execute();

		return [
			'access_token' => $access_token,
			'refresh_token' => $refresh_token,
			'token_type' => 'Bearer',
			'expires_in' => $this->auth->token_expires,
			'refresh_expires_in' => $this->auth->refresh_token_expires,
			'user' => [
				'user_id' => (int)($user['id'] ?? $user['user_id']),
				'role' => $user['role'] ?? 'u',
				'login' => $user['login'] ?? $login,
				'email' => $user['email'],
				'is_verified' => isset($user['is_verified']) ? ($user['is_verified'] == '1') : false
			]
		];
	}


	/**
	 * Refresh a token
	 * @param string $refresh_token
	 * @return array
	 */
	public function refresh(string $refresh_token): array {
		$refresh_token_hash = $this->auth->hash_data($refresh_token);

		$token_data = $this->db->select()
			->from(TABLE_TOKENS)
			->where('refresh', $refresh_token_hash)
			->where('refresh_expires', time(), '>')
			->limit(1)
			->first();

		if(!$token_data) {
			throw new DomainException('Invalid or expired refresh token', 401);
		}

		$user = $this->db->select()
			->from(TABLE_USERS)
			->where('id', $token_data['user_id'])
			->where('is_active', '1')
			->limit(1)
			->first();

		if(!$user) {
			throw new DomainException('User not found or inactive', 401);
		}

		if($user['is_banned'] == '1' && (int)$user['ban_expired'] > time()) {
			throw new DomainException('Account is banned', 403);
		}

		$new_access_token = $this->auth->generate_token();
		$new_refresh_token = $this->auth->generate_token(64);

		$this->db->delete(TABLE_TOKENS)
			->where('id', $token_data['id'])
			->execute();

		$this->auth->store_token($new_access_token, $new_refresh_token, (int)$user['id']);

		$this->db->update(TABLE_USERS)
			->data(['last_activity' => time()])
			->where('id', $user['id'])
			->execute();

		return [
			'access_token' => $new_access_token,
			'refresh_token' => $new_refresh_token,
			'token_type' => 'Bearer',
			'expires_in' => $this->auth->token_expires,
			'refresh_expires_in' => $this->auth->refresh_token_expires
		];
	}


	/**
	 * Request a password recovery
	 * @param string $email
	 * @return array
	 */
	public function request_password_recovery(string $email): array {
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return [];
		}

		$user = $this->db->select()
			->from(TABLE_USERS)
			->where('email', $email)
			->where('is_active', '1')
			->limit(1)
			->first();

		if(!$user) {
			return [];
		}

		if($user['is_banned'] == '1' && (int)$user['ban_expired'] > time()) {
			return [];
		}

		$recovery_code = str_pad((string)random_int(100000, 999999), $this->recovery_code_length, '0', STR_PAD_LEFT);
		$code_hash = $this->auth->hash_data($recovery_code);
		$expires_at = time() + 1800;

		$this->db->delete(TABLE_VERIFICATION_CODES)
			->where('user_id', $user['id'])
			->where('code_type', 'password_reset')
			->execute();

		$this->db->insert(TABLE_VERIFICATION_CODES)->data([
			'code_hash' => $code_hash,
			'user_id' => (int)$user['id'],
			'email' => $user['email'],
			'code_type' => 'password_reset',
			'expires_at' => $expires_at,
			'used_at' => null,
			'attempts' => 0,
			'max_attempts' => $this->max_recovery_attempts
		])->execute();

		try {

			$site_name = $this->settings->get_by_key('site_name') ?? 'RooCMS';
			$site_domain = $this->settings->get_by_key('site_domain') ?? _DOMAIN;
			$site_url = 'https://' . $site_domain;

			$subject = 'Password recovery on ' . $site_name;

			$this->mailer->send_with_template([
				'to' => $user['email'],
				'subject' => $subject,
				'template' => 'notice',
				'data' => [
					'title' => 'Recovery password',
					'message' => "Your recovery code: {$recovery_code}\nValid for 30 minutes.",
					'user_name' => $user['login'] ?? '',
					'site_name' => $site_name,
					'site_url' => $site_url,
				],
			]);
		} catch (Exception $e) {
			// ignore mail errors
		}

		$response_data = [];
		if(defined('DEBUGMODE') && DEBUGMODE) {
			$response_data['recovery_code'] = $recovery_code;
		}

		return $response_data;
	}


	/**
	 * Reset a password
	 * @param string $token
	 * @param string $new_password
	 */
	public function reset_password(string $token, string $new_password): void {
		$code_hash = $this->auth->hash_data($token);

		$verification_code = $this->db->select()
			->from(TABLE_VERIFICATION_CODES)
			->where('code_hash', $code_hash)
			->where('code_type', 'password_reset')
			->where('expires_at', time(), '>')
			->where('used_at', null, 'IS')
			->limit(1)
			->first();

		if(!$verification_code) {
			throw new DomainException('Invalid or expired reset code', 401);
		}

		if((int)$verification_code['attempts'] >= (int)$verification_code['max_attempts']) {
			throw new DomainException('Maximum attempts exceeded', 429);
		}

		$user = $this->db->select()
			->from(TABLE_USERS)
			->where('id', $verification_code['user_id'])
			->where('is_active', '1')
			->limit(1)
			->first();

		if(!$user) {
			$this->db->update(TABLE_VERIFICATION_CODES)
				->data(['attempts' => (int)$verification_code['attempts'] + 1])
				->where('id', $verification_code['id'])
				->execute();
			throw new DomainException('User not found', 404);
		}

		if($user['is_banned'] == '1' && (int)$user['ban_expired'] > time()) {
			throw new DomainException('Account is banned', 403);
		}

		$hashed_password = $this->auth->hash_password($new_password);

		$this->db->update(TABLE_USERS)
			->data([
				'password' => $hashed_password,
				'updated_at' => time()
			])
			->where('id', $user['id'])
			->execute();

		$this->db->update(TABLE_VERIFICATION_CODES)
			->data(['used_at' => date('Y-m-d H:i:s')])
			->where('id', $verification_code['id'])
			->execute();

		$this->db->delete(TABLE_TOKENS)
			->where('user_id', $user['id'])
			->execute();

		$this->db->delete(TABLE_VERIFICATION_CODES)
			->where('user_id', $user['id'])
			->where('code_type', 'password_reset')
			->execute();
	}


	/**
	 * Update a password
	 * @param int $user_id
	 * @param string $current_password
	 * @param string $new_password
	 */
	public function update_password(int $user_id, string $current_password, string $new_password): void {
		$user = $this->db->select()
			->from(TABLE_USERS)
			->where('id', $user_id)
			->limit(1)
			->first();

		if(!$user) {
			throw new DomainException('User not found', 404);
		}

		if(!$this->auth->verify_password($current_password, $user['password'])) {
			throw new DomainException('Current password is incorrect', 401);
		}

		$hashed_password = $this->auth->hash_password($new_password);

		$this->db->update(TABLE_USERS)
			->data([
				'password' => $hashed_password,
				'updated_at' => time()
			])
			->where('id', $user_id)
			->execute();

		$this->db->delete(TABLE_TOKENS)
			->where('user_id', $user_id)
			->execute();
	}


	/**
	 * Logout for a user
	 * @param int $user_id
	 * @param string $token
	 */
	public function logout(int $user_id, string $token): void {
		$token_hash = $this->auth->hash_data($token);
		$this->db->delete(TABLE_TOKENS)
			->where('user_id', $user_id)
			->where('token', $token_hash)
			->execute();
	}


	/**
	 * Logout all devices for a user
	 * @param int $user_id
	 */
	public function logout_all_devices(int $user_id): void {
		$this->db->delete(TABLE_TOKENS)
			->where('user_id', $user_id)
			->execute();
	}


	/**
	 * Revoke a specific refresh token (and its paired access token)
	 * @param int $user_id
	 * @param string $refresh_token
	 */
	public function revoke_refresh_token(int $user_id, string $refresh_token): void {
		$refresh_hash = $this->auth->hash_data($refresh_token);
		$this->db->delete(TABLE_TOKENS)
			->where('user_id', $user_id)
			->where('refresh', $refresh_hash)
			->execute();
	}
}