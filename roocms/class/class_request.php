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
 * Request Class
 * Provides utilities for working with requests
 */
class Request {

    private $get;
    private $post;
    private $files;


    /**
     * Constructor
     */
    public function __construct() {
        
        // Sanitize the input data for GET, POST and FILES
        $this->get = sanitize_input_data($_GET);
        $this->post = sanitize_input_data($_POST);
        $this->files = $this->sanitize_files($_FILES);
    }

    
    /**
     * Sanitize the files
     * @param array $files
     * @return array
     */
    private function sanitize_files(array $files): array {
        $clean = [];
        foreach ($files as $key => $file) {
            if (is_array($file['name'])) {
                // Multiple files
                foreach ($file['name'] as $index => $name) {
                    $clean[$key][$index] = [
                        'name' => sanitize_filename($name),
                        'type' => $file['type'][$index],
                        'tmp_name' => $file['tmp_name'][$index],
                        'error' => $file['error'][$index],
                        'size' => $file['size'][$index]
                    ];
                }
            } else {
                // Single file
                $clean[$key] = [
                    'name' => sanitize_filename($file['name']),
                    'type' => $file['type'],
                    'tmp_name' => $file['tmp_name'],
                    'error' => $file['error'],
                    'size' => $file['size']
                ];
            }
        }
        return $clean;
    }
    

    /**
     * Get the value of the GET parameter
     * @param ?string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(?string $key = null, $default = null) {
        if ($key === null) return $this->get;
        return $this->get[$key] ?? $default;
    }
    

    /**
     * Get the value of the POST parameter
     * @param ?string $key
     * @param mixed $default
     * @return mixed
     */
    public function post(?string $key = null, $default = null) {
        if ($key === null) return $this->post;
        return $this->post[$key] ?? $default;
    }
    

    /**
     * Get the value of the FILE parameter
     * @param ?string $key
     * @param mixed $default
     * @return mixed
     */
    public function file(?string $key = null) {
        if ($key === null) return $this->files;
        return $this->files[$key] ?? null;
    }
    

    /**
     * Check if the GET parameter exists
     * @param string $key
     * @return bool
     */
    public function has_get(string $key): bool {
        return isset($this->get[$key]);
    }

    
    /**
     * Check if the POST parameter exists
     * @param string $key
     * @return bool
     */
    public function has_post(string $key): bool {
        return isset($this->post[$key]);
    }


    /**
     * Check if the FILE parameter exists
     * @param string $key
     * @return bool
     */
    public function has_file(string $key): bool {
        return isset($this->files[$key]);
    }
}