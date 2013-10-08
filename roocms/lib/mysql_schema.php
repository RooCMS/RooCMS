<?php
/**
* @package      RooCMS
* @subpackage	Library
* @subpackage	MySQL Schema
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.0
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*	RooCMS - Russian free content managment system
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
*   along with this program.  If not, see <http://www.gnu.org/licenses/
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
								  `type` enum('component','mod','widget') NOT NULL DEFAULT 'component',
								  `sort` int(10) unsigned NOT NULL DEFAULT '1',
								  `name` varchar(255) NOT NULL,
								  `title` varchar(255) NOT NULL,
								  `ico` varchar(255) NOT NULL,
								  UNIQUE KEY `id` (`id`),
								  UNIQUE KEY `name` (`name`)
								) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";
$id = 1;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` (`id`, `type`, `sort`, `name`, `title`, `ico`) VALUES (".$id.", 'component', 1, 'global', 'Основные настройки', 'cog')";			$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` (`id`, `type`, `sort`, `name`, `title`, `ico`) VALUES (".$id.", 'component', 2, 'gd', 'Обработка изображений', 'picture')";		$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` (`id`, `type`, `sort`, `name`, `title`, `ico`) VALUES (".$id.", 'component', 5, 'cp', 'Панель Администратора', 'unlock-alt')";	$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` (`id`, `type`, `sort`, `name`, `title`, `ico`) VALUES (".$id.", 'component', 4, 'tpl', 'Настройки шаблонизации', 'desktop')";	$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` (`id`, `type`, `sort`, `name`, `title`, `ico`) VALUES (".$id.", 'component', 3, 'rss', 'RSS', 'rss')";							$id++;


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
								  `default` text NOT NULL,
								  UNIQUE KEY `id` (`id`),
								  UNIQUE KEY `option` (`option_name`),
								  KEY `part` (`part`)
								) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";
$id = 1;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'global', 1, 'Название сайта', 'Глобальный заголовок сайта', 'site_title', 'string', '', '', '')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'global', 2, 'Глобальный заголовок', 'Применять название сайта глобально ко всем заголовкам?', 'global_site_title', 'boolean', '', 'true', 'true')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'global', 3, 'Мета описание', 'Глобальное мета описание сайта', 'meta_description', 'string', '', '', '')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'global', 4, 'Мета ключевые слова', 'Глобальные ключевые слова для сайта', 'meta_keywords', 'string', '', '', '')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'global', 5, 'Заголовок 304', 'Опция включает/выключает ответ заголовка с кодом 304 на запрос IF_MODIFED_SINCE от поисковых роботов там где это разрешено.\r\nВо включенном состоянии опция позволит поисковым роботам быстрее индексировать ваш сайт и ускоряет работу шаблонизатора.', 'if_modifed_since', 'boolean', '', 'false', 'false')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 1, 'Максимальная ширина изображений', 'Укажите максимальную ширину загружаемых изображений в пикселях. \r\nВ случае если изображение окажется больше указанной ширины, оно будет пропорционально уменьшено', 'gd_image_maxwidth', 'int', '', '900', '900')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 2, 'Максимальная высота изображений', 'Укажите максимальную ширину загружаемых изображений в пикселях. \r\nВ случае если изображение окажется больше указанной ширины, оно будет пропорционально уменьшено', 'gd_image_maxheight', 'int', '', '900', '900')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 3, 'Ширина миниатюры', 'Укажите размер миниатюры изображения по горизонтали (в пикселях)', 'gd_thumb_image_width', 'int', '', '100', '100')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 4, 'Высота миниатюры', 'Укажите размер миниатюры изображения по вертикали(в пикселях)', 'gd_thumb_image_height', 'int', '', '100', '100')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 5, 'Тип миниатюры', 'Выберите алгоритм генерации миниатюр. \r\nЗаполнение - полностью заполнит миниатюру.\r\nПо размеру - пропорции изображения будут вписаны в пропорции миниатюры.\r\n', 'gd_thumb_type_gen', 'select', 'Заполнение|fill\r\nПо размеру|size', 'fill', 'fill')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 6, 'Цвет фона миниатюры', 'Данный параметр устанавливает цвет фона для миниатюр, если вы выбрали тип генерации &quot;по размеру&quot;', 'gd_thumb_bgcolor', 'color', '', '#ffffff', '#ffffff')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 7, 'Качество миниатюр', 'Укажите качество создаваемых миниатюр от 1 до 100 \r\nОпция применима только для jpg миниатюр.', 'gd_thumb_jpg_quality', 'int', '', '90', '90')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 8, 'Вкл/выкл водяной знак', 'Использовать на загружаемых изображениях Watermark (полупрозрачный копирайт) для защиты изображений от копирования на сторонние ресурсы?', 'gd_use_watermark', 'boolean', '', 'true', 'true')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 9, 'Первая строка водяного знака', 'Первая строчка водяного знака накладываемого на изображение', 'gd_watermark_string_one', 'string', '', '', '')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 10, 'Вторая строка водяного знака', 'Вторая строчка водяного знака накладываемого на изображение', 'gd_watermark_string_two', 'string', '', 'http://".$_SERVER['SERVER_NAME']."', 'http://".$_SERVER['SERVER_NAME']."')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'cp', 1, 'Вход в панель управления', 'Укажите название файла (скрипта) через который вы будете заходить в Панель Управления.\r\nВнимание! После изменения этой настройки, изменится URI панели управления. В случае если вы изменяли вручную шаблоны панели управления, проверьте, что вы везде указали переменную {&#36SCRIPT_NAME}', 'cp_script', 'string', '', 'acp.php', 'acp.php')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'cp', 2, 'E-mail администратора', 'Укажите адрес электронной почты администратора Он будет использоваться для системных уведомлений.', 'cp_email', 'email', '', '".$site['sysemail']."', '".$site['sysemail']."')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'tpl', 1, 'Вкл/выкл режим отладки шаблонов', 'Опцпия активирует принудительную перекомпиляцию шаблонов при каждом вызове.\r\nНикогда не используйте это действие в условиях реальной эксплуатации', 'tpl_recompile_force', 'boolean', '', 'false', 'false')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'rss', 1, 'TTL', 'Время жизни фида в секундах', 'rss_ttl', 'int', '', '240', '240')"; $id++;


/**
* Таблица страктуры сайта
*/
$sql['DROP '.STRUCTURE_TABLE] = "DROP TABLE IF EXISTS `".STRUCTURE_TABLE."`";
$sql['CREATE'.STRUCTURE_TABLE] = "CREATE TABLE IF NOT EXISTS `".STRUCTURE_TABLE."` (
									  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
									  `alias` varchar(255) NOT NULL,
									  `parent_id` int(10) unsigned NOT NULL,
									  `sort` int(10) unsigned NOT NULL DEFAULT '0',
									  `title` varchar(255) NOT NULL,
									  `meta_description` varchar(255) NOT NULL,
									  `meta_keywords` varchar(255) NOT NULL,
                                      `noindex` enum('0','1') NOT NULL DEFAULT '0',
									  `type` enum('html','php','feed') NOT NULL DEFAULT 'html',
									  `childs` int(10) unsigned NOT NULL DEFAULT '0',
									  `page_id` int(10) unsigned NOT NULL DEFAULT '0',
									  `date_create` int(30) unsigned NOT NULL DEFAULT '0',
									  `date_modified` int(30) unsigned NOT NULL DEFAULT '0',
									  `rss` enum('0','1') NOT NULL DEFAULT '1',
									  `items_per_page` int(10) unsigned NOT NULL DEFAULT '10',
									  `items` int(10) unsigned NOT NULL DEFAULT '0',
									  PRIMARY KEY (`id`),
									  UNIQUE KEY `alias` (`alias`),
									  KEY `type` (`type`),
									  KEY `page_id` (`page_id`)
									) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";

