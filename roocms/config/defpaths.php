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
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################


/**
 * Web $Path
 */
define('_DOMAIN',	str_ireplace(array('http://','https://','www.'), '', $site['domain']));


/**
* RooCMS $Path
*/
const _ROOCMS       = _SITEROOT.'/roocms';
const _MODULES      = _ROOCMS.'/modules';
const _SERVICES     = _ROOCMS.'/services';
const _HELPERS      = _ROOCMS.'/helpers';
const _API          = _SITEROOT.'/api';
const _UPLOAD       = _SITEROOT.'/up';
const _UPLOADFILES  = _UPLOAD.'/files';
const _UPLOADIMG    = _UPLOAD.'/img';
const _UPLOADAV     = _UPLOAD.'/av';
const _MIGRATIONS   = _ROOCMS.'/database/migrations';
const _BACKUPS      = _ROOCMS.'/database/backups';
const _STORAGE      = _SITEROOT.'/storage';
const _ASSETS       = _STORAGE.'/assets';
const _LOGS         = _STORAGE.'/logs';


/**
 * Roocms $Logs
 */
const ERRORSLOG = _LOGS."/lowerrors.log";
const SYSERRLOG = _LOGS."/syserrors.log";
const DEBUGSLOG = _LOGS."/debug.log";
