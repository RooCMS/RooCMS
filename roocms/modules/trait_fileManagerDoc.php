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
 * Trait for operations File Documents
 * Handles PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ODT, ODS, ODP, TXT, RTF files
 */
trait FileManagerDoc {

    /**
     * Process uploaded document
	 * Extracts metadata from document
     * 
     * @param int $media_id Media ID
     * @param string $file_path Full path to uploaded file
     * @return bool Success
     */
    protected function process_document(int $media_id, string $file_path): bool {
        
        try {
            // Validate document
            if(!$this->is_valid_document($file_path)) {
                return false;
            }
            
            // Get document info
            $doc_info = pathinfo($file_path);
            $extension = strtolower($doc_info['extension']);
            
            // Extract metadata based on document type
            $metadata = $this->extract_document_metadata($file_path, $extension);
            
            // Update media metadata
            if(!empty($metadata)) {
                $this->db->update(TABLE_MEDIA)
                    ->data([
                        'metadata' => json_encode($metadata),
                        'updated_at' => time()
                    ])
                    ->where('id', $media_id, '=')
                    ->execute();
            }
            
            return true;
            
        } catch(\Exception $e) {
            return false;
        }
    }


    /**
     * Extract document metadata
     * 
     * @param string $file_path File path
     * @param string $extension File extension
     * @return array Metadata
     */
    private function extract_document_metadata(string $file_path, string $extension): array {
        
        $metadata = [
            'extension' => $extension,
            'readable' => is_readable($file_path),
            'size_human' => format_file_size(filesize($file_path))
        ];
        
        // Extract PDF metadata
        if($extension === 'pdf') {
            $pdf_meta = $this->extract_pdf_metadata($file_path);
            $metadata = array_merge($metadata, $pdf_meta);
        }
        
        // Extract text file metadata
        if($extension === 'txt') {
            $txt_meta = $this->extract_text_metadata($file_path);
            $metadata = array_merge($metadata, $txt_meta);
        }
        
        return $metadata;
    }


    /**
     * Extract PDF metadata
     * 
     * @param string $file_path PDF file path
     * @return array Metadata
     */
    private function extract_pdf_metadata(string $file_path): array {
        
        $metadata = [];
        
        // Try to read PDF content
        $content = @file_get_contents($file_path);
        if($content === false) {
            return $metadata;
        }
        
        // Extract page count (simple method)
        if(preg_match("/\/N\s+(\d+)/", $content, $matches)) {
            $metadata['pages'] = (int)$matches[1];
        } elseif(preg_match_all("/\/Page\W/", $content, $matches)) {
            $metadata['pages'] = count($matches[0]);
        }
        
        // Extract title
        if(preg_match("/\/Title\s*\(([^)]+)\)/", $content, $matches)) {
            $metadata['title'] = trim($matches[1]);
        }
        
        // Extract author
        if(preg_match("/\/Author\s*\(([^)]+)\)/", $content, $matches)) {
            $metadata['author'] = trim($matches[1]);
        }
        
        // Extract creation date
        if(preg_match("/\/CreationDate\s*\(([^)]+)\)/", $content, $matches)) {
            $metadata['creation_date'] = trim($matches[1]);
        }
        
        return $metadata;
    }


    /**
     * Extract text file metadata
     * 
     * @param string $file_path Text file path
     * @return array Metadata
     */
    private function extract_text_metadata(string $file_path): array {
        
        $metadata = [];
        
        // Count lines
        $line_count = 0;
        $handle = @fopen($file_path, 'r');
        if($handle !== false) {
            while(!feof($handle)) {
                fgets($handle);
                $line_count++;
            }
            fclose($handle);
            $metadata['lines'] = $line_count;
        }
        
        // Detect encoding
        $content = @file_get_contents($file_path, false, null, 0, 4096);
        if($content !== false) {
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ASCII', 'ISO-8859-1', 'Windows-1251'], true);
            if($encoding) {
                $metadata['encoding'] = $encoding;
            }
            
            // Count words (approximate)
            $word_count = str_word_count($content);
            $metadata['words'] = $word_count;
        }
        
        return $metadata;
    }


