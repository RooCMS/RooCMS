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


class CspController extends BaseController {
    
    public function report(): void {
        $input = $this->get_input_data();
        
        if (!isset($input['csp-report'])) {
            $this->error_response('Invalid CSP report', 400);
            return;
        }
        
        // Log the violation
        $this->log_csp_violation($input['csp-report']);
        
        // Return 200 OK without data
        $this->json_response(['status' => 'reported'], 200);
    }
    
    private function log_csp_violation(array $report): void {
        $log_entry = json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'violation' => $report,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'uri' => $_SERVER['REQUEST_URI'] ?? ''
        ], JSON_UNESCAPED_UNICODE);
        
        if (defined('SYSERRLOG')) {
            error_log('[CSP VIOLATION] ' . $log_entry . PHP_EOL, 3, SYSERRLOG);
        }
    }
}