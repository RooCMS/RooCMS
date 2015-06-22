<?php
/**
* @package      RooCMS
* @subpackage	Library
* @author       alex Roosso
* @copyright    2010-2015 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2.5
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2014 alex Roosso aka alexandr Belov info@roocms.com
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see http://www.gnu.org/licenses/
*
*
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2014 alex Roosso (александр Белов) info@roocms.com
*
*   Это программа является свободным программным обеспечением. Вы можете
*   распространять и/или модифицировать её согласно условиям Стандартной
*   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
*   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
*
*   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
*   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
*   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
*   Общественную Лицензию GNU для получения дополнительной информации.
*
*   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
*   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || (!defined('ACP') && !defined('INSTALL'))) die('Access Denied');
//#########################################################

$sql = array();


/**
* Разделы конфигурации
*/
$sql['DROP '.CONFIG_PARTS] = "DROP TABLE IF EXISTS `".CONFIG_PARTS."`";
$sql['CREATE '.CONFIG_PARTS] = "CREATE TABLE IF NOT EXISTS `".CONFIG_PARTS."` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `type` enum('global', 'component') NOT NULL DEFAULT 'component',
				  `sort` int(10) unsigned NOT NULL DEFAULT '1',
				  `name` varchar(255) NOT NULL,
				  `title` varchar(255) NOT NULL,
				  `ico` varchar(255) NOT NULL,
				  UNIQUE KEY `id` (`id`),
				  UNIQUE KEY `name` (`name`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";

$id = 1;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` VALUES (".$id.", 'global', 1, 'global', 'Общие настройки', 'cog')";		$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` VALUES (".$id.", 'global', 2, 'gd', 'Обработка изображений', 'picture-o')";	$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` VALUES (".$id.", 'global', 3, 'cp', 'Панель Администратора', 'shield')";	$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` VALUES (".$id.", 'global', 4, 'tpl', 'Настройки шаблонизации', 'desktop')";	$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` VALUES (".$id.", 'global', 5, 'rss', 'RSS', 'rss')";				$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` VALUES (".$id.", 'component', 6, 'feed', 'Ленты', 'th-list')";		$id++;


/**
* Параметры конфигурации
*/
$sql['DROP '.CONFIG_TABLE] = "DROP TABLE IF EXISTS `".CONFIG_TABLE."`";
$sql['CREATE'.CONFIG_TABLE] = "CREATE TABLE IF NOT EXISTS `".CONFIG_TABLE."` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `part` varchar(255) NOT NULL DEFAULT 'global',
				  `sort` int(10) unsigned NOT NULL DEFAULT '1',
				  `title` varchar(255) NOT NULL,
				  `description` text NOT NULL,
				  `option_name` varchar(255) NOT NULL,
				  `option_type` enum('boolean','bool','integer','int','string','color','text','textarea','date','email','select') NOT NULL DEFAULT 'boolean',
				  `variants` text NOT NULL,
				  `value` longtext NOT NULL,
				  `default_value` text NOT NULL,
				  `field_maxleight` smallint(4) unsigned NOT NULL DEFAULT '0',
				  UNIQUE KEY `id` (`id`),
				  UNIQUE KEY `option` (`option_name`),
				  KEY `part` (`part`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";
$id = 1;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'global', 1, 'Название сайта', 'Глобальный заголовок сайта', 'site_title', 'string', '', '".$site['title']."', '".$site['title']."', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'global', 1, 'Глобальный заголовок', 'Применять название сайта глобально ко всем заголовкам?', 'global_site_title', 'boolean', '', 'true', 'true', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'global', 3, 'Мета описание', 'Глобальное мета описание сайта', 'meta_description', 'string', '', '', '', 250)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'global', 4, 'Мета ключевые слова', 'Глобальные ключевые слова для сайта', 'meta_keywords', 'string', '', '', '', 250)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'global', 5, 'Заголовок 304', 'Опция включает/выключает ответ заголовка с кодом 304 на запрос IF_MODIFED_SINCE от поисковых роботов там где это разрешено.\r\nВо включенном состоянии опция позволит поисковым роботам быстрее индексировать ваш сайт и ускоряет работу шаблонизатора.', 'if_modifed_since', 'boolean', '', 'false', 'false', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'gd', 1, 'Максимальная ширина изображений', 'Укажите максимальную ширину загружаемых изображений в пикселях. \r\nВ случае если изображение окажется больше указанной ширины, оно будет пропорционально уменьшено', 'gd_image_maxwidth', 'int', '', '1200', '1200', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'gd', 2, 'Максимальная высота изображений', 'Укажите максимальную ширину загружаемых изображений в пикселях. \r\nВ случае если изображение окажется больше указанной ширины, оно будет пропорционально уменьшено', 'gd_image_maxheight', 'int', '', '1200', '1200', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'gd', 3, 'Ширина миниатюры', 'Укажите размер миниатюры изображения по горизонтали (в пикселях)', 'gd_thumb_image_width', 'int', '', '267', '267', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'gd', 4, 'Высота миниатюры', 'Укажите размер миниатюры изображения по вертикали(в пикселях)', 'gd_thumb_image_height', 'int', '', '150', '150', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'gd', 5, 'Тип миниатюры', 'Выберите алгоритм генерации миниатюр. \r\nЗаполнение - полностью заполнит миниатюру.\r\nПо размеру - пропорции изображения будут вписаны в пропорции миниатюры.\r\n', 'gd_thumb_type_gen', 'select', 'Заполнение|fill\r\nПо размеру|size', 'fill', 'fill', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'gd', 6, 'Цвет фона миниатюры', 'Данный параметр устанавливает цвет фона для миниатюр, если вы выбрали тип генерации &quot;по размеру&quot;', 'gd_thumb_bgcolor', 'color', '', '#ffffff', '#ffffff', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'gd', 7, 'Качество миниатюр', 'Укажите качество создаваемых миниатюр от 1 до 100 \r\nОпция применима только для jpg миниатюр.', 'gd_thumb_jpg_quality', 'int', '', '90', '90', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'gd', 8, 'Вкл/выкл водяной знак', 'Использовать на загружаемых изображениях Watermark (полупрозрачный копирайт) для защиты изображений от копирования на сторонние ресурсы?', 'gd_use_watermark', 'boolean', '', 'true', 'true', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'gd', 9, 'Первая строка водяного знака', 'Первая строчка водяного знака накладываемого на изображение', 'gd_watermark_string_one', 'string', '', '', '', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'gd', 10, 'Вторая строка водяного знака', 'Вторая строчка водяного знака накладываемого на изображение', 'gd_watermark_string_two', 'string', '', 'http://".$_SERVER['SERVER_NAME']."', '', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'cp', 1, 'Вход в панель управления', 'Укажите название файла (скрипта) через который вы будете заходить в Панель Управления.\r\nВнимание! После изменения этой настройки, изменится URI панели управления. В случае если вы изменяли вручную шаблоны панели управления, проверьте, что вы везде указали переменную {&#36;SCRIPT_NAME}', 'cp_script', 'string', '', 'acp.php', 'acp.php', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'cp', 2, 'E-mail администратора', 'Укажите адрес электронной почты администратора Он будет использоваться для системных уведомлений.', 'cp_email', 'email', '', '".$site['sysemail']."', '".$site['sysemail']."', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'tpl', 1, 'Вкл/выкл режим отладки шаблонов', 'Опцпия активирует принудительную перекомпиляцию шаблонов при каждом вызове.\r\nНикогда не используйте это действие в условиях реальной эксплуатации', 'tpl_recompile_force', 'boolean', '', 'false', 'false', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'rss', 1, 'Вкл/Выкл RSS лент', 'Опция глобального включения или отключения RSS лент', 'rss_power', 'boolean', '', 'true', 'true', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'rss', 2, 'TTL', 'Время жизни фида в секундах', 'rss_ttl', 'int', '', '240', '240', 0)"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` VALUES (".$id.", 'feed', 1, 'Количество элементов ленты на страницу', 'Опция устанавливает количество элементов ленты выводимых на одной странице. \nОпция действует глобально, если иное не указано в настройках ленты.', 'feed_items_per_page', 'int', '', '10', '10', 0)"; $id++;


