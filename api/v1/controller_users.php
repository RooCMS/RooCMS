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



/**
 * Users Controller
 * CRUD and profile operations for users
 */
class UsersController extends BaseController {

    private readonly UserService $userService;
    private readonly Auth $auth;
    private readonly SiteSettings $siteSettings;
    private readonly Mailer $mailer;


    /**
 	 * Constructor
 	 */
    public function __construct(UserService $userService, Auth $auth, SiteSettings $siteSettings, Mailer $mailer, Db $db) {
        parent::__construct($db);

        $this->userService = $userService;
        $this->auth = $auth;
        $this->siteSettings = $siteSettings;
        $this->mailer = $mailer;
    }


	/**
	 * List users with pagination and filters
	 * GET /api/v1/users
	 */
	public function index(): void {
		$this->log_request('users_index');

		$params = $this->get_query_params();
		$pagination = $this->get_pagination_params();

		// Optional support for explicit offset
		if(isset($_GET['offset'])) {
			$offset = max(0, (int)$_GET['offset']);
			$pagination['page'] = (int)floor($offset / $pagination['limit']) + 1;
		}

		$filters = [];
		if(!empty($params['search'])) {
			$filters['search'] = (string)$params['search'];
		}
		if(!empty($params['role']) && in_array($params['role'], ['u','m','a','su'], true)) {
			$filters['role'] = $params['role'];
		}
		if(isset($params['is_active'])) {
			$filters['is_active'] = (int)(bool)$params['is_active'];
		}
		if(isset($params['is_banned'])) {
			$filters['is_banned'] = (int)(bool)$params['is_banned'];
		}

		try {
			$list = $this->userService->get_users_list($pagination['page'], $pagination['limit'], $filters);
			$total = $this->userService->get_users_count($filters);
			$meta = $this->format_pagination_meta($total, $pagination['page'], $pagination['limit']);

			$this->json_response([
				'items' => $list,
				'meta' => $meta
			]);
		} catch(Exception $e) {
			$this->error_response('Failed to fetch users', 500);
		}
	}


	/**
	 * Get user by ID
	 * GET /api/v1/users/{user_id}
	 * 
	 * @param int $user_id User ID
	 * @return void
	 */
	public function show(int $user_id): void {
		$this->log_request('users_show', ['user_id' => $user_id]);
		$user = $this->userService->get_user($user_id);
		if(!$user) {
			$this->not_found_response('User not found');
			return;
		}
		$this->json_response($user);
	}


	/**
	 * Get current user
	 * GET /api/v1/users/me
	 * Requires: AuthMiddleware
	 * 
	 * @return void
	 */
	public function me(): void {
		$this->log_request('users_me');
		$current = $this->require_authentication();
		if(empty($current)) {
			return;
		}
		$user = $this->userService->get_user((int)$current['id']);
		$this->json_response($user);
	}


	/**
	 * Request verification email
	 * POST /api/v1/users/me/verify-email
	 * Requires: AuthMiddleware
	 * 
	 * @return void
	 */
	public function request_verify_email(): void {
		$this->log_request('users_request_verify_email');
		$current = $this->require_authentication();
		if(empty($current)) {
			return;
		}

		// Already verified
		if(isset($current['is_verified']) && (string)$current['is_verified'] === '1') {
			$this->error_response('Email already verified', 400);
			return;
		}

		try {
			$expires_at = time() + 86400; // 24h
			$plain_code = $this->auth->generate_token(24);
			$code_hash = $this->auth->hash_data($plain_code);

			// Remove previous verification codes
			$this->db->delete(TABLE_VERIFICATION_CODES)
				->where('user_id', (int)$current['id'])
				->where('code_type', 'verification')
				->execute();

			// Insert new verification code
			$this->db->insert(TABLE_VERIFICATION_CODES)->data([
				'code_hash' => $code_hash,
				'user_id' => (int)$current['id'],
				'email' => $current['email'] ?? null,
				'code_type' => 'verification',
				'expires_at' => $expires_at,
				'used_at' => null,
				'attempts' => 0,
				'max_attempts' => 3,
				'created_at' => time(),
				'updated_at' => time()
			])->execute();

			// Send email
			try {
				$site_name = $this->siteSettings->get_by_key('site_name') ?? 'RooCMS';
				$site_domain = $this->siteSettings->get_by_key('site_domain') ?? DOMAIN;
				$verification_mail_uri = $this->siteSettings->get_by_key('mailer_verification_mail_uri') ?? '/verify-email';
				$site_url = 'https://' . $site_domain;
				$verify_link = $site_url . $verification_mail_uri . '?' . rawurlencode($plain_code);

				$this->mailer->send_with_template([
					'to' => $current['email'],
					'subject' => 'Verify your email on ' . $site_name,
					'template' => 'notice',
					'data' => [
						'title' => 'Email verification',
						'message' => "Use the link to verify your email: {$verify_link}\nThe link is valid for 24 hours.",
						'user_name' => $current['login'] ?? '',
						'site_name' => $site_name,
						'site_url' => $site_url,
					],
				]);
			} catch(Exception $e) {
				// ignore mail errors
			}

			$response = [];
			if(defined('DEBUGMODE') && DEBUGMODE) {
				$response['verification_code'] = $plain_code;
			}
			$this->json_response($response, 200, 'Verification email sent');
		} catch(Exception $e) {
			$this->error_response('Failed to request verification email', 500);
		}
	}


