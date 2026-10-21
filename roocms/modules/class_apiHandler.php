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
        // Support Middleware@method syntax, default method is handle()
        [$middlewareClass, $method] = str_contains($middlewareSpec, '@')
            ? explode('@', $middlewareSpec, 2)
            : [$middlewareSpec, 'handle'];

        try {
            $middleware = $this->middlewareFactory->create($middlewareClass);
        } catch (Throwable $e) {
            $this->handle_error('Failed to create middleware '.$middlewareClass, $e);
            return false;
        }

        // A missing method must never let the request through
        if (!method_exists($middleware, $method)) {
            $this->handle_error('Method '.$method.' not found in middleware '.$middlewareClass);
            return false;
        }

        try {
            return (bool)$middleware->$method();
        } catch (Throwable $e) {
            $this->handle_error('Middleware '.$middlewareClass.' failed', $e);
            return false;
        }
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
            if (str_contains($handler, '@')) {
                [$controllerClass, $method] = explode('@', $handler, 2);

                try {
                    $controller = $this->controllerFactory->create($controllerClass);
                } catch (Throwable $e) {
                    $this->handle_error('Failed to create controller '.$controllerClass, $e);
                    return false;
                }

                if (!method_exists($controller, $method)) {
                    $this->handle_error('Method '.$method.' not found in controller '.$controllerClass);
                    return false;
                }

                // Errors raised while the controller works are not creation errors
                try {
                    return call_user_func_array([$controller, $method], $params);
                } catch (Throwable $e) {
                    $this->handle_error('Controller '.$controllerClass.'@'.$method.' failed', $e);
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
     * Handle general errors (500)
     * Cause is logged; details go to the client only in debug mode, they may contain SQL or paths
     * 
     * @param string $message Message
     * @param Throwable|null $e Cause
     * @return void
    */
    private function handle_error(string $message, ?Throwable $e = null): void {
        $details = $message.($e ? ': '.$e->getMessage() : '');
        error_log('ApiHandler: '.$details);
        http_response_code(500);
        
        $response = [
            'error' => true,
            'message' => (defined('DEBUGMODE') && DEBUGMODE) ? $details : 'Internal server error',
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
