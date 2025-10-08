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
 * Auth Class
 * Provides utilities for working with authentication
 */
class Auth {

    private Db $db;
    private SiteSettings $siteSettings;

    private string $hash_key;

    protected int $hash_cost;
    protected int $token_length;

    public int $token_expires;
    public int $refresh_token_expires;
    public int $password_length;

    

    /**
     * Constructor
     */
    public function __construct(Db $db, SiteSettings $siteSettings) {
        $this->db = $db;
        $this->siteSettings = $siteSettings;

        $this->hash_key = $this->siteSettings->get_by_key('security_token_hash_key') ?? 'RooCMS';
        $this->hash_cost = (int)($this->siteSettings->get_by_key('security_token_hash_cost') ?? 10);
        $this->token_length = (int)($this->siteSettings->get_by_key('security_token_length') ?? 32);
        $this->token_expires = (int)($this->siteSettings->get_by_key('security_token_expires') ?? 3600);
        $this->refresh_token_expires = (int)($this->siteSettings->get_by_key('security_refresh_token_expires') ?? 86400);
        $this->password_length = (int)($this->siteSettings->get_by_key('security_password_length') ?? 8);
    }


    /**
     * Generate password
     *
     * @param int|null $length
     * @return string
     */
    public function generate_password(?int $length = null): string {
        $length = $length ?? $this->password_length;
        
        $password = randcode($length, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$!%');

        return $password;
    }


    /**
     * Generate token
     *
     * @param int|null $length
     * @return string
     */
    public function generate_token(?int $length = null): string {

        $length = $length ?? $this->token_length;

        // Generate cryptographically secure random bytes
        $bytes = random_bytes($length);
        
        // Convert to readable string (base64 without special characters)
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }


    /**
     * Hash data
     *
     * @param string $data
     * @return string
     */
    public function hash_password(string $data): string {
        
        // options for password_hash
        $options = [
            'cost' => $this->hash_cost,
        ];

        $hash = password_hash($data, PASSWORD_DEFAULT, $options);
        if($hash === false) {
            throw new RuntimeException('Password hashing failed');
        }
        return $hash;
    }


    /**
     * Verify data
     *
     * @param string $data
     * @param string $hash
     * @return bool
     */
    public function verify_password(string $data, string $hash): bool {
        return password_verify($data, $hash);
    }


    /**
     * Hash data using HMAC
     *
     * @param string $data
     * @return string
     */
    public function hash_data(string $data): string {
        return hash_hmac('sha3-256', $data, $this->hash_key);
    }


    /**
     * Verify data
     *
     * @param string $data
     * @param string $hash
     * @return bool
     */
    public function verify_data(string $data, string $hash): bool {
        return hash_equals($hash, $this->hash_data($data));
    }


    /**
     * Store token (hashes tokens before storing)
     *
     * @param string $token
     * @param string $refresh_token
     * @param int $user_id
     * @param int|null $expires
     * @return void
     */
    public function store_token(string $token, string $refresh_token, int $user_id, ?int $expires = null): void {
        $expires = $expires ?? $this->token_expires;
        $expires = time() + $expires;
        $refresh_token_expires = time() + $this->refresh_token_expires;

        // Hash tokens before storing in database
        $token_hash = $this->hash_data($token);
        $refresh_hash = $this->hash_data($refresh_token);

        // insert token to database
        $this->db->insert_array([
            'token' => $token_hash,
            'refresh' => $refresh_hash,
            'user_id' => $user_id,
            'token_expires' => $expires,
            'refresh_expires' => $refresh_token_expires,
            'created_at' => time()
        ], TABLE_TOKENS);
    }


    /**
     * Revoke token by user id
     * This function will revoke all tokens for the user
     *
     * @param int $user_id
     * @return void
     */
    public function revoke_token_by_user_id(int $user_id): void {
        $this->db->query('DELETE FROM ' . TABLE_TOKENS . ' WHERE user_id = ?', [$user_id]);
    }


    /**
     * Revoke token universal
     * 
     * @param string $token
     * @return void
     */
    public function revoke_token($token) {
        $this->revoke_access_token($token);
        $this->revoke_refresh_token($token);
    }


    /**
     * Revoke access token
     *
     * @param string $token
     * @return void
     */
    public function revoke_access_token(string $token): void {
        $token_hash = $this->hash_data($token);
        $this->db->query('DELETE FROM ' . TABLE_TOKENS . ' WHERE token = ?', [$token_hash]);
    }


    /**
     * Revoke refresh token
     *
     * @param string $refresh_token
     * @return void
     */
    public function revoke_refresh_token(string $refresh_token): void {
        $refresh_hash = $this->hash_data($refresh_token);
        $this->db->query('DELETE FROM ' . TABLE_TOKENS . ' WHERE refresh = ?', [$refresh_hash]);
    }
}