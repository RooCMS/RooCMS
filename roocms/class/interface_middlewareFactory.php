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
 * Interface for middleware factory
 * Provides dependency injection for middleware creation
 */
interface MiddlewareFactory {

    /**
     * Create middleware instance by class name
     *
     * @param string $middlewareClass Full class name of the middleware
     * @return object Middleware instance
     * @throws Exception If middleware cannot be created
     */
    public function create(string $middlewareClass): object;
}
