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
 * This code will measure the speed of executing an operation with different values of algorithmic complexity of hashing
 * on your server and determine
 * its maximum value, without degrading performance. A good base
 * value is 10, but if your server is powerful enough, you can
 * set it to more. This script finds the maximum value, at which
 * hashing will fit into a value ≤ 350 milliseconds, which is considered an acceptable delay
 * for systems that process interactive inputs.
 */

$timeTarget = 0.350; // 350 milliseconds

$cost = 8;

do {
    $cost++;
    $start = microtime(true);
    password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);
    $end = microtime(true);
} while (($end - $start) < $timeTarget);

echo "Optimal cost: " . $cost;
