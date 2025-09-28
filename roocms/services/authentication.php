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
 * Authentication Service
 * Handles user authentication and token management
 */
class AuthenticationService {

    private Db $db;
    private Auth $auth;
    private UserValidationService $validator;



    /**
     * Constructor with dependency injection
     */
    public function __construct(Db $db, Auth $auth, UserValidationService $validator) {
        $this->db = $db;
        $this->auth = $auth;
        $this->validator = $validator;
    }


    /**
     * Authenticate user with login and password
     * 
     * @param string $login User login
     * @param string $password User password
     * @return array Authentication response with tokens and user data
     * @throws DomainException If authentication fails
     */
    public function login(string $login, string $password): array {
        $user = $this->validator->get_user_by_login($login);
        
        // Validate user existence and credentials
        if(!$user || $user['is_deleted'] == '1' || !$this->auth->verify_password($password, $user['password'])) {
            throw new DomainException('Invalid credentials', 401);
        }

        // Check account status
        $this->validator->validate_account_status($user);

        // Generate tokens and update activity
        $user_id = $this->validator->get_user_id($user);
        $tokens = $this->generate_user_tokens($user_id);
        
        $this->update_user_activity($user_id);

        return [
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'token_type' => 'Bearer',
            'expires_in' => $this->auth->token_expires,
            'refresh_expires_in' => $this->auth->refresh_token_expires,
            'user' => $this->format_user_data($user, $login)
        ];
    }


    /**
     * Refresh access token using refresh token
     * 
     * @param string $refresh_token Refresh token
     * @return array New tokens
     * @throws DomainException If refresh fails
     */
    public function refresh_token(string $refresh_token): array {
        // Hash refresh token and find in database
        $refresh_hash = $this->auth->hash_data($refresh_token);
        
        $token_data = $this->db->select()
            ->from(TABLE_TOKENS)
            ->where('refresh', $refresh_hash)
            ->where('refresh_expires', time(), '>')
            ->limit(1)
            ->first();
        
        if(!$token_data) {
            throw new DomainException('Invalid or expired refresh token', 401);
        }

        $user_id = (int)$token_data['user_id'];
        
        // Verify user still exists and is active
        $user = $this->db->select()->from(TABLE_USERS)->where('id', $user_id)->first();
        if(!$user || $user['is_deleted'] == '1') {
            throw new DomainException('User not found', 401);
        }

        $this->validator->validate_account_status($user);

        // Revoke old refresh token and generate new ones
        $this->revoke_refresh_token_internal($refresh_token);
        $tokens = $this->generate_user_tokens($user_id);
        $this->update_user_activity($user_id);

        return [
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'token_type' => 'Bearer',
            'expires_in' => $this->auth->token_expires,
            'refresh_expires_in' => $this->auth->refresh_token_expires
        ];
    }


    /**
     * Logout user from a specific device/token
     * @param int $user_id
     * @param string $refresh_token Refresh token to revoke
     * @throws DomainException
     */
    public function logout(int $user_id, string $refresh_token): void {
        $this->revoke_refresh_token_internal($refresh_token);
    }


    /**
     * Logout user by access token (revoke associated refresh token)
     * @param int $user_id
     * @param string $access_token Access token to find and revoke
     * @throws DomainException
     */
    public function logout_by_access_token(int $user_id, string $access_token): void {
        $token_hash = $this->auth->hash_data($access_token);
        
        // Find token record by access token
        $token_data = $this->db->select()
            ->from(TABLE_TOKENS)
            ->where('token', $token_hash)
            ->where('user_id', $user_id)
            ->limit(1)
            ->first();
        
        if (!$token_data) {
            throw new DomainException('Token not found', 404);
        }
        
        // Delete the entire token record (both access and refresh)
        $this->db->delete(TABLE_TOKENS)
            ->where('id', $token_data['id'])
            ->execute();
    }


    /**
     * Logout from all devices (revoke all tokens)
     * 
     * @param int $user_id User ID
     */
    public function logout_all_devices(int $user_id): void {
        $this->db->delete(TABLE_TOKENS)
            ->where('user_id', $user_id)
            ->execute();
    }


