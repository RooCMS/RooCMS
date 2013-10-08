{* Шаблон "головы" *}
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" id="roocms">
<head>
<title>{$site['title']}</title>
<meta name="description" 			content="{$site['description']}" />
<meta name="keywords" 				content="{$site['keywords']}" />
<meta name="robots"					content="{$robots}" />
<meta name="revisit-after"			content="5 days" />
<meta name="revisit"				content="5" />
<meta name="Document-state" 		content="dynamic" />
<meta name="Resource-type" 			content="document" />
<meta name="Author" 				content="alex Roosso @ {$site['domain']}" lang="ru" />
<meta name="Copyright" 				content="RooCMS @ {$site['domain']}" lang="ru" />
<meta name="generator" 				content="RooCMS" />
<meta name="url" 					content="{$site['domain']}" />
<meta name="Subject"				content="{$site['description']}" />
<meta http-equiv="Content-Type" 	content="{$charset}" />
<meta http-equiv="Content-language" content="ru" />
<meta http-equiv="Pragma" 			content="no-cache" />
<meta http-equiv="Expires" 			content="-1" />
<meta http-equiv="Cache-Control" 	content="max-age=3600, must-revalidate" />

<link href="favicon.ico" 	rel="icon" 			type="image/x-icon" />
<link href="favicon.ico" 	rel="shortcut icon" type="image/x-icon" />

<!-- seo -->
<meta name="google-site-verification" 	content="4yncfVL_W31VKPYG3A45jt5tuDPHjrP-ytDtIdz-Yys" />
<meta name='yandex-verification' 		content='60ea4e7aaa8b83ec' />
<!-- /seo -->

<base href="{if trim($site['domain']) != ""}{$site['domain']}{else}http://{$smarty.server.SERVER_NAME}{/if}" /><!--[if IE]></base><![endif]-->

{if !empty($rsslink)}
<!-- RSS 2.0 -->
<link rel="alternate" type="application/rss+xml" title="{$site['title']}" href="{$rsslink}" />
{/if}

<!-- Style -->
<link rel="stylesheet" type="text/css" href="{$SKIN}/style.css{$build}" media="screen" />

<!-- JS -->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<script type="text/javascript" src="plugin/iepngfix_tilebg.js{$build}"></script>
<script type="text/javascript" src="plugin/jquery-core.min.js.php{$build}"></script>
<script type="text/javascript" src="plugin/jquery-migrate.min.js.php{$build}"></script>
<script type="text/javascript" src="plugin/jquery.corner.js.php{$build}"></script>
<script type="text/javascript" src="plugin/lightbox.js.php{$build}"></script>
<script type="text/javascript" src="plugin/colorbox.js.php{$build}"></script>
<script type="text/javascript" src="plugin/bootstrap.php{$build}{if !empty($build)}&{else}?{/if}short"></script>
<script type="text/javascript" src="{$SKIN}/roocms.js{$build}"></script>

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

{$fuckie}

{if trim($error) != ""}
	<div class="alert alert-danger t12 text-left in fade" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	    {$error}
	</div>
{/if}
{if trim($info) != ""}
	<div class="alert alert-info t12 text-left in fade notification-info" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {$info}
	</div>
{/if}

<div class="container">
	<div class="header">
    	<h1><a href="/"><img src="{$SKIN}/img/logo.png" border="0" style="vertical-align: top;"></a> RooCMS <small><sup>demo</sup>&beta;</small></h1>
    	 {$blocks->load("nav_pages")}
	</div>
	{* Хлебные крошки *}
	{if !empty($mites)}
	    <ul class="breadcrumb">
        	<li>
            	<a href="/index.php">Главная</a>
        	</li>
        	{foreach from=$mites item=smites key=i name=mites}
            	<li{if $smarty.foreach.mites.last} class="active"{/if}>
            		{if !$smarty.foreach.mites.last}<a href="{$SCRIPT_NAME}?page={$smites['alias']}">{$smites['title']}</a>{else}{$smites['title']}{/if}
            	</li>
        	{/foreach}
	    </ul>
	{/if}