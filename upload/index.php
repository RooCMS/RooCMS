<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program. If not, see http://www.gnu.org/licenses/
 */
$protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
header($protocol.' 301 Moved Permanently');
header("Location: /");