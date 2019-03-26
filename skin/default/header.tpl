{* Шаблон "головы" *}
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" id="roocms">
<head>
<title>{$site['title']}</title>
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
<meta name="viewport" 			content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
<meta http-equiv="Content-Type" 	content="{$charset}" />
<meta http-equiv="Content-language"	content="ru" />
<meta http-equiv="Pragma" 		content="no-cache" />
<meta http-equiv="Expires" 		content="-1" />
<meta http-equiv="Cache-Control" 	content="max-age=3600, must-revalidate" />

<link href="favicon.ico" rel="icon" 		type="image/x-icon" />
<link href="favicon.ico" rel="shortcut icon"	type="image/x-icon" />

<!-- seo -->
<meta name="google-site-verification" 	content="4yncfVL_W31VKPYG3A45jt5tuDPHjrP-ytDtIdz-Yys" />
<meta name='yandex-verification' 	content='60ea4e7aaa8b83ec' />
<!-- /seo -->

<base href="{if trim($site['domain']) != ""}http{if isset($smarty.server.HTTPS)}s{/if}://{$site['domain']}{else}http{if isset($smarty.server.HTTPS)}s{/if}://{$smarty.server.SERVER_NAME}{/if}" /><!--[if IE]></base><![endif]-->

{if !empty($rsslink)}<!-- RSS 2.0 -->
<link rel="alternate" type="application/rss+xml" title="{$site['title']}" href="{$rsslink}" />{/if}

<!-- Style -->
<link rel="stylesheet" type="text/css" href="/plugin/fancybox/jquery.fancybox.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="/plugin/bootstrap/css/bootstrap.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="/plugin/font-awesome/css/font-awesome.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="/plugin/bs-select/css/bootstrap-select.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="/plugin/bs-datepicker/css/bootstrap-datepicker.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="{$SKIN}/css/style.min.css{$build}" media="screen" />

<!-- JS -->
<script type="text/javascript" src="/plugin/jquery-core.min.js{$build}"></script>
<script type="text/javascript" src="/plugin/jquery-migrate.min.js{$build}"></script>
{*<script type="text/javascript" src="/plugin/jquery.touchswipe.min.js{$build}"></script>*}
<script type="text/javascript" src="/plugin/fancybox/jquery.fancybox.min.js{$build}"></script>
<script type="text/javascript" src="/plugin/bootstrap/js/bootstrap.bundle.min.js{$build}"></script>
<script type="text/javascript" src="/plugin/bs-select/js/bootstrap-select.min.js{$build}"></script>
<script type="text/javascript" src="/plugin/bs-select/js/i18n/defaults-ru_RU.min.js{$build}"></script>
<script type="text/javascript" src="/plugin/bs-datepicker/js/bootstrap-datepicker.min.js{$build}"></script>
<script type="text/javascript" src="/plugin/bs-datepicker/js/locales/bootstrap-datepicker.ru.min.js{$build}"></script>
<script type="text/javascript" src="{$SKIN}/js/roocms.min.js{$build}"></script>


<script type="text/javascript">
	{literal}
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-21055124-4']);
	_gaq.push(['_setDomainName', '.roocms.com']);
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
	{/literal}
</script>

{$jscript}

</head>
<body>

{if trim($error) != ""}
	<div class="toast fade notice" role="alert" aria-live="assertive" aria-atomic="true">
		<div class="toast-header">
			<strong class="mr-auto">Ошибка</strong>
			<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="toast-body">
			{$error}
		</div>
	</div>
{/if}
{if trim($info) != ""}
	<div class="toast fade notice" role="status" aria-live="polite" aria-atomic="true">
		<div class="toast-header">
			<strong class="mr-auto">Сообщение</strong>
			<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="toast-body">
			{$info}
		</div>
	</div>
{/if}

<!--[if lte IE 9]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/" class="alert-link" rel="nofollow">upgrade your browser</a> to improve your experience and security.</p>
<![endif]-->

<div class="container header mt-3">
	<div class="row">
		<div class="col-sm-5 col-lg-4 align-middle mt-n1">
			<a href="/" title="{$config->site_title}"><img src="{$SKIN}/img/logo.png" border="0" id="logo" alt="{$config->site_title}"></a>
		</div>
		<div class="col-sm-7 col-lg-8 align-middle">
			<nav class="nav flex-column flex-sm-row mt-4">
				{foreach from=$navtree item=navitem key=k name=navigate}
					{if $navitem['level'] == 0}
						<a class="flex-sm-fill nav-link {if isset($smarty.get.page) && $smarty.get.page == $navitem['alias']} active{/if}" href="/index.php?page={$navitem['alias']}">{$navitem['title']}</a>
					{/if}
				{/foreach}

				<a class="flex-sm-fill nav-link" data-toggle="collapse" href="#collapseAllMenu" role="button" aria-expanded="false" aria-controls="collapseAllMenu">
					Все меню<i class="fas fa-fw fa-caret-down"></i>
				</a>

				<div class="collapse" id="collapseAllMenu">
					<div class="card card-body">
						Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
					</div>
				</div>
			</nav>
		</div>
	</div>
</div>

<div class="container-fluid header">
	<div class="row">
		<div class="container">
			<div class="row">
				<div class="col-sm-7 col-xs-12">
					{$module->load("auth")}
				</div>
			</div>
		</div>
	</div>
</div>
{if !empty($navtree)}
<div class="container-fluid navigation">
	<div class="row">
		<div class="container">
			<div class="row navigation-level-0">
				<div class="col-md-12">
					<span class="btn btn-link pull-right text-uppercase navigation-full visible-lg visible-md">Все меню</span>
					<span class="btn btn-link pull-right text-uppercase navigation-full-xs visible-sm visible-xs"><span class="glyphicon glyphicon-align-justify"></span></span>
					<div class="container navigation-submenu">
						<div class="row">
							{assign var=rows value=0}
							{foreach from=$navtree item=nitem key=k name=navigate}
								{if $nitem['level'] == 0}
									{if !$smarty.foreach.navigate.first}</div>{/if}
									{assign var=rows value=$rows+1}
									{if $rows == 5}
										{assign var=rows value=1}
										</div><div class="row">
									{/if}
									<div class="col-lg-3 col-md-4 col-xs-12 text-overflow">
									<a href="/index.php?page={$nitem['alias']}" class="btn btn-link btn-sm btn-block text-uppercase ptsans topmenu topmenu-title">{$nitem['title']}</a>
								{else}
									<a href="/index.php?page={$nitem['alias']}" class="btn btn-link btn-sm btn-block ptsans topmenu">{$nitem['title']}</a>
								{/if}
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
</div>
{/if}
<div class="container-fluid breadcrumb-line">
	<div class="row">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					{$breadcrumb}
				</div>
			</div>
		</div>
	</div>
</div>


