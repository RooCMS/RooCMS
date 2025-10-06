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
 * Database Backup Service
 * Business logic layer for database backup operations
 */
class BackupService {

    private readonly Db $db;
    private readonly DbBackuper $backuper;

    

    /**
     * Constructor with dependency injection
     */
    public function __construct(Db $db, DbBackuper $backuper) {
        $this->db = $db;
        $this->backuper = $backuper;
    }
    

    /**
     * Create new database backup
     *
     * @param array $options Backup options
     * @return array Backup result with metadata
     * @throws Exception If backup creation fails
     */
    public function create_backup(array $options = []): array {
        // Validate options
        $validated_options = $this->validate_backup_options($options);
        
        try {
            // Create backup using DbBackuper
            $result = $this->backuper->create_backup($validated_options);
            
            // Log backup creation
            $this->log_backup_operation('create', $result);
            
            return [
                'success' => true,
                'data' => $result,
                'message' => 'Backup created successfully'
            ];
            
        } catch (Exception $e) {
            $this->log_backup_error('create', $e->getMessage());
            throw $e;
        }
    }
    

    /**
     * Restore database from backup
     *
     * @param string $filename Backup filename
     * @param array $options Restore options
     * @return array Restore result with metadata
     * @throws Exception If restore fails
     */
    public function restore_backup(string $filename, array $options = []): array {
        // Validate filename
        if (empty($filename)) {
            throw new InvalidArgumentException('Backup filename is required');
        }
        
        // Security check: validate filename
        if (!$this->is_valid_backup_filename($filename)) {
            throw new InvalidArgumentException('Invalid backup filename');
        }
        
        // Validate options
        $validated_options = $this->validate_restore_options($options);
        
        try {
            // Restore backup using DbBackuper
            $result = $this->backuper->restore_backup($filename, $validated_options);
            
            // Log restore operation
            $this->log_backup_operation('restore', $result, $filename);
            
            return [
                'success' => true,
                'data' => $result,
                'message' => 'Database restored successfully'
            ];
            
        } catch (Exception $e) {
            $this->log_backup_error('restore', $e->getMessage(), $filename);
            throw $e;
        }
    }
    

    /**
     * Get list of available backups
     *
     * @param array $filters Optional filters (date_from, date_to, limit)
     * @return array List of backups with metadata
     */
    public function list_backups(array $filters = []): array {
        try {
            $backups = $this->backuper->list_backups();
            
            // Apply filters if provided
            if (!empty($filters)) {
                $backups = $this->apply_backup_filters($backups, $filters);
            }
            
            return [
                'success' => true,
                'data' => $backups,
                'count' => count($backups)
            ];
            
        } catch (Exception $e) {
            throw new RuntimeException('Failed to list backups: ' . $e->getMessage());
        }
    }
    

    /**
     * Delete backup file
     *
     * @param string $filename Backup filename to delete
     * @return array Delete result
     * @throws Exception If deletion fails
     */
    public function delete_backup(string $filename): array {
        // Validate filename
        if (empty($filename)) {
            throw new InvalidArgumentException('Backup filename is required');
        }
        
        // Security check: validate filename
        if (!$this->is_valid_backup_filename($filename)) {
            throw new InvalidArgumentException('Invalid backup filename');
        }
        
        try {
            $deleted = $this->backuper->delete_backup($filename);
            
            if ($deleted) {
                $this->log_backup_operation('delete', ['filename' => $filename]);
                
                return [
                    'success' => true,
                    'message' => 'Backup deleted successfully',
                    'filename' => $filename
                ];
            } else {
                throw new RuntimeException('Backup file not found or deletion failed');
            }
            
        } catch (Exception $e) {
            $this->log_backup_error('delete', $e->getMessage(), $filename);
            throw $e;
        }
    }
    