    /**
     * Revoke specific refresh token
     * 
     * @param int $user_id User ID
     * @param string $refresh_token Refresh token to revoke
     */
    public function revoke_refresh_token(int $user_id, string $refresh_token): void {
        $this->revoke_refresh_token_internal($refresh_token);
    }


    /**
     * Update user password (requires current password verification)
     * 
     * @param int $user_id User ID
     * @param string $current_password Current password
     * @param string $new_password New password
     * @throws DomainException If current password is invalid
     */
    public function update_password(int $user_id, string $current_password, string $new_password): void {
        $user = $this->db->select()->from(TABLE_USERS)->where('id', $user_id)->first();
        
        if(!$user || !$this->auth->verify_password($current_password, $user['password'])) {
            throw new DomainException('Current password is incorrect', 400);
        }

        $this->db->update(TABLE_USERS)
            ->data([
                'password' => $this->auth->hash_password($new_password),
                'updated_at' => time()
            ])
            ->where('id', $user_id)
            ->execute();

        // Revoke all tokens to force re-login
        $this->logout_all_devices($user_id);
    }


    /**
     * Generate access and refresh tokens for user
     * 
     * @param int $user_id User ID
     * @return array Tokens array
     */
    private function generate_user_tokens(int $user_id): array {
        $access_token = $this->auth->generate_token();
        $refresh_token = $this->auth->generate_token(64);
        
        $this->auth->store_token($access_token, $refresh_token, $user_id);
        
        return [
            'access_token' => $access_token,
            'refresh_token' => $refresh_token
        ];
    }


    /**
     * Update user last activity timestamp
     * 
     * @param int $user_id User ID
     */
    private function update_user_activity(int $user_id): void {
        $this->db->update(TABLE_USERS)
            ->data(['last_activity' => time()])
            ->where('id', $user_id)
            ->execute();
    }


    /**
     * Revoke refresh token (internal method)
     * 
     * @param string $refresh_token Refresh token to revoke
     */
    private function revoke_refresh_token_internal(string $refresh_token): void {
        $refresh_hash = $this->auth->hash_data($refresh_token);
        
        $this->db->delete(TABLE_TOKENS)
            ->where('refresh', $refresh_hash)
            ->execute();
    }


    /**
     * Format user data for response
     * 
     * @param array $user User data from database
     * @param string $login Original login (fallback)
     * @return array Formatted user data
     */
    private function format_user_data(array $user, string $login): array {
        return [
            'user_id' => $this->validator->get_user_id($user),
            'role' => $user['role'] ?? 'u',
            'login' => $user['login'] ?? $login,
            'email' => $user['email'],
            'is_verified' => $this->validator->is_user_verified($user)
        ];
    }


    /**
     * Verify access token and get user data
     * 
     * @param string $token Access token
     * @return array|null User data or null if invalid
     */
    public function verify_token(string $token): ?array {
        try {
            // Hash the token before searching in database
            $token_hash = $this->auth->hash_data($token);

            // Find valid token
            $token_data = $this->db->select()
                ->from(TABLE_TOKENS)
                ->where('token', $token_hash)
                ->where('token_expires', time(), '>')
                ->limit(1)
                ->first();

            if (!$token_data) {
                return null;
            }

            // Get user data
            $user = $this->db->select()
                ->from(TABLE_USERS)
                ->where('id', $token_data['user_id'])
                ->where('is_active', '1')
                ->limit(1)
                ->first();

            if (!$user) {
                return null;
            }

            // Update last activity
            $this->update_user_activity((int)$user['id']);

            return $user;

        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * Get current user by token
     * 
     * @param string $token Access token
     * @return array|null User data or null if invalid
     */
    public function get_current_user(string $token): ?array {
        return $this->verify_token($token);
    }


    /**
     * Check if user has specific role
     * 
     * @param array $user User data
     * @param string|array $required_roles Required role(s)
     * @return bool True if user has required role
     */
    public function user_has_role(array $user, string|array $required_roles): bool {
        $user_role = $user['role'] ?? 'u';
        $roles = is_array($required_roles) ? $required_roles : [$required_roles];
        
        return in_array($user_role, $roles, true);
    }
}