$sql['INSERT '.STRUCTURE_TABLE." ID #".$id] = "INSERT INTO `".STRUCTURE_TABLE."` (`id`, `alias`, `parent_id`, `sort`, `title`, `meta_description`, `meta_keywords`, `noindex`, `type`, `childs`, `page_id`, `date_create`, `date_modified`, `rss`, `items_per_page`, `items`)
												VALUES (1, 'index', 0, 0, 'Главная страница', '', '', '0','html', 3, 1, ".time().", ".time().", '1', 10, 0)";

/**
* HTML страницы
*/
$sql['DROP '.PAGES_HTML_TABLE] = "DROP TABLE IF EXISTS `".PAGES_HTML_TABLE."`";
$sql['CREATE'.PAGES_HTML_TABLE] = "CREATE TABLE IF NOT EXISTS `".PAGES_HTML_TABLE."` (
									  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
									  `sid` int(10) unsigned NOT NULL,
									  `content` longtext NOT NULL,
									  `date_modified` int(30) NOT NULL,
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
									  `date_modified` int(30) NOT NULL,
									  PRIMARY KEY (`id`),
									  UNIQUE KEY `sid` (`sid`)
									) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";


/**
* Ленты
*/
$sql['DROP '.PAGES_FEED_TABLE] = "DROP TABLE IF EXISTS `".PAGES_FEED_TABLE."`";
$sql['CREATE'.PAGES_FEED_TABLE] = "CREATE TABLE IF NOT EXISTS `".PAGES_FEED_TABLE."` (
									  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
									  `sid` int(10) unsigned NOT NULL,
									  `date_create` int(30) unsigned NOT NULL,
									  `date_update` int(30) unsigned NOT NULL,
									  `date_publications` int(30) unsigned NOT NULL,
									  `title` varchar(255) NOT NULL,
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
									  `alias` varchar(255) NOT NULL,
									  `type` enum('html','php') NOT NULL DEFAULT 'html',
									  `title` varchar(255) NOT NULL,
									  `content` longtext NOT NULL,
									  `date_create` int(30) unsigned NOT NULL DEFAULT '0',
									  `date_modified` int(30) unsigned NOT NULL DEFAULT '0',
									  PRIMARY KEY (`id`),
									  UNIQUE KEY `alias` (`alias`)
									) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";

