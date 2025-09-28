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
 * User Validation Service
 * Handles all user validation operations
 */
class UserValidationService {

    private User $user;
    private SiteSettings $siteSettings;



    /**
     * Construct
     */
    public function __construct(User $user, SiteSettings $siteSettings) {
        $this->user = $user;
        $this->siteSettings = $siteSettings;
    }


    /**
     * Check if login already exists
     * 
     * @param string $login Login to check
     * @return bool True if exists
     */
    public function login_exists(string $login): bool {
        return $this->user->login_exists($login);
    }


    /**
     * Check if email already exists
     * 
     * @param string $email Email to check
     * @return bool True if exists
     */
    public function email_exists(string $email): bool {
        return $this->user->email_exists($email);
    }


    /**
     * Validate user credentials and status
     * 
     * @param string $login User login
     * @param string $password User password
     * @return array User data if valid
     * @throws DomainException If validation fails
     */
    public function validate_user_credentials(string $login, string $password): array {
        $user = $this->user->get_user_by_login($login);
        
        // Check user existence
        if(!$user) {
            throw new DomainException('Invalid credentials', 401);
        }

        return $user;
    }


    /**
     * Validate user account status
     * 
     * @param array $user User data
     * @throws DomainException If account has issues
     */
    public function validate_account_status(array $user): void {
        if($user['is_active'] != '1') {
            throw new DomainException('Account is not active', 403);
        }
        
        if($user['is_banned'] == '1' && (int)$user['ban_expired'] > time()) {
            throw new DomainException('Account is banned', 403);
        }
    }


    /**
     * Get user by login for authentication
     * 
     * @param string $login User login
     * @return array|null User data or null if not found
     */
    public function get_user_by_login(string $login): ?array {
        return $this->user->get_user_by_login($login);
    }


    /**
     * Get user by email for recovery operations
     * 
     * @param string $email User email
     * @return array|null User data or null if not found/invalid
     */
    public function get_user_by_email(string $email): ?array {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $user = $this->user->get_user_by_email($email);

        if(!$user) {
            return null;
        }

        // Additional checks for recovery operations
        if($user['is_active'] != '1') {
            return null;
        }

        if($user['is_banned'] == '1' && (int)$user['ban_expired'] > time()) {
            return null;
        }

        return $user;
    }


    /**
     * Validate password strength
     *
     * @param string $password Password to validate
     * @throws DomainException If password is too short
     */
    public function validate_password_strength(string $password): void {
        // Get minimum password length from site settings or use default 8
        $min_length = (int)($this->siteSettings->get_by_key('security_password_length') ?? 8);

        if (strlen($password) < $min_length) {
            throw new DomainException("Password must be at least " . $min_length . " characters long", 400);
        }
    }


    /**
     * Validate registration data
     *
     * @param string $login Login to validate
     * @param string $email Email to validate
     * @param string $password Password to validate
     * @throws DomainException If validation fails
     */
    public function validate_registration_data(string $login, string $email, string $password): void {
        $validation_checks = [
            ['method' => 'login_exists', 'value' => $login, 'error' => 'Login already exists'],
            ['method' => 'email_exists', 'value' => $email, 'error' => 'Email already exists']
        ];

        foreach($validation_checks as $check) {
            if($this->{$check['method']}($check['value'])) {
                throw new DomainException($check['error'], 409);
            }
        }

        // Validate password strength
        $this->validate_password_strength($password);
    }


    /**
     * Get user ID from user data (handles different field names)
     * 
     * @param array $user User data
     * @return int User ID
     */
    public function get_user_id(array $user): int {
        return (int)($user['id'] ?? $user['user_id'] ?? 0);
    }

    
    /**
     * Check if user is verified
     * 
     * @param array $user User data
     * @return bool True if verified
     */
    public function is_user_verified(array $user): bool {
        return ($user['is_verified'] ?? '0') == '1';
    }
}
