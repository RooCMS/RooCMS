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
 * Trait for operations Media Audio
 * Handles MP3, WAV, OGG, FLAC, AAC, M4A audio files
 */
trait MediaAudio {

    /**
     * Process uploaded audio
     * Extracts metadata (duration, bitrate, artist, title, album)
     * 
     * @param int $media_id Media ID
     * @param string $file_path Full path to uploaded file
     * @return bool Success
     */
    protected function process_audio(int $media_id, string $file_path): bool {
        
        try {
            # Validate audio
            if(!$this->is_valid_audio($file_path)) {
                return false;
            }
            
            # Extract audio metadata
            $metadata = $this->extract_audio_metadata($file_path);
            
            # Update media record with audio info
            $update_data = [
                'updated_at' => time()
            ];
            
            if(isset($metadata['duration'])) {
                $update_data['duration'] = $metadata['duration'];
            }
            
            if(!empty($metadata)) {
                $update_data['metadata'] = json_encode($metadata);
            }
            
            $this->db->update(TABLE_MEDIA)
                ->data($update_data)
                ->where('id', $media_id, '=')
                ->execute();
            
            return true;
            
        } catch(\Exception $e) {
            return false;
        }
    }


    /**
     * Extract audio metadata
     * 
     * @param string $file_path Audio file path
     * @return array Metadata
     */
    private function extract_audio_metadata(string $file_path): array {
        
        $extension_raw = pathinfo($file_path, PATHINFO_EXTENSION);
        $format = is_string($extension_raw) ? $extension_raw : '';
        
        $metadata = [
            'format' => $format,
            'size_human' => $this->format_file_size(filesize($file_path))
        ];
        
        # Try to get audio info using FFprobe if available
        if($this->is_command_available('ffprobe')) {
            $ffprobe_data = $this->extract_audio_metadata_ffprobe($file_path);
            $metadata = array_merge($metadata, $ffprobe_data);
        }
        
        # Try to extract ID3 tags for MP3
        $extension_raw = pathinfo($file_path, PATHINFO_EXTENSION);
        $extension = is_string($extension_raw) ? strtolower($extension_raw) : '';
        if($extension === 'mp3') {
            $id3_data = $this->extract_mp3_id3_tags($file_path);
            $metadata = array_merge($metadata, $id3_data);
        }
        
        return $metadata;
    }


    /**
     * Extract audio metadata using FFprobe
     * 
     * @param string $file_path Audio file path
     * @return array Metadata
     */
    private function extract_audio_metadata_ffprobe(string $file_path): array {
        
        # Execute ffprobe command
        $command = sprintf('ffprobe -v quiet -print_format json -show_format -show_streams %s 2>&1', escapeshellarg($file_path));
        $output = shell_exec($command);
        
        # Early returns for invalid data
        if (!$output) {
            return [];
        }
        $data = @json_decode($output, true);
        if (!$data) {
            return [];
        }
        
        $metadata = [];
        
        # Extract format metadata using array mapping
        $format = $data['format'] ?? [];
        $format_map = [
            'duration' => fn($v) => (int)round((float)$v),
            'bit_rate' => fn($v) => ['bitrate' => (int)$v, 'bitrate_human' => $this->format_bitrate((int)$v)],
            'format_name' => fn($v) => $v
        ];
        
        foreach($format_map as $key => $processor) {
            if(isset($format[$key])) {
                $result = $processor($format[$key]);
                $metadata = is_array($result) ? array_merge($metadata, $result) : array_merge($metadata, [$key => $result]);
            }
        }
        
        # Extract tags with fallback keys
        $tags = $format['tags'] ?? [];
        $tag_map = [
            'artist' => ['artist', 'ARTIST'],
            'title' => ['title', 'TITLE'], 
            'album' => ['album', 'ALBUM'],
            'year' => ['date', 'DATE', 'year'],
            'genre' => ['genre', 'GENRE'],
            'comment' => ['comment', 'COMMENT']
        ];
        
        foreach($tag_map as $field => $keys) {
            $metadata[$field] = array_reduce($keys, fn($carry, $key) => $carry ?? ($tags[$key] ?? null), null);
        }
        
        # Extract audio stream info
        $audio_stream = array_filter($data['streams'] ?? [], fn($s) => ($s['codec_type'] ?? '') === 'audio')[0] ?? null;
        $audio_stream && ($metadata = array_merge($metadata, [
            'codec' => $audio_stream['codec_name'] ?? null,
            'sample_rate' => isset($audio_stream['sample_rate']) ? (int)$audio_stream['sample_rate'] : null,
            'channels' => $audio_stream['channels'] ?? null
        ]));
        
        return $metadata;
    }


