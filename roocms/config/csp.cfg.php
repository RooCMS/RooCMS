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



function get_csp_header(): string {
    $csp = [
        "default-src" => "'self'",
        "script-src" => "'self'",
        "style-src" => "'self' 'unsafe-inline'",
        "img-src" => "'self' data: https:",
        "font-src" => "'self'",
        "connect-src" => "'self'",
        "frame-src" => "'none'",
        "object-src" => "'none'",
        "base-uri" => "'self'",
        "form-action" => "'self'",
        "frame-ancestors" => "'none'",
        "upgrade-insecure-requests" => "",
        "report-uri" => "/api/v1/csp-report"
    ];
    
    // In development mode, add unsafe-eval for debugging
    if (defined('DEBUGMODE') && DEBUGMODE) {
        $csp["script-src"] .= " 'unsafe-eval'";
    }
    
    $directives = [];
    foreach ($csp as $directive => $value) {
        if (!empty($value)) {
            $directives[] = $directive . ' ' . $value;
        }
    }
    
    return implode('; ', $directives);
}

// Setting the CSP header
function set_csp_header(): void {
    if (!headers_sent()) {
        header('Content-Security-Policy: ' . get_csp_header());
    }
}