$sql['INSERT '.BLOCKS_TABLE." ID #".$id] = "INSERT INTO `".BLOCKS_TABLE."` (`id`, `alias`, `type`, `title`, `content`, `date_create`, `date_modified`)
											VALUES (".$id.", 'nav_pages', 'php', 'Меню', 'global &#36;structure;\r\n\r\necho &lt;&lt;&lt;HTML\r\n        &lt;div class=&quot;navbar navbar-default&quot;&gt;\r\n\r\n					  &lt;div class=&quot;navbar-header&quot;&gt;\r\n						&lt;button type=&quot;button&quot; class=&quot;navbar-toggle&quot; data-toggle=&quot;collapse&quot; data-target=&quot;.navbar-exmenu-collapse&quot;&gt;\r\n						  &lt;span class=&quot;sr-only&quot;&gt;Toggle navigation&lt;/span&gt;\r\n						  &lt;span class=&quot;icon-bar&quot;&gt;&lt;/span&gt;\r\n						  &lt;span class=&quot;icon-bar&quot;&gt;&lt;/span&gt;\r\n						  &lt;span class=&quot;icon-bar&quot;&gt;&lt;/span&gt;\r\n						&lt;/button&gt;\r\n					  &lt;/div&gt;\r\n\r\n					&lt;div class=&quot;collapse navbar-collapse navbar-exmenu-collapse&quot;&gt;\r\n						&lt;ul class=&quot;nav navbar-nav&quot;&gt;\r\nHTML;\r\n\r\n&#36;tree = &#36;structure-&gt;sitetree;\r\n\r\n&#36;submenu = false;\r\n\r\nforeach(&#36;tree as &#36;k=&gt;&#36;v) &#123;\r\n	if(&#36;v[&#39;level&#39;] == 0) &#123;\r\n		echo &quot;\r\n			&lt;li&gt;&lt;a href=\\&quot;/\\&quot;&gt;Главная&lt;/a&gt;&lt;/li&gt;\r\n			&lt;li class=\\&quot;divider-vertical\\&quot;&gt;&lt;/li&gt;\r\n		&quot;;\r\n	&#125;\r\n	\r\n	if(&#36;v[&#39;level&#39;] == 1) &#123;\r\n		if(&#36;submenu) &#123;\r\n			&#36;submenu = false;\r\n			echo &quot;&lt;/ul&gt;&lt;/li&gt;&quot;;\r\n		&#125;\r\n		\r\n		echo &quot;\\n&lt;li&gt;&lt;a href=\\&quot;/index.php?page=&#123;&#36;v[&#39;alias&#39;]&#125;\\&quot;&gt;&#123;&#36;v[&#39;title&#39;]&#125;&lt;/a&gt;&lt;/li&gt;&quot;;\r\n		\r\n		if(&#36;v[&#39;childs&#39;] &gt; 0) &#123;\r\n			&#36;submenu = true;\r\n			echo &quot;\r\n				&lt;li class=\\&quot;dropdown\\&quot;&gt;\r\n					&lt;a href=\\&quot;#\\&quot; class=\\&quot;dropdown-toggle\\&quot; data-toggle=\\&quot;dropdown\\&quot;&gt;\r\n						&lt;b class=\\&quot;caret\\&quot;&gt;&lt;/b&gt;\r\n					&lt;/a&gt;\r\n					&lt;ul class=\\&quot;dropdown-menu\\&quot;&gt;\r\n			&quot;;\r\n		&#125;\r\n		else echo &quot;&lt;li class=\\&quot;divider-vertical\\&quot;&gt;&lt;/li&gt;&quot;;\r\n	&#125;\r\n	\r\n	if(&#36;v[&#39;level&#39;] &gt; 1) &#123;\r\n		echo &quot;\\n&lt;li&gt;&lt;a href=\\&quot;/index.php?page=&#123;&#36;v[&#39;alias&#39;]&#125;\\&quot;&gt;&#123;&#36;v[&#39;title&#39;]&#125;&lt;/a&gt;&lt;/li&gt;&quot;;	\r\n	&#125;\r\n&#125;\r\n\r\nif(&#36;submenu) &#123;\r\n	&#36;submenu = false;\r\n	echo &quot;&lt;/ul&gt;&lt;/li&gt;&quot;;\r\n&#125;\r\n\r\necho &lt;&lt;&lt;HTML\r\n				&lt;/ul&gt;\r\n			&lt;/div&gt;\r\n        &lt;/div&gt;\r\nHTML;\r\n', ".time().", ".time().")";