/**
* Таблица страктуры сайта
*/
$sql['DROP '.STRUCTURE_TABLE] = "DROP TABLE IF EXISTS `".STRUCTURE_TABLE."`";
$sql['CREATE'.STRUCTURE_TABLE] = "CREATE TABLE IF NOT EXISTS `".STRUCTURE_TABLE."` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `alias` varchar(255) NOT NULL COMMENT 'Unique name for structure unit',
					  `parent_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Identifaction parent structure unit',
					  `sort` int(10) unsigned NOT NULL DEFAULT '0',
					  `title` varchar(255) NOT NULL,
					  `meta_description` varchar(255) NOT NULL,
					  `meta_keywords` varchar(255) NOT NULL,
					  `noindex` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'meta tag robots flag',
					  `page_type` enum('html','php','feed') NOT NULL DEFAULT 'html',
					  `childs` int(10) unsigned NOT NULL DEFAULT '0',
					  `page_id` int(10) unsigned NOT NULL DEFAULT '0',
					  `date_create` int(20) unsigned NOT NULL DEFAULT '0' COMMENT 'format: unixtimestamp',
					  `date_modified` int(20) unsigned NOT NULL DEFAULT '0' COMMENT 'format: unixtimestamp',
					  `rss` enum('0','1') NOT NULL DEFAULT '1',
					  `show_child_feeds` enum('none','default','forced') NOT NULL DEFAULT 'none',
					  `items_per_page` int(10) unsigned NOT NULL DEFAULT '0',
					  `items_sorting` enum('datepublication','title_asc','title_desc','manual_sorting') NOT NULL DEFAULT 'datepublication',
					  `items` int(10) unsigned NOT NULL DEFAULT '0',
					  `thumb_img_width` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Thumbnail image width [in pixels]',
					  `thumb_img_height` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Thumbnail image height [in pixels]',
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `alias` (`alias`),
					  KEY `type` (`page_type`),
					  KEY `page_id` (`page_id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";

$id = 1;
$sql['INSERT '.STRUCTURE_TABLE." ID #".$id] = "INSERT INTO `".STRUCTURE_TABLE."` (`id`, `alias`, `parent_id`, `sort`, `title`, `meta_description`, `meta_keywords`, `noindex`, `page_type`, `childs`, `page_id`, `date_create`, `date_modified`, `rss`, `show_child_feeds`, `items_per_page`, `items_sorting`, `items`, `thumb_img_width`, `thumb_img_height`)
									  VALUES (1, 'index', 0, 0, 'Главная страница', '', 'index', '0', 'html', 5, 1, ".time().", ".time().", '1', 'none', 0, 'datepublication', 0, 0, 0)";

