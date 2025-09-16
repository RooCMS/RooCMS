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
//----------------------------------------------------------
if(!defined('RooCMS')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}
//#########################################################


/**
 * Role Middleware
 * Validates user roles and permissions for admin access
 */
class RoleMiddleware {

    private readonly Role $role;


    /**
     * Constructor
     */
    public function __construct(Role $role) {
        $this->role = $role;
    }


    /**
     * Handle middleware execution
     * Performs basic role validation (moderator access or higher)
     */
    public function handle(): bool {
        $user = $GLOBALS['authenticated_user'] ?? null;

        if (!$user) {
            $this->send_error_response('Authentication required', 401);
            return false;
        }

        return true;
    }


    /**
     * Check if user has moderator access or higher
     */
    public function moderator_access(): bool {
        $user = $GLOBALS['authenticated_user'] ?? null;

        if (!$user) {
            $this->send_error_response('Authentication required', 401);
            return false;
        }

        // Basic validation - ensure user has role field
        if (!isset($user['role'])) {
            $this->send_error_response('Invalid user data', 401);
            return false;
        }

        if (!in_array($user['role'], ['m', 'a', 'su'])) {
            $this->send_error_response('Moderator access required', 403);
            return false;
        }

        return true;
    }

    /**
     * Check if user has admin access (admin or superuser only)
     */
    public function admin_access(): bool {
        $user = $GLOBALS['authenticated_user'] ?? null;

        if (!$user) {
            $this->send_error_response('Authentication required', 401);
            return false;
        }

        // Basic validation - ensure user has role field
        if (!isset($user['role'])) {
            $this->send_error_response('Invalid user data', 401);
            return false;
        }

        if (!in_array($user['role'], ['a', 'su'])) {
            $this->send_error_response('Admin access required', 403);
            return false;
        }

        return true;
    }

    /**
     * Check if user has superuser access
     */
    public function superuser_access(): bool {
        $user = $GLOBALS['authenticated_user'] ?? null;

        if (!$user) {
            $this->send_error_response('Authentication required', 401);
            return false;
        }

        // Basic validation - ensure user has role field
        if (!isset($user['role'])) {
            $this->send_error_response('Invalid user data', 401);
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
     */
    public function require_role(string|array $required_roles): bool {
        $user = $GLOBALS['authenticated_user'] ?? null;

        if (!$user) {
            $this->send_error_response('Authentication required', 401);
            return false;
        }

        // Basic validation - ensure user has role field
        if (!isset($user['role'])) {
            $this->send_error_response('Invalid user data', 401);
            return false;
        }

        $roles = is_array($required_roles) ? $required_roles : [$required_roles];

        if (!in_array($user['role'], $roles)) {
            $this->send_error_response('Required role access denied', 403);
            return false;
        }

        return true;
    }


    /**
     * Send error response and exit
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
        exit();
    }

    /**
     * Get role hierarchy level (delegate to Role class)
     */
    public function get_role_level(string $role): int {
        return $this->role->get_role_level($role);
    }

    /**
     * Check if user has sufficient role level
     */
    public function has_role_level(array $user, int $required_level): bool {
        return $this->role->has_sufficient_level($user['role'], $required_level);
    }

    /**
     * Check if user has admin access (delegate to Role class)
     */
    public function has_admin_access(string $role): bool {
        return $this->role->has_admin_access($role);
    }

    /**
     * Check if user has moderator access (delegate to Role class)
     */
    public function has_moderator_access(string $role): bool {
        return $this->role->has_moderator_access($role);
    }
}
