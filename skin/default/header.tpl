{* Шаблон "головы" *}
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" id="roocms">
<head>
<title>{$site['title']}{if $site['pagination']['page'] != 1} (Страница: {$site['pagination']['page']}){/if}{if $config->global_site_title} &bull; {$config->site_title}{/if}</title>
<meta name="description" 		content="{$site['description']}" />
<meta name="keywords" 			content="{$site['keywords']}" />
<meta name="robots"			content="{if $noindex == 1}no-index,no-follow,all{else}index, follow, all{/if}" />
<meta name="revisit-after"		content="5 days" />
<meta name="revisit"			content="5" />
<meta name="Document-state" 		content="dynamic" />
<meta name="Resource-type" 		content="document" />
<meta name="Author" 			content="alex Roosso @ {$site['domain']}" lang="ru" />
<meta name="Copyright" 			content="RooCMS @ {$site['domain']}" lang="ru" />
<meta name="url" 			content="{$site['domain']}" />
<meta name="Subject"			content="{$site['description']}" />
<meta name="viewport" 			content="width=device-width, height=device-height, initial-scale=1.0, shrink-to-fit=no">
<meta http-equiv="Content-language"	content="ru" />
<meta http-equiv="Cache-Control" 	content="max-age=3600, must-revalidate" />
<meta http-equiv="X-UA-Compatible" 	content="IE=edge">
<meta http-equiv="Content-Type" 	content="{$charset}" />
<meta charset="utf-8">

<link href="favicon.ico" rel="icon" 		type="image/x-icon" />
<link href="favicon.ico" rel="shortcut icon"	type="image/x-icon" />

<!-- seo -->
<meta name="google-site-verification" 	content="4yncfVL_W31VKPYG3A45jt5tuDPHjrP-ytDtIdz-Yys" />
<meta name='yandex-verification' 	content='60ea4e7aaa8b83ec' />
<!-- /seo -->

<base href="{if trim($site['domain']) != ""}{$site['protocol']}://{$site['domain']}{else}http{if isset($smarty.server.HTTPS)}s{/if}://{$smarty.server.SERVER_NAME}{$smarty.server.SCRIPT_NAME|replace:'index.php':''}{/if}" /><!--[if IE]></base><![endif]-->

{if !empty($rsslink)}<!-- RSS 2.0 -->
<link rel="alternate" type="application/rss+xml" title="{$site['title']}" href="{$rsslink}" />{/if}

<!-- Style -->
<link rel="stylesheet" type="text/css" href="plugin/fancybox/jquery.fancybox.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="plugin/bootstrap/css/bootstrap.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="plugin/font-awesome/css/font-awesome.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="plugin/bs-select/css/bootstrap-select.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="plugin/bs-datepicker/css/bootstrap-datepicker.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="{$SKIN}/css/style.min.css{$build}" media="screen" />

<!-- JS -->
<script type="text/javascript" src="plugin/jquery-core.min.js{$build}"></script>
<script type="text/javascript" src="plugin/jquery-migrate.min.js{$build}"></script>
{*<script type="text/javascript" src="plugin/jquery.touchswipe.min.js{$build}"></script>*}
<script type="text/javascript" src="plugin/fancybox/jquery.fancybox.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bootstrap/js/bootstrap.bundle.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bs-select/js/bootstrap-select.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bs-select/js/i18n/defaults-ru_RU.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bs-datepicker/js/bootstrap-datepicker.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bs-datepicker/js/locales/bootstrap-datepicker.ru.min.js{$build}"></script>
<script type="text/javascript" src="{$SKIN}/js/roocms.min.js{$build}"></script>

{$jscript}

