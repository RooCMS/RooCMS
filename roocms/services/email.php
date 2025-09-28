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
 * Email Service
 * Handles all email operations including verification
 */
class EmailService {

    private Db $db;
    private Auth $auth;
    private SiteSettings $siteSettings;
    private Mailer $mailer;



    /**
     * Constructor
     */
    public function __construct(Db $db, Auth $auth, SiteSettings $siteSettings, Mailer $mailer) {
        $this->db = $db;
        $this->auth = $auth;
        $this->siteSettings = $siteSettings;
        $this->mailer = $mailer;
    }


    /**
     * Get site configuration for emails
     * 
     * @return array Site config
     */
    private function get_site_config(): array {
        return [
            'name' => $this->siteSettings->get_by_key('site_name') ?? 'RooCMS',
            'domain' => $this->siteSettings->get_by_key('site_domain') ?? _DOMAIN,
            'verify_uri' => $this->siteSettings->get_by_key('mailer_verification_mail_uri') ?? '/verify-email'
        ];
    }


    /**
     * Send welcome email to new user
     * 
     * @param string $email User email
     * @param string $login User login
     */
    public function send_welcome_email(string $email, string $login): void {
        try {
            $site_config = $this->get_site_config();
            $site_url = 'https://' . $site_config['domain'];

            $this->mailer->send_with_template([
                'to' => $email,
                'subject' => 'Welcome to ' . $site_config['name'] . '!',
                'template' => 'welcome',
                'data' => [
                    'user_name' => $login,
                    'user_email' => $email,
                    'site_name' => $site_config['name'],
                    'site_url' => $site_url,
                    'login_url' => $site_url
                ],
            ]);
        } catch (Exception $e) {
            // Email sending is optional for welcome emails
        }
    }


    /**
     * Send password recovery email
     * 
     * @param array $user User data
     * @param string $recovery_code Recovery code
     */
    public function send_recovery_email(array $user, string $recovery_code): void {
        try {
            $site_config = $this->get_site_config();
            $site_url = 'https://' . $site_config['domain'];

            $this->mailer->send_with_template([
                'to' => $user['email'],
                'subject' => 'Password recovery on ' . $site_config['name'],
                'template' => 'notice',
                'data' => [
                    'title' => 'Recovery password',
                    'message' => "Your recovery code: ".$recovery_code."\nValid for 30 minutes.",
                    'user_name' => $user['login'] ?? '',
                    'site_name' => $site_config['name'],
                    'site_url' => $site_url,
                ],
            ]);
        } catch (Exception $e) {
            // Email sending is optional
        }
    }


    /**
     * Request email verification for user
     * 
     * @param array $user User data
     * @return array Response data (with debug info if needed)
     * 
     * TODO: This method need move to another class
     */
    public function request_email_verification(array $user): array {
        $user_id = (int)($user['id'] ?? $user['user_id']);
        $plain_code = $this->auth->generate_token(24);
        
        // Store verification code
        $this->store_verification_code(
            $user_id,
            $user['email'] ?? null,
            $plain_code,
            'verification',
            time() + 86400, // 24h
            3
        );

        // Send verification email
        $this->send_verification_email($user, $plain_code);

        return (defined('DEBUGMODE') && DEBUGMODE) ? ['verification_code' => $plain_code] : [];
    }


    /**
     * Send email verification message
     * 
     * @param array $user User data
     * @param string $verification_code Verification code
     */
    private function send_verification_email(array $user, string $verification_code): void {
        try {
            $site_config = $this->get_site_config();
            $verify_link = 'https://' . $site_config['domain'] . $site_config['verify_uri'] . '?' . rawurlencode($verification_code);

            $this->mailer->send_with_template([
                'to' => $user['email'],
                'subject' => 'Verify your email on ' . $site_config['name'],
                'template' => 'notice',
                'data' => [
                    'title' => 'Email verification',
                    'message' => "Use the link to verify your email:\n\n ".$verify_link."\nThe link is valid for 24 hours.",
                    'user_name' => $user['login'] ?? '',
                    'site_name' => $site_config['name'],
                    'site_url' => 'https://' . $site_config['domain'],
                ],
            ]);
        } catch (Exception $e) {
            // Email sending is optional - don't fail the request
        }
    }


    /**
     * Verify email with verification code
     * 
     * @param string $verification_code Verification code
     * @throws DomainException If verification fails
     */
    public function verify_email(string $verification_code): void {
        $code_hash = $this->auth->hash_data($verification_code);

        $sql = "SELECT * FROM " . TABLE_VERIFICATION_CODES . "
                WHERE code_hash = ?
                AND code_type = 'verification'
                AND expires_at > ?
                AND used_at IS NULL
                LIMIT 1";

        $record = $this->db->fetch_assoc($sql, [$code_hash, time()]);

        if(!$record) {
            throw new DomainException('Invalid or expired verification code', 400);
        }

        // Mark code as used and verify user
        $this->db->transaction(function() use ($record) {
            // Mark verification code as used
            $this->db->update(TABLE_VERIFICATION_CODES)
                ->data(['used_at' => time()])
                ->where('id', $record['id'])
                ->execute();

            // Mark user as verified
            $this->db->update(TABLE_USERS)
                ->data(['is_verified' => '1', 'updated_at' => time()])
                ->where('id', $record['user_id'])
                ->execute();
        });
    }


    /**
     * Store verification code in database
     * 
     * @param int $user_id User ID
     * @param string|null $email User email
     * @param string $plain_code Plain text code
     * @param string $code_type Code type (verification, password_reset)
     * @param int $expires_at Expiration timestamp
     * @param int $max_attempts Maximum attempts
     */
    public function store_verification_code(int $user_id, ?string $email, string $plain_code, string $code_type, int $expires_at, int $max_attempts): void {
        $current_time = time();

        // Remove old codes of the same type
        $this->db->delete(TABLE_VERIFICATION_CODES)
            ->where('user_id', $user_id)
            ->where('code_type', $code_type)
            ->execute();

        // Insert new code
        $this->db->insert(TABLE_VERIFICATION_CODES)->data([
            'code_hash' => $this->auth->hash_data($plain_code),
            'user_id' => $user_id,
            'email' => $email,
            'code_type' => $code_type,
            'expires_at' => $expires_at,
            'used_at' => null,
            'attempts' => 0,
            'max_attempts' => $max_attempts,
            'created_at' => $current_time,
            'updated_at' => $current_time
        ])->execute();
    }


    /**
     * Get verification code record
     * 
     * @param string $code_hash Hashed code
     * @param string $code_type Code type
     * @return array|null Code record or null if not found
     */
    public function get_verification_code(string $code_hash, string $code_type): ?array {
        $sql = "SELECT * FROM " . TABLE_VERIFICATION_CODES . "
                WHERE code_hash = ?
                AND code_type = ?
                AND expires_at > ?
                AND used_at IS NULL
                LIMIT 1";

        return $this->db->fetch_assoc($sql, [$code_hash, $code_type, time()]) ?: null;
    }


    /**
     * Mark verification code as used
     * 
     * @param int $code_id Code record ID
     */
    public function mark_code_as_used(int $code_id): void {
        $this->db->update(TABLE_VERIFICATION_CODES)
            ->data(['used_at' => time()])
            ->where('id', $code_id)
            ->execute();
    }
}
