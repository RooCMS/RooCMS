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
 * Trait for operations Media Videos
 * Handles MP4, AVI, MKV, MOV, WMV, FLV, WEBM video files
 */
trait MediaVideo {

    /**
     * Process uploaded video
     * Extracts metadata (duration, resolution, codec)
     * 
     * @param int $media_id Media ID
     * @param string $file_path Full path to uploaded file
     * @return bool Success
     */
    protected function process_video(int $media_id, string $file_path): bool {
        
        try {
            # Validate video
            if(!$this->is_valid_video($file_path)) {
                return false;
            }
            
            # Extract video metadata
            $metadata = $this->extract_video_metadata($file_path);
            
            # Update media record with video info
            $update_data = [
                'updated_at' => time()
            ];
            
            if(isset($metadata['duration'])) {
                $update_data['duration'] = $metadata['duration'];
            }
            
            if(isset($metadata['width'])) {
                $update_data['width'] = $metadata['width'];
            }
            
            if(isset($metadata['height'])) {
                $update_data['height'] = $metadata['height'];
            }
            
            if(!empty($metadata)) {
                $update_data['metadata'] = json_encode($metadata);
            }
            
            $this->db->query_update('TABLE_MEDIA', $update_data, ['id' => $media_id]);
            
            return true;
            
        } catch(\Exception $e) {
            return false;
        }
    }


    /**
     * Extract video metadata using getID3 or basic methods
     * 
     * @param string $file_path Video file path
     * @return array Metadata
     */
    private function extract_video_metadata(string $file_path): array {
        
        $metadata = [
            'format' => pathinfo($file_path, PATHINFO_EXTENSION),
            'size_human' => $this->format_file_size(filesize($file_path))
        ];
        
        # Try to get video info using shell commands (if available)
        # This is a basic implementation. For production, consider using FFmpeg or getID3 library
        
        # Check if ffprobe is available
        if($this->is_command_available('ffprobe')) {
            $ffprobe_data = $this->extract_video_metadata_ffprobe($file_path);
            $metadata = array_merge($metadata, $ffprobe_data);
        }
        
        return $metadata;
    }


    /**
     * Extract video metadata using FFprobe
     * 
     * @param string $file_path Video file path
     * @return array Metadata
     */
    private function extract_video_metadata_ffprobe(string $file_path): array {
        
        $metadata = [];
        
        # Build ffprobe command
        $command = sprintf(
            'ffprobe -v quiet -print_format json -show_format -show_streams %s 2>&1',
            escapeshellarg($file_path)
        );
        
        # Execute command
        $output = shell_exec($command);
        
        if(!$output) {
            return $metadata;
        }
        
        # Parse JSON output
        $data = @json_decode($output, true);
        
        if(!$data) {
            return $metadata;
        }
        
        # Extract format info
        if(isset($data['format'])) {
            if(isset($data['format']['duration'])) {
                $metadata['duration'] = (int)round((float)$data['format']['duration']);
            }
            
            if(isset($data['format']['bit_rate'])) {
                $metadata['bitrate'] = (int)$data['format']['bit_rate'];
                $metadata['bitrate_human'] = $this->format_bitrate((int)$data['format']['bit_rate']);
            }
            
            if(isset($data['format']['format_name'])) {
                $metadata['format_name'] = $data['format']['format_name'];
            }
        }
        
        # Extract video stream info
        if(isset($data['streams'])) {
            foreach($data['streams'] as $stream) {
                if($stream['codec_type'] === 'video') {
                    $metadata['width'] = $stream['width'] ?? null;
                    $metadata['height'] = $stream['height'] ?? null;
                    $metadata['codec'] = $stream['codec_name'] ?? null;
                    $metadata['fps'] = $this->parse_fps($stream['r_frame_rate'] ?? '');
                    break;
                }
            }
        }
        
        return $metadata;
    }


    /**
     * Check if command is available on system
     * 
     * @param string $command Command name
     * @return bool Available
     */
    private function is_command_available(string $command): bool {
        
        # On Windows
        if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec(sprintf('where %s 2>NUL', escapeshellarg($command)));
            return !empty($output);
        }
        
