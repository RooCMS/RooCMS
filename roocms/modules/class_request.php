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

    public $get;
    public $post;
    public $files;


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
     * 
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
}