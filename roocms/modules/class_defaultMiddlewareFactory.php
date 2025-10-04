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
 * Default implementation of MiddlewareFactory
 * Creates middleware instances using dependency injection
 */
class DefaultMiddlewareFactory implements MiddlewareFactory {

    private readonly AuthenticationService $authService;
    private readonly UserValidationService $validator;
    private readonly Role $role;


    
    /**
     * Constructor
     */
    public function __construct(AuthenticationService $authService, UserValidationService $validator, Role $role) {
        $this->authService = $authService;
        $this->validator = $validator;
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
            throw new Exception("Middleware class '".$middlewareClass."' not found");
        }

        // Inject dependencies based on middleware type
        switch ($middlewareClass) {
            case 'AuthMiddleware':
                return new AuthMiddleware($this->authService, $this->validator);
            case 'RoleMiddleware':
                return new RoleMiddleware($this->role, $this->authService);
            default:
                // For other middleware without dependencies
                return new $middlewareClass();
        }
    }
}
