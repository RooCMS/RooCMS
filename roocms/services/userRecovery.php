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
 * User Recovery Service
 * Handles password recovery operations
 */
class UserRecoveryService {

    private Db $db;
    private Auth $auth;
    private UserValidationService $validator;
    private EmailService $emailService;

    private int $recovery_code_length = 6;
    private int $max_recovery_attempts = 3;
    private int $recovery_code_expires = 1800; // 30 minutes



    /**
     * Constructor
     */
    public function __construct(Db $db, Auth $auth, UserValidationService $validator, EmailService $emailService) {
        $this->db = $db;
        $this->auth = $auth;
        $this->validator = $validator;
        $this->emailService = $emailService;
    }


    /**
     * Request password recovery by email
     * 
     * @param string $email User email
     * @return array Response data (with debug info if needed)
     */
    public function request_password_recovery(string $email): array {
        $user = $this->validator->get_user_by_email($email);
        
        if(!$user) {
            // Return empty response for security (don't reveal if email exists)
            return [];
        }

        $recovery_code = $this->generate_recovery_code();
        $code_hash = $this->auth->hash_data($recovery_code);
        $expires_at = time() + $this->recovery_code_expires;

        // Store recovery code
        $this->emailService->store_verification_code(
            $this->validator->get_user_id($user),
            $user['email'],
            $recovery_code,
            'password_reset',
            $expires_at,
            $this->max_recovery_attempts
        );

        // Send recovery email
        $this->emailService->send_recovery_email($user, $recovery_code);

        return (defined('DEBUGMODE') && DEBUGMODE) ? ['recovery_code' => $recovery_code] : [];
    }


    /**
     * Reset password using recovery token
     * 
     * @param string $token Recovery token
     * @param string $new_password New password
     * @throws DomainException If reset fails
     */
    public function reset_password(string $token, string $new_password): void {
        $code_hash = $this->auth->hash_data($token);
        $record = $this->emailService->get_verification_code($code_hash, 'password_reset');

        if(!$record) {
            throw new DomainException('Invalid or expired recovery code', 400);
        }

        // Check attempts limit
        if($record['attempts'] >= $record['max_attempts']) {
            throw new DomainException('Too many recovery attempts', 429);
        }

        // Validate new password
        $this->validate_new_password($new_password);

        // Reset password and mark code as used
        $this->db->transaction(function() use ($record, $new_password) {
            // Update password
            $this->db->query(
                'UPDATE ' . TABLE_USERS . ' SET password = ?, updated_at = ? WHERE id = ?',
                [$this->auth->hash_password($new_password), time(), $record['user_id']]
            );

            // Mark recovery code as used
            $this->emailService->mark_code_as_used($record['id']);

            // Revoke all existing tokens to force re-login
            $this->auth->revoke_token_by_user_id($record['user_id']);
        });
    }


    /**
     * Verify recovery token without resetting password
     * 
     * @param string $token Recovery token
     * @return array Token info
     * @throws DomainException If token is invalid
     */
    public function verify_recovery_token(string $token): array {
        $code_hash = $this->auth->hash_data($token);
        $record = $this->emailService->get_verification_code($code_hash, 'password_reset');

        if(!$record) {
            throw new DomainException('Invalid or expired recovery code', 400);
        }

        if($record['attempts'] >= $record['max_attempts']) {
            throw new DomainException('Too many recovery attempts', 429);
        }

        return [
            'valid' => true,
            'user_id' => $record['user_id'],
            'email' => $record['email'],
            'expires_at' => $record['expires_at'],
            'attempts' => $record['attempts'],
            'max_attempts' => $record['max_attempts']
        ];
    }


    /**
     * Increment recovery attempts counter
     * 
     * @param string $token Recovery token
     */
    public function increment_recovery_attempts(string $token): void {
        $code_hash = $this->auth->hash_data($token);
        
        $this->db->query(
            'UPDATE ' . TABLE_VERIFICATION_CODES . ' 
             SET attempts = attempts + 1, updated_at = ? 
             WHERE code_hash = ? AND code_type = ?',
            [time(), $code_hash, 'password_reset']
        );
    }


