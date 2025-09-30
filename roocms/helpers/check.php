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
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################



/**
 * Check PHP version and safe mode
 * @return array{check: string, value: string, status: bool, message: string}[]
 */
function check_php_version(): array {
    $php_version = PHP_VERSION;
    $results = [];

    if (version_compare($php_version, '8.1', "<")) {
        $results[] = [
            "check" => "PHP Version",
            "value" => $php_version,
            "status" => false,
            "message" => "PHP version is not compatible with RooCMS. PHP 8.1 or higher is required."
        ];
    } else {
        $results[] = [
            "check" => "PHP Version",
            "value" => $php_version,
            "status" => true,
            "message" => ""
        ];
    }

    return $results;
}


/**
 * Check PHP extensions
 * @return array{check: string, value: string, status: bool, message: string}[]
 */
function check_php_extensions(): array {
    $required_extensions = ['Core', 'pdo', 'standard', 'mbstring', 'calendar', 'date', 'pcre', 'gd', 'curl', 'openssl', 'json', 'fileinfo', 'zip', 'exif'];

    $loaded_extensions = get_loaded_extensions();
    $results = [];

    foreach ($required_extensions as $extension) {
        if (!in_array($extension, $loaded_extensions, true)) {
            $results[] = [
                "check" => "Extension: {$extension}",
                "value" => "Missing",
                "status" => false,
                "message" => "Without this extension, RooCMS will be unstable!"
            ];
        } else {
            $results[] = [
                "check" => "Extension: {$extension}",
                "value" => "Installed",
                "status" => true,
                "message" => ""
            ];
        }
    }

    return $results;
}


/**
 * Check PHP ini settings
 * @return array{check: string, value: string, status: bool, message: string}[]
 */
function check_php_ini(): array {
    // Helper function to create check result
    $make_check = fn(string $name, string $value, bool $status, string $message = '') => [
        'check' => $name,
        'value' => $value,
        'status' => $status,
        'message' => $message
    ];

    // Define all checks with their validation logic
    $checks = [
        // PCRE UTF-8 support
        fn() => (fn($has_utf8) => $make_check(
            'Support PCRE UTF-8',
            $has_utf8 ? 'On' : 'Off',
            $has_utf8,
            $has_utf8 ? '' : 'Regular expressions do not support UTF-8'
        ))((bool)preg_match('//u', '')),

        // Memory limit (minimum 128M)
        fn() => (fn($limit, $bytes) => $make_check(
            'Memory Limit',
            $limit,
            $bytes >= 134217728,
            $bytes >= 134217728 ? '' : 'Memory limit should be at least 128M for stable RooCMS operation'
        ))(getenv_or_ini('MEMORY_LIMIT'), parse_size(getenv_or_ini('MEMORY_LIMIT'))),

        // Max execution time (minimum 30 seconds)
        fn() => (fn($time) => $make_check(
            'Max Execution Time',
            $time > 0 ? $time . 's' : 'Unlimited',
            $time === 0 || $time >= 30,
            ($time === 0 || $time >= 30) ? '' : 'Max execution time should be at least 30 seconds'
        ))((int)getenv_or_ini('MAX_EXECUTION_TIME')),

        // Upload max filesize (minimum 8M)
        fn() => (fn($size) => $make_check(
            'Upload Max Filesize',
            (string)$size,
            $size >= 8388608,
            $size >= 8388608 ? '' : 'Upload max filesize should be at least 8M'
        ))(parse_size(getenv_or_ini('UPLOAD_MAX_FILESIZE'))),

        // Post max size (minimum 8M)
        fn() => (fn($size) => $make_check(
            'Post Max Size',
            (string)$size,
            $size >= 8388608,
            $size >= 8388608 ? '' : 'Post max size should be at least 8M'
        ))(parse_size(getenv_or_ini('POST_MAX_SIZE'))),

        // Timezone
        fn() => (fn($tz) => $make_check(
            'Timezone',
            empty($tz) ? 'Not set' : $tz,
            !empty($tz),
            !empty($tz) ? '' : 'Timezone should be configured in php.ini'
        ))(getenv_or_ini('TIMEZONE'))
    ];

    // Execute all checks and return results
    return array_map(fn($check) => $check(), $checks);
}

