<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
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
if(!defined('RooCMS') || (!defined('ACP') && !defined('INSTALL'))) {
	die('Access Denied');
}
//#########################################################

$sql = [];


/**
* Configuration part
*/
$sql['DROP '.CONFIG_PARTS_TABLE] = "DROP TABLE IF EXISTS ".CONFIG_PARTS_TABLE;

// Create ENUM type for config parts
$sql['CREATE ENUM type_component'] = "DO $$ 
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'component_type') THEN
        CREATE TYPE component_type AS ENUM ('global', 'component');
    END IF;
END $$";

$sql['CREATE '.CONFIG_PARTS_TABLE] = "CREATE TABLE ".CONFIG_PARTS_TABLE." (
			  id SERIAL PRIMARY KEY,
			  type component_type NOT NULL DEFAULT 'component',
			  sort INTEGER NOT NULL DEFAULT 1,
			  name VARCHAR(255) NOT NULL UNIQUE,
			  title VARCHAR(255) NOT NULL,
			  ico VARCHAR(255) NOT NULL
			)";

$id = 1;
$sql['INSERT '.CONFIG_PARTS_TABLE." ID #".$id] = "INSERT INTO ".CONFIG_PARTS_TABLE." VALUES (".$id.", 'global', 1, 'global', 'Общие настройки', 'cog')";		$id++;
$sql['INSERT '.CONFIG_PARTS_TABLE." ID #".$id] = "INSERT INTO ".CONFIG_PARTS_TABLE." VALUES (".$id.", 'global', 2, 'gd', 'Обработка изображений', 'image')";		$id++;
$sql['INSERT '.CONFIG_PARTS_TABLE." ID #".$id] = "INSERT INTO ".CONFIG_PARTS_TABLE." VALUES (".$id.", 'global', 3, 'users', 'Настройка пользователей', 'users')";	$id++;
$sql['INSERT '.CONFIG_PARTS_TABLE." ID #".$id] = "INSERT INTO ".CONFIG_PARTS_TABLE." VALUES (".$id.", 'global', 4, 'captcha', 'Captcha', 'theater-masks')";		$id++;
$sql['INSERT '.CONFIG_PARTS_TABLE." ID #".$id] = "INSERT INTO ".CONFIG_PARTS_TABLE." VALUES (".$id.", 'global', 5, 'cp', 'Панель Администратора', 'user-astronaut')";	$id++;
$sql['INSERT '.CONFIG_PARTS_TABLE." ID #".$id] = "INSERT INTO ".CONFIG_PARTS_TABLE." VALUES (".$id.", 'global', 6, 'tpl', 'Настройки шаблонизации', 'desktop')";	$id++;
$sql['INSERT '.CONFIG_PARTS_TABLE." ID #".$id] = "INSERT INTO ".CONFIG_PARTS_TABLE." VALUES (".$id.", 'global', 7, 'rss', 'RSS', 'rss')";				$id++;
$sql['INSERT '.CONFIG_PARTS_TABLE." ID #".$id] = "INSERT INTO ".CONFIG_PARTS_TABLE." VALUES (".$id.", 'global', 8, 'uagreement', 'Клиентское соглашение', 'gavel')";	$id++;
$sql['INSERT '.CONFIG_PARTS_TABLE." ID #".$id] = "INSERT INTO ".CONFIG_PARTS_TABLE." VALUES (".$id.", 'component', 7, 'feed', 'Ленты', 'th-list')";			$id++;


/**
* Config table
*/
$sql['DROP '.CONFIG_TABLE] = "DROP TABLE IF EXISTS ".CONFIG_TABLE;

// Create ENUM type for config option types
$sql['CREATE ENUM option_type'] = "DO $$ 
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'config_option_type') THEN
        CREATE TYPE config_option_type AS ENUM ('boolean','int','string','color','text','html','date','email','select','image');
    END IF;
END $$";

$sql['CREATE'.CONFIG_TABLE] = "CREATE TABLE ".CONFIG_TABLE." (
			  id SERIAL PRIMARY KEY,
			  part VARCHAR(255) NOT NULL DEFAULT 'global',
			  sort INTEGER NOT NULL DEFAULT 1,
			  title VARCHAR(255) NOT NULL,
			  desc_msg VARCHAR(255) NOT NULL DEFAULT '',
			  option_name VARCHAR(255) NOT NULL UNIQUE,
			  option_type config_option_type NOT NULL DEFAULT 'boolean',
			  default_var VARCHAR(255) NOT NULL DEFAULT '',
			  var VARCHAR(255) NOT NULL DEFAULT '',
			  field_maxleight SMALLINT NOT NULL DEFAULT 0,
			  option_values TEXT NOT NULL DEFAULT ''
			)";

// Create indexes
$sql['CREATE INDEX config_part'] = "CREATE INDEX idx_config_part ON ".CONFIG_TABLE." (part)";

// NOTE: Остальные таблицы нужно будет добавить аналогичным образом
// Это базовая структура для демонстрации принципа адаптации

/**
* Example of how to handle more complex scenarios
*/

// If you need to read the full MySQL schema and convert it:
/*
$mysql_schema_file = file_get_contents(__DIR__ . '/db_mysql_schema.php');

// Function to convert MySQL syntax to PostgreSQL
function mysql_to_postgresql($mysql_query) {
    $postgresql_query = $mysql_query;
    
    // Remove backticks
    $postgresql_query = str_replace('`', '', $postgresql_query);
    
    // Convert AUTO_INCREMENT to SERIAL
    $postgresql_query = preg_replace('/INT\(\d+\)\s+UNSIGNED\s+NOT\s+NULL\s+AUTO_INCREMENT/', 'SERIAL', $postgresql_query);
    $postgresql_query = preg_replace('/INT\(\d+\)\s+NOT\s+NULL\s+AUTO_INCREMENT/', 'SERIAL', $postgresql_query);
    
    // Convert data types
    $postgresql_query = preg_replace('/INT\(\d+\)\s+UNSIGNED/', 'INTEGER', $postgresql_query);
    $postgresql_query = preg_replace('/INT\(\d+\)/', 'INTEGER', $postgresql_query);
    $postgresql_query = preg_replace('/SMALLINT\(\d+\)\s+UNSIGNED/', 'SMALLINT', $postgresql_query);
    $postgresql_query = preg_replace('/SMALLINT\(\d+\)/', 'SMALLINT', $postgresql_query);
    $postgresql_query = preg_replace('/VARCHAR\((\d+)\)/', 'VARCHAR($1)', $postgresql_query);
    
    // Remove MySQL-specific clauses
    $postgresql_query = preg_replace('/ENGINE=\w+/', '', $postgresql_query);
    $postgresql_query = preg_replace('/DEFAULT CHARSET=\w+/', '', $postgresql_query);
    $postgresql_query = preg_replace('/PACK_KEYS=\d+/', '', $postgresql_query);
    $postgresql_query = preg_replace('/AUTO_INCREMENT=\d+/', '', $postgresql_query);
    
    // Handle UNIQUE KEY and KEY
    $postgresql_query = preg_replace('/,\s*UNIQUE KEY\s+`\w+`\s+\(`(\w+)`\)/', '', $postgresql_query);
    $postgresql_query = preg_replace('/,\s*KEY\s+`\w+`\s+\(`(\w+)`\)/', '', $postgresql_query);
    
    // Clean up extra commas and spaces
    $postgresql_query = preg_replace('/,\s*\)/', ')', $postgresql_query);
    $postgresql_query = preg_replace('/\s+/', ' ', $postgresql_query);
    
    return trim($postgresql_query);
}
*/

?>
