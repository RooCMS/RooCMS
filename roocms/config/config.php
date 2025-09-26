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
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}
//#########################################################


//#########################################################
//	MySQL database connection settings
//---------------------------------------------------------
$db_info = [];
//---------------------------------------------------------
$db_info['host'] = "mariadb-11.7";				#	Database host (default is localhost)
$db_info['user'] = "test";						#	Database user (default is root)
$db_info['pass'] = "test";						#	Database password (default is empty)
$db_info['base'] = "devroocms2";				#	Database name (default is empty)
$db_info['port'] = "3306";					    #	Database port 3306 for mysql, 5432 for postgresql, firebird (default is 3306)
$db_info['type'] = "mysql";					    #	Database type mysql, postgresql, firebird (default is mysql)
$db_info['prefix'] = "roocms_";					#	Database prefix (default is roocms_)
//#########################################################


//#########################################################
//	Site parameters
//---------------------------------------------------------
$site = [];
//---------------------------------------------------------
$site['title'] = "RooCMS 2.0";					#	Site title
$site['domain'] = "dev.roocms.com";				#	default domain
$site['scheme'] = "https";		    		    #	Site scheme
$site['sysemail'] = "info@roocms.com";			#	Service email
$site['theme'] = "default";				        #	Site theme
//#########################################################