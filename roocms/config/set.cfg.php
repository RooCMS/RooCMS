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
 * Debug mode switcher
 */
const DEBUGMODE = false;


/**
 * Get the amount of memory allocated at the start of the work
 * In the future, the efficiency will be calculated
 */
define('MEMORYUSAGE', memory_get_usage());

/**
 * Set the CSP header
 */
set_csp_header();

/**
 * Set the Service-Worker-Allowed header
 */
header('Service-Worker-Allowed: /');

/**
 * Start GZip
 */
ob_start('ob_gzhandler', 8);


/**
 * Set up PHP
 */
set_time_limit(45);
#ini_set('memory_limit', 			'512M');
#ini_set('upload_tmp_dir', 			'/tmp');	# temporary directory for uploaded files. (uncomment if you have difficulties with PHP settings)

/**
 * Set up serialize precision
 * Uncomment if you need the maximum precision of serialization, but on your server there is a problem with serialization
 */
#ini_set('serialize_precision', 	'-1');

/**
 * Set up default charset
 * Uncomment if you have difficulties with PHP settings
 */
#ini_set('default_charset',			'utf-8');


/**
 * Set up timezone
 */
date_default_timezone_set('UTC');

/**
 * Set up error log
 * For the case if PHP does not write errors to a file
 */
#ini_set('log_errors',				1);

/**
 * Set up style for error display
 */
#ini_set('error_prepend_string',		'');
#ini_set('error_append_string',		'');


/**
 * Set up Multibyte String
 */
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');


/**
 * Security headers
 */
header('X-Frame-Options: sameorigin'); 			# rule to use Iframe only within the site. Protect for "Clickjacking" / Use "deny" to ban iframes completely
header('X-Content-Type-Options: nosniff'); 		# Check mimetype
header('X-XSS-Protection: 1; mode=block'); 		# XSS Block
#header('X-XSS-Protection: 1; report=/?part=report&act=XSS'); 	# XSS Block and report (future)

/**
 * Signature in header
 */
header('X-Engine: RooCMS');
header('X-Engine-Copyright: 2010-'.date('Y').' (c) RooCMS');
header('X-Engine-Site: http://www.roocms.com');