    /**
     * Extract ID3 tags from MP3 file
     * Simple implementation without external libraries
     * 
     * @param string $file_path MP3 file path
     * @return array Metadata
     */
    private function extract_mp3_id3_tags(string $file_path): array {
        
        $metadata = [];
        
        # Read last 128 bytes for ID3v1 tags
        $handle = @fopen($file_path, 'rb');
        if($handle === false) {
            return $metadata;
        }
        
        # Seek to 128 bytes before end
        fseek($handle, -128, SEEK_END);
        $tag = fread($handle, 128);
        fclose($handle);
        
        # Check for TAG header
        if(substr($tag, 0, 3) !== 'TAG') {
            return $metadata;
        }
        
        # Extract fields
        $metadata['title'] = trim(substr($tag, 3, 30));
        $metadata['artist'] = trim(substr($tag, 33, 30));
        $metadata['album'] = trim(substr($tag, 63, 30));
        $metadata['year'] = trim(substr($tag, 93, 4));
        $metadata['comment'] = trim(substr($tag, 97, 30));
        
        # Genre byte
        $genre_byte = ord(substr($tag, 127, 1));
        $metadata['genre'] = $this->get_id3_genre($genre_byte);
        
        # Remove empty values
        $metadata = array_filter($metadata, fn($val) => !empty($val));
        
        return $metadata;
    }


    /**
     * Get genre name by ID3v1 genre byte
     * 
     * @param int $genre_byte Genre byte
     * @return string|null Genre name
     */
    private function get_id3_genre(int $genre_byte): ?string {
        
        $genres = [
            0 => 'Blues', 1 => 'Classic Rock', 2 => 'Country', 3 => 'Dance',
            4 => 'Disco', 5 => 'Funk', 6 => 'Grunge', 7 => 'Hip-Hop',
            8 => 'Jazz', 9 => 'Metal', 10 => 'New Age', 11 => 'Oldies',
            12 => 'Other', 13 => 'Pop', 14 => 'R&B', 15 => 'Rap',
            16 => 'Reggae', 17 => 'Rock', 18 => 'Techno', 19 => 'Industrial',
            20 => 'Alternative', 21 => 'Ska', 22 => 'Death Metal', 23 => 'Pranks',
            24 => 'Soundtrack', 25 => 'Euro-Techno', 26 => 'Ambient'
        ];
        
        return $genres[$genre_byte] ?? null;
    }


    /**
     * Validate if file is a valid audio
     * 
     * @param string $file_path File path
     * @return bool Is valid audio
     */
    private function is_valid_audio(string $file_path): bool {
        
        if(!file_exists($file_path) || !is_readable($file_path)) {
            return false;
        }
        
        $path_info = pathinfo($file_path);
        $extension = strtolower($path_info['extension']);
        
        $allowed_extensions = [
            'mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma', 'ape', 'opus'
        ];
        
        return in_array($extension, $allowed_extensions, true);
    }


    /**
     * Get audio info by ID
     * 
     * @param int $media_id Media ID
     * @return array|false Audio info or false
     */
    public function get_audio_info(int $media_id): array|false {
        return $this->get_media_info($media_id, 'audio');
    }


    /**
     * Process audio-specific info (overrides Media class method)
     * 
     * @param array $media Base media data
     * @return array Processed media info with audio-specific enhancements
     */
    private function process_audio_info(array $media): array {
        # Add audio-specific processing
        # This method is called by get_media_info() via match()
        
        # Add formatted file size for convenience
        if(isset($media['file_size'])) {
            $media['file_size_formatted'] = $this->format_file_size($media['file_size']);
        }
        
        # Format duration
        if(isset($media['metadata']['duration'])) {
            $media['duration_formatted'] = $this->format_duration((int)$media['metadata']['duration']);
        }
        
        # Format bitrate if available
        if(isset($media['metadata']['bitrate'])) {
            $media['bitrate_formatted'] = $this->format_bitrate((int)$media['metadata']['bitrate']);
        }
        
        # Add music-specific formatting
        if(isset($media['metadata']['artist']) && isset($media['metadata']['title'])) {
            $media['display_name'] = $media['metadata']['artist'] . ' - ' . $media['metadata']['title'];
        }
        
        return $media;
    }


