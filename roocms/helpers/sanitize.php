<?php declare(strict_types=1);
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
 * TODO: Extract to trash
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
 * Check if string contains valid JSON
 */
function is_json_string(string $value): bool {
    if (empty($value)) {
        return false;
    }

    // Quick check for JSON structure indicators
    $firstChar = $value[0] ?? '';
    if (!in_array($firstChar, ['{', '[', '"'])) {
        return false;
    }

    json_decode($value);
    return json_last_error() === JSON_ERROR_NONE;
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
 * Clear string from dangerous characters
 *
 * @param string $string
 * @return string
 */
function clearing_string(string $string) : string {
    return trim(str_ireplace(['?','!','@','#','$','%','^','&','*','(',')','{','}','[',']','|','<','>','/','\\','"','`','.',',','~','=',';'], '', $string));
}


/**
 * Parsing data
 * This function takes data processed by the htmlspecialchars() function and clears all special characters.
 *
 * @param string $text - Text buffer, which needs to be parsed
 *
 * @return string
 */
function clearing_html(string $text) : string {
    $text = strip_tags($text);
    $text = str_ireplace(['&lt;','&gt;','&#123;','&#125;','&#39;','&quot;','&amp;','&#36;'], '', $text);

    return $text;
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


/**
 * Sanitize path
 *
 * @param string $uri
 * @return string|false
 */
function sanitize_path(string $uri): string|false {
    // Get path from URI
    $path = parse_url($uri, PHP_URL_PATH);

    if ($path === false || $path === null) {
        return false;
    }
    
    // Decode URL-encoded symbols (handle double encoding)
    $path = urldecode(urldecode($path));
    
    // Normalize path separators to forward slashes
    $path = str_replace('\\', '/', $path);
    
    // Remove multiple slashes
    $path = preg_replace('#/{2,}#', '/', $path);
    
    // Enhanced path traversal protection
    // Remove various forms of directory traversal patterns
    $dangerous_patterns = [
        '#\.\.(?:/|\\\\)+#',            // ../ or ..\ (including repeated)
        '#%2e%2e(?:%2f|%5c)+#i',        // URL-encoded ../ or ..\
        '#%252e%252e(?:%2f|%5c)+#i',    // Double encoding
        '#\.{3,}#',                     // 3+ dots in a row
    ];
    
    foreach ($dangerous_patterns as $pattern) {
        $path = preg_replace($pattern, '', $path);
    }

    // Remove any remaining directory traversal sequences iteratively
    do {
        $old_path = $path;
        $path = str_replace(['../', '..\\', '..'], '', $path);
        $path = preg_replace('#/{2,}#', '/', $path);
    } while ($old_path !== $path);
    
    // Additional security: block null bytes and control characters
    if (strpos($path, "\0") !== false || preg_match('/[\x00-\x1f\x7f]/', $path)) {
        return false;
    }
    
    // Block suspicious file extensions if present
    $suspicious_extensions = ['.php', '.asp', '.aspx', '.jsp', '.exe', '.bat', '.sh'];
    foreach ($suspicious_extensions as $ext) {
        if (stripos($path, $ext) !== false) {
            return false;
        }
    }
    
    // Make sure the path starts with a slash
    if (substr($path, 0, 1) !== '/') {
        $path = '/' . $path;
    }
    
    // Limit the path length
    if (strlen($path) > 2048) {
        return false;
    }
    
    // Final validation: ensure no directory traversal remains
    if (strpos($path, '../') !== false || strpos($path, '..\\') !== false) {
        return false;
    }
    
    return $path;
}