    /**
     * Get recovery statistics
     * 
     * @return array Recovery stats
     */
    public function get_recovery_stats(): array {
        $today = strtotime('today');
        $week_ago = strtotime('-7 days');

        return [
            'total_requests' => (int)$this->db->fetch_column(
                'SELECT COUNT(*) FROM ' . TABLE_VERIFICATION_CODES . ' WHERE code_type = ?',
                ['password_reset']
            ),
            'today_requests' => (int)$this->db->fetch_column(
                'SELECT COUNT(*) FROM ' . TABLE_VERIFICATION_CODES . ' WHERE code_type = ? AND created_at >= ?',
                ['password_reset', $today]
            ),
            'week_requests' => (int)$this->db->fetch_column(
                'SELECT COUNT(*) FROM ' . TABLE_VERIFICATION_CODES . ' WHERE code_type = ? AND created_at >= ?',
                ['password_reset', $week_ago]
            ),
            'successful_resets' => (int)$this->db->fetch_column(
                'SELECT COUNT(*) FROM ' . TABLE_VERIFICATION_CODES . ' WHERE code_type = ? AND used_at IS NOT NULL',
                ['password_reset']
            ),
            'active_codes' => (int)$this->db->fetch_column(
                'SELECT COUNT(*) FROM ' . TABLE_VERIFICATION_CODES . ' WHERE code_type = ? AND expires_at > ? AND used_at IS NULL',
                ['password_reset', time()]
            )
        ];
    }


    /**
     * Clean expired recovery codes
     * 
     * @return int Number of cleaned codes
     */
    public function clean_expired_codes(): int {
        $result = $this->db->query(
            'DELETE FROM ' . TABLE_VERIFICATION_CODES . ' 
             WHERE code_type = ? AND expires_at <= ?',
            ['password_reset', time()]
        );

        return $result->rowCount();
    }


    /**
     * Check if user can request recovery (rate limiting)
     * 
     * @param string $email User email
     * @return bool True if can request
     */
    public function can_request_recovery(string $email): bool {
        $user = $this->validator->get_user_by_email($email);
        
        if(!$user) {
            return false;
        }

        $user_id = $this->validator->get_user_id($user);
        $rate_limit_window = 300; // 5 minutes
        $max_requests = 3;

        $recent_requests = (int)$this->db->fetch_column(
            'SELECT COUNT(*) FROM ' . TABLE_VERIFICATION_CODES . ' 
             WHERE user_id = ? AND code_type = ? AND created_at > ?',
            [$user_id, 'password_reset', time() - $rate_limit_window]
        );

        return $recent_requests < $max_requests;
    }


    /**
     * Generate recovery code
     * 
     * @return string Recovery code
     */
    private function generate_recovery_code(): string {
        $min = (int)str_repeat('1', $this->recovery_code_length - 1);
        $max = (int)str_repeat('9', $this->recovery_code_length);
        
        return str_pad(
            (string)random_int($min, $max), 
            $this->recovery_code_length, 
            '0', 
            STR_PAD_LEFT
        );
    }


    /**
     * Validate new password strength
     * 
     * @param string $password Password to validate
     * @throws DomainException If password is weak
     */
    private function validate_new_password(string $password): void {
        if(strlen($password) < $this->auth->password_length) {
            throw new DomainException('Password must be at least ' . $this->auth->password_length . ' characters long', 400);
        }

        // Additional password strength checks can be added here
        if(!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            throw new DomainException('Password must contain both letters and numbers', 400);
        }
    }


    /**
     * Get user recovery history
     * 
     * @param int $user_id User ID
     * @param int $limit Number of records to return
     * @return array Recovery history
     */
    public function get_user_recovery_history(int $user_id, int $limit = 10): array {
        return $this->db->fetch_all(
            'SELECT code_type, created_at, used_at, attempts, expires_at 
             FROM ' . TABLE_VERIFICATION_CODES . ' 
             WHERE user_id = ? AND code_type = ? 
             ORDER BY created_at DESC 
             LIMIT ?',
            [$user_id, 'password_reset', $limit]
        );
    }

    
    /**
     * Revoke all active recovery codes for user
     * 
     * @param int $user_id User ID
     */
    public function revoke_user_recovery_codes(int $user_id): void {
        $this->db->query(
            'UPDATE ' . TABLE_VERIFICATION_CODES . ' SET used_at = ? WHERE user_id = ? AND code_type = ? AND used_at IS NULL',
            [time(), $user_id, 'password_reset']
        );
    }
}
