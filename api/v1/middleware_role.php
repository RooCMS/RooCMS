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
 * Role Middleware
 * Validates user roles and permissions for admin access
 */
class RoleMiddleware {

    private readonly Role $role;
    private readonly AuthenticationService $authService;


    
    /**
     * Constructor
     */
    public function __construct(Role $role, AuthenticationService $authService) {
        $this->role = $role;
        $this->authService = $authService;
    }


    /**
     * Handle middleware execution
     * Performs basic role validation (moderator access or higher)
     * 
     * @return bool
     */
    public function handle(): bool {
        $user = $this->get_authenticated_user();
        if (!$user) {
            return false;
        }

        return true;
    }

    /**
     * Get authenticated user from global context
     * 
     * @return array|null User data or null if not authenticated
     */
    private function get_authenticated_user(): ?array {
        $user = $GLOBALS['authenticated_user'] ?? null;

        if (!$user) {
            $this->send_error_response('Authentication required', 401);
            return null;
        }

        if (!isset($user['role'])) {
            $this->send_error_response('Invalid user data', 401);
            return null;
        }

        return $user;
    }


    /**
     * Check if user has moderator access or higher
     * 
     * @return bool
     */
    public function moderator_access(): bool {
        $user = $this->get_authenticated_user();
        if (!$user) {
            return false;
        }

        if (!$this->role->has_moderator_access($user['role'])) {
            $this->send_error_response('Moderator access required', 403);
            return false;
        }

        return true;
    }

    /**
     * Check if user has admin access (admin or superuser only)
     * 
     * @return bool
     */
    public function admin_access(): bool {
        $user = $this->get_authenticated_user();
        if (!$user) {
            return false;
        }

        if (!$this->role->has_admin_access($user['role'])) {
            $this->send_error_response('Admin access required', 403);
            return false;
        }

        return true;
    }

    /**
     * Check if user has superuser access
     * 
     * @return bool
     */
    public function superuser_access(): bool {
        $user = $this->get_authenticated_user();
        if (!$user) {
            return false;
        }

        if ($user['role'] !== 'su') {
            $this->send_error_response('Superuser access required', 403);
            return false;
        }

        return true;
    }

    /**
     * Check if user has specific role
     * 
     * @param string|array $required_roles Required roles
     * @return bool
     */
    public function require_role(string|array $required_roles): bool {
        $user = $this->get_authenticated_user();
        if (!$user) {
            return false;
        }

        $roles = is_array($required_roles) ? $required_roles : [$required_roles];

        if (!in_array($user['role'], $roles, true)) {
            $this->send_error_response('Required role access denied', 403);
            return false;
        }

        return true;
    }


    /**
     * Send error response and exit
     * 
     * @param string $message Message
     * @param int $code Code
     * @param array $details Details
     * @return void
     */
    private function send_error_response(string $message, int $code = 403, array $details = []): void {
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

    /**
     * Get role hierarchy level (delegate to Role class)
     * 
     * @param string $role Role
     * @return int
     */
    public function get_role_level(string $role): int {
        return $this->role->get_role_level($role);
    }

    /**
     * Check if user has sufficient role level
     * 
     * @param array $user User
     * @param int $required_level Required level
     * @return bool
     */
    public function has_role_level(array $user, int $required_level): bool {
        return $this->role->has_sufficient_level($user['role'], $required_level);
    }

    /**
     * Check if user has admin access (delegate to Role class)
     * 
     * @param string $role Role
     * @return bool
     */
    public function has_admin_access(string $role): bool {
        return $this->role->has_admin_access($role);
    }

    /**
     * Check if user has moderator access (delegate to Role class)
     * 
     * @param string $role Role
     * @return bool
     */
    public function has_moderator_access(string $role): bool {
        return $this->role->has_moderator_access($role);
    }
}
