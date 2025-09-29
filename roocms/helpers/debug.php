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
 * Load Debugger
 */
$debug = new Debugger;


/**
 * Quick debug with label (shorthand method)
 *
 * @param mixed $var
 * @param string $label
 */
function debug(mixed $var, string $label = 'Debug'): void {

    global $debug;
    
    $debug->rundebug($var, $label, true);
}


/**
 * Simple variable dump (no detailed analysis)
 *
 * @param mixed $var
 * @param string $label
 */
function dump(mixed $var, string $label = 'Dump'): void {

    global $debug;

    $debug->rundebug($var, $label, false);
}