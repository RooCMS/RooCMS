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
 * Backup API Controller
 * Handles database backup and restore operations via REST API
 */
class BackupController extends BaseController {

	private BackupService $backupService;
	private Auth $auth;
	

	/**
	 * Constructor with dependency injection
	 *
	 * @param BackupService $backupService Backup service
	 * @param Auth $auth Authentication service
	 */
	public function __construct(BackupService $backupService, Auth $auth, Db $db) {
		parent::__construct($db);
		
		$this->backupService = $backupService;
		$this->auth = $auth;
	}


	/**
	 * Create database backup
	 * POST /api/v1/backup/create
	 */
	public function create(): void {
		try {
			// Admin permissions already checked by middleware

			$input = $this->get_input_data();
			
			// Parse backup options
			$options = [
				'compress' => $input['compress'] ?? true,
				'include_data' => $input['include_data'] ?? true,
				'include_structure' => $input['include_structure'] ?? true,
				'exclude_tables' => $input['exclude_tables'] ?? [],
				'filename' => $input['filename'] ?? null,
				'universal_format' => $input['universal_format'] ?? true
			];

			$result = $this->backupService->create_backup($options);
			$this->json_response($result);

		} catch(Exception $e) {
			$this->error_response($e->getMessage(), 500);
		}
	}


	/**
	 * Restore database from backup
	 * POST /api/v1/backup/restore
	 */
	public function restore(): void {
		try {
			// Admin permissions already checked by middleware

			$input = $this->get_input_data();
			
			if(empty($input['filename'])) {
				$this->error_response('Filename is required', 400);
				return;
			}

			// Parse restore options
			$options = [
				'drop_existing' => $input['drop_existing'] ?? false,
				'ignore_errors' => $input['ignore_errors'] ?? false,
				'batch_size' => $input['batch_size'] ?? 1000
			];

			$result = $this->backupService->restore_backup($input['filename'], $options);
			$this->json_response($result);

		} catch(Exception $e) {
			$this->error_response($e->getMessage(), 500);
		}
	}


	/**
	 * List available backups
	 * GET /api/v1/backup/list
	 */
	public function list(): void {
		try {
			// Admin permissions already checked by middleware

			$filters = $this->get_query_params();
			$result = $this->backupService->list_backups($filters);
			$this->json_response($result);

		} catch(Exception $e) {
			$this->error_response($e->getMessage(), 500);
		}
	}


	/**
	 * Delete backup file
	 * DELETE /api/v1/backup/delete/{filename}
	 */
	public function delete(): void {
		try {
			// Admin permissions already checked by middleware

			$filename = $this->get_path_parameter('filename');
			
			if(empty($filename)) {
				$this->error_response('Filename is required', 400);
				return;
			}

			$result = $this->backupService->delete_backup($filename);
			$this->json_response($result);

		} catch(Exception $e) {
			$this->error_response($e->getMessage(), 500);
		}
	}


	/**
	 * Download backup file
	 * GET /api/v1/backup/download/{filename}
	 */
	public function download(): void {
		try {
			// Admin permissions already checked by middleware

			$filename = $this->get_path_parameter('filename');
			
			if(empty($filename)) {
				$this->error_response('Filename is required', 400);
				return;
			}

			// Security check: prevent path traversal
			if(strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
				$this->error_response('Invalid filename', 400);
				return;
			}

			$backups_result = $this->backupService->list_backups();
			$backups = $backups_result['data'] ?? [];
			$backup_file = null;

			// Find the backup file
			foreach($backups as $backup) {
				if($backup['filename'] === $filename) {
					$backup_file = $backup['filepath'];
					break;
				}
			}

			if(!$backup_file || !file_exists($backup_file)) {
				$this->not_found_response('Backup file not found');
				return;
			}

			// Set headers for file download
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Content-Length: ' . filesize($backup_file));
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');

			// Output file content
			readfile($backup_file);
			exit;

		} catch(Exception $e) {
			$this->error_response($e->getMessage(), 500);
		}
	}


	/**
	 * Get backup operation logs
	 * GET /api/v1/backup/logs
	 */
	public function logs(): void {
		try {
			// Admin permissions already checked by middleware

			$limit = (int) ($_GET['limit'] ?? 100);
			$result = $this->backupService->get_backup_logs($limit);
			$this->json_response($result);

		} catch(Exception $e) {
			$this->error_response($e->getMessage(), 500);
		}
	}


	/**
	 * Get backup system status and statistics
	 * GET /api/v1/backup/status
	 */
	public function status(): void {
		try {
			// Admin permissions already checked by middleware

			$result = $this->backupService->get_system_status();
			$this->json_response($result);

		} catch(Exception $e) {
			$this->error_response($e->getMessage(), 500);
		}
	}


	/**
	 * Get path parameter from URL
	 *
	 * @param string $name Parameter name
	 * @return string|null Parameter value
	 */
	private function get_path_parameter(string $name): ?string {
		$uri = env('REQUEST_URI') ?? '';
		$path = parse_url($uri, PHP_URL_PATH);
		$segments = explode('/', trim($path, '/'));
		
		// For backup download: /api/v1/backup/download/{filename}
		// Expected segments: [api, v1, backup, download, filename]
		if($name === 'filename') {
			// Get the last segment which should be the filename
			$filename = end($segments);
			
			// Validate that it looks like a backup filename
			if($filename && preg_match('/^[a-zA-Z0-9_.-]+\.(sql|sql\.gz|sql\.bz2)$/', $filename)) {
				return urldecode($filename);
			}
		}
		
		return null;
	}
}