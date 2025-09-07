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
 * Authentication Controller
 * Handles user authentication, registration, and password management
 */
class AuthController extends BaseController {

    private readonly Auth $auth;
    
    private int $token_expires          = 3600;
    private int $refresh_token_expires  = 604800;
    private int $recovery_token_expires = 1800;

    private int $password_min_length    = 6;
    private int $login_min_length       = 3;
    private int $login_max_length       = 50;



    /**
     * Constructor with dependency injection
     */
    public function __construct(Db|null $db = null) {
        parent::__construct($db);
        
        if (!$this->is_database_available()) {
            $this->error_response('Database connection required', 500);
            return;
        }
        
        $this->auth = new Auth($this->db);

        $this->token_expires = $this->auth->token_expires;
        $this->refresh_token_expires = $this->auth->refresh_token_expires;
    }


    /**
     * User login
     * POST /api/v1/auth/login
     */
    public function login(): void {
        $this->log_request('auth_login');
        
        $data = $this->get_input_data();
        
        // Validate required fields
        $required = ['login', 'password'];
        $validation_errors = $this->validate_required($data, $required);
        
        if (!empty($validation_errors)) {
            $this->validation_error_response($validation_errors);
            return;
        }

        // Additional validation
        if (strlen($data['login']) < $this->login_min_length) {
            $validation_errors['login'] = 'Login must be at least ' . $this->login_min_length . ' characters';
        }
        
        if (strlen($data['password']) < $this->password_min_length) {
            $validation_errors['password'] = 'Password must be at least ' . $this->password_min_length . ' characters';
        }

        if (!empty($validation_errors)) {
            $this->validation_error_response($validation_errors);
            return;
        }

        try {
            // Find user by login
            $user = $this->db->select()
                ->from(TABLE_USERS)
                ->where('login', '=', $data['login'])
                ->limit(1)
                ->fetchOne();

            if (!$user) {
                $this->error_response('Invalid credentials', 401);
                return;
            }

            // Check if user is active
            if ($user['is_active'] != '1') {
                $this->error_response('Account is not active', 403);
                return;
            }

            // Check if user is banned
            if ($user['is_banned'] == '1' && $user['ban_expired'] > time()) {
                $this->error_response('Account is banned', 403, [
                    'ban_reason' => $user['ban_reason'],
                    'ban_expires' => format_timestamp($user['ban_expired'])
                ]);
                return;
            }

            // Verify password
            if (!$this->auth->verify($data['password'], $user['password'])) {
                $this->error_response('Invalid credentials', 401);
                return;
            }

            // Generate tokens
            $access_token = $this->auth->generate_token();
            $refresh_token = $this->auth->generate_token(64);
            
            // Store tokens in database
            $this->auth->store_token($access_token, $refresh_token, $user['user_id']);

            // Update last activity
            $this->db->update(TABLE_USERS)
                ->set(['last_activity' => time()])
                ->where('user_id', '=', $user['user_id'])
                ->execute();

            // Return success response with tokens
            $response_data = [
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
                'token_type' => 'Bearer',
                'expires_in' => 3600, // 1 hour
                'user' => [
                    'user_id' => $user['user_id'],
                    'login' => $user['login'],
                    'email' => $user['email'],
                    'is_verified' => $user['is_verified'] == '1'
                ]
            ];

            $this->json_response($response_data, 200, 'Login successful');

        } catch (Exception $e) {
            $this->error_response('Login failed', 500);
        }
    }