/**
 * Check file system permissions
 * @return array{check: string, value: string, status: bool, message: string}[]
 */
function check_filesystem(): array {
    $results = [];
    $pathsToCheck = [
        'storage/logs' => 'Logs directory',
        'storage/assets' => 'Assets directory',
        'upload/files' => 'Upload files directory',
        'upload/images' => 'Upload images directory'
    ];

    foreach ($pathsToCheck as $path => $description) {
        $fullPath = __DIR__ . '/../../' . $path;

        if (!is_dir($fullPath)) {
            $results[] = [
                "check" => $description,
                "value" => "Directory not exists",
                "status" => false,
                "message" => "Directory {$path} does not exist"
            ];
        } elseif (!is_writable($fullPath)) {
            $results[] = [
                "check" => $description,
                "value" => "Not writable",
                "status" => false,
                "message" => "Directory {$path} is not writable"
            ];
        } else {
            $results[] = [
                "check" => $description,
                "value" => "Writable",
                "status" => true,
                "message" => ""
            ];
        }
    }

    // Check disk space (minimum 100MB free)
    $free_space = disk_free_space(__DIR__);
    if ($free_space < 104857600) { // 100MB
        $results[] = [
            "check" => "Disk Space",
            "value" => round($free_space / 1024 / 1024) . "MB",
            "status" => false,
            "message" => "At least 100MB of free disk space is required"
        ];
    } else {
        $results[] = [
            "check" => "Disk Space",
            "value" => round($free_space / 1024 / 1024) . "MB",
            "status" => true,
            "message" => ""
        ];
    }

    return $results;
}

/**
 * Check server environment
 * @return array{check: string, value: string, status: bool, message: string}[]
 */
function check_server_environment(): array {
    $results = [];

    // Check if running on HTTPS
    $https_value = getenv_or_ini('HTTPS');
    $is_https = $https_value !== null && $https_value === 'on';
    $results[] = [
        "check" => "HTTPS Support",
        "value" => $is_https ? "Enabled" : "Disabled",
        "status" => true, // Not critical for development
        "message" => $is_https ? "" : "Consider enabling HTTPS for production"
    ];

    // Check server software
    $server_software = getenv_or_ini('SERVER_SOFTWARE') ?? 'Unknown';
    $results[] = [
        "check" => "Web Server",
        "value" => $server_software,
        "status" => true,
        "message" => ""
    ];

    // Check if mod_rewrite is available (for Apache)
    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        $has_mod_rewrite = in_array('mod_rewrite', $modules, true);
        $results[] = [
            "check" => "Apache mod_rewrite",
            "value" => $has_mod_rewrite ? "Available" : "Not available",
            "status" => $has_mod_rewrite,
            "message" => $has_mod_rewrite ? "" : "mod_rewrite is recommended for URL rewriting"
        ];
    }

    return $results;
}


/**
 * Run all system checks
 * @return array{checks: array, is_allowed: bool}
 */
function run_system_checks(): array {
    $all_checks = [
        ...check_php_version(),
        ...check_php_extensions(),
        ...check_php_ini(),
        ...check_filesystem(),
        ...check_server_environment()
    ];

    $is_allowed = array_reduce($all_checks, static fn(bool $carry, array $check): bool => $carry && $check['status'], true);

    return [
        'checks' => $all_checks,
        'is_allowed' => $is_allowed
    ];
}


/**
 * Parse size string to bytes
 * @param string $size
 * @return int
 */
function parse_size(string $size): int {
    $unit = strtolower(substr($size, -1));
    $value = (int)substr($size, 0, -1);

    return match ($unit) {
        'g' => $value * 1024 * 1024 * 1024,
        'm' => $value * 1024 * 1024,
        'k' => $value * 1024,
        default => (int)$size
    };
}


/**
 * Get environment variable or ini setting
 * @param string $key
 * @return string
 */
function getenv_or_ini(string $key): string {
    return (string)(env($key) ?? ini_get($key) ?? '');
}
