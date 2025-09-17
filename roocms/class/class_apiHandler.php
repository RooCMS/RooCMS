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
 * API Handler class for handling HTTP routes
 * Supports RESTful routing with dynamic parameters
 */
class ApiHandler {

    private array $routes = [];

    private ControllerFactory $controllerFactory;
    private MiddlewareFactory $middlewareFactory;



    /**
     * Constructor
     *
     * @param ControllerFactory $controllerFactory Factory for creating controller instances
     * @param MiddlewareFactory $middlewareFactory Factory for creating middleware instances
     */
    public function __construct(ControllerFactory $controllerFactory, MiddlewareFactory $middlewareFactory) {
        $this->controllerFactory = $controllerFactory;
        $this->middlewareFactory = $middlewareFactory;
    }


    /**
     * Add GET route
     * 
     * @param string $pattern Pattern
     * @param callable|string $handler Handler
     * @param array $middleware Middleware
     */
    public function get(string $pattern, callable|string $handler, array $middleware = []): void {
        $this->add_route('GET', $pattern, $handler, $middleware);
    }


    /**
     * Add POST route
     * 
     * @param string $pattern Pattern
     * @param callable|string $handler Handler
     * @param array $middleware Middleware
     */
    public function post(string $pattern, callable|string $handler, array $middleware = []): void {
        $this->add_route('POST', $pattern, $handler, $middleware);
    }


    /**
     * Add PUT route
     * 
     * @param string $pattern Pattern
     * @param callable|string $handler Handler
     * @param array $middleware Middleware
     */
    public function put(string $pattern, callable|string $handler, array $middleware = []): void {
        $this->add_route('PUT', $pattern, $handler, $middleware);
    }


    /**
     * Add DELETE route
     * 
     * @param string $pattern Pattern
     * @param callable|string $handler Handler
     * @param array $middleware Middleware
     */
    public function delete(string $pattern, callable|string $handler, array $middleware = []): void {
        $this->add_route('DELETE', $pattern, $handler, $middleware);
    }


    /**
     * Add PATCH route
     * 
     * @param string $pattern Pattern
     * @param callable|string $handler Handler
     * @param array $middleware Middleware
     */
    public function patch(string $pattern, callable|string $handler, array $middleware = []): void {
        $this->add_route('PATCH', $pattern, $handler, $middleware);
    }


    /**
     * Add route to routing table
     * 
     * @param string $method Method
     * @param string $pattern Pattern
     * @param callable|string $handler Handler
     * @param array $middleware Middleware
     */
    private function add_route(string $method, string $pattern, callable|string $handler, array $middleware = []): void {
        // Convert pattern to regex
        $regex = $this->convert_pattern_to_regex($pattern);
        
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'regex' => $regex,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }


    /**
     * Convert URL pattern to regex
     * 
     * @param string $pattern Pattern
     * @return string
     */
    private function convert_pattern_to_regex(string $pattern): string {
        // Escape forward slashes
        $regex = str_replace('/', '\/', $pattern);
        
        // Replace {id} and any {*_id} with numeric pattern
        $regex = preg_replace('/\{id\}/', '(\d+)', $regex);
        $regex = preg_replace('/\{[A-Za-z0-9_]*_id\}/', '(\d+)', $regex);
        
        // Replace {param} with regex pattern for alphanumeric parameters
        $regex = preg_replace('/\{([^}]+)\}/', '([^\/]+)', $regex);
        
        return '/^' . $regex . '$/';
    }


