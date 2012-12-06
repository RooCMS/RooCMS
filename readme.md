[RooCMS](http://www.roocms.com)
========
- Автор:	alex Roosso
- Контакт:	info@roocms.com
- Лицензия:	GNU GPL v2 (текст лицензии в файле license)



Рекомендуемые системные требования
----------------------------------
- Сервер:	*nix Apache
- PHP:		5.2.*, 5.3
- MySQL		5.0.*, 5.1.*
- PHP Extension: 
		gd2
		session
		mysql
		pcre
		mbstring
		iconv
		hash




Установка и настройка RooCMS версия 1.00 ночная сборка 9
--------------------------------------------------------
1. Скопируйте все файлы из данной папки в корень вашего сайта.
2. Откройте любым текстовым редактором файл /roocms/config/config.php
3. В указанном файле в строках с 37 по 40 укажите данные необходимые для подключения к БД
4. В указанном файле в строках 49 и 50 укажите логин и пароль администратора RooCMS
5. В указанном файле в строке 59 укажите название вашего сайта.
6. В указанном файле в строке 60 укажите доменное имя где распологается ваш сайт.
7. В указанном файле в строке 61 укажите почтовый ящик для системных сообщений.
8. Сохраните изменения и закройте файл.
9. Зайдите в систему управления вашей БД (например: PhpMyAdmin) и импортируйте файл dump.sql который лежит в данной папке. После удалите его.


В корне расположены файлы, на примере которых Вы можете создавать страницы вашего сайта.
Документация, инструкции, описание функций появятся в ближайшие время на сайте.


Войти в панель управления можно по адресу: http://www.ваш_сайт.ru/acp.php


Автоматический инсталятор появится в следующих выпусках RooCMS.


Это все ещё бета версия.



Обновление RooCMS с версии 1.00 nb 8 до 1.00 nb 9
--------------------------------------------------

1. Сделайте резервную копию всех файлов и базы данных.
2. Удалите все файлы RooCMS, кроме файлов:
	в корне сайта (или иных файлов созданных вами) и файлов /inc/style.css , /roocms/module/menu.php
	Кроме папок /upload/* и /roocms/templates/* и файлов в них 
	Удалите так же папку /roocms/config/* , но предварительно сохраните файл config.php, который лежит в ней.
3. Скопируйте все папки из данного архива в корень вашего сайта, за исключением папок /upload/* и /roocms/templates/* и файлов из корня данного архива и файлов /inc/style.css , /roocms/module/menu.php
4. Перенесите настройки из сохраненного ранее файла config.php в файл /roocms/config/config.php
5. Из Папки /roocms/templates/ удалите все файлы с префиксом acp_* и добавьте файлы из архива с этим префиксом в папку.
6. Скопирайте файл из архива /roocms/tempaltes/db_error.html в папку /roocms/templates/
7. Выполните все sql запросы из файла update.sql через php_my_admin. (Если вы используйте иной префикс БД для вашего сайта, не забудьте отредактировать его)
8. 

Откройте файл /roocms/templates/user_news.php и найдите строки:

	//***************************
	// 	{html:prev_news} 
	//	Кросслинкинг на предыдущую новость
	function prev_news($data) {

	
Добавте перед ними следующий код:
	
	//***************************
	// 	{html:navpage_el} 
	//	Элмент навигации по страницам (предыдущая)
	function navpage_prev_el($page, $category) {
	$topage = ($page != 1) ? "&page=".$page : "" ;
	$HTML = <<<HTML
		<b><a href="{THIS}?category={$category}{$topage}" class="linkb" title="Предыдущая страница">&larr;</a></b>
	HTML;
	return $HTML;
	}

	//***************************
	// 	{html:navpage_el} 
	//	Элмент навигации по страницам (следующая)
	function navpage_next_el($page, $category) {
	$topage = ($page != 1) ? "&page=".$page : "" ;
	$HTML = <<<HTML
		<b><a href="{THIS}?category={$category}{$topage}" class="linkb" title="Следующая страница">&rarr;</a></b>
	HTML;
	return $HTML;
	}
	
	//***************************
	// 	{html:rsslink}
	//	RSS ссылка
	function rsslink($link) {
	$HTML = <<<HTML
		<br /><a href="{$link}" class="button" style="vertical-align: middle;text-decoration: none;"><img src="/img/misc/rss.gif" border="0" style="vertical-align: middle;"> <u>RSS 2.0</u> </a>
	HTML;
	return $HTML;
	}
	
9. Обновите шаблон до новой версии /roocms/templates/module_last_news.php

Обвновление должно быть успешно завершено. В случае возникновения ошибок, включите режим отладки.
Для этого в файле /roocms/class/class_debug.php в классе Debug установите для переменной $debug значение = 1

Если возникнут ошибки опишите их подробно в своем письме на электронную почту info@roocms.com
Ответ Вы получите в течении 2 рабочих дней.