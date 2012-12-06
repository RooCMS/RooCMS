<?php

class tpl_items_acp_header {

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
<meta name="revisit-after"			content="7 days" />
<meta name="Document-state" 		content="dynamic" />
<meta name="Resource-type" 			content="document" />
<meta name="Author" 				content="alex Roosso @ {domain}" lang="ru" />
<meta name="Copyright" 				content="RooCMS @ {domain}" lang="ru" />
<meta name="generator" 				content="Notead++" />
<meta http-equiv="Content-Type" 	content="{charset}" />  
<meta http-equiv="Content-language" content="ru" />
<meta http-equiv="Subject"			content="{description}" />
<!-- no cache headers -->
<meta http-equiv="Pragma" 			content="no-cache" />
<meta http-equiv="Expires" 			content="-1" />
<meta http-equiv="Cache-Control" 	content="no-cache" />
<!-- end no cache headers -->

<!-- FavIcon -->
<link href="favicon.ico" 	rel="icon" 			type="image/x-icon" />
<link href="favicon.ico" 	rel="shortcut icon" type="image/x-icon" />

<base href="{domain}" /><!--[if IE]></base><![endif]-->

<!-- Style -->
<link rel="stylesheet" type="text/css" href="inc/acp.css?v={build}" media="screen" />
<link rel="stylesheet" type="text/css" href="inc/jquery-ui.css?v={build}" media="screen"/>
{CSS}

<!-- JS -->
{JSCRIPT}

</head>
<body>

{error}{info}
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
<script type="text/javascript" src="inc/jquery.cookie.js?v={build}"></script>
<script type="text/javascript" src="inc/jquery-ui.min.js.php?v={build}"></script>
<script type="text/javascript" src="inc/jquery.select.js?v={build}"></script>
<script type="text/javascript" src="inc/jquery.corner.js.php?v={build}"></script>
<script type="text/javascript" src="plugin/colorbox.js?v={build}"></script>
<script type="text/javascript" src="plugin/tooltip.js?v={build}"></script>
<script type="text/javascript" src="inc/roocms_acp.js?v={build}"></script>
<script type="text/javascript" src="inc/roocms.js?v={build}"></script>

JS;
return $JS;
}

//#########################################################
//#		Элементы шаблона
//#########################################################

}

?>