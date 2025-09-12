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
 * Authentication Middleware
 * Validates Bearer tokens and sets current user context
 */
class AuthMiddleware {

    private readonly Db $db;
    private readonly Auth $auth;

    /**
     * Constructor
     */
    public function __construct(Db $db, Auth $auth) {
        $this->db = $db;
        $this->auth = $auth;
    }

    
    /**
     * Handle middleware execution
     * Returns true if authentication is successful, false otherwise
     */
    public function handle(): bool {
        try {
            $token = $this->get_bearer_token();

            if (!$token) {
                $this->send_error_response('Authorization token required', 401);
                return false;
            }

            $user = $this->authenticate_token($token);

            if (!$user) {
                return false; // Error response already sent
            }

            // Set authenticated user in global context
            // TODO: Move to global context
            $GLOBALS['authenticated_user'] = $user;

            return true;

        } catch (Exception $e) {
            $this->send_error_response('Authentication failed', 401);
            return false;
        }
    }


    /**
     * Get bearer token from Authorization header
     */
    private function get_bearer_token(): string|null {
        // Try common server variables
        $candidates = [
            env('HTTP_AUTHORIZATION'),
            env('Authorization'),
            env('HTTP_Authorization'),
            env('REDIRECT_HTTP_AUTHORIZATION')
        ];

        foreach ($candidates as $header) {
            if (is_string($header) && $header !== '') {
                if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
                    return $matches[1];
                }
            }
        }

        // Fallback to getallheaders()/apache_request_headers()
        $all = function_exists('getallheaders') ? getallheaders() : (function_exists('apache_request_headers') ? apache_request_headers() : []);
        if (is_array($all)) {
            foreach ($all as $key => $value) {
                if (stripos((string)$key, 'Authorization') === 0 && is_string($value)) {
                    if (preg_match('/Bearer\s+(.*)$/i', $value, $m)) {
                        return $m[1];
                    }
                }
            }
        }

        return null;
    }


    /**
     * Authenticate token and return user data
     */
    private function authenticate_token(string $token): array|null {
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
                $this->send_error_response('Invalid or expired token', 401);
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
                $this->send_error_response('User not found or inactive', 401);
                return null;
            }

            // Check if user is banned
            if ($user['is_banned'] == '1' && (int)$user['ban_expired'] > time()) {
                $this->send_error_response('Account is banned', 403, [
                    'ban_reason' => $user['ban_reason'],
                    'ban_expires' => format_timestamp($user['ban_expired'])
                ]);
                return null;
            }

            // Update last activity
            $this->db->update(TABLE_USERS)
                ->data(['last_activity' => time()])
                ->where('id', $user['id'])
                ->execute();

            return $user;

        } catch (Exception $e) {
            $this->send_error_response('Authentication failed', 401);
            return null;
        }
    }

    /**
     * Send error response and exit
     */
    private function send_error_response(string $message, int $code = 401, array $details = []): void {
        http_response_code($code);
        
        $response = [
            'error' => true,
            'message' => $message,
            'status_code' => $code,
            'timestamp' => format_timestamp(time())
        ];
        
        if (!empty($details)) {
            $response['details'] = $details;
        }
        
        output_json($response);
    }
}
