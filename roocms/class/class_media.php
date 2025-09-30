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
 * Class for operations Media Content
 */
class Media {

    use MediaImage, MediaDoc, MediaVideo, MediaAudio, MediaArch;

    private Db $db;
    private SiteSettings $siteSettings;
    private ?GD $gd = null;

    # Allowed MIME types by category (will be loaded from SiteSettings in future)
    private array $allowed_mime_types = [];
    
    # Max file sizes in bytes (will be loaded from SiteSettings in future)
    private array $max_file_sizes = [];
    
    # Number of files for MediaArch trait
    protected int $numFiles = 0;

    # Media statuses
    public const STATUSES = [
        'uploaded',
        'processing', 
        'ready',
        'error',
        'deleted'
    ];

    # Media types
    public const TYPES = [
        'image',
        'document',
        'video', 
        'audio',
        'archive',
        'other'
    ];

    private const VARIANT_TYPES = [
        'thumbnail',
        'large',
        'original'
    ];
    
    private const ENTITY_TYPES = [
        'post',
        'page',
        'category',
        'product',
        'service',
        'news',
        'gallery',
        'tag',
        'user',
        'content',
        'other'
    ];

    private const RELATIONSHIP_TYPES = [
        'attachment',
        'avatar',
        'content',
        'other'
    ];

    private const STATUS_TYPES = [
        'uploaded',
        'processing',
        'ready',
        'error',
        'deleted'
    ];

    public int $id;
    public string $uuid;
    public string $original_name;
    public string $filename;
    public string $file_path;
    public string $mime_type;
    public int $file_size;
    public string $media_type;
    public string $extension;
    public ?int $width;
    public ?int $height;
    public ?int $duration;
    public ?array $metadata;
    public ?string $description;
    public ?string $keywords;
    public ?int $user_id;
    public string $status;
    public string $created_at;
    public string $updated_at;



    /**
     * Constructor
     * 
     * @param Db $db Database
     * @param SiteSettings $siteSettings Site settings
     * @param GD $gd GD instance
     */
    public function __construct(Db $db, SiteSettings $siteSettings, GD $gd) {
        $this->db = $db;
        $this->siteSettings = $siteSettings;
        $this->gd = $gd;
        
        # Initialize default values (will be replaced with SiteSettings in future)
        $this->init_default_mime_types();
        $this->init_default_file_sizes();
    }