    /**
     * User registration
     * POST /api/v1/auth/register
     */
    public function register(): void {
        $this->log_request('auth_register');
        
        $data = $this->get_input_data();
        
        // Validate required fields
        $required = ['login', 'email', 'password'];
        $validation_errors = $this->validate_required($data, $required);
        
        if (!empty($validation_errors)) {
            $this->validation_error_response($validation_errors);
            return;
        }

        // Additional validation
        if (strlen($data['login']) < $this->login_min_length || strlen($data['login']) > $this->login_max_length) {
            $validation_errors['login'] = 'Login must be between ' . $this->login_min_length . ' and ' . $this->login_max_length . ' characters';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $validation_errors['email'] = 'Invalid email format';
        }

        if (strlen($data['password']) < $this->password_min_length) {
            $validation_errors['password'] = 'Password must be at least ' . $this->password_min_length . ' characters';
        }

        // Check for password confirmation if provided
        if (isset($data['password_confirmation'])) {
            if ($data['password'] !== $data['password_confirmation']) {
                $validation_errors['password_confirmation'] = 'Password confirmation does not match';
            }
        }

        if (!empty($validation_errors)) {
            $this->validation_error_response($validation_errors);
            return;
        }

        try {
            // Check if login already exists
            $existing_login = $this->db->select()
                ->from(TABLE_USERS)
                ->where('login', '=', $data['login'])
                ->limit(1)
                ->fetchOne();

            if ($existing_login) {
                $this->error_response('Login already exists', 409);
                return;
            }

            // Check if email already exists
            $existing_email = $this->db->select()
                ->from(TABLE_USERS)
                ->where('email', '=', $data['email'])
                ->limit(1)
                ->fetchOne();

            if ($existing_email) {
                $this->error_response('Email already exists', 409);
                return;
            }

            // Hash password
            $hashed_password = $this->auth->hash($data['password']);
            $current_time = time();

            // Insert new user
            $user_id = $this->db->insert(TABLE_USERS)->data([
                'login' => $data['login'],
                'email' => $data['email'],
                'password' => $hashed_password,
                'is_active' => '1',
                'is_verified' => '0',
                'is_banned' => '0',
                'ban_expired' => 0,
                'ban_reason' => '',
                'created_at' => $current_time,
                'updated_at' => $current_time,
                'last_activity' => $current_time
            ])->execute();

            if (!$user_id) {
                $this->error_response('Registration failed', 500);
                return;
            }

            // Generate tokens
            $access_token = $this->auth->generate_token();
            $refresh_token = $this->auth->generate_token(64);
            
            // Store tokens in database
            $this->auth->store_token($access_token, $refresh_token, $user_id);

            // Return success response
            $response_data = [
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'user' => [
                    'user_id' => $user_id,
                    'login' => $data['login'],
                    'email' => $data['email'],
                    'is_verified' => false
                ]
            ];

            $this->created_response($response_data, 'Registration successful');

        } catch (Exception $e) {
            $this->error_response('Registration failed', 500);
        }
    }


    /**
     * User logout
     * POST /api/v1/auth/logout
     * Requires: AuthMiddleware
     */
    public function logout(): void {
        $this->log_request('auth_logout');
        
        $user = $this->require_authentication();
        if (!$user) {
            return; // Error response already sent
        }

        try {
            // Remove all tokens for this user (logout from all devices)
            $this->db->delete(TABLE_TOKENS)
                ->where('user_id', '=', $user['user_id'])
                ->execute();

            $this->json_response(null, 200, 'Logout successful');

        } catch (Exception $e) {
            $this->error_response('Logout failed', 500);
        }
    }


    /**
     * Refresh token
     * POST /api/v1/auth/refresh
     */
    public function refresh(): void {
        $this->log_request('auth_refresh');
        
        $data = $this->get_input_data();
        
        if (!isset($data['refresh_token']) || empty($data['refresh_token'])) {
            $this->error_response('Refresh token required', 400);
            return;
        }

        try {
            // Find valid refresh token
            $token_data = $this->db->select()
                ->from(TABLE_TOKENS)
                ->where('refresh', '=', $data['refresh_token'])
                ->where('refresh_expires', '>', time())
                ->limit(1)
                ->fetchOne();

            if (!$token_data) {
                $this->error_response('Invalid or expired refresh token', 401);
                return;
            }

            // Get user data
            $user = $this->db->select()
                ->from(TABLE_USERS)
                ->where('user_id', '=', $token_data['user_id'])
                ->where('is_active', '=', '1')
                ->limit(1)
                ->fetchOne();

            if (!$user) {
                $this->error_response('User not found or inactive', 401);
                return;
            }

            // Check if user is banned
            if ($user['is_banned'] == '1' && $user['ban_expired'] > time()) {
                $this->error_response('Account is banned', 403);
                return;
            }

            // Generate new tokens
            $new_access_token = $this->auth->generate_token();
            $new_refresh_token = $this->auth->generate_token(64);

            // Remove old token
            $this->db->delete(TABLE_TOKENS)
                ->where('id', '=', $token_data['id'])
                ->execute();

            // Store new tokens
            $this->auth->store_token($new_access_token, $new_refresh_token, $user['user_id']);

            // Update last activity
            $this->db->update(TABLE_USERS)
                ->set(['last_activity' => time()])
                ->where('user_id', '=', $user['user_id'])
                ->execute();

            // Return new tokens
            $response_data = [
                'access_token' => $new_access_token,
                'refresh_token' => $new_refresh_token,
                'token_type' => 'Bearer',
                'expires_in' => 3600
            ];

            $this->json_response($response_data, 200, 'Token refreshed successfully');

        } catch (Exception $e) {
            $this->error_response('Token refresh failed', 500);
        }
    }


