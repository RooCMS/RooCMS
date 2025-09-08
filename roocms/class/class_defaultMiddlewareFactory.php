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
 * Default implementation of MiddlewareFactory
 * Creates middleware instances using dependency injection
 */
class DefaultMiddlewareFactory implements MiddlewareFactory {

    private readonly Db $db;
    private readonly Role $role;


    /**
     * Constructor
     */
    public function __construct(Db $db, Role $role) {
        $this->db = $db;
        $this->role = $role;
    }

    /**
     * Create middleware instance by class name
     *
     * @param string $middlewareClass Full class name of the middleware
     * @return object Middleware instance
     * @throws Exception If middleware cannot be created
     */
    public function create(string $middlewareClass): object {
        if (!class_exists($middlewareClass)) {
            throw new Exception("Middleware class '{$middlewareClass}' not found");
        }

        // Inject dependencies based on middleware type
        switch ($middlewareClass) {
            case 'AuthMiddleware':
                return new AuthMiddleware($this->db);
            case 'RoleMiddleware':
                return new RoleMiddleware($this->role);
            default:
                // For other middleware without dependencies
                return new $middlewareClass();
        }
    }
}
