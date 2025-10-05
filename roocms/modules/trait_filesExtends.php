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
 * Trait for extending file operations
 */
trait FilesExtends {



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
            'video' => '/files',
            'audio' => '/files',
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
        
        // Image types
        if(str_starts_with($mime_type, 'image/')) {
            return 'image';
        }
        
        // Video types
        if(str_starts_with($mime_type, 'video/')) {
            return 'video';
        }
        
        // Audio types
        if(str_starts_with($mime_type, 'audio/')) {
            return 'audio';
        }
        
        // Archive types
        $archive_extensions = ['zip', '7z', 'rar', 'tar', 'gz', 'bz2', 'xz'];
        if(in_array($extension, $archive_extensions, true)) {
            return 'archive';
        }
        
        // Document types
        $doc_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'txt', 'rtf'];
        if(in_array($extension, $doc_extensions, true)) {
            return 'document';
        }
        
        return 'other';
    }
}