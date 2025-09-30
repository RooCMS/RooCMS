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
            # Validate image
            if(!$this->is_valid_image($file_path)) {
                return false;
            }
            
            # Get path info
            $path_info = pathinfo($file_path);
            $directory = $path_info['dirname'];
            $filename_without_ext = $path_info['filename'];
            
            # Rename original file to have _orig suffix
            $original_file = $directory . '/' . $filename_without_ext . '_orig.' . $extension;
            rename($file_path, $original_file);
            
            # Use GD class to process image (creates _res and _thumb files)
            $this->gd->modify_image($filename_without_ext, $extension, $directory);
            
            # Store variants in database
            $this->store_image_variants($media_id, $directory, $filename_without_ext, $extension);
            
            return true;
            
        } catch(\Exception $e) {
            return false;
        }
    }


    /**
     * Store image variants in database
     * GD class creates _orig, _res and _thumb files
     * 
     * @param int $media_id Media ID
     * @param string $directory Directory path
     * @param string $filename Filename without extension
     * @param string $extension File extension
     * @return bool Success
     */
    private function store_image_variants(int $media_id, string $directory, string $filename, string $extension): bool {
        
        # Variants created by GD class
        $variants = [
            'original' => '_orig',
            'large' => '_res',
            'thumbnail' => '_thumb'
        ];
        
        $success = true;
        
        foreach($variants as $variant_type => $suffix) {
            $file_path = $directory . '/' . $filename . $suffix . '.' . $extension;
            
            # Check if file exists
            if(!file_exists($file_path)) {
                continue;
            }
            
            # Get file size
            $file_size = @filesize($file_path);
            if($file_size === false) {
                continue;
            }
            
            # Get image dimensions
            $image_info = @getimagesize($file_path);
            if($image_info === false) {
                continue;
            }
            
            # Save variant to database
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
            
            if(!$this->db->insert('TABLE_MEDIA_VARS', $variant_data)) {
                $success = false;
            }
        }
        
        return $success;
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
        
        # Check if MIME type is supported
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

}