</head>
<body>
{if !empty($error) || !empty($info)}
	<div class="position-absolute w-100 d-flex flex-column px-3" style="z-index: 999;">
		{if !empty($error)}
			{foreach from=$error item=e name=noterror}
				<div class="toast ml-auto border border-warning" role="alert" data-autohide="false" aria-live="assertive" aria-atomic="true">
					<div class="toast-header">
						<strong class="mr-auto"><i class="fas fa-exclamation-triangle fa-fw text-danger"></i> Ошибка</strong>
						{*<small>11 mins ago</small>*}
						<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="toast-body">
						{$e}
					</div>
				</div>
			{/foreach}
		{/if}
		{if !empty($info)}
			{foreach from=$info item=i name=notinfo}
				{$delay = 2250 * $smarty.foreach.notinfo.iteration}
				<div class="toast ml-auto border border-info" role="alert" data-delay="{$delay}" data-autohide="true" aria-live="assertive" aria-atomic="true">
					<div class="toast-header">
						<strong class="mr-auto"><i class="fas fa-info-circle fa-fw text-info"></i> Уведомление</strong>
						{*<small>11 mins ago</small>*}
						<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="toast-body">
						{$i}
					</div>
				</div>
			{/foreach}
		{/if}
	</div>
{/if}
<div class="container-fluid login my-0 collapse bg-primary text-light" id="LoginForm">
	<div class="row">
		<div class="container">
			<div class="row py-3 justify-content-center">
				<div class="col-lg-8 col-xl-7">
					<h4>Войти на сайт</h4>
					<form method="post" action="{$SCRIPT_NAME}?part=ucp&act=login" class="form-inline">
						<div class="input-group mr-1 mb-2">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inLogin"><i class="fas fa-fw fa-user-secret"></i></span>
							</div>
							<input type="text" name="login" class="form-control non-bgreq" placeholder="Логин" aria-label="Username" aria-describedby="inLogin" required>
						</div>
						<div class="input-group mr-1 mb-2">
							<div class="input-group-prepend">
								<span class="input-group-text" id="inPassword"><i class="fas fa-fw fa-key"></i></span>
							</div>
							<input type="password" name="password" class="form-control non-bgreq" placeholder="Пароль" aria-label="Password" aria-describedby="inPassword" required>
						</div>
						<div class="input-group mb-2">
							<button type="submit" name="userlogin" id="inputAuth" class="btn btn-outline-light" value="user">Войти <i class="fas fa-fw fa-sign-in-alt"></i></button>
						</div>
					</form>
					<a href="{$SCRIPT_NAME}?part=repass" class="text-light">Забыли пароль<i class="fas fa-fw fa-question-circle"></i></a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="container header my-3">
	<div class="row">
		<div class="col-sm-5 col-lg-4 align-middle text-center text-sm-left mt-n1">
			<a href="/" title="{$config->site_title}"><img src="{$SKIN}/img/logo.png" border="0" id="logo" alt="{$config->site_title}"></a>
		</div>
		<div class="col-sm-7 col-lg-8 align-middle">

			{$module->load("auth")}

			{if !empty($navtree)}
			<nav class="nav d-flex flex-column flex-sm-row align-items-center align-items-lg-start mt-3">
				{foreach from=$navtree item=navitem key=k name=navigate}
					{if $navitem['level'] == 0}
						<a class="{*flex-sm-fill*} d-none d-lg-inline nav-link text-primary roocms-topnav-link{if isset($smarty.get.page) && $smarty.get.page == $navitem['alias']} active{/if}" href="/index.php?page={$navitem['alias']}">{$navitem['title']}</a>
					{/if}
				{/foreach}

				<a class="flex-fill nav-link text-gray text-center text-sm-right roocms-topnav-link" data-toggle="collapse" href="#collapseAllMenu" role="button" aria-expanded="false" aria-controls="collapseAllMenu">
					<span class="d-inline d-lg-none">Меню</span> <i class="fas fa-fw fa-bars"></i>
				</a>
				<a class="nav-link text-gray text-center text-sm-right roocms-topnav-link" data-toggle="collapse" href="#collapseSearch" role="button" aria-expanded="false" aria-controls="collapseSearch">
					<span class="d-inline d-sm-none">Поиск</span> <i class="fas fa-fw fa-search"></i>
				</a>
			</nav>
			{/if}
		</div>
	</div>
	<div class="row">
		<div class="col-12 mt-3">
			<div class="collapse w-100" id="collapseSearch">
				{$module->load("search")}
			</div>
			<div class="collapse w-100" id="collapseAllMenu">
				<div class="card card-body bg-light">
					<div class="d-flex flex-row flex-wrap" role="navigation">
						{foreach from=$navtree item=navitem key=k name=navigate}
							{if $smarty.foreach.navigate.first}
								<div class="d-flex flex-column col-lg-3 col-md-4 col-sm-6 my-1 px-0">
							{/if}

							{if $navitem['level'] == 0 && !$smarty.foreach.navigate.first}
								</div>
								<div class="d-flex flex-column col-lg-3 col-md-4 col-sm-6 my-1 px-0">
							{/if}

							<a href="/index.php?page={$navitem['alias']}" class="text-dark rounded py-1 roocms-topnav-sublink{if $navitem['level'] == 0}-first{/if}">{$navitem['title']}{if !array_key_exists($userdata['gid'], $navitem['group_access']) && $userdata['title'] == "u" && !array_key_exists(0, $navitem['group_access'])}<i class="fas fa-fw fa-lock small" rel="tooltip" data-placement="left" title="Для просмотра страницы нужны расширенные права доступа"></i>{/if}</a>

							{if $smarty.foreach.navigate.last}
								</div>
							{/if}
						{/foreach}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--[if lte IE 9]>
<p class="bg-danger text-light">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/" class="text-warning" rel="nofollow" target="_blank">upgrade your browser</a> to improve your experience and security.</p>
<![endif]-->

<div class="container">
	<div class="row">
		<div class="col-12">
			{$breadcrumb}
		</div>
	</div>
</div>

