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
    private int $recovery_token_expires = 1800; // 30 minutes

    private int $password_min_length    = 6;
    private int $login_min_length       = 3;
    private int $login_max_length       = 50;
    
    private int $recovery_code_length   = 6;
    private int $max_recovery_attempts  = 3;



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
     * 
     * @param array $data
     * @return void
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
                ->first();

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
            if (!$this->auth->verify_password($data['password'], $user['password'])) {
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
                'expires_in' => $this->auth->token_expires,
                'user' => [
                    'user_id' => $user['user_id'],
                    'role' => $user['role'],
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
     * 
     * @return void
     */
    public function register(): void {
        $this->log_request('auth_register');
        
        $data = $this->get_input_data();
        
        // Validation pipeline using closures
        $validate = fn() => array_merge(
            $this->validate_required($data, ['login', 'email', 'password']),
            array_filter([
                'login' => match(true) {
                    strlen($data['login'] ?? '') < $this->login_min_length,
                    strlen($data['login'] ?? '') > $this->login_max_length 
                        => "Login must be between {$this->login_min_length} and {$this->login_max_length} characters",
                    default => null
                },
                'email' => !filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL) ? 'Invalid email format' : null,
                'password' => strlen($data['password'] ?? '') < $this->password_min_length 
                    ? "Password must be at least {$this->password_min_length} characters" : null,
                'password_confirmation' => isset($data['password_confirmation']) && $data['password'] !== $data['password_confirmation']
                    ? 'Password confirmation does not match' : null
            ])
        );

        // Check for existing users
        $check_existing = fn() => array_reduce(
            [['login', 'Login already exists'], ['email', 'Email already exists']],
            fn($carry, $check) => $carry ?: (
                $this->db->select()->from(TABLE_USERS)->where($check[0], '=', $data[$check[0]])->limit(1)->first()
                    ? ['message' => $check[1], 'code' => 409] 
                    : null
            ),
            null
        );

        // Create user data
        $create_user = fn() => (fn($time) => $this->db->insert(TABLE_USERS)->data([
            'login' => $data['login'],
            'email' => $data['email'], 
            'password' => $this->auth->hash_password($data['password']),
            'role' => 'u',
            'is_active' => '1',
            'is_verified' => '0',
            'is_banned' => '0',
            'ban_expired' => 0,
            'ban_reason' => '',
            'created_at' => $time,
            'updated_at' => $time,
            'last_activity' => $time
        ])->execute())(time());

        // Generate tokens
        $generate_tokens = fn($user_id) => (fn($access, $refresh) => [
            $this->auth->store_token($access, $refresh, $user_id),
            [
                'access_token' => $access,
                'refresh_token' => $refresh,
                'token_type' => 'Bearer',
                'expires_in' => $this->token_expires,
                'user' => [
                    'user_id' => $user_id,
                    'role' => 'u',
                    'login' => $data['login'],
                    'email' => $data['email'],
                    'is_verified' => false
                ]
            ]
        ][1])($this->auth->generate_token(), $this->auth->generate_token(64));

        // Execute registration pipeline
        try {
            // Validate input
            if ($validation_errors = $validate()) {
                $this->validation_error_response($validation_errors);
                return;
            }

            // Check existing users
            if ($existing_error = $check_existing()) {
                $this->error_response($existing_error['message'], $existing_error['code']);
                return;
            }

            // Create user and generate response
            if (!($user_id = $create_user())) {
                $this->error_response('Registration failed', 500);
                return;
            }

            $this->created_response($generate_tokens($user_id), 'Registration successful');

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
        if (empty($user)) {
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
     * 
     * @param array $data
     * @return void
     */
    public function refresh(): void {
        $this->log_request('auth_refresh');
        
        $data = $this->get_input_data();
        
        if (!isset($data['refresh_token']) || empty($data['refresh_token'])) {
            $this->error_response('Refresh token required', 400);
            return;
        }

        try {
            // Hash the refresh token before searching in database
            $refresh_token_hash = $this->auth->hash_data($data['refresh_token']);

            // Find valid refresh token
            $token_data = $this->db->select()
                ->from(TABLE_TOKENS)
                ->where('refresh', '=', $refresh_token_hash)
                ->where('refresh_expires', '>', time())
                ->limit(1)
                ->first();

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
                ->first();

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
     * 
     * @return void
     */
    public function recovery_password(): void {
        $this->log_request('password_recovery');
        
        $data = $this->get_input_data();
        
        // Validate required fields
        if (!isset($data['email']) || empty($data['email'])) {
            $this->error_response('Email is required', 400);
            return;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error_response('Invalid email format', 400);
            return;
        }

        try {
            // Find user by email
            $user = $this->db->select()
                ->from(TABLE_USERS)
                ->where('email', '=', $data['email'])
                ->where('is_active', '=', '1')
                ->limit(1)
                ->first();

            // Always return success to prevent email enumeration
            if (!$user) {
                $this->json_response(null, 200, 'If email exists, recovery code will be sent');
                return;
            }

            // Check if user is banned
            if ($user['is_banned'] == '1' && $user['ban_expired'] > time()) {
                $this->json_response(null, 200, 'If email exists, recovery code will be sent');
                return;
            }

            // Generate recovery code (6-digit numeric)
            $recovery_code = str_pad((string)random_int(100000, 999999), $this->recovery_code_length, '0', STR_PAD_LEFT);
            $code_hash = $this->auth->hash_data($recovery_code);
            $expires_at = time() + $this->recovery_token_expires;

            // Remove any existing recovery codes for this user
            $this->db->delete(TABLE_VERIFICATION_CODES)
                ->where('user_id', '=', $user['user_id'])
                ->where('code_type', '=', 'password_reset')
                ->execute();

            // Store recovery code
            $this->db->insert(TABLE_VERIFICATION_CODES)->data([
                'code_hash' => $code_hash,
                'user_id' => $user['user_id'],
                'email' => $user['email'],
                'code_type' => 'password_reset',
                'expires_at' => $expires_at,
                'used_at' => null,
                'attempts' => 0,
                'max_attempts' => $this->max_recovery_attempts
            ])->execute();

            // TODO: Send recovery email with code
            // For now, return code in response (remove in production!)
            $response_data = [];
            if (DEBUGMODE) {
                $response_data['recovery_code'] = $recovery_code; // Only in debug mode!
            }

            $this->json_response(
                $response_data,
                200, 
                'Recovery code sent to your email'
            );

        } catch (Exception $e) {
            $this->error_response('Password recovery failed', 500);
        }
    }


    /**
     * Reset password with token
     * POST /api/v1/auth/password/reset
     * 
     * @param array $data
     * @return void
     */
    public function reset_password(): void {
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
            // Find and validate recovery code
            $code_hash = $this->auth->hash_data($data['token']);
            
            $verification_code = $this->db->select()
                ->from(TABLE_VERIFICATION_CODES)
                ->where('code_hash', '=', $code_hash)
                ->where('code_type', '=', 'password_reset')
                ->where('expires_at', '>', time())
                ->where('used_at', 'IS', null)
                ->limit(1)
                ->first();

            if (!$verification_code) {
                $this->error_response('Invalid or expired reset code', 401);
                return;
            }

            // Check attempts limit
            if ($verification_code['attempts'] >= $verification_code['max_attempts']) {
                $this->error_response('Maximum attempts exceeded', 429);
                return;
            }

            // Get user
            $user = $this->db->select()
                ->from(TABLE_USERS)
                ->where('user_id', '=', $verification_code['user_id'])
                ->where('is_active', '=', '1')
                ->limit(1)
                ->first();

            if (!$user) {
                // Increment attempts even for invalid user
                $this->db->update(TABLE_VERIFICATION_CODES)
                    ->set(['attempts' => $verification_code['attempts'] + 1])
                    ->where('id', '=', $verification_code['id'])
                    ->execute();
                    
                $this->error_response('User not found', 404);
                return;
            }

            // Check if user is banned
            if ($user['is_banned'] == '1' && $user['ban_expired'] > time()) {
                $this->error_response('Account is banned', 403);
                return;
            }

            // Hash new password
            $hashed_password = $this->auth->hash_password($data['password']);

            // Update user password
            $this->db->update(TABLE_USERS)
                ->set([
                    'password' => $hashed_password,
                    'updated_at' => time()
                ])
                ->where('user_id', '=', $user['user_id'])
                ->execute();

            // Mark verification code as used
            $this->db->update(TABLE_VERIFICATION_CODES)
                ->set(['used_at' => time()])
                ->where('id', '=', $verification_code['id'])
                ->execute();

            // Remove all user tokens (force re-login on all devices)
            $this->db->delete(TABLE_TOKENS)
                ->where('user_id', '=', $user['user_id'])
                ->execute();

            // Clean up old/used verification codes for this user
            $this->db->delete(TABLE_VERIFICATION_CODES)
                ->where('user_id', '=', $user['user_id'])
                ->where('code_type', '=', 'password_reset')
                ->execute();

            $this->json_response(null, 200, 'Password reset successfully');

        } catch (Exception $e) {
            // Increment attempts on any error
            if (isset($verification_code)) {
                $this->db->update(TABLE_VERIFICATION_CODES)
                    ->set(['attempts' => $verification_code['attempts'] + 1])
                    ->where('id', '=', $verification_code['id'])
                    ->execute();
            }
            
            $this->error_response('Password reset failed', 500);
        }
    }


    /**
     * Update password for authenticated user
     * PUT /api/v1/auth/password
     * Requires: AuthMiddleware
     * 
     * @param array $data
     * @return void
     */
    public function update_password(): void {
        $this->log_request('password_update');
        
        $user = $this->require_authentication();
        if (empty($user)) {
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
            if (!$this->auth->verify_password($data['current_password'], $user['password'])) {
                $this->error_response('Current password is incorrect', 401);
                return;
            }

            // Hash new password
            $hashed_password = $this->auth->hash_password($data['new_password']);

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
