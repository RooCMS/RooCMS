<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
 * @package     RooCMS
 * @subpackage  Frontend
 * @subpackage  Main page
 * @author      alex Roosso
 * @copyright   2010-2019 (c) RooCMS
 * @link        http://www.roocms.com
 * @version     2.1.3
 * @since       $date$
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 */


/**
 * Инициализируем RooCMS
 */
define('_SITEROOT', dirname(__FILE__));
require_once _SITEROOT."/roocms/init.php";
require_once INIT_UI;
require_once INIT_SITE;


/**
 * Генерим HTML
 *
 * @return frontend html
 */
$tpl->out();