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
 * FilesService
 * Service layer for working with files
 * Contains business logic, validation, and error handling
 */
class FilesService {

	use FilesCommonService;

	private Db $db;
	private Files $files;
	private Role $role;
	private User $user;



	/**
	 * Constructor
	 */
	public function __construct(Db $db, Files $files, Role $role, User $user) {
		$this->db = $db;
		$this->files = $files;
		$this->role = $role;
		$this->user = $user;
	}


	/**
	 * Get file by ID
	 * 
	 * @param int $id Media ID
	 * @return array|null File data
	 */
	public function get_file(int $id): ?array {
		$file = $this->files->get_by_id($id);
		return $file ?: null;
	}


	/**
	 * Get file by UUID
	 * 
	 * @param string $uuid Media UUID
	 * @return array|null File data
	 */
	public function get_file_by_uuid(string $uuid): ?array {
		$file = $this->files->get_by_uuid($uuid);
		return $file ?: null;
	}


	/**
	 * Check if user has permission to modify the file (delete/update)
	 * 
	 * @param array $file File data
	 * @param array $current_user Current user data
	 * @param string $operation Operation type (delete, update)
	 * @throws DomainException
	 */
	private function check_modify_permissions(array $file, array $current_user, string $operation): void {
		$user_id = (int)$current_user['id'];
		$user_role = $current_user['role'] ?? Role::USER;
		$file_user_id = isset($file['user_id']) ? (int)$file['user_id'] : null;
		
		// Check if user is the author
		if($file_user_id !== null && $file_user_id === $user_id) {
			return; // Author can modify their own files
		}
		
		// Check if user has moderator access or higher (moderator, admin, su)
		if($this->role->has_moderator_access($user_role)) {
			return; // Moderator, admin, or superuser can modify any file
		}
		
		// Access denied
		throw new DomainException("Access denied: only file author or moderator/admin can {$operation} files", 403);
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
		
		// Validate media exists
		$file = $this->files->get_by_id($media_id);
		if(!$file) {
			throw new DomainException('File not found', 404);
		}
		
		// Attach
		$rel_id = $this->files->attach_to_entity($media_id, $entity_type, $entity_id, $relationship_type, $metadata);
		
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
		return $this->files->get_for_entity($entity_type, $entity_id, $relationship_type);
	}


