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
 * Trait for operations Media Images
 * Uses GD class for image processing
 */
trait MediaImage {

    /**
     * Process uploaded image
     * Uses GD class for image processing (resize, thumbnail, watermark)
     * 
     * @param int $media_id Media ID
     * @param string $file_path Full path to uploaded file
     * @param string $extension File extension
     * @return bool Success
     */
    protected function process_image(int $media_id, string $file_path, string $extension): bool {
        
        try {
            // Validate image
            if(!$this->is_valid_image($file_path)) {
                return false;
            }
            
            // Get path info
            $path_info = pathinfo($file_path);
            $directory = $path_info['dirname'];
            $filename_without_ext = $path_info['filename'];
            
            // Create image variants based on VARIANT_TYPES constant
            $success = $this->create_image_variants($media_id, $file_path, $directory, $filename_without_ext, $extension);
            
            return $success;
            
        } catch(\Exception $e) {
            return false;
        }
    }


    /**
     * Create image variants based on VARIANT_TYPES constant
     * 
     * @param int $media_id Media ID
     * @param string $original_file_path Path to original uploaded file
     * @param string $directory Directory path
     * @param string $filename Filename without extension
     * @param string $extension File extension
     * @return bool Success
     */
    private function create_image_variants(int $media_id, string $original_file_path, string $directory, string $filename, string $extension): bool {
        
        $success = true;
        
        // Get VARIANT_TYPES from parent Media class
        $variant_types = Media::VARIANT_TYPES;
        
        // First, create original file (just rename)
        $original_config = $variant_types['original'];
        $original_target = $directory . '/' . $filename . $original_config['suffix'] . '.' . $extension;
        
        if(!rename($original_file_path, $original_target)) {
            return false;
        }
        
        // Store original variant in database
        if(!$this->store_single_image_variant($media_id, $original_target, 'original')) {
            $success = false;
        }
        
        // Now create other variants from original
        foreach($variant_types as $variant_name => $variant_config) {
            
            // Skip original - already processed
            if($variant_name === 'original') {
                continue;
            }
            
            // Skip variants with no modification
            if($variant_config['modify'] === 'no') {
                continue;
            }
            
            try {
                // Use GD to create variant from original
                $this->gd->modify_image(
                    $variant_config['modify'],
                    $filename,
                    $extension,
                    $directory,
                    ['w' => $variant_config['width'], 'h' => $variant_config['height']],
                    $variant_config['suffix'],
                    $variant_config['watermark']
                );
                
                // Store variant in database
                $variant_file = $directory . '/' . $filename . $variant_config['suffix'] . '.' . $extension;
                if(!$this->store_single_image_variant($media_id, $variant_file, $variant_name)) {
                    $success = false;
                }
                
            } catch(\Exception $e) {
                $success = false;
            }
        }
        
        return $success;
    }


    /**
     * Store single image variant in database
     * 
     * @param int $media_id Media ID
     * @param string $file_path Full path to variant file
     * @param string $variant_type Variant type name
     * @return bool Success
     */
    private function store_single_image_variant(int $media_id, string $file_path, string $variant_type): bool {
        
        // Check if file exists
        if(!file_exists($file_path)) {
            return false;
        }
        
        // Get file size
        $file_size = @filesize($file_path);
        if($file_size === false) {
            return false;
        }
        
        // Get image dimensions
        $image_info = @getimagesize($file_path);
        if($image_info === false) {
            return false;
        }
        
        // Save variant to database
        $variant_data = [
            'media_id' => $media_id,
            'variant_type' => $variant_type,
            'file_path' => str_replace(_UPLOAD, '', $file_path),
            'file_size' => $file_size,
            'width' => $image_info[0],
            'height' => $image_info[1],
            'mime_type' => $image_info['mime'],
            'created_at' => time()
        ];
        
        return $this->db->insert_array($variant_data, TABLE_MEDIA_VARS);
    }




    /**
     * Validate if file is a valid image
     * 
     * @param string $file_path File path
     * @return bool Is valid image
     */
    private function is_valid_image(string $file_path): bool {
        
        if(!file_exists($file_path)) {
            return false;
        }
        
        $image_info = @getimagesize($file_path);
        if($image_info === false) {
            return false;
        }
        
        // Check if MIME type is supported
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        return in_array($image_info['mime'], $allowed_types, true);
    }


    /**
     * Get image variant by type
     * 
     * @param int $media_id Media ID
	 * @param string $variant_type Variant type (thumbnail, large, original)
     * @return array|false Variant data or false
     */
    public function get_image_variant(int $media_id, string $variant_type): array|false {
        
        $sql = "SELECT * FROM " . TABLE_MEDIA_VARS . " WHERE media_id = :media_id AND variant_type = :variant_type LIMIT 1";
        return $this->db->fetch_assoc($sql, [
            'media_id' => $media_id,
            'variant_type' => $variant_type
        ]);
    }


    /**
     * Get all image variants
     * 
     * @param int $media_id Media ID
     * @return array List of variants
     */
    public function get_all_image_variants(int $media_id): array {
        
        $sql = "SELECT * FROM " . TABLE_MEDIA_VARS . " WHERE media_id = :media_id";
        return $this->db->fetch_all($sql, ['media_id' => $media_id]);
    }


    /**
     * Get image info by ID
     * 
     * @param int $media_id Media ID
     * @return array|false Image info or false
     */
    public function get_image_info(int $media_id): array|false {
        return $this->get_media_info($media_id, 'image');
    }


    /**
     * Process image-specific info (overrides Media class method)
     * 
     * @param array $media Base media data
     * @return array Processed media info with image-specific enhancements
     */
    private function process_image_info(array $media): array {
        // Add image-specific processing
        // This method is called by get_media_info() via match()
        
        // Add formatted file size for convenience
        if(isset($media['file_size'])) {
            $media['file_size_formatted'] = $this->format_file_size($media['file_size']);
        }
        
        // Add image-specific metadata formatting
        if(isset($media['metadata']) && is_array($media['metadata'])) {
            // Format dimensions
            if(isset($media['metadata']['width']) && isset($media['metadata']['height'])) {
                $media['metadata']['dimensions'] = $media['metadata']['width'] . 'x' . $media['metadata']['height'];
            }
        }
        
        // Load variants for images
        $media['variants'] = $this->get_all_image_variants($media['id']);
        
        return $media;
    }

}