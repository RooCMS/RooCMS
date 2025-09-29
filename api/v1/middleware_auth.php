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
 * Authentication Middleware
 * Validates Bearer tokens and sets current user context
 */
class AuthMiddleware {

    private readonly AuthenticationService $authService;
    private readonly UserValidationService $validator;



    /**
     * Constructor
     */
    public function __construct(AuthenticationService $authService, UserValidationService $validator) {
        $this->authService = $authService;
        $this->validator = $validator;
    }

    
    /**
     * Handle middleware execution
     * Returns true if authentication is successful, false otherwise
     * 
     * @return bool
     */
    public function handle(): bool {
        try {
            $token = get_bearer_token();

            if (!$token) {
                $this->send_error_response('Authorization token required', 401);
                return false;
            }

            $user = $this->authService->verify_token($token);

            if (!$user) {
                $this->send_error_response('Invalid or expired token', 401);
                return false;
            }

            // Validate account status
            $this->validator->validate_account_status($user);

            // Set authenticated user in global context
            $GLOBALS['authenticated_user'] = $user;

            return true;

        } catch (DomainException $e) {
            $this->send_error_response($e->getMessage(), $e->getCode() ?: 401);
            return false;
        } catch (Exception $e) {
            $this->send_error_response('Authentication failed', 401);
            return false;
        }
    }



    /**
     * Send error response and exit
     * 
     * @param string $message Message
     * @param int $code Code
     * @param array $details Details
     * @return void
     */
    private function send_error_response(string $message, int $code = 401, array $details = []): never {
        
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
        // This line will never execute, but helps static analysis
        throw new RuntimeException('Response sent'); // TODO: Maybe it will break the analyzer?
    }
}
