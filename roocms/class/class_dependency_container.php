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
//	Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	http_response_code(403);
	header('Content-Type: text/plain; charset=utf-8');
	exit('403:Access denied');
}
//#########################################################


/**
 * Simple Dependency Injection Container
 * Manages service registration and automatic dependency resolution
 */
class DependencyContainer {

    private array $services = [];
    private array $singletons = [];
    private array $instances = [];


    /**
     * Register a service with its implementation
     *
     * @param string $interface Interface or service name
     * @param string|callable $implementation Class name or factory function
     * @param bool $singleton Whether to create singleton instance
     */
    public function register(string $interface, string|callable $implementation, bool $singleton = false): void {
        $this->services[$interface] = $implementation;
        $this->singletons[$interface] = $singleton;
    }


    /**
     * Get service instance, resolving dependencies automatically
     *
     * @param string $interface Interface or service name
     * @return object Service instance
     * @throws Exception If service is not registered or cannot be resolved
     */
    public function get(string $interface): object {
        // Return singleton instance if exists
        if ($this->singletons[$interface] ?? false) {
            if (isset($this->instances[$interface])) {
                return $this->instances[$interface];
            }
        }

        // Check if service is registered
        if (!isset($this->services[$interface])) {
            throw new Exception("Service '{$interface}' is not registered");
        }

        $implementation = $this->services[$interface];
        $instance = $this->resolve($implementation);

        // Store singleton instance
        if ($this->singletons[$interface] ?? false) {
            $this->instances[$interface] = $instance;
        }

        return $instance;
    }


    /**
     * Check if service is registered
     *
     * @param string $interface Interface or service name
     * @return bool
     */
    public function has(string $interface): bool {
        return isset($this->services[$interface]);
    }


    /**
     * Resolve service implementation
     *
     * @param string|callable $implementation
     * @return object
     * @throws Exception
     */
    private function resolve(string|callable $implementation): object {
        if (is_callable($implementation)) {
            return $implementation($this);
        }

        // Get constructor parameters via reflection
        $reflection = new ReflectionClass($implementation);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            // No constructor, create instance directly
            return new $implementation();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (!$type instanceof ReflectionNamedType) {
                // Parameter without type hint, try to use default value
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Cannot resolve parameter '{$parameter->getName()}' for class '{$implementation}' - no type hint or default value");
                }
                continue;
            }

            $type_name = $type->getName();

            // Skip built-in types and allow null values for nullable parameters
            if ($type->isBuiltin() || ($type->allowsNull() && !$parameter->isDefaultValueAvailable())) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } elseif ($type->allowsNull()) {
                    $dependencies[] = null;
                } else {
                    throw new Exception("Cannot resolve built-in parameter '{$parameter->getName()}' for class '{$implementation}' - no default value");
                }
                continue;
            }

            // Try to resolve dependency from container
            try {
                $dependencies[] = $this->get($type_name);
            } catch (Exception $e) {
                // If dependency cannot be resolved and parameter allows null, use null
                if ($type->allowsNull()) {
                    $dependencies[] = null;
                } else {
                    throw new Exception("Cannot resolve dependency '{$type_name}' for class '{$implementation}': " . $e->getMessage());
                }
            }
        }

        return new $implementation(...$dependencies);
    }


    /**
     * Set a specific instance (useful for testing or custom initialization)
     *
     * @param string $interface Interface or service name
     * @param object $instance Service instance
     * @param bool $singleton Whether to treat as singleton
     */
    public function set_instance(string $interface, object $instance, bool $singleton = false): void {
        $this->instances[$interface] = $instance;
        $this->singletons[$interface] = $singleton;
    }



    /**
     * Get all registered services
     *
     * @return array List of registered service names
     */
    public function get_registered_services(): array {
        return array_keys($this->services);
    }


    /**
     * Clear all instances and singletons (useful for testing)
     */
    public function clear(): void {
        $this->instances = [];
    }
}
