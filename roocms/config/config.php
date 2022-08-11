<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


//#########################################################
//	Настройки подключения к Базе Данных MySQL
//---------------------------------------------------------
$db_info = [];
//---------------------------------------------------------
$db_info['host'] = "";					#	Хост Базы Данных
$db_info['user'] = "";					#	Имя пользователя Базы Данных
$db_info['pass'] = "";					#	Пароль пользователя Базы Данных
$db_info['base'] = "";				#	Название Базы с данными
$db_info['prefix'] = "";					#	Префикс таблиц в Базе Данных
//#########################################################


//#########################################################
//	Site parameters
//---------------------------------------------------------
$site = [];
//---------------------------------------------------------
$site['title'] = "";						#	Site title
$site['domain'] = "";						#	default domain
$site['protocol'] = "";						#	Site protocol
$site['sysemail'] = "";						#	Service email
$site['skin'] = "default";					#	default skin
//#########################################################