    /**
     * Password recovery request
     * POST /api/v1/auth/password/recovery
     */
    public function recoveryPassword(): void {
        // TODO: Implement password recovery request
    }


    /**
     * Reset password with token
     * POST /api/v1/auth/password/reset
     */
    public function resetPassword(): void {
        $this->log_request('password_reset');
        
        $data = $this->get_input_data();
        
        // Validate required fields
        $required = ['token', 'password'];
        $validation_errors = $this->validate_required($data, $required);
        
        if (!empty($validation_errors)) {
            $this->validation_error_response($validation_errors);
            return;
        }

        if (strlen($data['password']) < $this->password_min_length) {
            $this->error_response('Password must be at least ' . $this->password_min_length . ' characters', 400);
            return;
        }

        // Check for password confirmation
        if (isset($data['password_confirmation'])) {
            if ($data['password'] !== $data['password_confirmation']) {
                $this->error_response('Password confirmation does not match', 400);
                return;
            }
        }

        try {
            // Find recovery token
            // TODO: Implement

            if (!$token_data) {
                $this->error_response('Invalid or expired reset token', 401);
                return;
            }

            // Get user
            $user = $this->db->select()
                ->from(TABLE_USERS)
                ->where('user_id', '=', $token_data['user_id'])
                ->where('is_active', '=', '1')
                ->limit(1)
                ->fetchOne();

            if (!$user) {
                $this->error_response('User not found', 404);
                return;
            }

            // Hash new password
            $hashed_password = $this->auth->hash($data['password']);

            // Update user password
            $this->db->update(TABLE_USERS)
                ->set([
                    'password' => $hashed_password,
                    'updated_at' => time()
                ])
                ->where('user_id', '=', $user['user_id'])
                ->execute();

            // Remove recovery token
            // TODO: Implement

            // Remove all user tokens (force re-login)
            $this->db->delete(TABLE_TOKENS)
                ->where('user_id', '=', $user['user_id'])
                ->execute();

            $this->json_response(null, 200, 'Password reset successfully');

        } catch (Exception $e) {
            $this->error_response('Password reset failed', 500);
        }
    }


    /**
     * Update password for authenticated user
     * PUT /api/v1/auth/password
     * Requires: AuthMiddleware
     */
    public function updatePassword(): void {
        $this->log_request('password_update');
        
        $user = $this->require_authentication();
        if (!$user) {
            return; // Error response already sent
        }

        $data = $this->get_input_data();
        
        // Validate required fields
        $required = ['current_password', 'new_password'];
        $validation_errors = $this->validate_required($data, $required);
        
        if (!empty($validation_errors)) {
            $this->validation_error_response($validation_errors);
            return;
        }

        if (strlen($data['new_password']) < $this->password_min_length) {
            $this->error_response('New password must be at least ' . $this->password_min_length . ' characters', 400);
            return;
        }

        // Check for password confirmation
        if (isset($data['new_password_confirmation'])) {
            if ($data['new_password'] !== $data['new_password_confirmation']) {
                $this->error_response('New password confirmation does not match', 400);
                return;
            }
        }

        try {
            // Verify current password
            if (!$this->auth->verify($data['current_password'], $user['password'])) {
                $this->error_response('Current password is incorrect', 401);
                return;
            }

            // Hash new password
            $hashed_password = $this->auth->hash($data['new_password']);

            // Update user password
            $this->db->update(TABLE_USERS)
                ->set([
                    'password' => $hashed_password,
                    'updated_at' => time()
                ])
                ->where('user_id', '=', $user['user_id'])
                ->execute();

            // Remove all user tokens (force re-login on all devices)
            $this->db->delete(TABLE_TOKENS)
                ->where('user_id', '=', $user['user_id'])
                ->execute();

            $this->json_response(null, 200, 'Password updated successfully. Please login again.');

        } catch (Exception $e) {
            $this->error_response('Password update failed', 500);
        }
    }

}
