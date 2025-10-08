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
 * Trait for operations File Archives
 * Handles ZIP, 7Z, RAR, TAR, GZ, BZ2, XZ archive files
 */
trait FileManagerArch {

    /**
     * Process uploaded archive
     * Extracts metadata (file count, uncompressed size, compression ratio)
     * 
     * @param int $media_id Media ID
     * @param string $file_path Full path to uploaded file
     * @return bool Success
     */
    protected function process_archive(int $media_id, string $file_path): bool {
        
        try {
            // Validate archive
            if(!$this->is_valid_archive($file_path)) {
                return false;
            }
            
            // Extract archive metadata
            $metadata = $this->extract_archive_metadata($file_path);
            
            // Update media metadata
            if(!empty($metadata)) {
                $this->db->query(
                    'UPDATE ' . TABLE_MEDIA . ' SET metadata = ?, updated_at = ? WHERE id = ?',
                    [json_encode($metadata), time(), $media_id]
                );
            }
            
            return true;
            
        } catch(\Exception $e) {
            return false;
        }
    }


    /**
     * Extract archive metadata
     * 
     * @param string $file_path Archive file path
     * @return array Metadata
     */
    private function extract_archive_metadata(string $file_path): array {
        
        $extension_raw = pathinfo($file_path, PATHINFO_EXTENSION);
        $extension = is_string($extension_raw) ? strtolower($extension_raw) : '';
        
        $metadata = [
            'format' => $extension,
            'size_human' => format_file_size(filesize($file_path))
        ];
        
        // Extract info based on archive type
        $archive_info = match($extension) {
            'zip' => $this->extract_zip_info($file_path),
            'tar' => $this->extract_tar_info($file_path),
            'gz' => $this->extract_gz_info($file_path),
            default => []
        };
        
        return array_merge($metadata, $archive_info);
    }


    /**
     * Extract ZIP archive info
     * 
     * @param string $file_path ZIP file path
     * @return array Archive info
     */
    private function extract_zip_info(string $file_path): array {
        
        // Early returns for invalid conditions
        if (!class_exists('ZipArchive')) {
            return [];
        }

        $zip = new \ZipArchive();
        if ($zip->open($file_path) !== true) {
            return [];
        }
        
        // Initialize info with basic data
        $info = [
            'files_count' => $zip->numFiles,
            'files' => []
        ];
        
        // Calculate sizes and build file list
        $total_uncompressed = 0;
        for($i = 0; $i < $zip->numFiles && count($info['files']) < 100; $i++) {
            $stat = $zip->statIndex($i);
            $stat && ($total_uncompressed += $stat['size']) && ($info['files'][] = [
                'name' => $stat['name'],
                'size' => $stat['size'],
                'compressed_size' => $stat['comp_size']
            ]);
        }
        
        // Add size information
        $info['uncompressed_size'] = $total_uncompressed;
        $info['uncompressed_size_human'] = format_file_size($total_uncompressed);
        
        // Calculate compression ratio if possible
        $compressed_size = filesize($file_path);
        ($compressed_size > 0 && $total_uncompressed > 0) && ($info['compression_ratio'] = round((1 - ($compressed_size / $total_uncompressed)) * 100, 2) . '%');
        
        // Add comment if exists
        $comment = $zip->getArchiveComment();
        $comment && ($info['comment'] = $comment);
        
        $zip->close();
        return $info;
    }


    /**
     * Extract TAR archive info
     * 
     * @param string $file_path TAR file path
     * @return array Archive info
     */
    private function extract_tar_info(string $file_path): array {
        
        $info = [];
        
        // Check if PharData class is available
        if(!class_exists('PharData')) {
            return $info;
        }
        
        try {
            $phar = new \PharData($file_path);
            
            // Count files
            $files_count = 0;
            $total_size = 0;
            $file_list = [];
            
            foreach($phar as $file) {
                $files_count++;
                $total_size += $file->getSize();
                
                // Add to file list (limit to first 100 files)
                if(count($file_list) < 100) {
                    $file_list[] = [
                        'name' => $file->getFilename(),
                        'size' => $file->getSize()
                    ];
                }
            }
            
            $info['files_count'] = $files_count;
            $info['uncompressed_size'] = $total_size;
            $info['uncompressed_size_human'] = format_file_size($total_size);
            $info['files'] = $file_list;
            
        } catch(\Exception $e) {
            // If extraction fails, return empty info
            return $info;
        }
        
        return $info;
    }


    /**
     * Extract GZ archive info
     * 
     * @param string $file_path GZ file path
     * @return array Archive info
     */
    private function extract_gz_info(string $file_path): array {
        
        $info = [];
        
        // GZ files typically contain a single file
        $info['files_count'] = 1;
        
        // Try to get original filename
        $handle = @gzopen($file_path, 'rb');
        if($handle !== false) {
            // Read uncompressed size (stored at end of file)
            fseek($handle, -4, SEEK_END);
            $data = fread($handle, 4);
            $uncompressed_size = unpack('V', $data)[1];
            
            $info['uncompressed_size'] = $uncompressed_size;
            $info['uncompressed_size_human'] = format_file_size($uncompressed_size);
            
            // Calculate compression ratio
            $compressed_size = filesize($file_path);
            if($compressed_size > 0 && $uncompressed_size > 0) {
                $ratio = (1 - ($compressed_size / $uncompressed_size)) * 100;
                $info['compression_ratio'] = round($ratio, 2) . '%';
            }
            
            gzclose($handle);
        }
        
        return $info;
    }


