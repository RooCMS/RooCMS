<?php

class tpl_items_user_header {

//#########################################################
//#		Основной шаблон
//#########################################################
function tpl_page() {
global $var;
$HTML = <<<HTML
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" id="roocms">
<head>
<title>{title}</title>
<meta name="description" 			content="{description}" />
<meta name="keywords" 				content="{keywords}" />
<meta name="robots"					content="index,follow,all" /> 
<meta name="revisit-after"			content="5 days" />
<meta name="Document-state" 		content="dynamic" />
<meta name="Resource-type" 			content="document" />
<meta name="Author" 				content="alex Roosso @ {domain}" lang="ru" />
<meta name="Copyright" 				content="RooCMS @ {domain}" lang="ru" />
<meta name="generator" 				content="Notead++" />
<meta http-equiv="Content-Type" 	content="{charset}" />  
<meta http-equiv="Content-language" content="ru" />
<meta http-equiv="Subject"			content="{description}" />
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

<base href="{domain}" /><!--[if IE]></base><![endif]-->

<!-- Style -->
<link rel="stylesheet" type="text/css" href="inc/style.css?v={build}" media="screen" />
<link rel="stylesheet" type="text/css" href="inc/jquery-ui.css?v={build}" media="screen"/>
{CSS}

<!-- JS -->
{JSCRIPT}

</head>
<body>

{FUCKIE}

<center>
<div style="width: 1000px;text-align: left;">
<a href="/" title="RooCMS - Бесплатная система управления сайтом [developer version]"><img src="img/logo.png" border="0" style="position: absolute;top: 15px;"></a>
{error}{info}
{module:menu}
HTML;
return $HTML;
}

//*****************************************************
// CSS
function tpl_css() {
$CSS = <<<CSS

CSS;
return $CSS;
}

//*****************************************************
// JS
function tpl_js() {
$JS = <<<JS
<script type="text/javascript" src="inc/iepngfix_tilebg.js?v={build}"></script>
<script type="text/javascript" src="inc/jquery-core.min.js.php?v={build}"></script>
<script type="text/javascript" src="inc/jquery-ui.min.js.php?v={build}"></script>
<script type="text/javascript" src="inc/jquery.corner.js.php?v={build}"></script>
<script type="text/javascript" src="plugin/colorbox.js?v={build}"></script>
<script type="text/javascript" src="inc/roocms.js?v={build}"></script>


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
JS;
return $JS;
}

//#########################################################
//#		Элементы шаблона
//#########################################################

}

?>