/**
* HTML страницы
*/
$sql['DROP '.PAGES_HTML_TABLE] = "DROP TABLE IF EXISTS `".PAGES_HTML_TABLE."`";
$sql['CREATE'.PAGES_HTML_TABLE] = "CREATE TABLE IF NOT EXISTS `".PAGES_HTML_TABLE."` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `sid` int(10) unsigned NOT NULL,
					  `content` longtext NOT NULL,
					  `date_modified` int(20) unsigned NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `sid` (`sid`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";

$sql['INSERT '.PAGES_HTML_TABLE." ID #".$id] = "INSERT INTO `".PAGES_HTML_TABLE."` (`id`, `sid`, `content`, `date_modified`)
									    VALUES (1, 1, '&lt;h1&gt;\r\n	Добро пожаловать!&lt;/h1&gt;\r\nЭто новый сайт, который был создан с помощью системы управления контентом &lt;a href=&quot;http://www.roocms.com&quot;&gt;RooCMS&lt;/a&gt; версии 1.00&lt;br /&gt;\r\n&lt;br /&gt;\r\nRooCMS - это русская система управления сайтом (контентом). Простая и удобная в использовании как программисту или верстальщику, так и людям, которые совершенно незнакомы с производством сайтов.', ".time().")";


/**
* PHP страницы
*/
$sql['DROP '.PAGES_PHP_TABLE] = "DROP TABLE IF EXISTS `".PAGES_PHP_TABLE."`";
$sql['CREATE'.PAGES_PHP_TABLE] = "CREATE TABLE IF NOT EXISTS `".PAGES_PHP_TABLE."` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `sid` int(10) unsigned NOT NULL,
					  `content` longtext NOT NULL,
					  `date_modified` int(20) unsigned NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `sid` (`sid`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";


/**
* Ленты
*/
$sql['DROP '.PAGES_FEED_TABLE] = "DROP TABLE IF EXISTS `".PAGES_FEED_TABLE."`";
$sql['CREATE'.PAGES_FEED_TABLE] = "CREATE TABLE IF NOT EXISTS `".PAGES_FEED_TABLE."` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `sid` int(10) unsigned NOT NULL,
					  `status` enum('0','1') NOT NULL DEFAULT '1',
					  `sort` int(10) unsigned NOT NULL DEFAULT '0',
					  `date_create` int(20) unsigned NOT NULL DEFAULT '0',
					  `date_update` int(20) unsigned NOT NULL DEFAULT '0',
					  `date_publications` int(20) unsigned NOT NULL DEFAULT '0',
					  `date_end_publications` int(20) unsigned NOT NULL DEFAULT '0',
					  `title` varchar(255) NOT NULL,
					  `meta_description` varchar(255) NOT NULL,
					  `meta_keywords` varchar(255) NOT NULL,
					  `brief_item` text NOT NULL,
					  `full_item` longtext NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `sid` (`sid`),
					  KEY `date_publications` (`date_publications`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";


/**
* Блоки
*/
$sql['DROP '.BLOCKS_TABLE] = "DROP TABLE IF EXISTS `".BLOCKS_TABLE."`";
$sql['CREATE '.BLOCKS_TABLE] = "CREATE TABLE IF NOT EXISTS `".BLOCKS_TABLE."` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `alias` varchar(255) NOT NULL COMMENT 'unique name for block',
				  `block_type` enum('html','php') NOT NULL DEFAULT 'html',
				  `title` varchar(255) NOT NULL,
				  `content` longtext NOT NULL COMMENT 'html or php code',
				  `date_create` int(20) unsigned NOT NULL DEFAULT '0' COMMENT 'format: unixtimestamp',
				  `date_modified` int(20) unsigned NOT NULL DEFAULT '0' COMMENT 'last modified date',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `alias` (`alias`)
                                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";
$id = 1;
$sql['INSERT '.BLOCKS_TABLE." ID #".$id] = "INSERT INTO `".BLOCKS_TABLE."` VALUES('".$id."', 'nav_pages', 'php', 'Меню', 'global &#36;structure;\r\n\r\necho &lt;&lt;&lt;HTML\r\n        &lt;div class=&quot;navbar navbar-default&quot;&gt;\r\n\r\n					  &lt;div class=&quot;navbar-header&quot;&gt;\r\n						&lt;button type=&quot;button&quot; class=&quot;navbar-toggle&quot; data-toggle=&quot;collapse&quot; data-target=&quot;.navbar-exmenu-collapse&quot;&gt;\r\n						  &lt;span class=&quot;sr-only&quot;&gt;Toggle navigation&lt;/span&gt;\r\n						  &lt;span class=&quot;icon-bar&quot;&gt;&lt;/span&gt;\r\n						  &lt;span class=&quot;icon-bar&quot;&gt;&lt;/span&gt;\r\n						  &lt;span class=&quot;icon-bar&quot;&gt;&lt;/span&gt;\r\n						&lt;/button&gt;\r\n					  &lt;/div&gt;\r\n\r\n					&lt;div class=&quot;collapse navbar-collapse navbar-exmenu-collapse&quot;&gt;\r\n						&lt;ul class=&quot;nav navbar-nav&quot;&gt;\r\nHTML;\r\n\r\n&#36;tree = &#36;structure-&gt;sitetree;\r\n\r\n&#36;submenu = false;\r\n\r\nforeach(&#36;tree as &#36;k=&gt;&#36;v) &#123;\r\n	if(&#36;v[&#39;level&#39;] == 0) &#123;\r\n		echo &quot;\r\n			&lt;li&gt;&lt;a href=\\&quot;/\\&quot;&gt;Главная&lt;/a&gt;&lt;/li&gt;\r\n			&lt;li class=\\&quot;divider-vertical\\&quot;&gt;&lt;/li&gt;\r\n		&quot;;\r\n	&#125;\r\n	\r\n	if(&#36;v[&#39;level&#39;] == 1) &#123;\r\n		if(&#36;submenu) &#123;\r\n			&#36;submenu = false;\r\n			echo &quot;&lt;/ul&gt;&lt;/li&gt;&quot;;\r\n		&#125;\r\n		\r\n		echo &quot;\\n&lt;li&gt;&lt;a href=\\&quot;/index.php?page=&#123;&#36;v[&#39;alias&#39;]&#125;\\&quot;&gt;&#123;&#36;v[&#39;title&#39;]&#125;&lt;/a&gt;&lt;/li&gt;&quot;;\r\n		\r\n		if(&#36;v[&#39;childs&#39;] &gt; 0) &#123;\r\n			&#36;submenu = true;\r\n			echo &quot;\r\n				&lt;li class=\\&quot;dropdown\\&quot;&gt;\r\n					&lt;a href=\\&quot;#\\&quot; class=\\&quot;dropdown-toggle\\&quot; data-toggle=\\&quot;dropdown\\&quot;&gt;\r\n						&lt;b class=\\&quot;caret\\&quot;&gt;&lt;/b&gt;\r\n					&lt;/a&gt;\r\n					&lt;ul class=\\&quot;dropdown-menu\\&quot;&gt;\r\n			&quot;;\r\n		&#125;\r\n		else echo &quot;&lt;li class=\\&quot;divider-vertical\\&quot;&gt;&lt;/li&gt;&quot;;\r\n	&#125;\r\n	\r\n	if(&#36;v[&#39;level&#39;] &gt; 1) &#123;\r\n		echo &quot;\\n&lt;li&gt;&lt;a href=\\&quot;/index.php?page=&#123;&#36;v[&#39;alias&#39;]&#125;\\&quot;&gt;&#123;&#36;v[&#39;title&#39;]&#125;&lt;/a&gt;&lt;/li&gt;&quot;;	\r\n	&#125;\r\n&#125;\r\n\r\nif(&#36;submenu) &#123;\r\n	&#36;submenu = false;\r\n	echo &quot;&lt;/ul&gt;&lt;/li&gt;&quot;;\r\n&#125;\r\n\r\necho &lt;&lt;&lt;HTML\r\n				&lt;/ul&gt;\r\n			&lt;/div&gt;\r\n        &lt;/div&gt;\r\nHTML;\r\n', ".time().", ".time().")";


/**
* Изображения
*/
$sql['DROP '.IMAGES_TABLE] = "DROP TABLE IF EXISTS `".IMAGES_TABLE."`";
$sql['CREATE '.IMAGES_TABLE] = "CREATE TABLE IF NOT EXISTS `".IMAGES_TABLE."` (
				  `id` int(10) NOT NULL AUTO_INCREMENT,
				  `attachedto` varchar(255) NOT NULL COMMENT 'site partition and identificator',
				  `filename` varchar(255) NOT NULL,
				  `fileext` varchar(10) NOT NULL COMMENT 'extension file',
				  `sort` int(10) unsigned NOT NULL DEFAULT '0',
				  `alt` varchar(255) NOT NULL COMMENT 'alternative text for hover or unuse image',
				  PRIMARY KEY (`id`),
				  KEY `filename` (`filename`),
				  KEY `attachedto` (`attachedto`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";


/**
 * Пользователи
 */
$sql['DROP '.USERS_TABLE] = "DROP TABLE IF EXISTS `".USERS_TABLE."`";
$sql['CREATE '.USERS_TABLE] = "CREATE TABLE  `".USERS_TABLE."` (
				 `uid` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
				 `gid` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0',
				 `status` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '1',
				 `login` VARCHAR( 255 ) NOT NULL ,
				 `nickname` VARCHAR( 255 ) NOT NULL ,
				 `email` VARCHAR( 255 ) NOT NULL ,
				 `title` ENUM(  'a',  'u' ) NOT NULL DEFAULT  'u',
				 `password` VARCHAR( 32 ) NOT NULL ,
				 `salt` CHAR( 4 ) NOT NULL ,
				 `date_create` INT( 20 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'format: unixtimestamp',
				 `date_update` INT( 20 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'format: unixtimestamp',
				 `last_visit` INT( 20 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'format: unixtimestamp',
				PRIMARY KEY (  `uid` ) ,
				UNIQUE KEY  `uid` (  `uid` ,  `login` ) ,
				UNIQUE KEY  `nickname` (  `nickname` )
				) ENGINE = MYISAM DEFAULT CHARSET = utf8;";


/**
 * Группы пользователей
 */
$sql['DROP '.USERS_GROUP_TABLE] = "DROP TABLE IF EXISTS `".USERS_GROUP_TABLE."`";
$sql['CREATE '.USERS_GROUP_TABLE] = "CREATE TABLE  `".USERS_GROUP_TABLE."` (
					 `gid` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
					 `title` VARCHAR( 255 ) NOT NULL ,
					 `users` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0',
					 `date_create` INT( 20 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'format: unixtimestamp',
					 `date_update` INT( 20 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'format: unixtimestamp',
					PRIMARY KEY (  `gid` ) ,
					UNIQUE KEY  `gid` (  `gid` )
					) ENGINE = MYISAM DEFAULT CHARSET = utf8;";


/**
 * Помощь администратора
 */
$sql['DROP '.HELP_TABLE] = "DROP TABLE IF EXISTS `".HELP_TABLE."`";
$sql['CREATE '.HELP_TABLE] = "CREATE TABLE IF NOT EXISTS `".HELP_TABLE."` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `uname` varchar(255) NOT NULL,
				  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
				  `sort` int(10) unsigned NOT NULL DEFAULT '0',
				  `childs` int(10) unsigned NOT NULL DEFAULT '0',
				  `title` varchar(255) NOT NULL,
				  `content` longtext NOT NULL,
				  `date_modified` int(20) unsigned NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `uname` (`uname`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=26";

$id = 1;
$sql['INSERT '.HELP_TABLE." ID #01"] = "INSERT INTO `".HELP_TABLE."` VALUES(1, 'help', 0, 0, 4, 'Помощь', '&lt;p&gt;Вы находитесь в справочном разделе системы управления контентом - RooCMS.&lt;br /&gt;\r\n&lt;br /&gt;\r\nЧто бы получить информацию о функциях панели управления выберите один из нижеприведенных разделов.&lt;/p&gt;\r\n', 1381472467)";
$sql['INSERT '.HELP_TABLE." ID #02"] = "INSERT INTO `".HELP_TABLE."` VALUES(2, 'acp_structure', 3, 2, 1, 'Структура', '&lt;p&gt;Данный раздел сайта отвечает за управления структурой сайта.&lt;br /&gt;\r\n&lt;br /&gt;\r\nДля наглядности структура сайта представлена ввиде иерархического дерева неограниченной вложенности.&lt;br /&gt;\r\n&lt;br /&gt;\r\nЧто бы добавить новый элемент в структуру нажмите на ссылку справа &amp;quot;&lt;code&gt;Создать новую страницу&lt;/code&gt;&amp;quot;.&lt;br /&gt;\r\n&lt;br /&gt;\r\nЧто бы отредактировать технические параметры отдельной страницы или раздела сайта нажмите на ссылку &amp;quot;&lt;code&gt;Редактировать&lt;/code&gt;&amp;quot;.&lt;br /&gt;\r\n&lt;br /&gt;\r\nЕсли хотите удалить какой либо раздел или страницу сайта, нажмите на ссылку &amp;quot;&lt;code&gt;Удалить&lt;/code&gt;&amp;quot;. Вы не сможете удалить структуреый элемент, если у него имеются подчиненный элементы. Предварительно вам необходимо будет перенести их в подчинение другому элементу или удалить.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;span class=&quot;label label-danger&quot;&gt;ВНИМАНИЕ!&lt;/span&gt; Вы не должны удалять корневую (главную) страницу сайта, ведь всем известно, что без главной страницы сайт существовать не может. Удаление корневой странице приведет к неработоспособности системы.&lt;/p&gt;\r\n', 1382922143)";
$sql['INSERT '.HELP_TABLE." ID #03"] = "INSERT INTO `".HELP_TABLE."` VALUES(3, 'acp', 1, 1, 2, 'Панель управления', '&lt;p&gt;В данном разделе собрана информация по управлению сайтом через Панель Администратора.&lt;/p&gt;\r\n\r\n&lt;p&gt;Для получения более подробной справки смотрите подразделы указанный ниже.&lt;/p&gt;\r\n', 1381439635)";
$sql['INSERT '.HELP_TABLE." ID #04"] = "INSERT INTO `".HELP_TABLE."` VALUES(4, 'acp_serverinfo', 8, 1, 0, 'Информация о сервере', '&lt;p&gt;В данном разделе представлена информация о технической и программной сущностях сервера на котором расположен ваш сайт.&lt;br /&gt;\r\nПодобная информация может быть интересна опытным веб мастерам или при обращении в службу поддержки вашего хостинг-провайдера или на сайте разработчика RooCMS.&lt;br /&gt;\r\n&lt;br /&gt;\r\n&lt;b&gt;Версия PHP&lt;/b&gt; - Указывается версия интерпретатора языка PHP на котором написан код данной системы управления. &lt;span class=&quot;label label-warning&quot;&gt;Внимание! RooCMS работает только на основе PHP версии не ниже 5.2.*&lt;/span&gt;&lt;br /&gt;\r\n&lt;b&gt;Версия Zend&lt;/b&gt; - Указывается версия ядра интерпретатора.&lt;br /&gt;\r\n&lt;b&gt;Версия MySQL&lt;/b&gt; - Указывается версия базы данных. В случае возникновения проблем в работе БД, данная информация поможет при поиске решения проблем.&lt;br /&gt;\r\n&lt;b&gt;Версия RooCMS&lt;/b&gt; - Указывается текущая версия системы управления сайтом используемая в данный момент.&lt;br /&gt;\r\n&lt;b&gt;Apache&lt;/b&gt; - Указывается подпись сервера. Из неё можно почерпнуть информацию о версии веб-сервера, наличии SSL, типа PHP интерпретатора (модуль или cgi) и иную информацию&lt;br /&gt;\r\n&lt;b&gt;Имя сервера&lt;/b&gt; - Указывается имя вашего сервера на котором расположен сайт&lt;br /&gt;\r\n&lt;b&gt;Адрес сервера &lt;/b&gt;- Указывается IP адрес вашего физического сервера на котором расположен сайт.&lt;br /&gt;\r\n&lt;b&gt;Протокол сервера&lt;/b&gt; - Указывается протокол передачи данных с сервера до клиента.&lt;br /&gt;\r\n&lt;b&gt;Операционная система&lt;/b&gt; - Указывается операционная система установленная на вашем физическом сервере.&lt;br /&gt;\r\n&lt;b&gt;Операционная система (build)&lt;/b&gt; - Указывается полное название фашей операционной системы, включая заголовок и версию сборки.&lt;br /&gt;\r\n&lt;b&gt;Лимит памяти&lt;/b&gt;&amp;nbsp; - Указывается лимит выделенной памяти под обработку скриптов установленный в настройках веб-сервера или интерпретатора.&lt;/p&gt;\r\n\r\n&lt;blockquote class=&quot;quote quote-info&quot;&gt;\r\n&lt;p&gt;&lt;span style=&quot;font-size:14px&quot;&gt;Данный лимит при необходимости можно увелечить.&lt;br /&gt;\r\nДля этого в файле: /roocms/config/set.cfg.php&lt;br /&gt;\r\nв строке: &lt;code&gt;ini_set(&amp;quot;memory_limit&amp;quot;, &amp;quot;&lt;u&gt;64&lt;/u&gt;M&amp;quot;);&lt;/code&gt;&lt;br /&gt;\r\nзадайте свое значение в формате &amp;quot;&lt;b&gt;&lt;u&gt;n&lt;/u&gt;&lt;/b&gt;M&amp;quot; (где &lt;b&gt;&lt;u&gt;n&lt;/u&gt;&lt;/b&gt; - число Мегабайт)&lt;/span&gt;&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;p&gt;&lt;b&gt;Максимальный размер файлов для загрузки&lt;/b&gt; - Указывается максимально допустимый размер файлов для загрузки в одну операцию.&lt;br /&gt;\r\nКоличество файлов не имеет значение, только общий объем файлов не должен превышать заданный здесь параметр.&lt;/p&gt;\r\n\r\n&lt;blockquote class=&quot;quote quote-info&quot;&gt;\r\n&lt;p&gt;&lt;span style=&quot;font-size:14px&quot;&gt;Данный лимит при необходимости можно увелечить вручную.&lt;br /&gt;\r\nДля этого в файле: /.htaccess&lt;br /&gt;\r\nв строке: &lt;code&gt;php_value upload_max_filesize &lt;u&gt;32&lt;/u&gt;M&lt;/code&gt;&lt;br /&gt;\r\nзадайте свое значение в формате &lt;q&gt;&lt;b&gt;&lt;u&gt;n&lt;/u&gt;&lt;/b&gt;M&lt;/q&gt; (где &lt;b&gt;&lt;u&gt;n&lt;/u&gt;&lt;/b&gt; - число Мегабайт)&lt;/span&gt;&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;p&gt;&lt;b&gt;Максимальный размер постинга&lt;/b&gt; - Указывается максимально допустимый размер загрузки данных передаваемых через формы. Если данный параметр меньше параметра &amp;quot;максимальный размера файлов для загрузки&amp;quot;, то он применяется к тому, как приоритетный.&lt;/p&gt;\r\n\r\n&lt;blockquote class=&quot;quote quote-info&quot;&gt;\r\n&lt;p&gt;&lt;span style=&quot;font-size:14px&quot;&gt;Данный лимит при необходимости можно увелечить вручную.&lt;br /&gt;\r\nДля этого задайте свое значение в формате &lt;q&gt;&lt;b&gt;&lt;u&gt;n&lt;/u&gt;&lt;/b&gt;M&lt;/q&gt; (где &lt;b&gt;&lt;u&gt;n&lt;/u&gt;&lt;/b&gt; - число Мегабайт)&lt;br /&gt;\r\nв файле: /.htaccess&lt;br /&gt;\r\nв строке: &lt;code&gt;php_value post_max_size &lt;u&gt;8&lt;/u&gt;M&lt;/code&gt;&lt;/span&gt;&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;p&gt;&lt;b&gt;Максимальное время исполнение скрипта&lt;/b&gt; - Указывается время (в секундах) в течении которого сервер готов выполнять скрипт. Если время выполнение скрипта окажется больше указанного в данном параметре, сервер прервет выполнение скрипта и выдаст ошибку.&lt;/p&gt;\r\n\r\n&lt;blockquote class=&quot;quote quote-info&quot;&gt;\r\n&lt;p&gt;&lt;span style=&quot;font-size:14px&quot;&gt;Данный лимит при необходимости можно увелечить вручную.&lt;br /&gt;\r\nДля этого задайте свое значение в формате &lt;b&gt;n&lt;/b&gt; (где &lt;b&gt;n&lt;/b&gt; - число секунд)&lt;br /&gt;\r\nв файле: /roocms/config/set.cfg.php&lt;br /&gt;\r\nв строке: &lt;code&gt;ini_set(&amp;quot;max_execution_time&amp;quot;, &lt;u&gt;30&lt;/u&gt;);&lt;/code&gt;&lt;/span&gt;&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;p&gt;&lt;b&gt;Корневая директория сайта&lt;/b&gt; - Указывает полный (физический) путь на сервере до корня вашего сайта.&lt;/p&gt;\r\n', 1393713064)";
$sql['INSERT '.HELP_TABLE." ID #05"] = "INSERT INTO `".HELP_TABLE."` VALUES(7, 'userui', 1, 0, 1, 'Пользовательская часть', '&lt;p&gt;В данном разделе содержится информации о пользовательской части сайта и работе с ней.&lt;/p&gt;\r\n\r\n&lt;p&gt;В подразделах ниже, вы найдете всю необходимую информацию.&lt;/p&gt;\r\n', 1381388640)";
$sql['INSERT '.HELP_TABLE." ID #06"] = "INSERT INTO `".HELP_TABLE."` VALUES(8, 'acp_main', 3, 0, 6, 'Главный экран (сводка по сайту)', '&lt;p&gt;На главной странице панели управления отображается краткая сводка по сайту.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;b&gt;Текущая версия RooCMS&lt;/b&gt; - Указана версия системы управления которую вы используете в данный момент.&lt;/p&gt;\r\n\r\n&lt;blockquote class=&quot;quote-danger&quot;&gt;\r\n&lt;p&gt;Если вы видите яркое, выделенное красным цветом, сообщение: &amp;quot;&lt;code&gt;Инсталятор RooCMS находится в корне сайта. В целях безопасности следует удалить инсталятор!&lt;/code&gt;&amp;quot; это означает, что после установки или обновления системы управления сайтом, вы не удалили папку install из корня вашего сайта. На деле сразу после установки RooCMS доступ к скриптам инсталяторам ограничивается паролем администратора и злоумышленник не сможет воспользоваться процедурой установки/обновления, что бы повредить ваш сайт. Но мы все же рекомендует в целях повышения безопасности удалить папку install сразу после установки или обновления сайта.&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;blockquote class=&quot;quote-success&quot;&gt;\r\n&lt;p&gt;На этом экране так же вы можете увидеть объявление, в случае если вышла новая редакция cms. Мы рекомендуем всегда использовать последнию версию платформы, поскольку с выходом каждой новой версии увеличивается число функций и опций системы управления сайтом.&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n', 1393712853)";
$sql['INSERT '.HELP_TABLE." ID #07"] = "INSERT INTO `".HELP_TABLE."` VALUES(9, 'uiblocks', 15, 0, 0, 'Использование блоков', '&lt;p&gt;Что бы использовать блоки в пользовательской части сайт, вам необходмо в нужном месте (непосредственно в шаблонах) разместить код: &lt;code&gt;&#123;&#36;blocks-&gt;load(&amp;#39;n&amp;#39;)&#125;&lt;/code&gt; вместо &lt;code&gt;n&lt;/code&gt; указав или &lt;kbd&gt;alias&lt;/kbd&gt; блока или &lt;kbd&gt;id&lt;/kbd&gt; блока.&lt;/p&gt;\r\n\r\n&lt;p&gt;Блоки можно использовать и непостредственно через Панель Управления при создании html или php страниц.&lt;br /&gt;\r\nЧто бы вызвать блок внутри вашей html страницы нужно сделать тоже самое, что и в шаблонах - вставить текст: &lt;code&gt;&#123;&#36;blocks-&gt;load(&amp;#39;n&amp;#39;)&#125;&lt;/code&gt; вместо &lt;code&gt;n&lt;/code&gt; указав или &lt;kbd&gt;alias&lt;/kbd&gt; блока или &lt;kbd&gt;id&lt;/kbd&gt; блока.&lt;/p&gt;\r\n\r\n&lt;p&gt;Для вызова блока внутри php страницы используйте код: &lt;code&gt;&#36;blocks-&gt;load(&amp;#39;n&amp;#39;);&lt;/code&gt; вместо &lt;code&gt;n&lt;/code&gt; укаывая &lt;kbd&gt;id&lt;/kbd&gt; или &lt;kbd&gt;alias&lt;/kbd&gt; блока.&lt;/p&gt;\r\n\r\n&lt;blockquote class=&quot;quote quote-info&quot;&gt;&lt;small&gt;Примечение: id или alias блока вы можете в увидеть в списке блоков в разделе панели управления &amp;quot;Блоки&amp;quot;&lt;/small&gt; &lt;small&gt;Внимание! Можно использовать кавычки &amp;quot; или &amp;#39;. А можно и без кавычек. Но только не в PHP.&lt;/small&gt;&lt;/blockquote&gt;\r\n', 1393712708)";
$sql['INSERT '.HELP_TABLE." ID #08"] = "INSERT INTO `".HELP_TABLE."` VALUES(10, 'acp_phpext', 8, 2, 0, 'PHP расширения', '&lt;p&gt;На этой странице представлен список PHP расширений, которые установлены на вашем сервере.&lt;/p&gt;\r\n\r\n&lt;p&gt;Зеленым цветом обозначены критические для работы расширения. Орнажевым цветом обозначены полезные, но не критические расширения.&lt;/p&gt;\r\n\r\n&lt;p&gt;Переключаяюсь между вкладками с заголовками расширений, вы можете просмотреть список функций расширения.&lt;/p&gt;\r\n', 1381532652)";
$sql['INSERT '.HELP_TABLE." ID #09"] = "INSERT INTO `".HELP_TABLE."` VALUES(11, 'acp_inivars', 8, 4, 0, 'PHP переменные', '&lt;p&gt;На данной странице приведена таблица с значениями настроек языка PHP.&lt;/p&gt;\r\n\r\n&lt;p&gt;В первой колонке приведено название параметра. Во второй текущее значение. В третьей значение настроек PHP по-умолчанию. В четвертой колонке обозначен доступ к параметру.&lt;/p&gt;\r\n\r\n&lt;p&gt;Если строка с параметром выделена зеленым цветом, значит текущие настройки действующие на сайт, отличаются от общих настроек PHP выставленных по-умолчанию администратором вашего сервера.&lt;/p&gt;\r\n\r\n&lt;p&gt;Узнать о значении тех или иных параметров и их влияния на работу сервера, можно из документации по PHP.&lt;/p&gt;\r\n\r\n&lt;h3&gt;Немного о доступе к настройкам&lt;/h3&gt;\r\n\r\n&lt;p&gt;Если в строке доступ обозначен &amp;quot;только &lt;code&gt;php.ini&lt;/code&gt; или &lt;code&gt;httpd.conf&lt;/code&gt;&amp;quot; это означает, что вы не сможете изменить значение данного параметра, если у вас нет доступ администратора сервера.&lt;/p&gt;\r\n\r\n&lt;p&gt;Некоторые параметры можно изменять через файл &lt;code&gt;.htaccess&lt;/code&gt; в колонке &amp;quot;доступ&amp;quot; есть соответсвующая пометка. Подробнее узнать об этом вы сможете в инструкциях по htaccess. Ищите описание команд php_value и php_flag&lt;/p&gt;\r\n\r\n&lt;p&gt;Если у строки в колонке &amp;quot;доступ&amp;quot; указано &lt;b&gt;полный доступ&lt;/b&gt;, это означает, что вы сможете влиять на данный параметр и с помошью команды PHP ini_set(); Более подробно о данной команде, вы сможете узнать в документации по языку PHP.&lt;/p&gt;\r\n', 1381530131)";
$sql['INSERT '.HELP_TABLE." ID #10"] = "INSERT INTO `".HELP_TABLE."` VALUES(12, 'acp_fileinfo', 8, 5, 0, 'Файлы и форматы', '&lt;p&gt;На данной странице предоставленны допустимые для загрузки форматы файлов. А так же указаны максимальные размеры файлов и постов для загрузки. (&lt;small&gt;Подробнее об этом можно узнать в разделе помощи описывающем &quot;Информация о сервере&quot;.&lt;/small&gt;)&lt;/p&gt;\r\n\r\n&lt;p&gt;При загрузке файлов через RooCMS все они проверяются не только по расширению, но и по типу файла. Узнать допустимые типы файлов можно наведя мышь на иконку расположенную рядом с расширением иконку.&lt;/p&gt;\r\n', 1393713261)";
$sql['INSERT '.HELP_TABLE." ID #11"] = "INSERT INTO `".HELP_TABLE."` VALUES(13, 'acp_license', 8, 6, 0, 'Лицензия RooCMS', '&lt;p&gt;На &lt;a href=&quot;http://www.roocms.com/index.php?page=license&quot; target=&quot;_blank&quot;&gt;данной странице &lt;sup&gt;&lt;i class=&quot;fa fa-external-link fa-fw&quot;&gt;&lt;/i&gt;&lt;/sup&gt;&lt;/a&gt; приведен полный текст лицензии на русском языке.&lt;/p&gt;\r\n\r\n&lt;blockquote class=&quot;quote quote-warning&quot;&gt;&lt;small&gt;Помните, что настоящий перевод Стандартной Общественной Лицензии GNU на русский язык не является официальным. Он не опубликован Фондом Свободного Программного Обеспечения и не устанавливает имеющих юридическую силу условий для распространения программного обеспечения, которое распространяется на условиях Стандартной Общественной Лицензии GNU. Условия, имеющие юридическую силу, закреплены исключительно в аутентичном тексте Стандартной Общественной Лицензии GNU на английском языке. Мы надеемся, что настоящий перевод поможет русскоязычным пользователям лучше понять содержание Стандартной Общественной Лицензии GNU.&lt;/small&gt;&lt;/blockquote&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;http://gplv3.fsf.org/&quot; rel=&quot;nofollow&quot; target=&quot;_blank&quot;&gt;Оригинальный текст лицензии &lt;sup&gt;&lt;i class=&quot;fa fa-external-link fa-fw&quot;&gt;&lt;/i&gt;&lt;/sup&gt;&lt;/a&gt;&lt;/p&gt;\r\n', 1393237620)";
$sql['INSERT '.HELP_TABLE." ID #12"] = "INSERT INTO `".HELP_TABLE."` VALUES(14, 'faq', 1, 99, 1, 'Частые вопросы', '&lt;p&gt;В данном разделе собрана информация не нашедшая места в других разделах, но в тоже время вызывающая частые вопросы пользователей, связанные с RooCMS.&lt;/p&gt;\r\n\r\n&lt;p&gt;Ниже приведен список наиболее часто задаваемых вопросов.&lt;/p&gt;\r\n', 1381441610)";
$sql['INSERT '.HELP_TABLE." ID #13"] = "INSERT INTO `".HELP_TABLE."` VALUES(15, 'uitemplates', 7, 2, 1, 'Работа с шаблонами', '&lt;p&gt;При работе с шаблонами сайта используется html и Smarty. Подробное руководство по использованию Smarty можно найти на сайте &lt;a href=&quot;http://smarty.net/&quot; rel=&quot;nofollow&quot; target=&quot;_blank&quot;&gt;официальном сайте&lt;/a&gt;.&lt;/p&gt;\r\n\r\n&lt;p&gt;Ниже в подразделах приведена информация, об использовании различных алгоритмов RooCMS используемых повсеместно в шаблонах.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n', 1382798199)";
$sql['INSERT '.HELP_TABLE." ID #14"] = "INSERT INTO `".HELP_TABLE."` VALUES(16, 'phpinfo', 8, 3, 0, 'PHP Info', '&lt;p&gt;На данной странице показана информация о PHP.&lt;/p&gt;\r\n\r\n&lt;p&gt;В эту информацию включено все касательно PHP и его расширений, включая авторов и разработчиков PHP,&lt;/p&gt;\r\n\r\n&lt;p&gt;Информация на этой странице собирается стандартной функцией языка &lt;code&gt;phpinfo();&lt;/code&gt;&lt;/p&gt;\r\n', 1382920506)";
$sql['INSERT '.HELP_TABLE." ID #15"] = "INSERT INTO `".HELP_TABLE."` VALUES(17, 'acp_structure_create', 2, 0, 0, 'Создание новой страницы', '&lt;p&gt;Что бы создать новую страницу сайта, необходимо нажать соответствующую ссылку в разделе &amp;quot;Структура&amp;quot;, после этого вы уведите форму для создания.&lt;/p&gt;\r\n\r\n&lt;p&gt;Форма имеет несколько полей, которые вам необходимо заполнить:&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;b&gt;Название страницы&lt;/b&gt; - необходимо указать название страницы. Оно будет использовано в мета теге title. Это поле является обязательным и влияет на поисковую оптимизацию.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;b&gt;Alias страницы&lt;/b&gt; - это уникальное имя страницы в структуре сайта. Оно используется в адресе страницы, а также служит идентификатором страницы. Поле может содержать кирилические символы. Если оставить поле пустым, alias будет сформирован автоматически.&lt;/p&gt;\r\n', 1382921507)";
$sql['INSERT '.HELP_TABLE." ID #16"] = "INSERT INTO `".HELP_TABLE."` VALUES(18, 'dev', 1, 5, 1, 'Разработчику', 'В разделе собрана информация, могущая быть полезной для разработчиков.', 1383981532)";
$sql['INSERT '.HELP_TABLE." ID #17"] = "INSERT INTO `".HELP_TABLE."` VALUES(19, 'dev_api', 18, 1, 4, 'API', '&lt;p&gt;Интерфейс программирования в пределах системы управления сайтом.&lt;/p&gt;\r\n\r\n&lt;p&gt;В разделе описан набор готовых классов, функций, и констант, предоставляемых RooCMS для использования в написании собственных программируемых модулей.&lt;/p&gt;\r\n', 1383981751)";
$sql['INSERT '.HELP_TABLE." ID #18"] = "INSERT INTO `".HELP_TABLE."` VALUES(20, 'randcode', 19, 0, 0, 'Генератор случайных кодов', '&lt;p&gt;Генератор случайных кодов&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;randcode(&#36;ns, &#36;symbols=&amp;quot;ABCEFHKLMNPRSTVXYZ123456789&amp;quot;);&lt;/code&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;&#36;ns&lt;/code&gt; - &lt;small&gt;[обязательное][целое число]&lt;/small&gt; задает длину кода&lt;br /&gt;\r\n&lt;code&gt;&#36;symbols&lt;/code&gt; - &lt;small&gt;[строка]&lt;/small&gt; в строке указываются символы, которые будут использоваться в коде. Регистр учитывается.&lt;/p&gt;\r\n\r\n&lt;blockquote&gt;&lt;b&gt;Пример 1:&lt;/b&gt;&lt;br /&gt;\r\nСгенерировать случайный код длиной 10 символов из цифр&lt;br /&gt;\r\n&lt;code&gt;&#36;code = randcode(10, &amp;quot;0123456789&amp;quot;);&lt;/code&gt;&lt;br /&gt;\r\n&lt;code&gt;echo &#36;code;&lt;/code&gt;&lt;br /&gt;\r\nВозможный результат:&lt;br /&gt;\r\n&lt;code&gt;7463883201&lt;/code&gt;&lt;/blockquote&gt;\r\n\r\n&lt;blockquote&gt;&lt;b&gt;Пример 2:&lt;/b&gt;&lt;br /&gt;\r\nСгенерировать случайный код длиной 6 символов из символов XYZ&lt;br /&gt;\r\n&lt;code&gt;&#36;code = randcode(6, &amp;quot;XYZ&amp;quot;);&lt;/code&gt;&lt;br /&gt;\r\n&lt;code&gt;echo &#36;code;&lt;/code&gt;&lt;br /&gt;\r\nВозможный результат:&lt;br /&gt;\r\n&lt;code&gt;ZZYXYZ&lt;/code&gt;&lt;/blockquote&gt;\r\n', 1384649861)";
$sql['INSERT '.HELP_TABLE." ID #19"] = "INSERT INTO `".HELP_TABLE."` VALUES(21, 'faq_redirect', 14, 0, 0, 'Перенаправление', '&lt;p&gt;Коды этого класса сообщают клиенту, что для успешного выполнения операции необходимо сделать другой запрос, как правило, по другому URI. Из данного класса пять кодов 301, 302, 303, 305 и 307 относятся непосредственно к перенаправлениям. Адрес, по которому клиенту следует произвести запрос, сервер указывает в заголовке Location. При этом допускается использование фрагментов в целевом URI.&lt;/p&gt;\r\n\r\n&lt;p&gt;По последним стандартам клиент может производить перенаправление без запроса пользователя только если второй ресурс будет запрашиваться методом GET или HEAD[6]. В предыдущих спецификациях говорилось, что для избежания круговых переходов пользователя следует спрашивать после 5-го подряд перенаправления[14]. При всех перенаправлениях, если метод запроса был не HEAD, то в тело ответа следует включить короткое гипертекстовое сообщение с целевым адресом, чтобы в случае ошибки пользователь смог сам произвести переход.&lt;/p&gt;\r\n\r\n&lt;p&gt;Разработчики HTTP отмечают, что многие клиенты при перенаправлениях с кодами 301 и 302 ошибочно применяют метод GET ко второму ресурсу, несмотря на то, что к первому запрос был с иным методом (чаще всего PUT). Чтобы избежать недоразумений, в версии HTTP/1.1 были введены коды 303 и 307 и их рекомендовано использовать вместо 302. Изменять метод нужно только если сервер ответил 303. В остальных случаях следующий запрос производить с исходным методом.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;b&gt;300 Multiple Choices&lt;/b&gt; &amp;mdash; по указанному URI существует несколько вариантов предоставления ресурса по типу MIME, по языку или по другим характеристикам. Сервер передаёт с сообщением список альтернатив, давая возможность сделать выбор клиенту автоматически или пользователю. Появился в HTTP/1.0.&lt;br /&gt;\r\n&lt;b&gt;301 Moved Permanently&lt;/b&gt; &amp;mdash; запрошенный документ был окончательно перенесен на новый URI, указанный в поле Location заголовка. Некоторые клиенты некорректно ведут себя при обработке данного кода. Появился в HTTP/1.0.&lt;br /&gt;\r\n&lt;b&gt;302 Found&lt;/b&gt; &amp;mdash; запрошенный документ временно доступен по другому URI, указанному в заголовке в поле Location. Этот код может быть использован, например, при управляемом сервером согласовании содержимого. Некоторые клиенты некорректно ведут себя при обработке данного кода. Введено в HTTP/1.0.&lt;br /&gt;\r\n&lt;b&gt;303 See Other&lt;/b&gt; &amp;mdash; документ по запрошенному URI нужно запросить по адресу в поле Location заголовка с использованием метода GET несмотря даже на то, что первый запрашивался иным методом. Этот код был введён вместе с 307-ым для избежания неоднозначности, чтобы сервер был уверен, что следующий ресурс будет запрошен методом GET. Например, на веб-странице есть поле ввода текста для быстрого перехода и поиска. После ввода данных браузер делает запрос методом POST, включая в тело сообщения введённый текст. Если обнаружен документ с введённым названием, то сервер отвечает кодом 303, указав в заголовке Location его постоянный адрес. Тогда браузер гарантировано его запросит методом GET для получения содержимого. В противном случае сервер просто вернёт клиенту страницу с результатами поиска. Введено в HTTP/1.1.&lt;br /&gt;\r\n&lt;b&gt;304 Not Modified&lt;/b&gt; &amp;mdash; сервер возвращает такой код, если клиент запросил документ методом GET, использовал заголовок If-Modified-Since или If-None-Match и документ не изменился с указанного момента. При этом сообщение сервера не должно содержать тела. Появился в HTTP/1.0.&lt;br /&gt;\r\n&lt;b&gt;305 Use Proxy&lt;/b&gt; &amp;mdash; запрос к запрашиваемому ресурсу должен осуществляться через прокси-сервер, URI которого указан в поле Location заголовка. Данный код ответа могут использовать только исходные HTTP-сервера (не прокси). Введено в HTTP/1.1.&lt;br /&gt;\r\n&lt;b&gt;306 (зарезервировано) &lt;/b&gt;&amp;mdash; использовавшийся раньше код ответа, в настоящий момент зарезервирован. Упомянут в RFC 2616 (обновление HTTP/1.1).&lt;br /&gt;\r\n&lt;b&gt;307 Temporary Redirect&lt;/b&gt; &amp;mdash; запрашиваемый ресурс на короткое время доступен по другому URI, указанный в поле Location заголовка. Этот код был введён вместе с 303 вместо 302-го для избежания неоднозначности. Введено в RFC 2616 (обновление HTTP/1.1).&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;small&gt;Источник Wikipedia&lt;/small&gt;&lt;/p&gt;\r\n', 1383985386)";
$sql['INSERT '.HELP_TABLE." ID #20"] = "INSERT INTO `".HELP_TABLE."` VALUES(22, 'go', 19, 0, 0, 'Переадресация', '&lt;h4&gt;Переадресация на заданную страницу&lt;/h4&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;go(&#36;address, &#36;code=301);&lt;/code&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;&#36;address&lt;/code&gt; - &lt;small&gt;[обязательное][строка]&lt;/small&gt; целевой адрес перенаправления. Указывать в формате QUERY_STRING&lt;br /&gt;\r\n&lt;code&gt;&#36;code&lt;/code&gt; - &lt;small&gt;[число]&lt;/small&gt; код переадресации.&lt;/p&gt;\r\n\r\n&lt;blockquote class=&quot;quote-info&quot;&gt;\r\n&lt;p&gt;Больше о кодах переадресации можно узнать из раздела помощи, в подразделе &amp;quot;Частые вопросы &amp;rarr; Перенаправления&amp;quot;&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;blockquote&gt;&lt;b&gt;Пример 1:&lt;/b&gt;&lt;br /&gt;\r\nОтправить посетителя на главную страницу&lt;br /&gt;\r\n&lt;code&gt;go(&amp;quot;index.php&amp;quot;);&lt;/code&gt;&lt;/blockquote&gt;\r\n\r\n&lt;blockquote&gt;\r\n&lt;p&gt;&lt;b&gt;Пример 2: &lt;/b&gt;&lt;br /&gt;\r\nУказать временную переадресацию&lt;br /&gt;\r\n&lt;code&gt;go(&amp;quot;index.php?page=newpage&amp;quot;, 303);&lt;/code&gt;&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;hr /&gt;\r\n&lt;h4&gt;Переадресация обратно&lt;/h4&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;goback();&lt;/code&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Данная функция может быть полезна при обработке ошибок или форм, а так же при обработке функций, после которых посетителя надо перенаправить обратно на страницу, с которой он запустил функцию.&lt;/p&gt;\r\n\r\n&lt;blockquote&gt;\r\n&lt;p&gt;&lt;b&gt;Пример 1:&lt;/b&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;if(&#36;result) go(&amp;quot;index.php&amp;quot;);&lt;/code&gt;&lt;br /&gt;\r\n&lt;code&gt;else goback();&lt;/code&gt;&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n', 1393717957)";
$sql['INSERT '.HELP_TABLE." ID #21"] = "INSERT INTO `".HELP_TABLE."` VALUES(23, 'sendmail', 19, 0, 0, 'Отправка почтовых сообщений', '&lt;div class=&quot;col-md-12&quot;&gt;\r\n&lt;p&gt;Функция отправки почтовых сообщений&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;sendmail(&#36;mail, &#36;theme, &#36;message, &#36;from=&amp;quot;robot&amp;quot;);&lt;/code&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;&#36;mail&lt;/code&gt; - &lt;small&gt;[обязательное][строка]&lt;/small&gt; полный почтовый адрес получателя. Например: user@roocms.com&lt;br /&gt;\r\n&lt;code&gt;&#36;theme&lt;/code&gt;- &lt;small&gt;[обязательное][строка]&lt;/small&gt; Тема письма.&lt;br /&gt;\r\n&lt;code&gt;&#36;message&lt;/code&gt; - &lt;small&gt;[обязательное][строка]&lt;/small&gt; Текст сообщения.&lt;br /&gt;\r\n&lt;code&gt;&#36;from&lt;/code&gt; - &lt;small&gt;[строка]&lt;/small&gt; почтовый адрес отправителя. Например webmaster@roocms.com&lt;/p&gt;\r\n\r\n&lt;blockquote&gt;&lt;b&gt;Пример 1:&lt;/b&gt;&lt;br /&gt;\r\nОтправить письмо посетителю сайта&lt;br /&gt;\r\n&lt;code&gt;sendmail(&amp;quot;user@roocms.com&amp;quot;,&amp;quot;Тема письма&amp;quot;,&amp;quot;Сообщение для посетителя сайта&amp;quot;,&amp;quot;roosso@roocms.com&amp;quot;);&lt;/code&gt;&lt;/blockquote&gt;\r\n&lt;/div&gt;\r\n', 1391827555)";
$sql['INSERT '.HELP_TABLE." ID #22"] = "INSERT INTO `".HELP_TABLE."` VALUES(25, 'debug', 19, 0, 0, 'Вызов отладки (Режим отладки)', '&lt;h3&gt;Режим отладки&lt;/h3&gt;\r\n\r\n&lt;p&gt;Во время работы над скриптами, разработчикам часто требуются функции отладки.&lt;/p&gt;\r\n\r\n&lt;p&gt;Что бы включить отладку скриптов и вывод ошибок, необходимо в файле: &lt;code&gt;/roocms/config/set.cfg.php&lt;/code&gt; изменить значение константы &lt;kbd&gt;DEBUGMODE&lt;/kbd&gt; на &lt;code&gt;true&lt;/code&gt; &lt;small&gt;(строка: 57)&lt;/small&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;По умолчанию отладка выключена.&lt;/p&gt;\r\n\r\n&lt;blockquote&gt;\r\n&lt;p&gt;&lt;b&gt;Пример включенного режима отладки:&lt;/b&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;define(&amp;#39;DEBUGMODE&amp;#39;,&amp;nbsp;&amp;nbsp; &amp;nbsp;true);&lt;/code&gt;&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;blockquote&gt;\r\n&lt;p&gt;&lt;b&gt;Пример выключенного режима отладки:&lt;/b&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;define(&amp;#39;DEBUGMODE&amp;#39;,&amp;nbsp;&amp;nbsp;&amp;nbsp; false);&lt;/code&gt;&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;blockquote class=&quot;quote-danger&quot;&gt;\r\n&lt;p&gt;Не забывайте выключать режим отладки, перед загрузкой скриптов и дампов на работающий сайт. Иначе потенциальный злоумышленник может искусственно вызвав ошибку, получить какую либо информацию о вашем сайте или сервере.&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;p&gt;При включенном режиме отладки, в случае возникновения ошибок в скриптах, вам будет показано уведомление об ошибке, с указанием типа ошибка, и её расположения в файле. Но помните, что ошибки так же будут видеть и все посетители сайта. Это сделано умышленно, что бы программисты и администраторы могли видеть сайт и работу его функций с позиции обычных посетителей и проводить отладку.&lt;/p&gt;\r\n\r\n&lt;p&gt;При выключенном режиме отладки все возможные ошибки и сбои не показываются никому, но при этом они записываются в специальные лог файлы. Все ошибки которые интерпретатор PHP может обработать и продолжать работу (некретические ошибки и уведомления), записываются в файл &lt;code&gt;/cache/logs/errors.log&lt;/code&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Критический ошибки, приводящие к остановке скриптов и работы с сайтом, записываются в файл &lt;code&gt;/cache/logs/php_error.log&lt;/code&gt; Если режим отладки выключен, то посетителю будет показана страница-заглушка (в случае если сервер может обратится к ней и выполнить её вывод. Некоторые критические ошибки не позволят этого сделать, и посетитель увидит стандартное сообщение об ошибке.&lt;/p&gt;\r\n\r\n&lt;blockquote class=&quot;quote-info&quot;&gt;\r\n&lt;p&gt;Доступ к этим файлам вы можете получить только через ftp.&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;h3&gt;Отладка переменных (массивов, констант и т.д. и т.п.)&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;debug(&#36;obj, &#36;expand=false);&lt;/code&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;&#36;obj&lt;/code&gt; - &lt;small&gt;[обязательное][переменная, массив, объект, константа]&lt;/small&gt; объект отладки&lt;br /&gt;\r\n&lt;code&gt;&#36;expand&lt;/code&gt; - &lt;small&gt;[булево]&lt;/small&gt; Значение &lt;code&gt;true&lt;/code&gt; выведет самые подробные результаты отладки, что требуется крайне редко.&lt;/p&gt;\r\n\r\n&lt;blockquote&gt;\r\n&lt;p&gt;&lt;b&gt;Пример 1:&lt;/b&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;&#36;r = array(1,2,3,5,4);&lt;/code&gt;&lt;br /&gt;\r\n&lt;code&gt;debug(&#36;r);&lt;/code&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Результат:&lt;/p&gt;\r\n&lt;code&gt;Array ( &lt;/code&gt;&lt;br /&gt;\r\n&lt;code&gt;[0] =&gt; 1&lt;/code&gt;&lt;br /&gt;\r\n&lt;code&gt;[1] =&gt; 2 &lt;/code&gt;&lt;br /&gt;\r\n&lt;code&gt;[2] =&gt; 3&lt;/code&gt;&lt;br /&gt;\r\n&lt;code&gt;[3] =&gt; 5&lt;/code&gt;&lt;br /&gt;\r\n&lt;code&gt;[4] =&gt; 4 &lt;/code&gt;&lt;br /&gt;\r\n&lt;code&gt;)&lt;/code&gt;&lt;br /&gt;\r\n&lt;small&gt;Результать отладки вы увидете внизу страницы. Это сделано специально, что бы не &amp;quot;рвать&amp;quot; шаблон во время отладки&lt;/small&gt;&lt;/blockquote&gt;\r\n\r\n&lt;blockquote&gt;\r\n&lt;p&gt;&lt;b&gt;Пример 2:&lt;/b&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;code&gt;&#36;r = array(1,2,3,5,4);&lt;/code&gt;&lt;br /&gt;\r\n&lt;code&gt;debug(&#36;r, true);&lt;/code&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Результат:&lt;/p&gt;\r\n\r\n&lt;p&gt;Попробуйте сами, что бы увидеть ;)&lt;/p&gt;\r\n&lt;/blockquote&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n', 1393716741)";


?>