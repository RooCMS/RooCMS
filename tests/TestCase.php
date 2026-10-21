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


/**
 * Thrown when an assertion fails
 */
class AssertionFailed extends Exception {}


/**
 * Thrown to mark a test as skipped (e.g. server is unreachable)
 */
class SkipTest extends Exception {}


/**
 * Base class for tests
 * Test methods are public methods whose name starts with "test"
 */
abstract class TestCase {

    /**
     * Runs before every test method
     */
    protected function setUp(): void {}


    /**
     * Run setUp, then the test method
     */
    final public function run(string $method): void {
        $this->setUp();
        $this->$method();
    }


    protected function skip(string $reason): never {
        throw new SkipTest($reason);
    }


    protected function fail(string $message): never {
        throw new AssertionFailed($message);
    }


    protected function assertTrue(mixed $value, string $message = ''): void {
        if($value !== true) {
            $this->fail($message ?: 'Failed asserting that '.$this->export($value).' is true');
        }
    }


    protected function assertFalse(mixed $value, string $message = ''): void {
        if($value !== false) {
            $this->fail($message ?: 'Failed asserting that '.$this->export($value).' is false');
        }
    }


    protected function assertNull(mixed $value, string $message = ''): void {
        if($value !== null) {
            $this->fail($message ?: 'Failed asserting that '.$this->export($value).' is null');
        }
    }


    protected function assertSame(mixed $expected, mixed $actual, string $message = ''): void {
        if($expected !== $actual) {
            $this->fail($message ?: 'Failed asserting that '.$this->export($actual).' is identical to '.$this->export($expected));
        }
    }


    protected function assertContains(mixed $needle, array $haystack, string $message = ''): void {
        if(!in_array($needle, $haystack, true)) {
            $this->fail($message ?: 'Failed asserting that array contains '.$this->export($needle));
        }
    }


    protected function assertStringContains(string $needle, string $haystack, string $message = ''): void {
        if(!str_contains($haystack, $needle)) {
            $this->fail($message ?: 'Failed asserting that string contains '.$this->export($needle));
        }
    }


    protected function assertStringNotContains(string $needle, string $haystack, string $message = ''): void {
        if(str_contains($haystack, $needle)) {
            $this->fail($message ?: 'Failed asserting that string does not contain '.$this->export($needle));
        }
    }


    /**
     * Assert that callback throws exception of the given class (and code, if passed)
     */
    protected function assertThrows(string $class, callable $callback, ?int $code = null, string $message = ''): void {
        try {
            $callback();
        } catch(Throwable $e) {
            if(!($e instanceof $class)) {
                $this->fail($message ?: 'Expected '.$class.', got '.get_class($e).': '.$e->getMessage());
            }
            if($code !== null && $e->getCode() !== $code) {
                $this->fail($message ?: 'Expected code '.$code.', got '.$e->getCode());
            }
            return;
        }

        $this->fail($message ?: 'Expected '.$class.' to be thrown, nothing was thrown');
    }


    private function export(mixed $value): string {
        return is_string($value) ? '"'.$value.'"' : var_export($value, true);
    }
}
