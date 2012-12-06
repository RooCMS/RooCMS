-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Окт 31 2011 г., 15:27
-- Версия сервера: 5.1.40
-- Версия PHP: 5.2.12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
--

-- --------------------------------------------------------

--
-- Структура таблицы `roocms_config__parts`
--

CREATE TABLE IF NOT EXISTS `roocms_config__parts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `part` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sort` int(3) unsigned NOT NULL,
  `type` enum('component','mod','module') NOT NULL DEFAULT 'component',
  PRIMARY KEY (`id`),
  UNIQUE KEY `part` (`part`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Дамп данных таблицы `roocms_config__parts`
--

INSERT INTO `roocms_config__parts` (`id`, `part`, `title`, `sort`, `type`) VALUES(1, 'Global', 'Глобальные настройки', 1, 'component');
INSERT INTO `roocms_config__parts` (`id`, `part`, `title`, `sort`, `type`) VALUES(2, 'GD', 'Настройка изображений', 10, 'component');
INSERT INTO `roocms_config__parts` (`id`, `part`, `title`, `sort`, `type`) VALUES(3, 'News', 'Новости', 20, 'mod');
INSERT INTO `roocms_config__parts` (`id`, `part`, `title`, `sort`, `type`) VALUES(4, 'Portfolio', 'Портфолио', 30, 'mod');
INSERT INTO `roocms_config__parts` (`id`, `part`, `title`, `sort`, `type`) VALUES(5, 'Gallery', 'Галерея изображений', 35, 'mod');
INSERT INTO `roocms_config__parts` (`id`, `part`, `title`, `sort`, `type`) VALUES(6, 'LastNews', 'Последние новости', 25, 'module');
INSERT INTO `roocms_config__parts` (`id`, `part`, `title`, `sort`, `type`) VALUES(7, 'VK', 'Вконтакте (общие)', 50, 'module');
INSERT INTO `roocms_config__parts` (`id`, `part`, `title`, `sort`, `type`) VALUES(8, 'VKComments', 'Вконтакте (комментарии)', 52, 'module');
INSERT INTO `roocms_config__parts` (`id`, `part`, `title`, `sort`, `type`) VALUES(9, 'VKLike', 'Вконтакте (Мне нравится)', 55, 'module');
INSERT INTO `roocms_config__parts` (`id`, `part`, `title`, `sort`, `type`) VALUES(10, 'GooglePlusOne', 'Google Plus One', 70, 'module');
INSERT INTO `roocms_config__parts` (`id`, `part`, `title`, `sort`, `type`) VALUES(11, 'Sape', 'Sape (Сапе)', 150, 'module');
INSERT INTO `roocms_config__parts` (`id`, `part`, `title`, `sort`, `type`) VALUES(12, 'RSS', 'RSS 2.0', 30, 'component');

-- --------------------------------------------------------

--
-- Структура таблицы `roocms_config__settings`
--

CREATE TABLE IF NOT EXISTS `roocms_config__settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `part` varchar(255) NOT NULL,
  `sort` int(3) unsigned NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL DEFAULT 'TitleOption',
  `description` text NOT NULL,
  `options` varchar(255) NOT NULL,
  `setting_name` varchar(255) NOT NULL,
  `options_type` enum('boolean','bool','int','integer','string','text','textarea','date','email','select') NOT NULL DEFAULT 'boolean',
  `variants` text NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `options` (`options`),
  KEY `part` (`part`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

--
-- Дамп данных таблицы `roocms_config__settings`
--

INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(1, 'Global', 1, 'Base Url', 'Укажите базовый путь к корню сайта.\r\nПример: http://www.roocms.com\r\n* без слеша на конце', 'baseurl', '', 'string', '', '');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(2, 'Global', 2, 'Meta Description', 'Описание всех страниц сайта для поисковой системы.', 'meta_description', '', 'string', '', 'RooCMS');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(3, 'Global', 3, 'Meta Keywords', 'Ключевые слова всего сайта, для поисковой системы\r\nРазделяйте слова запятыми', 'meta_keywords', '', 'string', '', 'RooCMS,  CMS, Content, Managment, System, ЦМС, система, управления, сайтом, open, source, web cms, веб цмс');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(4, 'Global', 5, 'Заголовок 304', 'Опция вкл/выкл ответ заголовка 304 на запрос IF_MODIFED_SINCE от поисковых роботов там где это разрешено.', 'if_modifed_since', '', 'boolean', '', 'false');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(5, 'Global', 10, 'Fuck Internet Explorer', 'Опция включает всплывающую рекомендацию отказаться от IE в пользу других браузеров.', 'fuckie', '', 'boolean', '', 'false');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(6, 'GD', 1, 'Создавать миниатюры', 'Опция указывает создавать ли миниатюры для загружаемых сообщений.', 'gd_resize_image', '', 'boolean', '', 'true');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(7, 'GD', 2, 'Ширина миниатюры', 'Укажите размер уменьшенного изображения по горизонтали (в пикселях)', 'gd_thumb_image_width', '', 'int', '', '120');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(8, 'GD', 3, 'Высота миниатюры', 'Укажите размер уменьшенного изображения по вертикали(в пикселях)', 'gd_thumb_image_height', '', 'int', '', '120');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(9, 'GD', 10, 'Водяной знак', 'Использовать на загружаемых изображениях Watermark (полупрозрачный копирайт) для защиты изображений от копирования на сторонние ресурсы?', 'gd_use_watermark', '', 'boolean', '', 'true');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(10, 'GD', 11, 'Первая строка водяного знака', 'Первая строчка водяного знака накладываемого на изображение', 'gd_watermark_string_one', '', 'string', '', 'RooCMS');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(11, 'GD', 12, 'Вторая строка водяного знака', 'Первая строчка водяного знака накладываемого на изображение', 'gd_watermark_string_two', '', 'string', '', 'http://dev.roocms.com');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(12, 'News', 1, 'Новостей на странице', 'Укажите количество новостей для показа на одной странице.', 'news_newsonpage', 'newsonpage', 'int', '', '5');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(13, 'News', 2, 'Отступ', 'Отступ для дочерних категорий\r\nУказывается в пискселях', 'news_indention', 'indention', 'int', '', '15');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(14, 'Portfolio', 1, 'Заголовок', 'Заголовок вашего портфолио.\r\nПример: Белов Александр - личное портфолио', 'portfolio_title', 'title', 'string', '', 'RooCMS - мод Портфолио');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(15, 'Portfolio', 4, 'Дата рождения', 'Введите Вашу дату рождения', 'portfolio_birthdate', 'birth_date', 'date', '', '11/29/2010');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(16, 'Portfolio', 5, 'О себе', 'Напишите, что нибудь о себе, что будет характеризовать Вас, как профессионала.', 'portfolio_about', 'about', 'textarea', '', 'Данный мод можно использоваться как для личного портфолио или как для каталога работ. Или даже для каталога сайтов, как это сделано на головном сайте в разделе &quot;Каталог&quot;, где размещаются сайты сделанные на основе RooCMS.\r\n\r\nОформление раздела зависит лишь от фантазии верстальщика и дизайнера (правда лучше в обратном порядке ;-) ). Несколько примеров приведены прямо здесь. Что бы посмотреть их выберите любую категорию портфолио слева.\r\n\r\nВ одном из примеров, мы попытались сделать, что то вроде наподобие мини сайта, что бы показать, как данный мод может функционировать вполне самостоятельно. А разделы портфолио и проекты, могут работать как разделы и подразделы сайта.');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(17, 'Portfolio', 6, 'Девиз', 'Напишите свой девиз или слоган', 'portfolio_motto', 'motto', 'string', '', 'RooCMS - Русская бесплатная система управления сайтом');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(18, 'Portfolio', 7, 'Телефон', 'Укажите номер вашего телефона', 'portfolio_phone', 'phone', 'string', '', '+7(*0*)**0-0*-*0');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(19, 'Portfolio', 8, 'E-Mail', 'Укажите ваш почтовый электронный адрес', 'portfolio_email', 'email', 'email', '', 'info@roocms.com');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(20, 'Portfolio', 9, 'ICQ', 'Укажите номер Вашего ICQ', 'portfolio_icq', 'icq', 'int', '', '3405729');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(21, 'Portfolio', 10, 'Страна', 'Укажите Вашу страну', 'portfolio_country', 'country', 'string', '', 'Россия');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(22, 'Portfolio', 11, 'Город', 'Укажите город Вашего пребывания', 'portfolio_city', 'city', 'string', '', 'Все города');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(23, 'Portfolio', 12, 'Мета описание', 'Описание портфолио для мета-тегов', 'portfolio_metadescription', 'meta_description', 'string', '', 'Демонстрация мода портфолио от RooCMS');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(24, 'Portfolio', 13, 'Мета Ключевые слова', 'Ключевые слова для мета-тегов', 'portfolio_metakeywords', 'meta_keywords', 'string', '', 'portfolio, php, css, mysql, js, seo, web, programming, smarty, программирование, пхп, хтмл, сео, html, ajax, roocms, cms, сайты на RooCMS, ЦМС');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(25, 'Portfolio', 14, 'Отступ', 'Отступ для дочерних категорий\r\nУказывается в пискселях', 'portfolio_indention', 'indention', 'int', '', '15');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(26, 'Portfolio', 15, 'Работ на странице', 'Количество работ отображаемых на одной странице', 'portfolio_workonpage', 'workonpage', 'int', '', '5');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(27, 'Gallery', 10, 'Изображений на странице', 'Указывается количество изображений на странице', 'gallery_imageonpage', 'imageonpage', 'int', '', '15');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(28, 'Gallery', 20, 'Отступ', 'Отступ для дочерних категорий\r\nУказывается в пискселях', 'gallery_indention', 'indention', 'int', '', '15');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(29, 'LastNews', 1, 'Заголовок блока', 'Укажите заголовок блока с последними новостями.\r\nЕсли вы используете блок в нескольких местах, и вам нужны разные заголовке, уберите заголовок в шаблоне модуля и указывайте его вручную при редактировании страниц.', 'lastnews_title', '', 'string', '', 'Свежие (и не очень) новости');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(30, 'LastNews', 2, 'Количество новостей', 'Укажите какое число послдених новостей должно отображать в блоке', 'lastnews_limit', '', 'int', '', '3');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(31, 'VK', 1, 'apiID Widget VK', 'Укажите API ID для Ваших виджетов от ВК', 'vk_apiid', '', 'int', '', '');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(32, 'VKComments', 1, 'Комментарий ВК', 'Включить комментарии с помошью ВКонтакте\r\nWidget VK Comments', 'vk_comments_on', '', 'boolean', '', 'false');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(33, 'VKComments', 3, 'Количество комментариев', 'Количество комментариев на одну страницу.\r\n(от 5 до 100)', 'vk_comments_limit', '', 'int', '', '10');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(34, 'VKComments', 5, 'Граффити', 'Разрешить граффити в расширенных сообщениях\r\n(Опция "Расширенные сообщения" должны быть включена"', 'vk_comments_graffiti', '', 'boolean', '', 'true');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(35, 'VKComments', 6, 'Фотографии', 'Разрешить фотографии в расширенных сообщениях\r\n(Опция "Расширенные сообщения" должны быть включена"', 'vk_comments_photo', '', 'boolean', '', 'true');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(36, 'VKComments', 7, 'Видео', 'Разрешить видео в расширенных сообщениях\r\n(Опция "Расширенные сообщения" должны быть включена"', 'vk_comments_video', '', 'boolean', '', 'false');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(37, 'VKComments', 8, 'Аудио', 'Разрешить аудио в расширенных сообщениях\r\n(Опция "Расширенные сообщения" должны быть включена"', 'vk_comments_audio', '', 'boolean', '', 'false');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(38, 'VKComments', 9, 'Ссылки', 'Разрешить ссылки в расширенных сообщениях\r\n(Опция "Расширенные сообщения" должны быть включена"', 'vk_comments_link', '', 'boolean', '', 'true');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(39, 'VKComments', 4, 'Расширенные сообщения', 'Разрешить использовать с сообщениях от Widget VK дополнительные возможности', 'vk_comments_attach', '', 'boolean', '', 'true');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(40, 'VKComments', 10, 'Вкл/выкл автообновления', 'Отключает обновление ленты комментариев в реальном времени.', 'vk_comments_norealtime', '', 'boolean', '', 'true');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(41, 'VKComments', 11, 'Автопубликация в статус', 'Автоматическая публикация комментария в статус пользователю', 'vk_comments_autopublish', '', 'boolean', '', 'false');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(42, 'VKComments', 12, 'Ширина виджета', 'Укажите в пикселах ширину виджета\r\n(минимальное: 300\r\nо - будет задавать ширину автоматически)', 'vk_comments_width', '', 'int', '', '0');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(43, 'VKLike', 1, 'Мне нравится ВК', 'Включить "мне нравится" с помошью ВКонтакте\r\nWidget VK Like', 'vk_like_on', '', 'boolean', '', 'false');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(44, 'VKLike', 2, 'Варианты кнопки', 'Выберите вариант внешнего вида кнопки', 'vk_like_type', '', 'select', 'Кнопка с текстовым счетчиком|full\r\nКнопка с миниатюрным счетчиком|button\r\nМиниатюрная кнопка|mini\r\nМиниатюрная кнопка, счетчик сверху|vertical', 'button');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(45, 'VKLike', 3, 'Название кнопки', 'Задайте какая надпись должна показываться на кнопке.', 'vk_like_verb', '', 'select', 'Мне нравится|0\r\nЭто интересно|1', '1');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(46, 'GooglePlusOne', 1, 'Вкл/выкл Google "+1" ', 'Включить кнопку "+1" от Google', 'google_plusone_on', '', 'boolean', '', 'false');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(47, 'GooglePlusOne', 2, 'Размер кнопки', 'Укажите размер кнопки', 'google_plusone_size', '', 'select', 'Маленькая (15 пикселей)|small\r\nСредняя (20 пикселей)|meduim\r\nСтандарт (24 пикселя)|standart\r\nБольшая (60 пикселей)|tall', 'meduim');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(48, 'GooglePlusOne', 3, 'Вкл/выкл счетчик', 'Опция включает/отключает счетчик для кнопки.\r\nДанная опция игнорируется, если выше вы выбрали вариант "большая (60 пикселей)"', 'google_plusone_count', '', 'boolean', '', 'true');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(49, 'Sape', 1, 'Вкл/выкл Sape', 'Включить показ рекламы от sape.ru\r\n(Предварительно не забудьте получить папку от sape и установить на неё необходимые права\r\nИнструкция находится по адресу http://www.sape.ru/site.php?act=add)', 'sape_on', '', 'boolean', '', 'false');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(50, 'Sape', 2, 'Хост идентификатор sape.ru', 'Вы можете узнать его значение в инструкции в пункте 2.\r\nЗначение состоит из 32 символов и выделено жирным шрифтом', 'sape_id', '', 'string', '', '');
INSERT INTO `roocms_config__settings` (`id`, `part`, `sort`, `title`, `description`, `options`, `setting_name`, `options_type`, `variants`, `value`) VALUES(51, 'RSS', 10, 'TTL', 'Время жизни фида в минутах.\r\nЗначение не может быть меньше 60.\r\nПо умолчанию: 240', 'rss_ttl', '', 'int', '', '300');

-- --------------------------------------------------------

--
-- Структура таблицы `roocms_gallery__category`
--

CREATE TABLE IF NOT EXISTS `roocms_gallery__category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(32) unsigned NOT NULL DEFAULT '0',
  `type` enum('category','part') NOT NULL DEFAULT 'category',
  `name` varchar(255) NOT NULL,
  `images` int(32) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `roocms_gallery__category`
--



-- --------------------------------------------------------

--
-- Структура таблицы `roocms_gallery__items`
--

CREATE TABLE IF NOT EXISTS `roocms_gallery__items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `thumb_img` varchar(255) NOT NULL,
  `original_img` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `roocms_gallery__items`
--



-- --------------------------------------------------------

--
-- Структура таблицы `roocms_news__category`
--

CREATE TABLE IF NOT EXISTS `roocms_news__category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL,
  `items` int(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT= ;

--
-- Дамп данных таблицы `roocms_news__category`
--



-- --------------------------------------------------------

--
-- Структура таблицы `roocms_news__files`
--

CREATE TABLE IF NOT EXISTS `roocms_news__files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `filename` varchar(255) NOT NULL,
  `ext` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `news_id` (`news_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `roocms_news__files`
--


-- --------------------------------------------------------

--
-- Структура таблицы `roocms_news__image`
--

CREATE TABLE IF NOT EXISTS `roocms_news__image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `original_img` varchar(255) NOT NULL,
  `thumb_img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `news_id` (`news_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `roocms_news__image`
--



-- --------------------------------------------------------

--
-- Структура таблицы `roocms_news__item`
--

CREATE TABLE IF NOT EXISTS `roocms_news__item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL,
  `date_create` int(30) unsigned NOT NULL DEFAULT '0',
  `date_update` int(30) unsigned NOT NULL DEFAULT '0',
  `date` int(30) unsigned NOT NULL DEFAULT '1',
  `brief_news` text NOT NULL,
  `full_news` longtext NOT NULL,
  `images` int(4) unsigned NOT NULL DEFAULT '0',
  `files` int(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `roocms_news__item`
--


-- --------------------------------------------------------

--
-- Структура таблицы `roocms_page__unit`
--

CREATE TABLE IF NOT EXISTS `roocms_page__unit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `def` enum('false','true') NOT NULL DEFAULT 'false',
  `alias` varchar(255) NOT NULL,
  `page_type` enum('html','php') NOT NULL DEFAULT 'html',
  `page_title` varchar(255) NOT NULL,
  `page_content` longtext NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL,
  `last_update` bigint(32) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `roocms_page__unit`
--


-- --------------------------------------------------------

--
-- Структура таблицы `roocms_portfolio__category`
--

CREATE TABLE IF NOT EXISTS `roocms_portfolio__category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `type` enum('category','part') NOT NULL DEFAULT 'category',
  `name` varchar(255) NOT NULL,
  `projects` int(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `roocms_portfolio__category`
--


-- --------------------------------------------------------

--
-- Структура таблицы `roocms_portfolio__projects`
--

CREATE TABLE IF NOT EXISTS `roocms_portfolio__projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(4) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `sub_title` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `poster` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `tags` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `roocms_portfolio__projects`
--


-- --------------------------------------------------------

--
-- Структура таблицы `roocms_portfolio__projects_steps`
--

CREATE TABLE IF NOT EXISTS `roocms_portfolio__projects_steps` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `step` int(10) unsigned NOT NULL,
  `step_picture` varchar(255) NOT NULL,
  `step_description` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `roocms_portfolio__projects_steps`
--

