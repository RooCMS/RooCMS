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
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################



/**
 * Users Controller
 * CRUD and profile operations for users
 */
class UsersController extends BaseController {

    private readonly UserService $userService;
	private readonly UserManageService $userManageService;
	private readonly UserListService $userListService;
    private readonly EmailService $emailService;
    private readonly Auth $auth;


	
    /**
	 * Constructor
	 */
    public function __construct(Db $db, Request $request, UserService $userService, UserManageService $userManageService, UserListService $userListService, EmailService $emailService, Auth $auth) {
        parent::__construct($db, $request);

        $this->userService = $userService;
        $this->userManageService = $userManageService;
        $this->userListService = $userListService;
        $this->emailService = $emailService;
        $this->auth = $auth;
    }


	/**
	 * List users with pagination and filters
	 * GET /api/v1/users
	 */
	public function index(): void {
		$this->log_request('users_index');

		$current = $this->require_authentication();
		if(empty($current)) {
			return;
		}

		$params = $this->get_query_params();
		$pagination = $this->get_pagination_params();

		// Handle explicit offset
		if(isset($pagination['offset'])) {
			$pagination['page'] = (int)floor(max(0, (int)$pagination['offset']) / $pagination['limit']) + 1;
		}

		// Build filters using array operations
		$filter_mappings = [
			'search' => fn($v) => !empty($v) ? (string)$v : null,
			'role' => fn($v) => (!empty($v) && in_array($v, ['u','m','a','su'], true)) ? $v : null,
			'is_active' => fn($v) => isset($v) ? (int)(bool)$v : null,
			'is_banned' => fn($v) => isset($v) ? (int)(bool)$v : null,
		];

		$filters = [];
		foreach($filter_mappings as $key => $mapper) {
			$value = $mapper($params[$key] ?? null);
			if($value !== null) {
				$filters[$key] = $value;
			}
		}

		// Add is_deleted filter for privileged roles only
		if(isset($params['is_deleted']) && in_array($current['role'] ?? 'u', ['m', 'a', 'su'], true)) {
			$filters['is_deleted'] = (int)(bool)$params['is_deleted'];
		}

		try {
			$list = $this->userListService->get_users_list($pagination['page'], $pagination['limit'], $filters);
			$total = $this->userListService->get_users_count($filters);
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
	 * Access rules:
	 * - Full access: User requesting own profile OR admin/superuser requesting any profile
	 * - Limited access: Unauthenticated users, regular users, moderators - only non-deleted profiles
	 * 
	 * @param int $user_id User ID
	 * @return void
	 */
	public function show(int $user_id): void {
		$this->log_request('users_show', ['user_id' => $user_id]);
		
		// Get current user (may be null if not authenticated)
		$current = $this->get_authenticated_user();
		
		// Use access-controlled method to get user data
		$user = $this->userService->get_user($user_id, $current);
		
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
		$user = $this->userService->get_user((int)$current['id'], $current);
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
		
		if(empty($current) || ((string)($current['is_verified'] ?? '0') === '1')) {
			if(!empty($current) && (string)($current['is_verified'] ?? '0') === '1') {
				$this->error_response('Email already verified', 400);
			}
			return;
		}

		try {
			$response = $this->emailService->request_email_verification($current);
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

		try {
			$this->emailService->verify_email($verification_code);
			$this->json_response(null, 200, 'Email verified successfully');
		} catch(DomainException $e) {
			$this->error_response($e->getMessage(), $e->getCode() ?: 400);
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
			$this->userManageService->delete_user((int)$current['id']);
			$this->json_response(null, 200, 'User account deleted');
		} catch(Exception $e) {
			$this->error_response('Failed to delete account', 500);
		}
	}


	/**
	 * Upload avatar for current user
	 * POST /api/v1/users/me/avatar
	 * Requires: AuthMiddleware
	 */
	public function upload_avatar(): void {
		$this->log_request('users_upload_avatar');
		
		try {
			// Require authentication
			$current_user = $this->require_authentication();
			if(empty($current_user)) {
				return; // Error response already sent
			}
			
			// Basic validation
			if(!isset($this->request->files['avatar'])) {
				$this->error_response('No avatar file provided', 400);
				return;
			}
			
			$file = $this->request->files['avatar'];
			
			if($file['error'] !== UPLOAD_ERR_OK) {
				$this->error_response('Avatar upload error: ' . $this->get_upload_error_message($file['error']), 400);
				return;
			}
			
			// Delegate avatar upload to service
			$avatar_path = $this->userService->upload_avatar($file, (int)$current_user['id']);
			
			$this->json_response([
				'message' => 'Avatar uploaded successfully',
				'avatar_path' => $avatar_path
			], 201);
			
		} catch(DomainException $e) {
			$this->error_response($e->getMessage(), $e->getCode());
		} catch(Exception $e) {
			$this->error_response('Failed to upload avatar: ' . $e->getMessage(), 500);
		}
	}


	/**
	 * Delete avatar for current user
	 * DELETE /api/v1/users/me/avatar
	 * Requires: AuthMiddleware
	 */
	public function delete_avatar(): void {
		$this->log_request('users_delete_avatar');
		
		try {
			// Require authentication
			$current_user = $this->require_authentication();
			if(empty($current_user)) {
				return; // Error response already sent
			}
			
			// Delegate avatar deletion to service
			$success = $this->userService->delete_avatar((int)$current_user['id']);
			
			if(!$success) {
				$this->error_response('No avatar to delete', 404);
				return;
			}
			
			$this->json_response([
				'message' => 'Avatar deleted successfully'
			]);
			
		} catch(DomainException $e) {
			$this->error_response($e->getMessage(), $e->getCode());
		} catch(Exception $e) {
			$this->error_response('Failed to delete avatar: ' . $e->getMessage(), 500);
		}
	}
}


