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
 * RooCMS $Versions
 */
const ROOCMS_MAJOR_VERSION   = '2';
const ROOCMS_MINOR_VERSION   = '0';
const ROOCMS_RELEASE_VERSION = '0';
const ROOCMS_BUILD_VERSION   = 'alpha';
const ROOCMS_VERSION         = ROOCMS_MAJOR_VERSION.".".ROOCMS_MINOR_VERSION.".".ROOCMS_RELEASE_VERSION;
const ROOCMS_FULL_VERSION    = ROOCMS_MAJOR_VERSION.".".ROOCMS_MINOR_VERSION.".".ROOCMS_RELEASE_VERSION." ".ROOCMS_BUILD_VERSION;
const ROOCMS_VERSION_ID      = ROOCMS_MAJOR_VERSION.ROOCMS_MINOR_VERSION.ROOCMS_RELEASE_VERSION;