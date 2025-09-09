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
 * Base controller class for all API controllers
 * Provides common functionality for JSON responses, validation, and database access
 */
abstract class BaseController {
    
    protected array|null $current_user = null;    
    protected readonly Db|null $db;



    /**
     * Constructor with dependency injection
     */
    public function __construct(Db|null $db = null) {
        $this->db = $db;
    }
    

    /**
     * Send successful JSON response
     */
    protected function json_response(mixed $data = null, int $code = 200, string $message = ''): void {
        http_response_code($code);
        
        $response = [
            'success' => true,
            'timestamp' => format_timestamp(time())
        ];
        
        if ($message !== '') {
            $response['message'] = $message;
        }
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        // output response
        output_json($response);
    }
    

    /**
     * Send error JSON response
     */
    protected function error_response(string $message, int $code = 400, array $details = []): void {
        http_response_code($code);
        
        $response = [
            'error' => true,
            'message' => $message,
            'status_code' => $code,
            'timestamp' => format_timestamp(time())
        ];
        
        if (!empty($details)) {
            $response['details'] = $details;
        }
        
        // output response
        output_json($response);
    }
    

    /**
     * Send created response (201)
     */
    protected function created_response(mixed $data, string $message = 'Resource created successfully'): void {
        $this->json_response($data, 201, $message);
    }
    

    /**
     * Send not found response (404)
     */
    protected function not_found_response(string $message = 'Resource not found'): void {
        $this->error_response($message, 404);
    }
    

    /**
     * Send validation error response (422)
     */
    protected function validation_error_response(array $errors, string $message = 'Validation failed'): void {
        $this->error_response($message, 422, ['validation_errors' => $errors]);
    }
    

    /**
     * Get request input data (JSON or form data) with security validation
     */
    protected function get_input_data(): array {
        $contentType = env('CONTENT_TYPE') ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            // Safely read and decode JSON input
            $input = file_get_contents('php://input');

            if ($input === false) {
                return [];
            }

            $data = safe_json_decode($input, 1048576); // 1MB limit

            if ($data === null) {
                // Log invalid JSON attempt if in debug mode
                if (DEBUGMODE && $this->db && defined('SYSERRLOG')) {
                    error_log('Invalid JSON input received: ' . substr($input, 0, 200) . '\r\n', 3, SYSERRLOG);
                }
                return [];
            }

            // Sanitize the decoded data
            return sanitize_input_data($data);
        }

        // Handle form data with sanitization
        return sanitize_input_data($_POST);
    }
    

    /**
     * Get query parameters with sanitization and optional type casting
     */
    protected function get_query_params(bool $auto_cast_types = true): array {
        $params = [];

        foreach ($_GET as $key => $value) {
            if ($value !== '' && $value !== null) {
                $sanitized_value = sanitize_input_data($value);

                if ($auto_cast_types) {
                    $params[$key] = $this->cast_param_type($sanitized_value);
                } else {
                    $params[$key] = $sanitized_value;
                }
            }
        }

        return $params;
    }


    /**
     * Cast query parameter to appropriate type
     */
    private function cast_param_type(string $value): mixed {
        // Skip casting if value looks like JSON
        if (is_json_string($value)) {
            $decoded = json_decode($value, true);
            return $decoded !== null ? $decoded : $value;
        }

        // Cast numeric values
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float) $value : (int) $value;
        }

        // Cast boolean values
        if (strtolower($value) === 'true') {
            return true;
        }
        if (strtolower($value) === 'false') {
            return false;
        }

        // Cast null values
        if (strtolower($value) === 'null') {
            return null;
        }

        return $value;
    }


    /**
     * Validate required fields
     */
    protected function validate_required(array $data, array $required): array {
        $errors = [];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $errors[$field] = "Field '{$field}' is required";
            }
        }
        
        return $errors;
    }
    
    
    /**
     * Get pagination parameters
     */
    protected function get_pagination_params(): array {
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        
        // Ensure minimum values
        $page = max(1, $page);
        $limit = max(1, min(100, $limit)); // Max 100 items per page
        
        $offset = ($page - 1) * $limit;
        
        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => $offset
        ];
    }

    
    /**
     * Format pagination meta information
     */
    protected function format_pagination_meta(int $total, int $page, int $limit): array {
        $total_pages = (int)ceil($total / $limit);
        
        return [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'total_pages' => $total_pages,
            'has_next' => $page < $total_pages,
            'has_prev' => $page > 1
        ];
    }

    
    /**
     * Log API request for debugging
     */
    protected function log_request(string $action = '', array $data = []): void {
        if ($this->db && defined('SYSERRLOG') && DEBUGMODE) {
            // Sanitize server data to prevent XSS and log injection
            $log_data = [
                'action' => sanitize_log($action),
                'method' => sanitize_log(env('REQUEST_METHOD') ?? 'UNKNOWN'),
                'uri' => sanitize_log(env('REQUEST_URI') ?? ''),
                'ip' => sanitize_log(env('REMOTE_ADDR') ?? ''),
                'timestamp' => date('Y-m-d H:i:s'),
                'data' => sanitize_input_data($data)
            ];

            error_log('API Request: ' . json_encode($log_data) . '\r\n', 3, SYSERRLOG);
        }
    }


    /**
     * Check if database is available
     */
    protected function is_database_available(): bool {
        return $this->db !== null && $this->db instanceof Db;
    }
    

    /**
     * Get database health status
     */
    protected function get_database_health(): array {
        if (!$this->is_database_available()) {
            return [
                'status' => 'error',
                'message' => 'Database connection not available'
            ];
        }
        
        try {
            // Try to get database health status if method exists
            if (method_exists($this->db, 'get_health_status')) {
                return $this->db->get_health_status();
            }
            
            // Fallback: simple connection test
            $this->db->query("SELECT 1");
            
            return [
                'status' => 'ok',
                'message' => 'Database connection OK',
                'queries_count' => $this->db->query_count ?? 0
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }


    /**
     * Get authenticated user from global context (set by AuthMiddleware)
     */
    protected function get_authenticated_user(): array|null {
        return $GLOBALS['authenticated_user'] ?? null;
    }


    /**
     * Require authenticated user or send error response
     */
    protected function require_authentication(): array|null {
        $user = $this->get_authenticated_user();
        
        if (empty($user)) {
            $this->error_response('Authentication required', 401);
            return null;
        }
        
        return $user;
    }


    /**
     * Check if current user has specific permission
     * This is a placeholder for future permission system
     */
    protected function check_permission(string $permission): bool {
        $user = $this->get_authenticated_user();
        
        if (empty($user)) {
            return false;
        }
        
        // TODO: Implement permission checking logic
        // For now, just return true for authenticated users
        return true;
    }
}
