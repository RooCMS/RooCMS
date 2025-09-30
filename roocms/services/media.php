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



class MediaService {

	private Db $db;
	private Media $media;



	/**
	 * Constructor
	 */
	public function __construct(Db $db, Media $media) {
		$this->db = $db;
		$this->media = $media;
	}


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
		
		# Validate upload
		$this->validate_upload($file);
		
		# Upload through Media class
		$media_id = $this->media->upload($file, $user_id, $options);
		
		if($media_id === false) {
			throw new DomainException('Failed to upload file', 500);
		}
		
		return $media_id;
	}


	/**
	 * Validate uploaded file
	 * 
	 * @param array $file File data from $_FILES
	 * @throws DomainException
	 */
	private function validate_upload(array $file): void {
		
		# Check if file was uploaded
		if(!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
			throw new DomainException('No file uploaded', 400);
		}
		
		# Check for upload errors
		if($file['error'] !== UPLOAD_ERR_OK) {
			$error_messages = [
				UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
				UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
				UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
				UPLOAD_ERR_NO_FILE => 'No file was uploaded',
				UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
				UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
				UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
			];
			
			$message = $error_messages[$file['error']] ?? 'Unknown upload error';
			throw new DomainException($message, 400);
		}
		
		# Check file size
		if($file['size'] <= 0) {
			throw new DomainException('File is empty', 400);
		}
		
		# Get MIME type
		$mime_type = mime_content_type($file['tmp_name']);
		$extension_raw = pathinfo($file['name'], PATHINFO_EXTENSION);
		$extension = is_string($extension_raw) ? strtolower($extension_raw) : '';
		
		# Determine media type
		$media_type = $this->determine_media_type($mime_type, $extension);
		
		# Check file size limits
		$max_size = $this->media->get_max_file_size($media_type);
		if($file['size'] > $max_size) {
			$max_size_mb = round($max_size / 1048576, 2);
			throw new DomainException("File size exceeds {$max_size_mb} MB limit", 413);
		}
		
		# Validate MIME type
		$all_mime_types = $this->media->get_allowed_mime_types();
		$allowed_mimes = array_merge(...array_values($all_mime_types));
		if(!in_array($mime_type, $allowed_mimes, true)) {
			throw new DomainException('File type not allowed', 415);
		}
	}


	/**
	 * Determine media type by MIME and extension
	 * 
	 * @param string $mime_type MIME type
	 * @param string $extension File extension
	 * @return string Media type
	 */
	private function determine_media_type(string $mime_type, string $extension): string {
		
		$all_mime_types = $this->media->get_allowed_mime_types();
		
		foreach($all_mime_types as $type => $mimes) {
			if(in_array($mime_type, $mimes, true)) {
				return $type;
			}
		}
		
		return 'other';
	}


	/**
	 * Get file by ID
	 * 
	 * @param int $id Media ID
	 * @return array|null File data
	 */
	public function get_file(int $id): ?array {
		$file = $this->media->get_by_id($id);
		return $file ?: null;
	}


	/**
	 * Get file by UUID
	 * 
	 * @param string $uuid Media UUID
	 * @return array|null File data
	 */
	public function get_file_by_uuid(string $uuid): ?array {
		$file = $this->media->get_by_uuid($uuid);
		return $file ?: null;
	}


	/**
	 * Delete file with validation
	 * 
	 * @param int $id Media ID
	 * @param int|null $user_id User ID for permission check
	 * @return bool Success
	 * @throws DomainException
	 */
	public function delete_file(int $id, ?int $user_id = null): bool {
		
		# Check if file exists
		$file = $this->media->get_by_id($id);
		if(!$file) {
			throw new DomainException('File not found', 404);
		}
		
		# Check ownership if user_id provided
		if($user_id !== null && isset($file['user_id']) && (int)$file['user_id'] !== $user_id) {
			throw new DomainException('Access denied', 403);
		}
		
		# Delete in transaction
		return (bool)$this->db->transaction(function() use ($id) {
			return $this->media->delete($id);
		});
	}


	/**
	 * Attach file to entity
	 * 
	 * @param int $media_id Media ID
	 * @param string $entity_type Entity type
	 * @param int $entity_id Entity ID
	 * @param string $relationship_type Relationship type
	 * @param array $metadata Additional metadata
	 * @return int Relationship ID
	 * @throws DomainException
	 */
	public function attach_to_entity(int $media_id, string $entity_type, int $entity_id, string $relationship_type = 'attachment', array $metadata = []): int {
		
		# Validate media exists
		$file = $this->media->get_by_id($media_id);
		if(!$file) {
			throw new DomainException('File not found', 404);
		}
		
		# Attach
		$rel_id = $this->media->attach_to_entity($media_id, $entity_type, $entity_id, $relationship_type, $metadata);
		
		if($rel_id === false) {
			throw new DomainException('Failed to attach file', 500);
		}
		
		return $rel_id;
	}


	/**
	 * Get files for entity
	 * 
	 * @param string $entity_type Entity type
	 * @param int $entity_id Entity ID
	 * @param string|null $relationship_type Filter by relationship
	 * @return array List of files
	 */
	public function get_files_for_entity(string $entity_type, int $entity_id, ?string $relationship_type = null): array {
		return $this->media->get_for_entity($entity_type, $entity_id, $relationship_type);
	}


	/**
	 * Get image variant
	 * 
	 * @param int $media_id Media ID
	 * @param string $variant_type Variant type (thumbnail, preview, large, original)
	 * @return array|null Variant data
	 */
	public function get_image_variant(int $media_id, string $variant_type): ?array {
		$variant = $this->media->get_image_variant($media_id, $variant_type);
		return $variant ?: null;
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
		
		# Validate status
		$allowed_statuses = ['uploaded', 'processing', 'ready', 'error', 'deleted'];
		if(!in_array($status, $allowed_statuses, true)) {
			throw new DomainException('Invalid status', 422);
		}
		
		return $this->media->update_status($id, $status);
	}


	/**
	 * Get files by media type
	 * 
	 * @param string $media_type Media type (image, video, audio, document, archive)
	 * @param int $limit Limit
	 * @param int $offset Offset
	 * @return array List of files
	 */
	public function get_by_type(string $media_type, int $limit = 50, int $offset = 0): array {
		
		# Guard parameters
		$limit = max(1, min(100, (int)$limit));
		$offset = max(0, (int)$offset);
		
		$query = "SELECT * FROM " . TABLE_MEDIA . " 
				  WHERE media_type = :media_type 
				  AND status != 'deleted'
				  ORDER BY created_at DESC 
				  LIMIT :limit OFFSET :offset";
		
		return $this->db->fetch_all($query, [
			'media_type' => $media_type,
			'limit' => $limit,
			'offset' => $offset
		]);
	}


	/**
	 * Get files by user
	 * 
	 * @param int $user_id User ID
	 * @param int $limit Limit
	 * @param int $offset Offset
	 * @return array List of files
	 */
	public function get_by_user(int $user_id, int $limit = 50, int $offset = 0): array {
		
		# Guard parameters
		$limit = max(1, min(100, (int)$limit));
		$offset = max(0, (int)$offset);
		
		$query = "SELECT * FROM " . TABLE_MEDIA . " 
				  WHERE user_id = :user_id 
				  AND status != 'deleted'
				  ORDER BY created_at DESC 
				  LIMIT :limit OFFSET :offset";
		
		return $this->db->fetch_all($query, [
			'user_id' => $user_id,
			'limit' => $limit,
			'offset' => $offset
		]);
	}


	/**
	 * Search files by name
	 * 
	 * @param string $search_term Search term
	 * @param int $limit Limit
	 * @return array List of files
	 */
	public function search_files(string $search_term, int $limit = 50): array {
		
		# Guard parameters
		$search_term = trim($search_term);
		if(empty($search_term)) {
			return [];
		}
		
		$limit = max(1, min(100, (int)$limit));
		
		$query = "SELECT * FROM " . TABLE_MEDIA . " 
				  WHERE original_name LIKE :search 
				  AND status != 'deleted'
				  ORDER BY created_at DESC 
				  LIMIT :limit";
		
		return $this->db->fetch_all($query, [
			'search' => '%' . $search_term . '%',
			'limit' => $limit
		]);
	}


	/**
	 * Get storage statistics
	 * 
	 * @return array Statistics
	 */
	public function get_storage_stats(): array {
		
		$query = "SELECT 
					media_type,
					COUNT(*) as count,
					SUM(file_size) as total_size
				  FROM " . TABLE_MEDIA . "
				  WHERE status != 'deleted'
				  GROUP BY media_type";
		
		$results = $this->db->fetch_all($query);
		
		$stats = [
			'total_files' => 0,
			'total_size' => 0,
			'by_type' => []
		];
		
		foreach($results as $row) {
			$stats['total_files'] += (int)$row['count'];
			$stats['total_size'] += (int)$row['total_size'];
			$stats['by_type'][$row['media_type']] = [
				'count' => (int)$row['count'],
				'size' => (int)$row['total_size'],
				'size_human' => $this->format_file_size((int)$row['total_size'])
			];
		}
		
		$stats['total_size_human'] = $this->format_file_size($stats['total_size']);
		
		return $stats;
	}


	/**
	 * Format file size to human-readable
	 * 
	 * @param int $bytes Size in bytes
	 * @return string Formatted size
	 */
	private function format_file_size(int $bytes): string {
		$units = ['B', 'KB', 'MB', 'GB', 'TB'];
		$power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
		return number_format($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
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
			if($this->media->delete((int)$file['id'])) {
				$cleaned++;
			}
		}
		
		return $cleaned;
	}


	/**
	 * Get allowed MIME types for upload
	 * 
	 * @param string|null $media_type Filter by media type
	 * @return array List of MIME types
	 */
	public function get_allowed_mime_types(?string $media_type = null): array {
		
		if($media_type !== null) {
			return $this->media->get_allowed_mime_types($media_type);
		}
		
		$all_types = $this->media->get_allowed_mime_types();
		return array_merge(...array_values($all_types));
	}


	/**
	 * Get max file size for media type
	 * 
	 * @param string $media_type Media type
	 * @return int Max size in bytes
	 */
	public function get_max_file_size(string $media_type): int {
		return $this->media->get_max_file_size($media_type);
	}
}
