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

    /**
     * Constructor
     */
    public function __construct(Db $db) {
        $this->db = $db;
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
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['HTTP_Authorization'] ?? '';

        if (preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            return $matches[1];
        }

        return null;
    }


    /**
     * Authenticate token and return user data
     */
    private function authenticate_token(string $token): array|null {
        try {
            // Find valid token
            $token_data = $this->db->select()
                ->from(TABLE_TOKENS)
                ->where('hash', '=', $token)
                ->where('token_expires', '>', time())
                ->limit(1)
                ->fetchOne();

            if (!$token_data) {
                $this->send_error_response('Invalid or expired token', 401);
                return null;
            }

            // Get user data
            $user = $this->db->select()
                ->from(TABLE_USERS)
                ->where('user_id', '=', $token_data['user_id'])
                ->where('is_active', '=', '1')
                ->limit(1)
                ->fetchOne();

            if (!$user) {
                $this->send_error_response('User not found or inactive', 401);
                return null;
            }

            // Check if user is banned
            if ($user['is_banned'] == '1' && $user['ban_expired'] > time()) {
                $this->send_error_response('Account is banned', 403, [
                    'ban_reason' => $user['ban_reason'],
                    'ban_expires' => format_timestamp($user['ban_expired'])
                ]);
                return null;
            }

            // Update last activity
            $this->db->update(TABLE_USERS)
                ->set(['last_activity' => time()])
                ->where('user_id', '=', $user['user_id'])
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
