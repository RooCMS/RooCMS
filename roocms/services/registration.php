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
 * Registration Service
 * Handles user registration operations
 */
class RegistrationService {

    private Db $db;
    private Auth $auth;
    private User $user;
    private UserValidationService $validator;
    private EmailService $emailService;



    /**
     * Constructor
     */
    public function __construct(Db $db, Auth $auth, User $user, UserValidationService $validator, EmailService $emailService) {
        $this->db = $db;
        $this->auth = $auth;
        $this->user = $user;
        $this->validator = $validator;
        $this->emailService = $emailService;
    }


    /**
     * Register a new user
     * 
     * @param string $login User login
     * @param string $email User email
     * @param string $password User password
     * @param array $profile_data Additional profile data (optional)
     * @return array Registration response with tokens and user data
     * @throws DomainException If registration fails
     */
    public function register(string $login, string $email, string $password, array $profile_data = []): array {
        // Validate registration data
        $this->validator->validate_registration_data($login, $email, $password);

        $time = time();
        
        // Create user and profile atomically
        $user_id = (int)$this->db->transaction(function() use ($login, $email, $password, $time, $profile_data) {
            // Create user using model
            $user_data = [
                'login' => $login,
                'email' => $email,
                'password' => $this->auth->hash_password($password),
                'role' => 'u',
                'is_active' => 1,
                'is_verified' => 0,
                'is_banned' => 0,
                'ban_expired' => $time,
                'ban_reason' => '',
                'last_activity' => $time
            ];
            
            $inserted_user_id = $this->user->create_user($user_data);
            if($inserted_user_id === false) {
                throw new RuntimeException('Registration failed', 500);
            }

            // Create profile using model (always create base profile)
            $this->user->upsert_profile($inserted_user_id, $profile_data);

            return $inserted_user_id;
        });

        // Generate tokens
        $access_token = $this->auth->generate_token();
        $refresh_token = $this->auth->generate_token(64);
        $this->auth->store_token($access_token, $refresh_token, $user_id);

        // Send welcome email
        $this->emailService->send_welcome_email($email, $login);

        return [
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'token_type' => 'Bearer',
            'expires_in' => $this->auth->token_expires,
            'refresh_expires_in' => $this->auth->refresh_token_expires,
            'user' => [
                'user_id' => $user_id,
                'login' => $login,
                'email' => $email,
                'is_verified' => false,
                'role' => 'u'
            ]
        ];
    }




    /**
     * Check if registration is allowed
     * 
     * @return bool True if registration is allowed
     */
    public function is_registration_enabled(): bool {
        // This could be configurable via site settings
        // For now, always allow registration
        return true;
    }


    /**
     * Get registration statistics
     * 
     * @return array Registration stats
     */
    public function get_registration_stats(): array {
        $today = strtotime('today');
        $week_ago = strtotime('-7 days');
        $month_ago = strtotime('-30 days');

        return [
            'total_users' => (int)$this->db->fetch_column(
                'SELECT COUNT(*) FROM ' . TABLE_USERS . ' WHERE is_deleted = 0'
            ),
            'today' => (int)$this->db->fetch_column(
                'SELECT COUNT(*) FROM ' . TABLE_USERS . ' WHERE created_at >= ? AND is_deleted = 0',
                [$today]
            ),
            'this_week' => (int)$this->db->fetch_column(
                'SELECT COUNT(*) FROM ' . TABLE_USERS . ' WHERE created_at >= ? AND is_deleted = 0',
                [$week_ago]
            ),
            'this_month' => (int)$this->db->fetch_column(
                'SELECT COUNT(*) FROM ' . TABLE_USERS . ' WHERE created_at >= ? AND is_deleted = 0',
                [$month_ago]
            ),
            'verified_users' => (int)$this->db->fetch_column(
                'SELECT COUNT(*) FROM ' . TABLE_USERS . ' WHERE is_verified = 1 AND is_deleted = 0'
            )
        ];
    }


}
