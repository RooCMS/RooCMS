<?php
/**
* @package      RooCMS
* @subpackage	Library
* @subpackage	MySQL Schema
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.7
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
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
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` (`id`, `type`, `sort`, `name`, `title`, `ico`) VALUES (".$id.", 'component', 1, 'global', 'Основные настройки', 'ico_settingsa.png')";	$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` (`id`, `type`, `sort`, `name`, `title`, `ico`) VALUES (".$id.", 'component', 2, 'gd', 'Обработка изображений', 'ico_imagea.png')";		$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` (`id`, `type`, `sort`, `name`, `title`, `ico`) VALUES (".$id.", 'component', 5, 'cp', 'Панель Администратора', 'adm.png')";			$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` (`id`, `type`, `sort`, `name`, `title`, `ico`) VALUES (".$id.", 'component', 4, 'tpl', 'Настройки шаблонизации', 'ico_tma.png')";	$id++;
$sql['INSERT '.CONFIG_PARTS." ID #".$id] = "INSERT INTO `".CONFIG_PARTS."` (`id`, `type`, `sort`, `name`, `title`, `ico`) VALUES (".$id.", 'component', 3, 'rss', 'RSS', 'ico_rssa.png')";						$id++;


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
											VALUES (".$id.", 'gd', 6, 'Цвет фона миниатюры', 'Данный параметр устанавливает цвет фона для миниатюр, если вы выбрали тип генерации \"по размеру\"', 'gd_thumb_bgcolor', 'color', '', '#ffffff', '#ffffff')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 7, 'Качество миниатюр', 'Укажите качество создаваемых миниатюр от 1 до 100 \r\nОпция применима только для jpg миниатюр.', 'gd_thumb_jpg_quality', 'int', '', '90', '90')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 8, 'Вкл/выкл водяной знак', 'Использовать на загружаемых изображениях Watermark (полупрозрачный копирайт) для защиты изображений от копирования на сторонние ресурсы?', 'gd_use_watermark', 'boolean', '', 'true', 'true')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 9, 'Первая строка водяного знака', 'Первая строчка водяного знака накладываемого на изображение', 'gd_watermark_string_one', 'string', '', '', '')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'gd', 8, 'Вторая строка водяного знака', 'Вторая строчка водяного знака накладываемого на изображение', 'gd_watermark_string_two', 'string', '', 'http://".$_SERVER['SERVER_NAME']."', 'http://".$_SERVER['SERVER_NAME']."')"; $id++;
$sql['INSERT '.CONFIG_TABLE." ID #".$id] = "INSERT INTO `".CONFIG_TABLE."` (`id`, `part`, `sort`, `title`, `description`, `option_name`, `option_type`, `variants`, `value`, `default`)
											VALUES (".$id.", 'cp', 1, 'Вход в панель управления', 'Укажите название файла (скрипта) через который вы будете заходить в Панель Управления.\r\n<b>Внимание!</b> После изменения этой настройки, изменится URI панели управления. В случае если вы изменяли вручную шаблоны панели управления, проверьте, что вы везде указали переменную {&#36SCRIPT_NAME}', 'cp_script', 'string', '', 'acp.php', 'acp.php')"; $id++;
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
									  UNIQUE KEY `alias` (`alias`)
									) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";
$id = 1;
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
$id = 1;
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
									  PRIMARY KEY (`id`)
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
$id = 1;
$sql['INSERT '.BLOCKS_TABLE." ID #".$id] = "INSERT INTO `".BLOCKS_TABLE."` (`id`, `alias`, `type`, `title`, `content`, `date_create`, `date_modified`)
											VALUES (".$id.", 'pages', 'php', 'Меню', 'global &#36;db;\r\n\r\n&#36;q = &#36;db-&gt;query(&quot;SELECT id, alias, title FROM &quot;.STRUCTURE_TABLE.&quot; ORDER BY id ASC&quot;);\r\nwhile(&#36;data = &#36;db-&gt;fetch_assoc(&#36;q)) &#123;\r\n	echo &lt;&lt;&lt;HTML\r\n	&lt;li&gt;&lt;a href=&quot;/index.php?page=&#123;&#36;data[&#39;alias&#39;]&#125;&quot;&gt;&#123;&#36;data[&#39;title&#39;]&#125;&lt;/a&gt;&lt;/li&gt;	\r\n\r\nHTML;\r\n&#125;\r\n', ".time().", ".time().")";


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
									  PRIMARY KEY (`id`)
									) ENGINE=MyISAM  DEFAULT CHARSET=utf8 PACK_KEYS=1 AUTO_INCREMENT=1";

?>