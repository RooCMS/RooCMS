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
 * Test runner without external dependencies
 *
 * Usage:
 *   php tests/run.php              run unit tests
 *   php tests/run.php http         run HTTP tests against ROOCMS_TEST_URL
 *   php tests/run.php all          run both
 *   php tests/run.php unit Role    run only tests whose "Class::method" contains "Role"
 */

require_once __DIR__.'/bootstrap.php';
require_once __DIR__.'/TestCase.php';


$suite  = $argv[1] ?? 'unit';
$filter = $argv[2] ?? null;

$suites = [
    'unit' => __DIR__.'/unit',
    'http' => __DIR__.'/http',
];

if($suite === 'all') {
    $dirs = $suites;
} elseif(isset($suites[$suite])) {
    $dirs = [$suite => $suites[$suite]];
} else {
    fwrite(STDERR, "Unknown suite '".$suite."'. Use: unit, http, all\n");
    exit(2);
}


/**
 * Load test files and collect test classes
 */
$before = get_declared_classes();
foreach($dirs as $dir) {
    foreach(glob($dir.'/*Test.php') ?: [] as $file) {
        require_once $file;
    }
}
$classes = array_filter(
    array_diff(get_declared_classes(), $before),
    fn(string $class) => is_subclass_of($class, TestCase::class) && !(new ReflectionClass($class))->isAbstract()
);


/**
 * Run tests
 */
$counts = ['pass' => 0, 'fail' => 0, 'error' => 0, 'skip' => 0];
$problems = [];

foreach($classes as $class) {
    foreach(get_class_methods($class) as $method) {
        if(!str_starts_with($method, 'test')) {
            continue;
        }

        $name = $class.'::'.$method;
        if($filter !== null && !str_contains($name, $filter)) {
            continue;
        }

        try {
            (new $class())->run($method);
            $counts['pass']++;
            echo "  PASS  ".$name."\n";
        } catch(SkipTest $e) {
            $counts['skip']++;
            echo "  SKIP  ".$name." (".$e->getMessage().")\n";
        } catch(AssertionFailed $e) {
            $counts['fail']++;
            $problems[] = [$name, $e->getMessage()];
            echo "  FAIL  ".$name."\n";
        } catch(Throwable $e) {
            $counts['error']++;
            $problems[] = [$name, get_class($e).': '.$e->getMessage().' ('.basename($e->getFile()).':'.$e->getLine().')'];
            echo "  ERROR ".$name."\n";
        }
    }
}


/**
 * Summary
 */
echo "\n";
foreach($problems as [$name, $message]) {
    echo $name."\n    ".$message."\n";
}

echo sprintf(
    "\n%d passed, %d failed, %d errors, %d skipped\n",
    $counts['pass'], $counts['fail'], $counts['error'], $counts['skip']
);

exit(($counts['fail'] + $counts['error']) > 0 ? 1 : 0);
