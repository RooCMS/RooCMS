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

    private readonly AuthService $authService;
    
    


    /**
     * Constructor with dependency injection
     */
    public function __construct(AuthService $authService, Db $db) {
        parent::__construct($db);

        $this->authService = $authService;
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
        if (strlen($data['login']) < $this->authService->login_min_length) {
            $validation_errors['login'] = 'Login must be at least ' . $this->authService->login_min_length . ' characters';
        }
        
        if (strlen($data['password']) < $this->authService->password_min_length) {
            $validation_errors['password'] = 'Password must be at least ' . $this->authService->password_min_length . ' characters';
        }

        if (!empty($validation_errors)) {
            $this->validation_error_response($validation_errors);
            return;
        }

        try {
            $response_data = $this->authService->login($data['login'], $data['password']);
            $this->json_response($response_data, 200, 'Login successful');
        } catch (DomainException $e) {
            $this->error_response($e->getMessage(), $e->getCode() ?: 400);
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
        
        // Validate input
        $validation_errors = array_merge(
            $this->validate_required($data, ['login', 'email', 'password']),
            array_filter([
                'login' => match(true) {
                    strlen($data['login'] ?? '') < $this->authService->login_min_length,
                    strlen($data['login'] ?? '') > $this->authService->login_max_length 
                        => "Login must be between " . $this->authService->login_min_length ." and ". $this->authService->login_max_length ." characters",
                    default => null
                },
                'email' => !filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL) ? 'Invalid email format' : null,
                'password' => strlen($data['password'] ?? '') < $this->authService->password_min_length 
                    ? "Password must be at least " . $this->authService->password_min_length ." characters" : null,
                'password_confirmation' => isset($data['password_confirmation']) && $data['password'] !== $data['password_confirmation']
                    ? 'Password confirmation does not match' : null
            ])
        );

        if (!empty($validation_errors)) {
            $this->validation_error_response($validation_errors);
            return;
        }

        try {
            $response_payload = $this->authService->register($data['login'], $data['email'], $data['password']);
            $this->created_response($response_payload, 'Registration successful');
        } catch (DomainException $e) {
            $this->error_response($e->getMessage(), $e->getCode() ?: 400);
        } catch (Exception $e) {
            $this->error_response('Registration failed', 500);
        }
    }


    /**
     * User logout
     * POST /api/v1/auth/logout
     * requires: AuthMiddleware
     * 
     * @return void
     */
    public function logout(): void {
        $this->log_request('auth_logout');
        
        $user = $this->require_authentication();
        if (empty($user)) {
            return; // Error response already sent
        }

        try {
            // Extract bearer using shared helper
            $access_token = get_bearer_token();
            if ($access_token === null) {
                $this->error_response('Authorization token required', 401);
                return;
            }
            $this->authService->logout((int)$user['id'], $access_token);

            $this->json_response(null, 200, 'Logout successful');
        } catch (Exception $e) {
            $this->error_response('Logout failed', 500);
        }
    }


    /**
     * User logout all devices
     * POST /api/v1/auth/logout/all
     * requires: AuthMiddleware
     * 
     * @return void
     */
    public function logout_all(): void {
        $this->log_request('auth_logout_all');
        
        $user = $this->require_authentication();
        if (empty($user)) {
            return; // Error response already sent
        }

        try {
            $this->authService->logout_all_devices((int)$user['id']);
            $this->json_response(null, 200, 'Logout all devices successful');
        } catch (Exception $e) {
            $this->error_response('Logout all devices failed', 500);
        }
    }


    /**
     * Revoke a specific refresh token
     * POST /api/v1/auth/refresh/revoke
     * requires: AuthMiddleware
     * 
     * @return void
     */
    public function revoke_refresh(): void {
        $this->log_request('auth_revoke_refresh');

        $user = $this->require_authentication();
        if (empty($user)) {
            return; // Error response already sent
        }

        $data = $this->get_input_data();
        if (!isset($data['refresh_token']) || $data['refresh_token'] === '') {
            $this->error_response('Refresh token required', 400);
            return;
        }

        try {
            $this->authService->revoke_refresh_token((int)$user['id'], (string)$data['refresh_token']);
            $this->json_response(null, 200, 'Refresh token revoked');
        } catch (Exception $e) {
            $this->error_response('Failed to revoke refresh token', 500);
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
            $response_data = $this->authService->refresh($data['refresh_token']);
            $this->json_response($response_data, 200, 'Token refreshed successfully');
        } catch (DomainException $e) {
            $this->error_response($e->getMessage(), $e->getCode() ?: 400);
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
            $response_data = $this->authService->request_password_recovery($data['email']);
            $this->json_response($response_data, 200, 'Recovery code sent to your email');
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

        if (strlen($data['password']) < $this->authService->password_min_length) {
            $this->error_response('Password must be at least ' . $this->authService->password_min_length . ' characters', 400);
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
            $this->authService->reset_password($data['token'], $data['password']);
            $this->json_response(null, 200, 'Password reset successfully');
        } catch (DomainException $e) {
            $this->error_response($e->getMessage(), $e->getCode() ?: 400);
        } catch (Exception $e) {
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

        if (strlen($data['new_password']) < $this->authService->password_min_length) {
            $this->error_response('New password must be at least ' . $this->authService->password_min_length . ' characters', 400);
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
            $this->authService->update_password((int)$user['id'], $data['current_password'], $data['new_password']);
            $this->json_response(null, 200, 'Password updated successfully. Please login again.');
        } catch (DomainException $e) {
            $this->error_response($e->getMessage(), $e->getCode() ?: 400);
        } catch (Exception $e) {
            $this->error_response('Password update failed', 500);
        }
    }

}