    /**
     * Dispatch request to appropriate handler
     * 
     * @param string $method Method
     * @param string $uri URI
     * @return mixed
     */
    public function dispatch(string $method, string $uri): mixed {
        // Remove query string from URI
        $uri = strtok($uri, '?');

        // Remove trailing slash except for root
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($route['regex'], $uri, $matches)) {
                // Remove full match from parameters
                array_shift($matches);
                
                // Execute middleware first
                foreach ($route['middleware'] as $middlewareClass) {
                    if (!$this->execute_middleware($middlewareClass)) {
                        return false;
                    }
                }
                
                // Execute handler
                return $this->execute_handler($route['handler'], $matches);
            }
        }
        
        // No route found
        $this->handle_not_found();
        return false;
    }


    /**
     * Execute middleware
     * Supports both 'Middleware' and 'Middleware@method' syntax
     * 
     * @param string $middlewareSpec Middleware specification
     * @return bool
     */
    private function execute_middleware(string $middlewareSpec): bool {
        // Support Middleware@method syntax
        if (strpos($middlewareSpec, '@') !== false) {
            list($middlewareClass, $method) = explode('@', $middlewareSpec, 2);

            try {
                $middleware = $this->middlewareFactory->create($middlewareClass);
                if (method_exists($middleware, $method)) {
                    return $middleware->$method();
                }
            } catch (Exception $e) {
                $this->handle_error('Failed to create middleware '.$middlewareClass.': '.$e->getMessage());
                return false;
            }
        } else {
            // Traditional middleware syntax - look for handle() method
            try {
                $middleware = $this->middlewareFactory->create($middlewareSpec);
                if (method_exists($middleware, 'handle')) {
                    return $middleware->handle();
                }
            } catch (Exception $e) {
                $this->handle_error('Failed to create middleware '.$middlewareSpec.': '.$e->getMessage());
                return false;
            }
        }

        return true;
    }


    /**
     * Execute route handler
     * 
     * @param callable|string $handler Handler
     * @param array $params Parameters
     * @return mixed
     */
    private function execute_handler(callable|string $handler, array $params = []): mixed {
        if (is_string($handler)) {
            // Handle Controller@method syntax
            if (strpos($handler, '@') !== false) {
                list($controllerClass, $method) = explode('@', $handler, 2);

                try {
                    $controller = $this->controllerFactory->create($controllerClass);

                    if (method_exists($controller, $method)) {
                        return call_user_func_array([$controller, $method], $params);
                    } else {
                        $this->handle_error('Method '.$method.' not found in controller '.$controllerClass);
                        return false;
                    }
                } catch (Exception $e) {
                    $this->handle_error('Failed to create controller '.$controllerClass.': '.$e->getMessage());
                    return false;
                }
            }
        } elseif (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
        
        $this->handle_error('Invalid handler provided');
        return false;
    }


    /**
     * Handle 404 Not Found
     * 
     * @return void
     */
    public function handle_not_found(): void {
        http_response_code(404);
        
        $response = [
            'error' => true,
            'message' => 'Endpoint not found',
            'status_code' => 404,
            'timestamp' => format_timestamp(time())
        ];
        
        output_json($response);
    }


    /**
     * Handle 405 Method Not Allowed
     * 
     * @param array $allowedMethods Allowed methods
     * @return void
    */
    public function handle_method_not_allowed(array $allowedMethods = []): void {
        http_response_code(405);
        
        if (!empty($allowedMethods)) {
            header('Allow: ' . implode(', ', $allowedMethods));
        }
        
        $response = [
            'error' => true,
            'message' => 'Method not allowed',
            'status_code' => 405,
            'allowed_methods' => $allowedMethods,
            'timestamp' => format_timestamp(time())
        ];
        
        output_json($response);
    }


    /**
     * Handle general errors
     * 
     * @param string $message Message
     * @return void
    */
    private function handle_error(string $message): void {
        http_response_code(500);
        
        $response = [
            'error' => true,
            'message' => $message,
            'status_code' => 500,
            'timestamp' => format_timestamp(time())
        ];
        
        output_json($response);
    }


    /**
     * Get all registered routes (for debugging)
     * 
     * @return array
    */
    public function get_routes(): array {
        return $this->routes;
    }


    /**
     * Check if route exists for given method and URI
     * 
     * @param string $method Method
     * @param string $uri URI
     * @return bool
     */
    public function route_exists(string $method, string $uri): bool {
        $uri = strtok($uri, '?');
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['regex'], $uri)) {
                return true;
            }
        }
        
        return false;
    }
}