    /**
     * Get backup system status and statistics
     *
     * @return array System status information
     */
    public function get_system_status(): array {
        try {
            $backups = $this->backuper->list_backups();
            $total_size = array_sum(array_column($backups, 'size'));
            
            return [
                'success' => true,
                'data' => [
                    'backup_count' => count($backups),
                    'total_size' => $total_size,
                    'total_size_human' => format_file_size($total_size),
                    'latest_backup' => !empty($backups) ? $backups[0] : null,
                    'oldest_backup' => !empty($backups) ? end($backups) : null,
                    'compression_enabled' => function_exists('gzencode'),
                    'backup_directory_writable' => is_writable(_BACKUPS),
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ];
            
        } catch (Exception $e) {
            throw new RuntimeException('Failed to get system status: ' . $e->getMessage());
        }
    }
    

    /**
     * Get backup operation logs
     *
     * @param int $limit Number of log entries to return
     * @return array Log entries
     */
    public function get_backup_logs(int $limit = 100): array {
        // For now return empty array, can be extended to read from log files
        return [
            'success' => true,
            'data' => [],
            'count' => 0
        ];
    }
    

    /**
     * Validate backup creation options
     *
     * @param array $options Raw options
     * @return array Validated options
     */
    private function validate_backup_options(array $options): array {
        $defaults = [
            'compress' => true,
            'include_data' => true,
            'include_structure' => true,
            'exclude_tables' => [],
            'filename' => null,
            'universal_format' => true
        ];
        
        $validated = array_merge($defaults, $options);
        
        // Validate exclude_tables
        if (!is_array($validated['exclude_tables'])) {
            throw new InvalidArgumentException('exclude_tables must be an array');
        }
        
        // Validate boolean options
        foreach (['compress', 'include_data', 'include_structure', 'universal_format'] as $bool_option) {
            $validated[$bool_option] = (bool) $validated[$bool_option];
        }
        
        // Validate filename if provided
        if ($validated['filename'] !== null && !$this->is_valid_backup_name($validated['filename'])) {
            throw new InvalidArgumentException('Invalid backup filename');
        }
        
        return $validated;
    }
    

    /**
     * Validate restore options
     *
     * @param array $options Raw options
     * @return array Validated options
     */
    private function validate_restore_options(array $options): array {
        $defaults = [
            'drop_existing' => false,
            'ignore_errors' => false,
            'batch_size' => 1000
        ];
        
        $validated = array_merge($defaults, $options);
        
        // Validate batch_size
        if (!is_numeric($validated['batch_size']) || $validated['batch_size'] < 1) {
            throw new InvalidArgumentException('batch_size must be a positive integer');
        }
        
        $validated['batch_size'] = (int) $validated['batch_size'];
        
        // Validate boolean options
        foreach (['drop_existing', 'ignore_errors'] as $bool_option) {
            $validated[$bool_option] = (bool) $validated[$bool_option];
        }
        
        return $validated;
    }
    

    /**
     * Check if backup filename is valid and safe
     *
     * @param string $filename Filename to validate
     * @return bool True if valid
     */
    private function is_valid_backup_filename(string $filename): bool {
        // Security check: prevent path traversal
        if (strpos($filename, '..') !== false || 
            strpos($filename, '/') !== false || 
            strpos($filename, '\\') !== false) {
            return false;
        }
        
        // Check if it looks like a backup filename
        return preg_match('/^[a-zA-Z0-9_.-]+\.(sql|sql\.gz)$/', $filename) === 1;
    }
    

    /**
     * Check if backup name is valid for creation
     *
     * @param string $name Backup name to validate
     * @return bool True if valid
     */
    private function is_valid_backup_name(string $name): bool {
        // Allow alphanumeric, underscore, hyphen, dot
        return preg_match('/^[a-zA-Z0-9_.-]+$/', $name) === 1;
    }
    

    /**
     * Apply filters to backup list
     *
     * @param array $backups List of backups
     * @param array $filters Filters to apply
     * @return array Filtered backups
     */
    private function apply_backup_filters(array $backups, array $filters): array {
        $filtered = $backups;
        
        // Date range filter
        if (isset($filters['date_from']) || isset($filters['date_to'])) {
            $filtered = array_filter($filtered, function($backup) use ($filters) {
                $backup_time = strtotime($backup['created']);
                
                if (isset($filters['date_from'])) {
                    $from_time = strtotime($filters['date_from']);
                    if ($backup_time < $from_time) return false;
                }
                
                if (isset($filters['date_to'])) {
                    $to_time = strtotime($filters['date_to']);
                    if ($backup_time > $to_time) return false;
                }
                
                return true;
            });
        }
        
        // Limit filter
        if (isset($filters['limit']) && is_numeric($filters['limit'])) {
            $filtered = array_slice($filtered, 0, (int) $filters['limit']);
        }
        
        return array_values($filtered); // Re-index array
    }
    

    /**
     * Log backup operation
     *
     * @param string $operation Operation type
     * @param array $result Operation result
     * @param string|null $filename Optional filename
     */
    private function log_backup_operation(string $operation, array $result, ?string $filename = null): void {
        if (DEBUGMODE) {
            $log_entry = [
                'timestamp' => date('Y-m-d H:i:s'),
                'operation' => $operation,
                'result' => $result,
                'filename' => $filename
            ];
            
            error_log('Backup operation: ' . json_encode($log_entry), 3, _LOGS . 'backup.log');
        }
    }
    

    /**
     * Log backup error
     *
     * @param string $operation Operation type
     * @param string $error Error message
     * @param string|null $filename Optional filename
     */
    private function log_backup_error(string $operation, string $error, ?string $filename = null): void {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'operation' => $operation,
            'error' => $error,
            'filename' => $filename
        ];
        
        error_log('Backup error: ' . json_encode($log_entry), 3, _LOGS . 'backup_errors.log');
    }
}