    /**
     * Get audio files by artist
     * 
     * @param string $artist Artist name
     * @return array List of audio files
     */
    public function get_audio_by_artist(string $artist): array {
        
        # Get all audio files
        $sql = "SELECT * FROM " . TABLE_MEDIA . " WHERE media_type = :media_type";
        $audio_files = $this->db->fetch_all($sql, ['media_type' => 'audio']);
        
        $results = [];
        
        foreach($audio_files as $audio) {
            # Decode metadata
            if(isset($audio['metadata']) && $audio['metadata']) {
                $metadata = json_decode($audio['metadata'], true);
                
                if(isset($metadata['artist']) && stripos($metadata['artist'], $artist) !== false) {
                    $audio['metadata'] = $metadata;
                    $results[] = $audio;
                }
            }
        }
        
        return $results;
    }


    /**
     * Get audio files by album
     * 
     * @param string $album Album name
     * @return array List of audio files
     */
    public function get_audio_by_album(string $album): array {
        
        # Get all audio files
        $sql = "SELECT * FROM " . TABLE_MEDIA . " WHERE media_type = :media_type";
        $audio_files = $this->db->fetch_all($sql, ['media_type' => 'audio']);
        
        $results = [];
        
        foreach($audio_files as $audio) {
            # Decode metadata
            if(isset($audio['metadata']) && $audio['metadata']) {
                $metadata = json_decode($audio['metadata'], true);
                
                if(isset($metadata['album']) && stripos($metadata['album'], $album) !== false) {
                    $audio['metadata'] = $metadata;
                    $results[] = $audio;
                }
            }
        }
        
        return $results;
    }


    /**
     * Get audio files by genre
     * 
     * @param string $genre Genre name
     * @return array List of audio files
     */
    public function get_audio_by_genre(string $genre): array {
        
        # Get all audio files
        $sql = "SELECT * FROM " . TABLE_MEDIA . " WHERE media_type = :media_type";
        $audio_files = $this->db->fetch_all($sql, ['media_type' => 'audio']);
        
        $results = [];
        
        foreach($audio_files as $audio) {
            # Decode metadata
            if(isset($audio['metadata']) && $audio['metadata']) {
                $metadata = json_decode($audio['metadata'], true);
                
                if(isset($metadata['genre']) && stripos($metadata['genre'], $genre) !== false) {
                    $audio['metadata'] = $metadata;
                    $results[] = $audio;
                }
            }
        }
        
        return $results;
    }


    /**
     * Get all unique artists
     * 
     * @return array List of artists
     */
    public function get_all_artists(): array {
        
        # Get all audio files
        $sql = "SELECT * FROM " . TABLE_MEDIA . " WHERE media_type = :media_type";
        $audio_files = $this->db->fetch_all($sql, ['media_type' => 'audio']);
        
        $artists = [];
        
        foreach($audio_files as $audio) {
            if(isset($audio['metadata']) && $audio['metadata']) {
                $metadata = json_decode($audio['metadata'], true);
                
                if(isset($metadata['artist']) && !empty($metadata['artist'])) {
                    $artists[] = $metadata['artist'];
                }
            }
        }
        
        # Return unique artists sorted alphabetically
        $artists = array_unique($artists);
        sort($artists);
        
        return $artists;
    }


    /**
     * Get all unique albums
     * 
     * @return array List of albums
     */
    public function get_all_albums(): array {
        
        # Get all audio files
        $sql = "SELECT * FROM " . TABLE_MEDIA . " WHERE media_type = :media_type";
        $audio_files = $this->db->fetch_all($sql, ['media_type' => 'audio']);
        
        $albums = [];
        
        foreach($audio_files as $audio) {
            if(isset($audio['metadata']) && $audio['metadata']) {
                $metadata = json_decode($audio['metadata'], true);
                
                if(isset($metadata['album']) && !empty($metadata['album'])) {
                    $albums[] = $metadata['album'];
                }
            }
        }
        
        # Return unique albums sorted alphabetically
        $albums = array_unique($albums);
        sort($albums);
        
        return $albums;
    }


    /**
     * Abstract methods
     */
    abstract public function format_file_size(int $size): string;
    abstract public function is_command_available(string $command): bool;
    abstract public function format_duration(int $duration): string;
    abstract public function format_bitrate(int $bitrate): string;
    abstract public function get_by_id(int $id): array|false;
}