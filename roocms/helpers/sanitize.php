<?php
declare(strict_types=1);
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
 * Sanitize string input
 * 
 * @param string $input
 * @return string
 */
function sanitize_string(string $input): string {
    return trim(strip_tags($input));
}


/**
 * Sanitize email input
 * 
 * @param string $email
 * @return string
 */
function sanitize_email(string $email): string {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}


/**
 * Validate email format
 * 
 * @param string $email
 * @return bool
 */
function is_valid_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


/**
 * Validate integer ID
 * 
 * @param mixed $id
 * @return bool
 */
function is_valid_id(mixed $id): bool {
    return is_numeric($id) && (int)$id > 0;
}