    /**
     * Validate if file is a valid archive
     * 
     * @param string $file_path File path
     * @return bool Is valid archive
     */
    private function is_valid_archive(string $file_path): bool {
        
        if(!file_exists($file_path) || !is_readable($file_path)) {
            return false;
        }
        
        $path_info = pathinfo($file_path);
        $extension = strtolower($path_info['extension']);
        
        $allowed_extensions = [
            'zip', '7z', 'rar', 'tar', 'gz', 'bz2', 'xz', 'tgz', 'tbz2'
        ];
        
        return in_array($extension, $allowed_extensions, true);
    }


    /**
     * Get archive info by ID
     * 
     * @param int $media_id Media ID
     * @return array|false Archive info or false
     */
    public function get_archive_info(int $media_id): array|false {
        return $this->get_media_info($media_id, 'archive');
    }


    /**
     * Process archive-specific info (overrides Media class method)
     * 
     * @param array $media Base media data
     * @return array Processed media info with archive-specific enhancements
     */
    private function process_archive_info(array $media): array {
        // Add formatted file size
        isset($media['file_size']) && ($media['file_size_formatted'] = format_file_size($media['file_size']));
        
        // Process metadata if exists
        if(!isset($media['metadata']) || !is_array($media['metadata'])) {
            return $media;
        }
        
        // Define metadata formatters
        $formatters = [
            'file_count' => fn($value) => $value . ' файлов',
            'uncompressed_size' => fn($value) => format_file_size((int)$value),
            'compression_ratio' => fn($value) => round($value, 1) . '%'
        ];
        
        // Apply formatters to existing metadata
        foreach($formatters as $key => $formatter) {
            isset($media['metadata'][$key]) && ($media['metadata'][$key . '_formatted'] = $formatter($media['metadata'][$key]));
        }
        
        return $media;
    }


    /**
     * List files in archive
     * 
     * @param int $media_id Media ID
     * @param int $limit Limit number of files to return
     * @return array|false List of files or false
     */
    public function list_archive_files(int $media_id, int $limit = 100): array|false {
        
        $media = $this->get_by_id($media_id);
        
        if(!$media || $media['media_type'] !== 'archive') {
            return false;
        }
        
        // Check if metadata contains file list
        if(isset($media['metadata']) && $media['metadata']) {
            $metadata = json_decode($media['metadata'], true);
            
            if(isset($metadata['files'])) {
                return array_slice($metadata['files'], 0, $limit);
            }
        }
        
        return [];
    }


    /**
     * Extract archive to directory
     * 
     * @param int $media_id Media ID
     * @param string $destination Destination directory
     * @return bool Success
     */
    public function extract_archive(int $media_id, string $destination): bool {
        
        $media = $this->get_by_id($media_id);
        
        if(!$media || $media['media_type'] !== 'archive') {
            return false;
        }
        
        $file_path = _UPLOAD . $media['file_path'] . '/' . $media['filename'];
        
        if(!file_exists($file_path)) {
            return false;
        }
        
        // Create destination directory if not exists
        if(!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        // Extract based on archive type
        $extension = strtolower($media['extension']);
        
        return match($extension) {
            'zip' => $this->extract_zip($file_path, $destination),
            'tar' => $this->extract_tar($file_path, $destination),
            default => false
        };
    }


    /**
     * Extract ZIP archive
     * 
     * @param string $file_path ZIP file path
     * @param string $destination Destination directory
     * @return bool Success
     */
    private function extract_zip(string $file_path, string $destination): bool {
        
        if(!class_exists('ZipArchive')) {
            return false;
        }
        
        $zip = new \ZipArchive();
        $result = $zip->open($file_path);
        
        if($result !== true) {
            return false;
        }
        
        $extracted = $zip->extractTo($destination);
        $zip->close();
        
        return $extracted;
    }


    /**
     * Extract TAR archive
     * 
     * @param string $file_path TAR file path
     * @param string $destination Destination directory
     * @return bool Success
     */
    private function extract_tar(string $file_path, string $destination): bool {
        
        if(!class_exists('PharData')) {
            return false;
        }
        
        try {
            $phar = new \PharData($file_path);
            $phar->extractTo($destination);
            return true;
            
        } catch(\Exception $e) {
            return false;
        }
    }


    /**
     * Get archives by file count
     * 
     * @param int $min_files Minimum file count
     * @param int $max_files Maximum file count
     * @return array List of archives
     */
    public function get_archives_by_file_count(int $min_files, int $max_files): array {
        
        // Get archives with JSON filtering (more efficient than PHP filtering)
        $sql = "SELECT * FROM " . TABLE_MEDIA . " 
                WHERE media_type = :media_type 
                AND metadata IS NOT NULL 
                AND JSON_EXTRACT(metadata, '$.files_count') BETWEEN :min_files AND :max_files";
        
        $archives = $this->db->fetch_all($sql, [
            'media_type' => 'archive',
            'min_files' => $min_files,
            'max_files' => $max_files
        ]);
        
        // Decode metadata for results
        return array_map(fn($archive) => [
            ...$archive,
            'metadata' => json_decode($archive['metadata'], true) ?: []
        ], $archives);
    }


    /**
     * Check if archive can be extracted
     * 
     * @param int $media_id Media ID
     * @return bool Can be extracted
     */
    public function can_extract_archive(int $media_id): bool {
        
        $media = $this->get_by_id($media_id);
        
        if(!$media || $media['media_type'] !== 'archive') {
            return false;
        }
        
        $extension = strtolower($media['extension']);
        
        return match($extension) {
            'zip' => class_exists('ZipArchive'),
            'tar' => class_exists('PharData'),
            default => false
        };
    }


    /**
     * Abstract methods
     */
    abstract public function get_by_id(int $id): array|false;
    abstract public function get_media_info(int $media_id, ?string $expected_type = null): array|false;
}