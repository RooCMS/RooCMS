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
<!-- cache headers -->
<meta http-equiv="Pragma" 			content="no-cache" />
<meta http-equiv="Expires" 			content="-1" />
<meta http-equiv="Cache-Control" 	content="max-age=3600, must-revalidate" />
<!-- no cache headers -->

<!-- seo -->
<meta name="google-site-verification" 	content="4yncfVL_W31VKPYG3A45jt5tuDPHjrP-ytDtIdz-Yys" />
<meta name='yandex-verification' 		content='60ea4e7aaa8b83ec' />
<!-- /seo -->

<!-- FavIcon -->
<link href="favicon.ico" 	rel="icon" 			type="image/x-icon" />
<link href="favicon.ico" 	rel="shortcut icon" type="image/x-icon" />

<base href="{if trim($site['domain']) != ""}{$site['domain']}{else}http://{$smarty.server.SERVER_NAME}{/if}" /><!--[if IE]></base><![endif]-->

{if !empty($rsslink)}
<!-- RSS 2.0 -->
<link rel="alternate" type="application/rss+xml" title="{$site['title']}" href="{$rsslink}" />
{/if}

<!-- Style -->
<link rel="stylesheet" type="text/css" href="{$SKIN}/style.css?v={$build}" media="screen" />
<!-- <link rel="stylesheet" type="text/css" href="inc/jquery-ui.css?v={$build}" media="screen"/> -->

<!-- JS -->
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="inc/iepngfix_tilebg.js?v={$build}"></script>
<script type="text/javascript" src="inc/jquery-core.min.js.php?v={$build}"></script>
<!-- <script type="text/javascript" src="inc/jquery-ui.min.js.php?v={$build}"></script> -->
<script type="text/javascript" src="inc/jquery.corner.js.php?v={$build}"></script>
<script type="text/javascript" src="plugin/colorbox.js?v={$build}"></script>
<script type="text/javascript" src="plugin/plusstrap.php?v={$build}"></script>
<script type="text/javascript" src="{$SKIN}/roocms.js?v={$build}"></script>

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

<div class="container">
	<div class="page-header">
		<h1><a href="/"><img src="{$SKIN}/img/logo.png" border="0" style="vertical-align: top;"></a> RooCMS <small>&beta;</small></h1>
		{$blocks->load("pages")}

	{if trim($error) != "" || trim($info) != ""}
		{literal}
		<script type="text/javascript">
			$(document).ready(function(){
				$(".alert").alert();
			});
		</script>
		{/literal}

	{/if}
	{if trim($error) != ""}
		<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">&times;</a>{$error}</div>
	{/if}
	{if trim($info) != ""}
		<div class="alert alert-info"><a class="close" data-dismiss="alert" href="#">&times;</a>{$info}</div>
	{/if}
	</div>
