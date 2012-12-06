UPDATE `roocms_config__parts` SET `title` = 'Галерея изображений' WHERE `roocms_config__parts`.`part` = 'Gallery' LIMIT 1 ;
ALTER TABLE `roocms_config__parts` ADD `type` ENUM( 'mod', 'module' ) NOT NULL DEFAULT 'mod';
ALTER TABLE `roocms_config__parts` CHANGE `type` `type` ENUM( 'component', 'mod', 'module' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'component';

UPDATE `roocms_config__parts` SET `type` = 'component' WHERE `roocms_config__parts`.`part` = 'Global' LIMIT 1 ;
UPDATE `roocms_config__parts` SET `type` = 'component' WHERE `roocms_config__parts`.`part` = 'GD' LIMIT 1 ;
UPDATE `roocms_config__parts` SET `type` = 'module' WHERE `roocms_config__parts`.`part` = 'VK' LIMIT 1 ;
UPDATE `roocms_config__parts` SET `type` = 'module' WHERE `roocms_config__parts`.`part` = 'VKComments' LIMIT 1 ;
UPDATE `roocms_config__parts` SET `type` = 'module' WHERE `roocms_config__parts`.`part` = 'VKLike' LIMIT 1 ;
UPDATE `roocms_config__parts` SET `type` = 'module' WHERE `roocms_config__parts`.`part` = 'Sape' LIMIT 1 ;
UPDATE `roocms_config__parts` SET `type` = 'module' WHERE `roocms_config__parts`.`part` = 'GooglePlusOne' LIMIT 1 ;

UPDATE `roocms_config__parts` SET `title` = 'Google Plus One' WHERE `roocms_config__parts`.`part` = 'GooglePlusOne' LIMIT 1 ;

INSERT INTO `roocms_config__parts` ( `id` , `part` , `title` , `sort` , `type` )
VALUES ( NULL , 'LastNews', 'Последние новости', '25', 'module' );

INSERT INTO `roocms_config__settings` ( `id` , `part` , `sort` , `title` , `description` , `options` , `setting_name` , `options_type` , `variants` , `value` )
VALUES (NULL , 'LastNews', '1', 'Заголовок блока', 'Укажите заголовок блока с последними новостями. \r\nЕсли вы используете блок в нескольких местах, и вам нужны разные заголовке, уберите заголовок в шаблоне модуля и указывайте его вручную при редактировании страниц.', 'lastnews_title', '', 'string', '', 'Свежие новости'), 
(NULL , 'LastNews', '1', 'Количество новостей', 'Укажите какое число послдених новостей должно отображать в блоке \r\nПо умолчанию выводится 2 послдение новости', 'lastnews_limit', '', 'int', '', '0');

INSERT INTO `roocms_config__parts` (`id` ,`part` ,`title` ,`sort` ,`type`)
VALUES (NULL , 'RSS', 'RSS 2.0', '30', 'component');

INSERT INTO `roocms_config__settings` (`id` ,`part` ,`sort` ,`title` ,`description` ,`options` ,`setting_name` ,`options_type` ,`variants` ,`value`)
VALUES (NULL , 'RSS', '10', 'TTL', 'Время жизни фида в минутах. Значение не может быть меньше 60. По умолчанию: 240', 'rss_ttl', '', 'int', '', '240');