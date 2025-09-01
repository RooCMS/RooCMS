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

$protocol = 'HTTP/1.1';
if (isset($_SERVER['SERVER_PROTOCOL']) && 
    preg_match('/^HTTP\/[12]\.[01]$/', $_SERVER['SERVER_PROTOCOL'])) {
    $protocol = $_SERVER['SERVER_PROTOCOL'];
}
header($protocol . ' 301 Moved Permanently');
header("Location: /");