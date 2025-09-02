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


/**
 * Safely decode JSON input with validation
 *
 * @param string $json_string
 * @param int $max_size Maximum allowed size in bytes
 * @return array|null Returns decoded array or null on error
 */
function safe_json_decode(string $json_string, int $max_size = 1048576): ?array {
    // Check size limit
    if (strlen($json_string) > $max_size) {
        return null;
    }

    // Decode JSON
    $data = json_decode($json_string, true, 512, JSON_THROW_ON_ERROR);

    // Check for JSON errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }

    return is_array($data) ? $data : null;
}


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
 * Sanitize data for logging (prevents XSS and log injection)
 *
 * @param string $input
 * @return string
 */
function sanitize_log(string $input): string {
    // Remove HTML tags and encode special characters
    $sanitized = strip_tags($input);

    // Remove or encode potentially dangerous characters
    $sanitized = preg_replace('/[<>"\'\\\\]/', '', $sanitized);

    // Limit length to prevent log bombing
    $sanitized = substr($sanitized, 0, 500);

    return trim($sanitized);
}


/**
 * Recursively sanitize input data array
 *
 * @param mixed $data
 * @return mixed
 */
function sanitize_input_data(mixed $data): mixed {
    if (is_string($data)) {
        // Basic XSS protection for strings
        return trim(strip_tags($data));
    }

    if (is_array($data)) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            // Sanitize keys (prevent XSS in array keys)
            $sanitized_key = is_string($key) ? trim(strip_tags($key)) : $key;
            $sanitized[$sanitized_key] = sanitize_input_data($value);
        }
        return $sanitized;
    }

    // Return other types as-is (int, float, bool, null)
    return $data;
}