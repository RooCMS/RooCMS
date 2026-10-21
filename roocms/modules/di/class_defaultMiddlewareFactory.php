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

    private readonly DependencyContainer $container;


    
    /**
     * Constructor
     */
    public function __construct(DependencyContainer $container) {
        $this->container = $container;
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

        // Registered in DI container
        if ($this->container->has($middlewareClass)) {
            return $this->container->get($middlewareClass);
        }

        // Middleware without required dependencies
        $constructor = (new ReflectionClass($middlewareClass))->getConstructor();
        if ($constructor === null || $constructor->getNumberOfRequiredParameters() === 0) {
            return new $middlewareClass();
        }

        throw new Exception("Middleware '".$middlewareClass."' is not registered in DI container");
    }
}
