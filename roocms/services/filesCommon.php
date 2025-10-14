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
 * FilesCommonService
 * Common methods for working with files
 * Contains common methods for working with files
 */
trait FilesCommonService {

    
	/**
	 * Upload file with validation
	 * 
	 * @param array $file Uploaded file from $_FILES
	 * @param int|null $user_id User ID who uploads
	 * @param array $options Additional options
	 * @return int Media ID
	 * @throws DomainException
	 */
	public function upload_file(array $file, ?int $user_id = null, array $options = []): int {
		
		// Validate basic file requirements using Media class
		$file_info = $this->files->validate_uploaded_file($file);
		
		// Validate business rules (file size limits, MIME types)
		$this->validate_upload($file, $file_info);
		
		// Upload through Media class
		$media_id = $this->files->upload($file, $user_id, $options);
		
		if($media_id === false) {
			throw new DomainException('Failed to upload file', 500);
		}
		
		return $media_id;
	}


	/**
	 * Validate uploaded file for business rules
	 * 
	 * @param array $file File data from $_FILES
	 * @param array $file_info File info from Files::validate_uploaded_file()  
	 * @throws DomainException
	 */
	private function validate_upload(array $file, array $file_info): void {
		$media_type = $file_info['media_type'];
		$mime_type = $file_info['mime_type'];
		
		// Check file size limits (business rule)
		$max_size = $this->files->get_max_file_size($media_type);
		if($file['size'] > $max_size) {
			$max_size_mb = round($max_size / 1048576, 2);
			throw new DomainException("File size exceeds {$max_size_mb} MB limit", 413);
		}
		
		// Validate MIME type (business rule)
		$all_mime_types = $this->files->get_allowed_mime_types();
		$allowed_mimes = array_merge(...array_values($all_mime_types));
		if(!in_array($mime_type, $allowed_mimes, true)) {
			throw new DomainException('File type not allowed', 415);
		}
	}


	/**
	 * Delete file with validation
	 * 
	 * @param int $id Media ID
	 * @param array|null $current_user Current user data for permission check
	 * @return bool Success
	 * @throws DomainException
	 */
	public function delete_file(int $id, ?array $current_user = null): bool {
		
		// Check if file exists
		$file = $this->files->get_by_id($id);
		if(!$file) {
			throw new DomainException('File not found', 404);
		}
		
		// Check permissions if user is provided
		if($current_user !== null) {
			$this->check_modify_permissions($file, $current_user, 'delete');
		}
		
		// Delete in transaction
		return (bool)$this->db->transaction(function() use ($id) {
			return $this->files->delete($id);
		});
	}


	/**
	 * Update media metadata
	 * 
	 * @param int $id Media ID
	 * @param array $data Data to update
	 * @param array|null $current_user Current user data for permission check
	 * @return array Updated media data
	 * @throws DomainException
	 */
	public function update_media_metadata(int $id, array $data, ?array $current_user = null): array {
		// Check if media exists
		$media = $this->files->get_by_id($id) ?: throw new DomainException('Media not found', 404);
		
		// Check permissions if user is provided
		if($current_user !== null) {
			$this->check_modify_permissions($media, $current_user, 'update');
		}
		
		// Filter allowed fields
		$allowed_fields = ['original_name', 'description', 'tags', 'status'];
		$update_data = array_intersect_key($data, array_flip($allowed_fields));
		
		// Validate status and check for empty data
		isset($update_data['status']) && !in_array($update_data['status'], Files::STATUS_TYPES, true) && throw new DomainException('Invalid status value', 400);
		empty($update_data) && throw new DomainException('No valid fields to update', 400);
		
		// Update media record with timestamp
		$success = $this->db->update_array($update_data + ['updated_at' => time()],	TABLE_MEDIA, 'id = ?', [$id]);
		
		// Check update success and return formatted data
		(!$success) && throw new DomainException('Failed to update media', 500);
		
		return $this->get_file_formatted($id);
	}


	/**
	 * Clean up deleted files older than specified days
	 * 
	 * @param int $days Days threshold
	 * @return int Number of cleaned files
	 */
	public function cleanup_deleted_files(int $days = 30): int {
		
		$threshold = time() - ($days * 86400);
		
		$query = "SELECT * FROM " . TABLE_MEDIA . " 
				  WHERE status = 'deleted' 
				  AND updated_at < :threshold";
		
		$files = $this->db->fetch_all($query, ['threshold' => $threshold]);
		
		$cleaned = 0;
		
		foreach($files as $file) {
			if($this->files->delete((int)$file['id'])) {
				$cleaned++;
			}
		}
		
		return $cleaned;
	}


	/**
	 * Update file status
	 * 
	 * @param int $id Media ID
	 * @param string $status New status
	 * @return bool Success
	 * @throws DomainException
	 */
	public function update_status(int $id, string $status): bool {
		
		// Validate status
		$allowed_statuses = ['uploaded', 'processing', 'ready', 'error', 'deleted'];
		if(!in_array($status, $allowed_statuses, true)) {
			throw new DomainException('Invalid status', 422);
		}
		
		return $this->files->update_status($id, $status);
	}
}