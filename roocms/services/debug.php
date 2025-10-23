<?php declare(strict_types=1);
/**
 * RooCMS - Open Source Free Content Managment System
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      https://www.roocms.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 */

//#########################################################
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################


/**
 * Debug Service
 * Service for managing debug logs
 */
class DebugService {

    /**
     * Read and parse debug logs from file
     *
     * @param int $max_entries Maximum number of entries to return (default: 100)
     * @return array Array of debug log entries
     */
    public function get_debug_logs(int $max_entries = 100): array {
        $debug_logs = [];

        if (!is_file(DEBUGSLOG) || !is_readable(DEBUGSLOG)) {
            return $debug_logs;
        }

        // Use read_file without locking to avoid blocking log writes
        // The parsing algorithm below is robust enough to handle partial reads
        $log_content = read_file(DEBUGSLOG);

        if ($log_content === false || empty($log_content)) {
            return $debug_logs;
        }

        // Convert the sequence of JSON objects into a JSON array
        // Normalize line endings for cross-platform compatibility (Windows/Unix)
        $normalized_content = str_replace(["\r\n", "\r"], "\n", $log_content);
        $normalized_content = trim($normalized_content);

        // Remove trailing comma and newline if present
        $normalized_content = rtrim($normalized_content, ",\n");

        // Split content by lines to process each JSON object separately
        // This is more robust than trying to parse the entire file as one JSON array
        $lines = explode("\n", $normalized_content);
        $current_json = '';
        $brace_count = 0;
        $valid_entries = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $current_json .= $line;

            // Count braces to determine when we have a complete JSON object
            $brace_count += substr_count($line, '{') - substr_count($line, '}');

            // When brace count reaches 0, we should have a complete JSON object
            if ($brace_count === 0 && !empty($current_json)) {
                // Remove trailing comma if present
                $current_json = rtrim($current_json, ',');

                // Try to decode this JSON object
                $entry = json_decode($current_json, true);
                if ($entry !== null && is_array($entry)) {
                    $valid_entries[] = $entry;

                    // Keep only the last N entries for performance
                    if (count($valid_entries) > $max_entries) {
                        array_shift($valid_entries);
                    }
                }

                // Reset for next object
                $current_json = '';
                $brace_count = 0;
            }
        }

        // Filter entries that have debug data
        foreach ($valid_entries as $entry) {
            if (isset($entry['debug']) && is_array($entry['debug'])) {
                $debug_logs[] = $entry;
            }
        }

        // Sort by timestamp (newest first)
        if (!empty($debug_logs)) {
            usort($debug_logs, function($a, $b) {
                $time_a = $a['timestamp'] ?? '';
                $time_b = $b['timestamp'] ?? '';

                // Convert timestamps to comparable values
                $time_a_val = is_numeric($time_a) ? (float)$time_a : strtotime($time_a);
                $time_b_val = is_numeric($time_b) ? (float)$time_b : strtotime($time_b);

                return $time_b_val <=> $time_a_val;
            });
        }

        return $debug_logs;
    }


    /**
     * Clear debug logs file
     *
     * @return bool True if successful, false otherwise
     * @throws Exception If clearing fails
     */
    public function clear_debug_logs(): bool {
        if (is_file(DEBUGSLOG) && is_writable(DEBUGSLOG)) {
            $result = file_put_contents(DEBUGSLOG, '');
            if ($result === false) {
                throw new Exception('Failed to clear debug log file');
            }
            return true;
        }
        return false;
    }


    /**
     * Get debug log file path
     *
     * @return string Path to debug log file
     */
    public function get_debug_log_path(): string {
        return DEBUGSLOG;
    }


    /**
     * Check if debug log file exists and is readable
     *
     * @return bool True if file exists and readable
     */
    public function debug_log_exists(): bool {
        return is_file(DEBUGSLOG) && is_readable(DEBUGSLOG);
    }


    /**
     * Get debug log file size in bytes
     *
     * @return int File size in bytes, 0 if file doesn't exist
     */
    public function get_debug_log_size(): int {
        if (!is_file(DEBUGSLOG)) {
            return 0;
        }
        return filesize(DEBUGSLOG) ?: 0;
    }
}
