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



class Auth {

    private Db $db;

    protected int $hash_cost                = 10;
    protected int $token_length             = 32;
    protected int $token_expires            = 3600;
    protected int $refresh_token_expires    = 86400;
    protected int $password_length          = 9;


    /**
     * Constructor
     */
    public function __construct(Db $db) {
        $this->db = $db;
    }


    /**
     * Generate password
     *
     * @param int $length
     * @return string
     */
    public function generate_password(int $length = null): string {
        $length = $length ?? $this->password_length;
        
        $password = randcode($length, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$!%');

        return $password;
    }


    /**
     * Generate token
     *
     * @param int $length
     * @return string
     */
    public function generate_token(int $length = null): string {

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
    public function hash(string $data): string {
        
        // options for password_hash
        $options = [
            'cost' => $this->hash_cost,
        ];

        return password_hash($data, PASSWORD_DEFAULT, $options);
    }


    /**
     * Verify data
     *
     * @param string $data
     * @param string $hash
     * @return bool
     */
    public function verify(string $data, string $hash): bool {
        return password_verify($data, $hash);
    }


    /**
     * Store token
     *
     * @param string $hash
     * @param int $user_id
     * @param int $expires
     * @return void
     */
    public function store_token(string $token_hash, string $refresh_hash, int $user_id, int $expires = null): void {
        $expires = $expires ?? $this->token_expires;
        $expires = time() + $expires;
        $refresh_token_expires = time() + $this->refresh_token_expires;

        // insert token to database
        $this->db->insert(TABLE_TOKENS)->data([
            'hash' => $token_hash,
            'refresh' => $refresh_hash,
            'user_id' => $user_id,
            'token_expires' => $expires,
            'refresh_expires' => $refresh_token_expires,
            'created_at' => time()
        ])->execute();
    }
}