/**
* Изображения
*/
$sql['DROP '.IMAGES_TABLE] = "DROP TABLE IF EXISTS `".IMAGES_TABLE."`";
$sql['CREATE'.IMAGES_TABLE] = "CREATE TABLE IF NOT EXISTS `".IMAGES_TABLE."` (
									  `id` int(10) NOT NULL AUTO_INCREMENT,
									  `attachedto` varchar(255) NOT NULL,
									  `filename` varchar(255) NOT NULL,
									  `sort` int(10) unsigned NOT NULL DEFAULT '0',
									  `alt` varchar(255) NOT NULL,
									  PRIMARY KEY (`id`),
									  KEY `filename` (`filename`),
									  KEY `attachedto` (`attachedto`)
									) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";

$sql['DROP '.HELP_TABLE] = "DROP TABLE IF EXISTS `".HELP_TABLE."`";
$sql['CREATE'.HELP_TABLE] = "CREATE TABLE IF NOT EXISTS `".HELP_TABLE."` (
							  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
							  `uname` varchar(255) NOT NULL,
							  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
							  `sort` int(10) unsigned NOT NULL DEFAULT '0',
							  `childs` int(10) unsigned NOT NULL DEFAULT '0',
							  `title` varchar(255) NOT NULL,
							  `content` longtext NOT NULL,
							  `date_modified` int(30) unsigned NOT NULL DEFAULT '0',
							  PRIMARY KEY (`id`),
							  UNIQUE KEY `uname` (`uname`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=9";

$id = 1;
$sql['INSERT '.HELP_TABLE." ID #".$id] = "INSERT INTO `devroocms_help` (`id`, `uname`, `parent_id`, `sort`, `childs`, `title`, `content`, `date_modified`)
										  VALUES(".$id.", 'help', 0, 0, 2, 'Помощь', 'Вы находитесь  в справочном разделе системы управления контентом - RooCMS.&lt;br /&gt;\r\n&lt;br /&gt;\r\nЧто бы получить информацию о функциях панели управления выберите один из нижеприведенных разделов.', 1362725257)"; $id++;
$sql['INSERT '.HELP_TABLE." ID #".$id] = "INSERT INTO `devroocms_help` (`id`, `uname`, `parent_id`, `sort`, `childs`, `title`, `content`, `date_modified`)
										  VALUES(".$id.", 'structure', 3, 2, 0, 'Структура', '&lt;p&gt;Данный раздел сайта отвечает за управления структурой сайта.&lt;br /&gt;\r\n&lt;br /&gt;\r\nДля наглядности структура сайта представлена ввиде иерархического дерева неограниченной вложенности.&lt;br /&gt;\r\n&lt;br /&gt;\r\nЧто бы добавить новый элемент в структуру нажмите на ссылку справа &amp;quot;Создать новую страницу&amp;quot;.&lt;br /&gt;\r\n&lt;br /&gt;\r\nЧто бы отредактировать технические параметры отдельной страницы или раздела сайта нажмите на ссылку &amp;quot;редактировать&amp;quot;.&lt;br /&gt;\r\n&lt;br /&gt;\r\nЕсли хотите удалить какой либо раздел или страницу сайта, нажмите на ссылку &amp;quot;удалить&amp;quot;. Вы не сможете удалить элемент структуру, если у него имеются подчиненный элементы. Предварительно вам необходимо будет перенести их в подчинение другому элементу или удалить. Вы так же не можете удалить корневую (главную) страницу сайта, ведь всем известно, что без главной страницы сайт существовать не может.&lt;/p&gt;\r\n', 1381249809)"; $id++;
$sql['INSERT '.HELP_TABLE." ID #".$id] = "INSERT INTO `devroocms_help` (`id`, `uname`, `parent_id`, `sort`, `childs`, `title`, `content`, `date_modified`)
										  VALUES(".$id.", 'acp', 1, 1, 2, 'Панель управления сайтом', 'На главной странице панели управления отображается краткая сводка по сайту.&lt;br /&gt;\r\n&lt;br /&gt;\r\nТекущая версия RooCMS - Указана версия системы управления которую вы используете в данный момент.&lt;br /&gt;\r\n&lt;br /&gt;\r\nЕсли вы видите яркое, выделенное красным цветом, сообщение: &amp;quot;Инсталятор RooCMS находится в корне сайта. В целях безопасности следует удалить инсталятор!&amp;quot; это означает, что после установки или обновления системы управления сайтом, вы не удалили папку install из корня вашего сайта. На деле сразу после установки RooCMS доступ к скриптам инсталяторам ограничивается паролем администратора и злоумышленник не сможет воспользоваться процедурой установки/обновления, что бы повредить ваш сайт. Но мы все же рекомендует в целях повышения безопасности удалить папку install сразу после установки или обновления сайта.', 1362725257)"; $id++;
$sql['INSERT '.HELP_TABLE." ID #".$id] = "INSERT INTO `devroocms_help` (`id`, `uname`, `parent_id`, `sort`, `childs`, `title`, `content`, `date_modified`)
										  VALUES(".$id.", 'acp_serverinfo', 8, 2, 0, 'Информация о сервере', '&lt;p&gt;В данном разделе представлена информация о технической и программной сущностях сервера на котором расположен ваш сайт.&lt;br /&gt;\r\nПодобная информация может быть интересна опытным веб мастерам или при обращении в службу поддержки вашего хостинг-провайдера.&lt;br /&gt;\r\n&lt;br /&gt;\r\n&lt;b&gt;Версия PHP&lt;/b&gt; - Указывается версия интерпретатора языка PHP на котором написан код данной системы управления.&lt;br /&gt;\r\n&lt;b&gt;Версия Zend&lt;/b&gt; - Указывается версия ядра интерпретатора.&lt;br /&gt;\r\n&lt;b&gt;Версия MySQL&lt;/b&gt; - Указывается версия базы данных.&lt;br /&gt;\r\n&lt;b&gt;Версия RooCMS&lt;/b&gt; - Указывается текущая версия системы управления сайтом используемая в данный момент.&lt;br /&gt;\r\n&lt;b&gt;Apache&lt;/b&gt; - Указывается подпись сервера. Из неё можно почерпнуть информацию о версии веб-сервера, наличии SSL, типа PHP интерпретатора (модуль или cgi) и иную информацию&lt;br /&gt;\r\n&lt;b&gt;Имя сервера&lt;/b&gt; - Указывается имя вашего сервера на котором расположен сайт&lt;br /&gt;\r\n&lt;b&gt;Адрес сервера &lt;/b&gt;- Указывается IP адрес вашего физического сервера на котором расположен сайт.&lt;br /&gt;\r\n&lt;b&gt;Протокол сервера&lt;/b&gt; - Указывается протокол передачи данных с сервера до клиента.&lt;br /&gt;\r\n&lt;b&gt;Операционная система&lt;/b&gt; - Указывается операционная система установленная на вашем физическом сервере.&lt;br /&gt;\r\n&lt;b&gt;Лимит памяти&lt;/b&gt;&amp;nbsp; - Указывается лимит выделенной памяти под обработку скриптов установленный в настройках веб-сервера или интерпретатора.&lt;br /&gt;\r\n&lt;b&gt;Максимальный размер файлов для загрузки&lt;/b&gt; - Указывается максимально допустимый размера файлов для загрузки в одну операцию. Данный параметр указывает максимальный объем для любого количества загружаем за один проход скрипта файлов.&lt;br /&gt;\r\n&lt;b&gt;Максимальный размер постинга&lt;/b&gt; - Указывается максимально допустимый размер загрузки данных передаваемых через формы. Если данный параметр меньше параметра &amp;quot;максимальный размера файлов для загрузки&amp;quot;, то он применяется к тому, как приоритетный.&lt;br /&gt;\r\n&lt;b&gt;Максимальное время исполнение скрипта&lt;/b&gt; - Указывается время в течении которого сервер готов выполнять скрипт. Если время выполнение скрипта окажется больше указанного в данном параметре, сервер прервет выполнение скрипта и выдаст ошибку.&lt;br /&gt;\r\n&lt;b&gt;Корневая директория сайта&lt;/b&gt; - Указывает полный (физический) путь на сервере до корня вашего сайта.&lt;/p&gt;\r\n', 1381249959)"; $id++;
$sql['INSERT '.HELP_TABLE." ID #".$id] = "INSERT INTO `devroocms_help` (`id`, `uname`, `parent_id`, `sort`, `childs`, `title`, `content`, `date_modified`)
										  VALUES(".$id.", 'userui', 1, 0, 0, 'Пользовательская часть сайта', '&lt;p&gt;В данном разделе содержится информации о пользовательской части сайта&lt;/p&gt;\r\n', 1381203674)"; $id++;
$sql['INSERT '.HELP_TABLE." ID #".$id] = "INSERT INTO `devroocms_help` (`id`, `uname`, `parent_id`, `sort`, `childs`, `title`, `content`, `date_modified`)
										  VALUES(".$id.", 'roocms', 3, 0, 1, 'Главный экран', '&lt;p&gt;*&lt;/p&gt;\r\n', 1381249864)"; $id++;

?>