        # On Unix/Linux
        $output = shell_exec(sprintf('which %s 2>/dev/null', escapeshellarg($command)));
        return !empty($output);
    }


    /**
     * Parse FPS from fraction string
     * 
     * @param string $fps_string FPS string like "30000/1001"
     * @return float|null FPS value
     */
    private function parse_fps(string $fps_string): ?float {
        
        if(empty($fps_string)) {
            return null;
        }
        
        if(str_contains($fps_string, '/')) {
            [$numerator, $denominator] = explode('/', $fps_string);
            if($denominator > 0) {
                return round((float)$numerator / (float)$denominator, 2);
            }
        }
        
        return (float)$fps_string;
    }


    /**
     * Format bitrate to human-readable format
     * 
     * @param int $bitrate Bitrate in bits per second
     * @return string Formatted bitrate
     */
    private function format_bitrate(int $bitrate): string {
        
        if($bitrate >= 1000000) {
            return round($bitrate / 1000000, 2) . ' Mbps';
        }
        
        if($bitrate >= 1000) {
            return round($bitrate / 1000, 2) . ' Kbps';
        }
        
        return $bitrate . ' bps';
    }


    /**
     * Validate if file is a valid video
     * 
     * @param string $file_path File path
     * @return bool Is valid video
     */
    private function is_valid_video(string $file_path): bool {
        
        if(!file_exists($file_path) || !is_readable($file_path)) {
            return false;
        }
        
        $path_info = pathinfo($file_path);
        $extension = strtolower($path_info['extension']);
        
        $allowed_extensions = [
            'mp4', 'avi', 'mkv', 'mov', 'wmv', 'flv', 'webm', 'mpeg', 'mpg', '3gp', 'm4v'
        ];
        
        return in_array($extension, $allowed_extensions, true);
    }


    /**
     * Get video info by ID
     * 
     * @param int $media_id Media ID
     * @return array|false Video info or false
     */
    public function get_video_info(int $media_id): array|false {
        
        $media = $this->get_by_id($media_id);
        
        if(!$media || $media['media_type'] !== 'video') {
            return false;
        }
        
        # Decode metadata
        if(isset($media['metadata']) && $media['metadata']) {
            $media['metadata'] = json_decode($media['metadata'], true);
        }
        
        # Format duration
        if(isset($media['duration'])) {
            $media['duration_formatted'] = $this->format_duration($media['duration']);
        }
        
        # Add resolution label
        if(isset($media['width']) && isset($media['height'])) {
            $media['resolution'] = $media['width'] . 'x' . $media['height'];
            $media['quality_label'] = $this->get_quality_label($media['height']);
        }
        
        return $media;
    }


    /**
     * Format duration to human-readable format
     * 
     * @param int $seconds Duration in seconds
     * @return string Formatted duration
     */
    private function format_duration(int $seconds): string {
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        if($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }
        
        return sprintf('%02d:%02d', $minutes, $secs);
    }


    /**
     * Get quality label by height
     * 
     * @param int $height Video height
     * @return string Quality label
     */
    private function get_quality_label(int $height): string {
        
        return match(true) {
            $height >= 2160 => '4K',
            $height >= 1440 => '2K',
            $height >= 1080 => '1080p (Full HD)',
            $height >= 720 => '720p (HD)',
            $height >= 480 => '480p (SD)',
            $height >= 360 => '360p',
            default => '240p'
        };
    }


    /**
     * Get videos by duration range
     * 
     * @param int $min_duration Minimum duration in seconds
     * @param int $max_duration Maximum duration in seconds
     * @return array List of videos
     */
    public function get_videos_by_duration(int $min_duration, int $max_duration): array {
        
        # Custom query for duration range
        $query = "SELECT * FROM " . TABLE_MEDIA . " 
                  WHERE media_type = 'video' 
                  AND duration >= :min_duration 
                  AND duration <= :max_duration 
                  ORDER BY created_at DESC";
        
        return $this->db->query_execute($query, [
            'min_duration' => $min_duration,
            'max_duration' => $max_duration
        ]);
    }


    /**
     * Get videos by resolution
     * 
     * @param int $min_width Minimum width
     * @param int $min_height Minimum height
     * @return array List of videos
     */
    public function get_videos_by_resolution(int $min_width, int $min_height): array {
        
        $query = "SELECT * FROM " . TABLE_MEDIA . " 
                  WHERE media_type = 'video' 
                  AND width >= :min_width 
                  AND height >= :min_height 
                  ORDER BY created_at DESC";
        
        return $this->db->query_execute($query, [
            'min_width' => $min_width,
            'min_height' => $min_height
        ]);
    }


    /**
     * Abstract methods
     */
    abstract public function get_by_id(int $id): array|false;
    abstract public function format_file_size(int $size): string;
}