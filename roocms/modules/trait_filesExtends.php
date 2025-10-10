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
     * @param string $extension File extension (case-insensitive)
     * @return string Media type
     */
    private function determine_media_type(string $mime_type, string $extension): string {
        
        $extension = strtolower($extension);
        
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
        
        // Fallback by extension for cases where MIME type is generic or incorrect
        
        // Audio extensions
        $audio_extensions = ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma', 'ape', 'opus'];
        if(in_array($extension, $audio_extensions, true)) {
            return 'audio';
        }
        
        // Video extensions
        $video_extensions = ['mp4', 'avi', 'mkv', 'mov', 'wmv', 'flv', 'webm', 'mpeg', 'mpg', '3gp', 'm4v'];
        if(in_array($extension, $video_extensions, true)) {
            return 'video';
        }
        
        // Archive extensions
        $archive_extensions = ['zip', '7z', 'rar', 'tar', 'gz', 'bz2', 'xz'];
        if(in_array($extension, $archive_extensions, true)) {
            return 'archive';
        }
        
        // Document extensions
        $doc_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'txt', 'rtf'];
        if(in_array($extension, $doc_extensions, true)) {
            return 'document';
        }
        
        // Image extensions (fallback for cases where MIME is wrong)
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff', 'svg'];
        if(in_array($extension, $image_extensions, true)) {
            return 'image';
        }
        
        return 'other';
    }


    /**
     * Validate if file is valid for given media type
     * 
     * @param string $file_path File path
     * @param string $expected_type Expected media type (image, video, audio, document, archive)
     * @return bool Is valid file
     */
    protected function is_valid_file(string $file_path, string $expected_type): bool {
        
        if(!file_exists($file_path) || !is_readable($file_path)) {
            return false;
        }
        
        $path_info = pathinfo($file_path);
        $extension = strtolower($path_info['extension']);
        
        // Get MIME type if possible
        $mime_type = '';
        if(function_exists('mime_content_type')) {
            $mime_type = mime_content_type($file_path) ?: '';
        }
        
        // Determine actual media type
        $actual_type = $this->determine_media_type($mime_type, $extension);
        
        // Check if matches expected type
        return $actual_type === $expected_type;
    }


    /**
     * Check if command is available on system
     * 
     * @param string $command Command name
     * @return bool Available
     */
    protected function is_command_available(string $command): bool {
        
        // On Windows
        if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec(sprintf('where %s 2>NUL', escapeshellarg($command)));
            return !empty($output);
        }
        
        // On Unix/Linux
        $output = shell_exec(sprintf('which %s 2>/dev/null', escapeshellarg($command)));
        return !empty($output);
    }


    /**
     * Format bitrate to human-readable format
     * 
     * @param int $bitrate Bitrate in bits per second
     * @return string Formatted bitrate
     */
    protected function format_bitrate(int $bitrate): string {
        
        if($bitrate >= 1000000) {
            return round($bitrate / 1000000, 2) . ' Mbps';
        }
        
        if($bitrate >= 1000) {
            return round($bitrate / 1000, 2) . ' Kbps';
        }
        
        return $bitrate . ' bps';
    }


    /**
     * Format duration to human-readable format
     * 
     * @param int $seconds Duration in seconds
     * @return string Formatted duration
     */
    protected function format_duration(int $seconds): string {
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        if($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }
        
        return sprintf('%02d:%02d', $minutes, $secs);
    }
}