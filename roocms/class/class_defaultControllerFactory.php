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
 * Default implementation of ControllerFactory
 * Creates controller instances using reflection/new operator
 */
class DefaultControllerFactory implements ControllerFactory {

    private readonly Db $db;



    /**
     * Constructor
     */
    public function __construct(Db $db) {
        $this->db = $db;
    }

    /**
     * Create controller instance by class name
     *
     * @param string $controllerClass Full class name of the controller
     * @return object Controller instance
     * @throws Exception If controller cannot be created
     */
    public function create(string $controllerClass): object {
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller class '{$controllerClass}' not found");
        }

        return new $controllerClass($this->db);
    }
}
