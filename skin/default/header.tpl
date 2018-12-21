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
<meta name="viewport" 			content="width=device-width, initial-scale=1.0">
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
<link rel="stylesheet" type="text/css" href="{$SKIN}/style.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="plugin/fancybox/jquery.fancybox.min.css{$build}" media="screen" />

<!-- JS -->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<script type="text/javascript" src="plugin/iepngfix_tilebg.min.js{$build}"></script>
<script type="text/javascript" src="plugin/jquery-core.min.js{$build}"></script>
<script type="text/javascript" src="plugin/jquery-migrate.min.js{$build}"></script>
<script type="text/javascript" src="plugin/fancybox/jquery.fancybox.min.js{$build}"></script>
<script type="text/javascript" src="plugin/jquery.touchswipe.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bootstrap.php{$build}{if trim($build) != ""}&{else}?{/if}short"></script>
<script type="text/javascript" src="{$SKIN}/roocms.min.js{$build}"></script>

{literal}
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-21055124-4']);
  _gaq.push(['_setDomainName', '.roocms.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
{/literal}
</head>
<body>

{if trim($error) != ""}
	<div class="alert alert-danger t12 text-left in fade notice" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		{$error}
	</div>
{/if}
{if trim($info) != ""}
	<div class="alert alert-info t12 text-left in fade notice" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		{$info}
	</div>
{/if}

<div class="container-fluid header">
	<div class="row">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<a href="/"><img src="{$SKIN}/img/logo.png" border="0" id="logo"></a>
				</div>
				<div class="col-md-6">
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
					{foreach from=$navtree item=nitem key=k name=navigate}
						{if $nitem['level'] == 0}
							<a href="/index.php?page={$nitem['alias']}" class="btn btn-link text-uppercase">{$nitem['title']}</a>
						{/if}
					{/foreach}
					<span class="btn btn-link pull-right text-uppercase navigation-full">Все меню</span>
					<div class="container navigation-submenu">
						<div class="row">
							{foreach from=$navtree item=nitem key=k name=navigate}
								{if $nitem['level'] == 0}
									{if !$smarty.foreach.navigate.first}</div>{/if}
									<div class="col-lg-3">
									<div class="ptsans text-uppercase text-bold navigation-title"><strong>{$nitem['title']}</strong></div>
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
<div class="container">
	<div class="row">
		<div class="col-sm-12">
			{$breadcrumb}
		</div>
	</div>
</div>
<div class="container">

