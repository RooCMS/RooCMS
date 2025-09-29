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
 * Health Check Controller
 * Provides system health status and diagnostics
 */
class HealthController extends BaseController {
    
    /**
     * Get system health status
     * GET /api/v1/health
     */
    public function index(): void {
        $this->log_request('health_check');
        
        $checks = [
            'api' => $this->check_api_health(),
            'database' => $this->get_database_health()
        ];
        
        $overall_status = $this->determine_overall_status($checks);
        
        $response = [
            'status' => $overall_status,
            'checks' => $checks,
            'system_info' => $this->get_system_info()
        ];
        
        // Set appropriate HTTP status code
        $httpCode = $overall_status === 'healthy' ? 200 : 503;
        
        $this->json_response($response, $httpCode);
    }
    

    /**
     * Get detailed system information
     * GET /api/v1/health/details
     */
    public function details(): void {
        $this->log_request('health_details');

        $response = [
            'api check' => $this->check_api_health(),
            'database check' => $this->get_database_health(),
            'system_info' => $this->get_detailed_system_info(),
            'php_info' => $this->get_php_info(),
            'roocms_info' => $this->get_roocms_info()
        ];

        $this->json_response($response);
    }
    

    /**
     * Check API health
     * 
     * @return array
     */
    private function check_api_health(): array {
        try {
            return [
                'status' => 'ok',
                'message' => 'API is responding normally',
                'response_time' => round(microtime(true) - (env('REQUEST_TIME_FLOAT') ?? microtime(true)), 4)
            ];
            
        } catch (Throwable $e) {
            return [
                'status' => 'error',
                'message' => 'API health check failed: ' . $e->getMessage()
            ];
        }
    }
      

    /**
     * Determine overall system status
     * 
     * @param array $checks Checks
     * @return string
     */
    private function determine_overall_status(array $checks): string {
        foreach ($checks as $check) {
            if (is_array($check) && isset($check['status'])) {
                if ($check['status'] === 'error') {
                    return 'unhealthy';
                }
            }
        }
        
        return 'healthy';
    }
    

    /**
     * Get basic system information
     * 
     * @return array
     */
    private function get_system_info(): array {
        return [
            'timestamp' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get()
        ];
    }
    

    /**
     * Get detailed system information
     * 
     * @return array
     */
    private function get_detailed_system_info(): array {
        return array_merge($this->get_system_info(), [
            'memory_usage' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit')
            ]
        ]);
    }
    

    /**
     * Get PHP configuration info
     * 
     * @return array
     */
    private function get_php_info(): array {
        return [
            'version' => PHP_VERSION,
            'configuration' => [
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'post_max_size' => ini_get('post_max_size'),
                'upload_max_filesize' => ini_get('upload_max_filesize')
            ]
        ];
    }
    

    /**
     * Get RooCMS specific information
     * 
     * @return array
     */
    private function get_roocms_info(): array {
        $info = [
            'version' => defined('ROOCMS_FULL_VERSION') ? ROOCMS_FULL_VERSION : 'Unknown',
            'major_version' => defined('ROOCMS_MAJOR_VERSION') ? ROOCMS_MAJOR_VERSION : 'Unknown',
            'minor_version' => defined('ROOCMS_MINOR_VERSION') ? ROOCMS_MINOR_VERSION : 'Unknown',
            'release_version' => defined('ROOCMS_RELEASE_VERSION') ? ROOCMS_RELEASE_VERSION : 'Unknown',
            'build' => defined('ROOCMS_BUILD_VERSION') ? ROOCMS_BUILD_VERSION : 'Unknown'
        ];
        
        return $info;
    }
}