	/**
	 * Get image variant
	 * 
	 * @param int $media_id Media ID
	 * @param string $variant_type Variant type (thumbnail, preview, large, original)
	 * @return array|null Variant data
	 */
	public function get_image_variant(int $media_id, string $variant_type): ?array {
		$variant = $this->files->get_variant($media_id, $variant_type);
		return $variant ?: null;
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
		
		// Guard parameters
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
		
		// Guard parameters
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
		
		// Guard parameters
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
				'size_human' => format_file_size((int)$row['total_size'])
			];
		}
		
		$stats['total_size_human'] = format_file_size($stats['total_size']);
		
		return $stats;
	}


	/**
	 * Get allowed MIME types for upload
	 * 
	 * @param string|null $media_type Filter by media type
	 * @return array List of MIME types
	 */
	public function get_allowed_mime_types(?string $media_type = null): array {
		
		if($media_type !== null) {
			return $this->files->get_allowed_mime_types($media_type);
		}
		
		$all_types = $this->files->get_allowed_mime_types();
		return array_merge(...array_values($all_types));
	}


	/**
	 * Get max file size for media type
	 * 
	 * @param string $media_type Media type
	 * @return int Max size in bytes
	 */
	public function get_max_file_size(string $media_type): int {
		return $this->files->get_max_file_size($media_type);
	}


	/**
	 * Get media list with filters and pagination
	 * 
	 * @param array $filters Filters array
	 * @param int $page Page number
	 * @param int $limit Items per page
	 * @return array List of media with formatted data
	 */
	public function get_media_list(array $filters = [], int $page = 1, int $limit = 20): array {
		// Define filter mappings (reuse from get_media_count)
		$filter_map = [
			'media_type' => 'media_type = :media_type',
			'status' => 'status = :status', 
			'user_id' => 'user_id = :user_id'
		];
		
		// Build WHERE conditions and parameters
		$where_conditions = [];
		$params = [];
		
		foreach($filter_map as $key => $condition) {
			isset($filters[$key]) && ($where_conditions[] = $condition) && ($params[$key] = $filters[$key]);
		}
		
		// Handle search filter separately
		isset($filters['search']) && ($where_conditions[] = '(original_name LIKE :search OR description LIKE :search)') && ($params['search'] = '%' . $filters['search'] . '%');
		
		// Build and execute SQL with pagination
		$sql = 'SELECT * FROM ' . TABLE_MEDIA . 
			   (!empty($where_conditions) ? ' WHERE ' . implode(' AND ', $where_conditions) : '') . 
			   ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
		
		$params += ['limit' => $limit, 'offset' => ($page - 1) * $limit];
		
		// Fetch and format results
		return array_map(fn($media) => $this->format_media_data($media), $this->db->fetch_all($sql, $params));
	}


	/**
	 * Get total count of media files with filters
	 * 
	 * @param array $filters Filters array
	 * @return int Total count
	 */
	public function get_media_count(array $filters = []): int {
		// Define filter mappings
		$filter_map = [
			'media_type' => 'media_type = :media_type',
			'status' => 'status = :status', 
			'user_id' => 'user_id = :user_id'
		];
		
		// Build WHERE conditions and parameters
		$where_conditions = [];
		$params = [];
		
		foreach($filter_map as $key => $condition) {
			isset($filters[$key]) && ($where_conditions[] = $condition) && ($params[$key] = $filters[$key]);
		}
		
		// Handle search filter separately
		isset($filters['search']) && ($where_conditions[] = '(original_name LIKE :search OR description LIKE :search)') && ($params['search'] = '%' . $filters['search'] . '%');
		
		// Build and execute SQL
		$sql = 'SELECT COUNT(*) as total FROM ' . TABLE_MEDIA . (!empty($where_conditions) ? ' WHERE ' . implode(' AND ', $where_conditions) : '');
		
		return (int)$this->db->fetch_assoc($sql, $params)['total'];
	}


	/**
	 * Get file with formatted data
	 * 
	 * @param int $id Media ID
	 * @return array|null Formatted media data or null
	 */
	public function get_file_formatted(int $id): ?array {
		$media = $this->files->get_by_id($id);
		
		if(!$media) {
			return null;
		}
		
		// Get variants
		$media['variants'] = $this->files->get_variants($id);
		
		return $this->format_media_data($media);
	}


	/**
	 * Get variant file info
	 * 
	 * @param int $media_id Media ID
	 * @param string $variant_type Variant type
	 * @return array|null Variant info or null
	 */
	public function get_variant_file(int $media_id, string $variant_type): ?array {
		$sql = 'SELECT * FROM ' . TABLE_MEDIA_VARS . ' WHERE media_id = :media_id AND variant_type = :variant_type LIMIT 1';
		$result = $this->db->fetch_assoc($sql, [
			'media_id' => $media_id,
			'variant_type' => $variant_type
		]);
		
		return $result ?: null;
	}


	/**
	 * Format media data for output
	 * 
	 * @param array $media Raw media data
	 * @return array Formatted media data
	 */
	private function format_media_data(array $media): array {
		// Format file size using Media class method
		$media['file_size_human'] = format_file_size($media['file_size']);
		
		// Format timestamps
		$media['created_at_formatted'] = date('Y-m-d H:i:s', $media['created_at']);
		$media['updated_at_formatted'] = date('Y-m-d H:i:s', $media['updated_at']);
		
		// Decode metadata if exists
		if(!empty($media['metadata'])) {
			$media['metadata'] = json_decode($media['metadata'], true);
		}
		
		return $media;
	}
}
