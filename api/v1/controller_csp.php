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
//	Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}
//#########################################################


/**
 * CSP Controller
 * Handles CSP violation reports
 */
class CspController extends BaseController {
    
    /**
     * Report CSP violation
     * POST /api/v1/csp/report
     * 
     * @return void
     */
    public function report(): void {
        // Try to read input directly for CSP reports
        $raw_input = read_input_stream('php://input');

        if (!empty($raw_input)) {
            // Try to decode JSON
            $input = json_decode($raw_input, true);
            if ($input === null) {
                // Try safe_json_decode as fallback
                $input = safe_json_decode($raw_input, 1048576);
                if ($input === null) {
                    $this->error_response('Invalid JSON in CSP report', 400);
                    return;
                }
            }
            $input = sanitize_input_data($input);
        } else {
            $input = $this->get_input_data();
        }

        if (!isset($input['csp-report'])) {
            $this->error_response('Invalid CSP report', 400);
            return;
        }

        // Log the violation
        $this->log_csp_violation($input['csp-report']);

        // Return 200 OK without data
        $this->json_response(['status' => 'reported'], 200);
    }
    

    /**
     * Log CSP violation
     * 
     * @param array $report CSP violation report
     * @return void
     */
    private function log_csp_violation(array $report): void {

        $ip = filter_var(env('REMOTE_ADDR'), FILTER_VALIDATE_IP) ?? '';
        $uri = sanitize_log(env('REQUEST_URI') ?? '');
        
        $log_entry = json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'violation' => $report,
            'ip' => $ip,
            'uri' => $uri
        ], JSON_UNESCAPED_UNICODE);
        
        if (defined('SYSERRLOG')) {
            error_log('[CSP VIOLATION] ' . $log_entry . PHP_EOL, 3, SYSERRLOG);
        }
    }
}