	/**
	 * Verify email
	 * GET /api/v1/users/verify-email/{verification_code}
	 * 
	 * @param string $verification_code Verification code
	 * @return void
	 */
	public function verify_email(string $verification_code): void {
		$this->log_request('users_verify_email');

		$code_hash = $this->auth->hash_data($verification_code);

		$sql = "SELECT * FROM " . TABLE_VERIFICATION_CODES . "
				WHERE code_hash = ?
				AND code_type = 'verification'
				AND expires_at > ?
				AND used_at IS NULL
				LIMIT 1";

		$record = $this->db->fetch_assoc($sql, [$code_hash, time()]);

		if(!$record) {
			$this->error_response('Invalid or expired verification code', 400);
			return;
		}

		try {
			$this->db->transaction(function() use ($record) {
				$this->db->update(TABLE_USERS)
					->data(['is_verified' => 1, 'updated_at' => time()])
					->where('id', $record['user_id'])
					->execute();

				$this->db->update(TABLE_VERIFICATION_CODES)
					->data(['used_at' => time()])
					->where('id', $record['id'])
					->execute();
			});

			$this->json_response(null, 200, 'Email verified successfully');
		} catch(Exception $e) {
			$this->error_response('Email verification failed', 500);
		}
	}


	/**
	 * Update current user
	 * PATCH /api/v1/users/me
	 * Requires: AuthMiddleware
	 * 
	 * @return void
	 */
	public function update_me(): void {
		$this->log_request('users_update_me');
		$current = $this->require_authentication();
		if(empty($current)) {
			return;
		}

		$data = $this->get_input_data();
		$field_mapping = [
			'user' => ['email'],
			'profile' => ['nickname','first_name','last_name','gender','avatar','bio','birthday','website','is_public']
		];

		// Filter data by allowed fields for each type
		$user_updates = array_intersect_key($data, array_flip($field_mapping['user']));
		$profile_updates = array_intersect_key($data, array_flip($field_mapping['profile']));

		if(empty($user_updates) && empty($profile_updates)) {
			$this->error_response('No valid fields to update', 400);
			return;
		}

		try {
			$this->db->transaction(function() use ($current, $user_updates, $profile_updates) {
				if($user_updates) {
					$this->userService->update_user((int)$current['id'], $user_updates);
				}
				if($profile_updates) {
					$this->userService->upsert_profile((int)$current['id'], $profile_updates);
				}
			});
			$this->json_response(null, 200, 'Profile updated');
		} catch(DomainException $e) {
			$this->error_response($e->getMessage(), $e->getCode() ?: 400);
		} catch(Exception $e) {
			$this->error_response('Update failed', 500);
		}
	}


	/**
	 * Delete current user
	 * DELETE /api/v1/users/me
	 * Requires: AuthMiddleware
	 * 
	 * @return void
	 */
	public function delete_me(): void {
		$this->log_request('users_delete_me');
		$current = $this->require_authentication();
		if(empty($current)) {
			return;
		}

		try {
			$this->userService->delete_user((int)$current['id']);
			$this->json_response(null, 200, 'User account deleted');
		} catch(Exception $e) {
			$this->error_response('Failed to delete account', 500);
		}
	}


	/**
	 * Update user
	 * PUT /api/v1/users/{user_id}
	 * Requires: AuthMiddleware + RoleMiddleware@admin_access
	 * 
	 * @param int $user_id User ID
	 * @return void
	 */
	public function update_user(int $user_id): void {
		$this->log_request('users_update_admin', ['user_id' => $user_id]);
		$data = $this->get_input_data();

		$allowed_user_fields = ['email','is_active','is_verified','is_banned','ban_expired','ban_reason'];
		$allowed_profile_fields = ['nickname','first_name','last_name','gender','avatar','bio','birthday','website','is_public'];

		$user_updates = [];
		$profile_updates = [];

		foreach($allowed_user_fields as $field) {
			if(array_key_exists($field, $data)) {
				$user_updates[$field] = $data[$field];
			}
		}
		foreach($allowed_profile_fields as $field) {
			if(array_key_exists($field, $data)) {
				$profile_updates[$field] = $data[$field];
			}
		}

		if(empty($user_updates) && empty($profile_updates)) {
			$this->error_response('No valid fields to update', 400);
			return;
		}

		try {
			$this->db->transaction(function() use ($user_id, $user_updates, $profile_updates) {
				if(!empty($user_updates)) {
					$this->userService->update_user($user_id, $user_updates);
				}
				if(!empty($profile_updates)) {
					$this->userService->upsert_profile($user_id, $profile_updates);
				}
			});
			$this->json_response(null, 200, 'User updated');
		} catch(DomainException $e) {
			$this->error_response($e->getMessage(), $e->getCode() ?: 400);
		} catch(Exception $e) {
			$this->error_response('Update failed', 500);
		}
	}


	/**
	 * Delete user
	 * DELETE /api/v1/users/{user_id}
	 * Requires: AuthMiddleware + RoleMiddleware@admin_access
	 * 
	 * @param int $user_id User ID
	 * @return void
	 */
	public function delete_user(int $user_id): void {
		$this->log_request('users_delete_admin', ['user_id' => $user_id]);
		try {
			$this->userService->delete_user($user_id);
			$this->json_response(null, 200, 'User deleted');
		} catch(Exception $e) {
			$this->error_response('Failed to delete user', 500);
		}
	}
}