    /**
     * Validate if file is a valid document
     * 
     * @param string $file_path File path
     * @return bool Is valid document
     */
    private function is_valid_document(string $file_path): bool {
        
        if(!file_exists($file_path) || !is_readable($file_path)) {
            return false;
        }
        
        $path_info = pathinfo($file_path);
        $extension = strtolower($path_info['extension']);
        
        $allowed_extensions = [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'odt', 'ods', 'odp', 'txt', 'rtf'
        ];
        
        return in_array($extension, $allowed_extensions, true);
    }


    /**
     * Get document info by ID
     * 
     * @param int $media_id Media ID
     * @return array|false Document info or false
     */
    public function get_document_info(int $media_id): array|false {
        return $this->get_media_info($media_id, 'document');
    }


    /**
     * Process document-specific info (overrides Media class method)
     * 
     * @param array $media Base media data
     * @return array Processed media info with document-specific enhancements
     */
    private function process_document_info(array $media): array {
        // Add document-specific processing
        // This method is called by get_media_info() via match()
        
        // Add formatted file size for convenience
        if(isset($media['file_size'])) {
            $media['file_size_formatted'] = format_file_size($media['file_size']);
        }
        
        // Add document-specific metadata processing if needed
        if(isset($media['metadata']) && is_array($media['metadata'])) {
            // Add human-readable page count, etc.
            if(isset($media['metadata']['pages'])) {
                $media['metadata']['pages_formatted'] = $media['metadata']['pages'] . ' страниц';
            }
        }
        
        return $media;
    }


    /**
     * Check if document is text-based and readable
     * 
     * @param int $media_id Media ID
     * @return bool Is text document
     */
    public function is_text_document(int $media_id): bool {
        
        $media = $this->get_by_id($media_id);
        
        if(!$media) {
            return false;
        }
        
        $text_extensions = ['txt', 'rtf', 'md', 'csv'];
        return in_array($media['extension'], $text_extensions, true);
    }


    /**
     * Get document content (for text files only)
     * 
     * @param int $media_id Media ID
     * @param int $max_length Maximum length to read (default: 10000 chars)
     * @return string|false Document content or false
     */
    public function get_document_content(int $media_id, int $max_length = 10000): string|false {
        
        $media = $this->get_by_id($media_id);
        
        if(!$media || !$this->is_text_document($media_id)) {
            return false;
        }
        
        $file_path = _UPLOAD . $media['file_path'] . '/' . $media['filename'];
        
        if(!file_exists($file_path) || !is_readable($file_path)) {
            return false;
        }
        
        $content = @file_get_contents($file_path, false, null, 0, $max_length);
        
        return $content !== false ? $content : false;
    }


    /**
     * Search documents by content (for text files)
     * 
     * @param string $search_term Search term
     * @return array List of matching documents
     */
    public function search_documents_by_content(string $search_term): array {
        
        // Get all text documents
        $sql = "SELECT * FROM " . TABLE_MEDIA . " WHERE media_type = :media_type";
        $documents = $this->db->fetch_all($sql, ['media_type' => 'document']);
        
        $results = [];
        
        foreach($documents as $doc) {
            // Only search in text files
            if(!in_array($doc['extension'], ['txt', 'csv'], true)) {
                continue;
            }
            
            $file_path = _UPLOAD . $doc['file_path'] . '/' . $doc['filename'];
            
            if(!file_exists($file_path)) {
                continue;
            }
            
            // Read file content
            $content = @file_get_contents($file_path);
            
            if($content === false) {
                continue;
            }
            
            // Search for term (case-insensitive)
            if(stripos($content, $search_term) !== false) {
                $results[] = $doc;
            }
        }
        
        return $results;
    }

    /**
     * Abstract methods
     */
    abstract public function get_by_id(int $id): array|false;
    abstract public function get_media_info(int $media_id, ?string $expected_type = null): array|false;
}