    /**
     * Initialize default allowed MIME types
     * TODO: Load from SiteSettings
     */
    private function init_default_mime_types(): void {
        $this->allowed_mime_types = [
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'document' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'application/rtf'
            ],
            'video' => [
                'video/mp4',
                'video/avi',
                'video/x-msvideo',
                'video/quicktime',
                'video/x-ms-wmv',
                'video/x-flv',
                'video/webm'
            ],
            'audio' => [
                'audio/mpeg',
                'audio/wav',
                'audio/ogg',
                'audio/flac',
                'audio/aac',
                'audio/x-m4a'
            ],
            'archive' => [
                'application/zip',
                'application/x-7z-compressed',
                'application/x-rar-compressed',
                'application/x-tar',
                'application/gzip',
                'application/x-bzip2'
            ]
        ];
    }


    /**
     * Initialize default max file sizes
     * TODO: Load from SiteSettings
     */
    private function init_default_file_sizes(): void {
        $this->max_file_sizes = [
            'image' => 10485760,     // 10 MB
            'document' => 52428800,  // 50 MB
            'video' => 524288000,    // 500 MB
            'audio' => 104857600,    // 100 MB
            'archive' => 104857600,  // 100 MB
            'other' => 10485760      // 10 MB
        ];
    }


    /**
     * Get allowed MIME types
     * 
     * @param string|null $media_type Filter by media type
     * @return array MIME types
     */
    public function get_allowed_mime_types(?string $media_type = null): array {
        if($media_type !== null && isset($this->allowed_mime_types[$media_type])) {
            return $this->allowed_mime_types[$media_type];
        }
        
        return $this->allowed_mime_types;
    }


    /**
     * Get max file size for media type
     * 
     * @param string $media_type Media type
     * @return int Max size in bytes
     */
    public function get_max_file_size(string $media_type): int {
        return $this->max_file_sizes[$media_type] ?? $this->max_file_sizes['other'];
    }


    /**
     * Format file size to human readable format
     * 
     * @param int $bytes File size in bytes
     * @return string Formatted file size
     */
    public function format_file_size(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        
        return number_format($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }


    /**
     * Validate uploaded file basic requirements
     * 
     * @param array $file File data from $_FILES
     * @return array Validated file info [mime_type, extension, media_type]
     * @throws DomainException On validation failure
     */
    public function validate_uploaded_file(array $file): array {
        # Check if file was uploaded
        if(!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new DomainException('Invalid uploaded file', 400);
        }
        
        # Check for upload errors
        if($file['error'] !== UPLOAD_ERR_OK) {
            throw new DomainException('File upload error: ' . $file['error'], 400);
        }
        
        # Check file size
        if($file['size'] <= 0) {
            throw new DomainException('File is empty', 400);
        }
        
        # Get MIME type
        $mime_type = mime_content_type($file['tmp_name']);
        if($mime_type === false) {
            throw new DomainException('Cannot determine file type', 400);
        }
        
        # Determine extension  
        $extension_raw = pathinfo($file['name'], PATHINFO_EXTENSION);
        $extension = is_string($extension_raw) ? strtolower($extension_raw) : '';
        
        # Determine media type
        $media_type = $this->determine_media_type($mime_type, $extension);
        
        return [
            'mime_type' => $mime_type,
            'extension' => $extension, 
            'media_type' => $media_type
        ];
    }


    /**
     * Upload file to storage
     * 
     * @param array $file Uploaded file from $_FILES
     * @param int|null $user_id User ID who uploads file
     * @param array $options Additional options
     * @return int|false Media ID or false on failure
     */
    public function upload(array $file, ?int $user_id = null, array $options = []): int|false {
        
        try {
            # Validate file using centralized validation
            $file_info = $this->validate_uploaded_file($file);
            $mime_type = $file_info['mime_type'];
            $extension = $file_info['extension'];
            $media_type = $file_info['media_type'];
        } catch(DomainException) {
            return false;  # Media class returns false on failure
        }

        # Get file info
        $original_name = sanitize_filename($file['name']);
        $tmp_name = $file['tmp_name'];
        $file_size = $file['size'];
        
        # Generate unique filename
        $uuid = $this->generate_uuid();
        $filename = $this->generate_filename($uuid, $extension);
        
        # Determine storage path
        $file_path = $this->get_storage_path($media_type);
        $full_path = _UPLOAD . $file_path . '/' . $filename;
        
        # Create directory if not exists
        if(!is_dir(_UPLOAD . $file_path)) {
            mkdir(_UPLOAD . $file_path, 0755, true);
        }
        
        # Move uploaded file
        if(!move_uploaded_file($tmp_name, $full_path)) {
            return false;
        }
        
        # Get image dimensions if it's an image
        $width = null;
        $height = null;
        if($media_type === 'image') {
            $size = @getimagesize($full_path);
            if($size !== false) {
                $width = $size[0];
                $height = $size[1];
            }
        }
        
        # Insert into database
        $data = [
            'uuid' => $uuid,
            'user_id' => $user_id,
            'original_name' => $original_name,
            'filename' => $filename,
            'file_path' => $file_path,
            'mime_type' => $mime_type,
            'file_size' => $file_size,
            'media_type' => $media_type,
            'extension' => $extension,
            'width' => $width,
            'height' => $height,
            'metadata' => isset($options['metadata']) ? json_encode($options['metadata']) : null,
            'status' => 'uploaded',
            'created_at' => time(),
            'updated_at' => time()
        ];
        
        $result = $this->db->insert('TABLE_MEDIA')
            ->data($data)
            ->execute();
        
        if($result->rowCount() === 0) {
            # Clean up file if database insert failed
            @unlink($full_path);
            return false;
        }
        
        $media_id = (int)$this->db->insert_id();
        
        # Process file based on media type
        match($media_type) {
            'image' => $this->process_image($media_id, $full_path, $extension),
            'video' => $this->process_video($media_id, $full_path),
            'audio' => $this->process_audio($media_id, $full_path),
            'document' => $this->process_document($media_id, $full_path),
            'archive' => $this->process_archive($media_id, $full_path),
            default => null
        };
        
        # Update status to ready
        $this->update_status($media_id, 'ready');
        
        return $media_id;
    }


    /**
     * Get media by ID
     * 
     * @param int $id Media ID
     * @return array|false Media data or false
     */
    public function get_by_id(int $id): array|false {
        $sql = "SELECT * FROM " . TABLE_MEDIA . " WHERE id = :id LIMIT 1";
        return $this->db->fetch_assoc($sql, ['id' => $id]);
    }


    /**
     * Get media by UUID
     * 
     * @param string $uuid Media UUID
     * @return array|false Media data or false
     */
    public function get_by_uuid(string $uuid): array|false {
        $sql = "SELECT * FROM " . TABLE_MEDIA . " WHERE uuid = :uuid LIMIT 1";
        return $this->db->fetch_assoc($sql, ['uuid' => $uuid]);
    }


    /**
     * Delete media file and all its variants
     * 
     * @param int $id Media ID
     * @return bool Success
     */
    public function delete(int $id): bool {
        
        # Get media info
        $media = $this->get_by_id($id);
        if(!$media) {
            return false;
        }
        
        # Delete physical files
        $file_path = _UPLOAD . $media['file_path'] . '/' . $media['filename'];
        if(file_exists($file_path)) {
            @unlink($file_path);
        }
        
        # Delete variants
        $variants = $this->get_variants($id);
        foreach($variants as $variant) {
            $variant_path = _UPLOAD . $variant['file_path'];
            if(file_exists($variant_path)) {
                @unlink($variant_path);
            }
        }
        
        # Delete from database (CASCADE will handle variants and relations)
        $result = $this->db->delete('TABLE_MEDIA')
            ->where('id', '=', $id)
            ->execute();
        
        return $result->rowCount() > 0;
    }


    /**
     * Get media variants (thumbnails, large, original)
     * 
     * @param int $media_id Media ID
     * @return array List of variants
     */
    public function get_variants(int $media_id): array {
        $sql = "SELECT * FROM " . TABLE_MEDIA_VARS . " WHERE media_id = :media_id ORDER BY id";
        return $this->db->fetch_all($sql, ['media_id' => $media_id]);
    }


    /**
     * Add media relationship to entity
     * 
     * @param int $media_id Media ID
     * @param string $entity_type Entity type (post, page, user, etc.)
     * @param int $entity_id Entity ID
     * @param string $relationship_type Relationship type
     * @param array $metadata Additional metadata
     * @return int|false Relationship ID or false
     */
    public function attach_to_entity(int $media_id, string $entity_type, int $entity_id, string $relationship_type = 'attachment', array $metadata = []): int|false {
        
        $data = [
            'media_id' => $media_id,
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'relationship_type' => $relationship_type,
            'metadata' => !empty($metadata) ? json_encode($metadata) : null,
            'created_at' => time()
        ];
        
        return $this->db->insert('TABLE_MEDIA_RELS', $data);
    }


    /**
     * Get media for entity
     * 
     * @param string $entity_type Entity type
     * @param int $entity_id Entity ID
     * @param string|null $relationship_type Filter by relationship type
     * @return array List of media
     */
    public function get_for_entity(string $entity_type, int $entity_id, ?string $relationship_type = null): array {
        
        $where = [
            'entity_type' => $entity_type,
            'entity_id' => $entity_id
        ];
        
        if($relationship_type !== null) {
            $where['relationship_type'] = $relationship_type;
        }
        
        $sql = "SELECT * FROM " . TABLE_MEDIA_RELS . " WHERE entity_type = :entity_type AND entity_id = :entity_id";
        $params = ['entity_type' => $entity_type, 'entity_id' => $entity_id];
        
        if($relationship_type) {
            $sql .= " AND relationship_type = :relationship_type";
            $params['relationship_type'] = $relationship_type;
        }
        
        $sql .= " ORDER BY sort_order ASC";
        $relations = $this->db->fetch_all($sql, $params);
        
        $media_list = [];
        foreach($relations as $relation) {
            $media = $this->get_by_id($relation['media_id']);
            if($media) {
                $media['relationship'] = $relation;
                $media_list[] = $media;
            }
        }
        
        return $media_list;
    }


    /**
     * Update media status
     * 
     * @param int $id Media ID
     * @param string $status New status
     * @return bool Success
     */
    public function update_status(int $id, string $status): bool {
        
        if(!in_array($status, self::STATUS_TYPES, true)) {
            return false;
        }
        
        $result = $this->db->update('TABLE_MEDIA')
            ->data([
                'status' => $status,
                'updated_at' => time()
            ])
            ->where('id', '=', $id)
            ->execute();
        
        return $result->rowCount() > 0;
    }


    /**
     * Generate UUID v4
     * 
     * @return string UUID
     */
    private function generate_uuid(): string {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }


    /**
     * Generate unique filename
     * 
     * @param string $uuid UUID
     * @param string $extension File extension
     * @return string Filename
     */
    private function generate_filename(string $uuid, string $extension): string {
        return $uuid . '.' . $extension;
    }


    /**
     * Get storage path for media type
     * 
     * @param string $media_type Media type
     * @return string Relative path
     */
    private function get_storage_path(string $media_type): string {
        
        return match($media_type) {
            'image' => '/img',
            'video' => '/av',
            'audio' => '/av',
            'document' => '/files',
            'archive' => '/files',
            default => '/files'
        };
    }


    /**
     * Determine media type by MIME type and extension
     * 
     * @param string $mime_type MIME type
     * @param string $extension File extension
     * @return string Media type
     */
    private function determine_media_type(string $mime_type, string $extension): string {
        
        # Image types
        if(str_starts_with($mime_type, 'image/')) {
            return 'image';
        }
        
        # Video types
        if(str_starts_with($mime_type, 'video/')) {
            return 'video';
        }
        
        # Audio types
        if(str_starts_with($mime_type, 'audio/')) {
            return 'audio';
        }
        
        # Archive types
        $archive_extensions = ['zip', '7z', 'rar', 'tar', 'gz', 'bz2', 'xz'];
        if(in_array($extension, $archive_extensions, true)) {
            return 'archive';
        }
        
        # Document types
        $doc_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'txt', 'rtf'];
        if(in_array($extension, $doc_extensions, true)) {
            return 'document';
        }
        
        return 'other';
    }


}