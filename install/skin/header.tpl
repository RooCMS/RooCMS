<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" id="roocms">
<head>
<title>{$site['title']}</title>
<meta name="robots"					content="no-index,no-follow,all" /> 
<meta name="revisit-after"			content="365 days" />
<meta name="Document-state" 		content="dynamic" />
<meta name="Resource-type" 			content="document" />
<meta name="Author" 				content="alex Roosso @ {$site['domain']}" lang="ru" />
<meta name="Copyright" 				content="RooCMS @ {$site['domain']}" lang="ru" />
<meta name="generator" 				content="Notead++" />
<meta http-equiv="Content-Type" 	content="{$charset}" />  
<meta http-equiv="Content-language" content="ru" />
<!-- no cache headers -->
<meta http-equiv="Pragma" 			content="no-cache" />
<meta http-equiv="Expires" 			content="-1" />
<meta http-equiv="Cache-Control" 	content="no-cache" />
<!-- end no cache headers -->

<!-- FavIcon -->
<link href="favicon.ico" 	rel="icon" 			type="image/x-icon" />
<link href="favicon.ico" 	rel="shortcut icon" type="image/x-icon" />

<base href="{if trim($site['domain']) != ""}{$site['domain']}{else}http://{$smarty.server.SERVER_NAME}{/if}" /><!--[if IE]></base><![endif]-->

<!-- Style -->
<link rel="stylesheet" type="text/css" href="skin/acp/style.css?v={$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="inc/jquery-ui.css?v={$build}" media="screen"/>
<link rel="stylesheet" type="text/css" href="inc/colorpicker.css?v={$build}" media="screen"/>

<!-- JS -->
<!--[if lt IE 10]>
<script type="text/javascript" src="inc/PIE.js"></script>
<![endif]-->
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="inc/iepngfix_tilebg.js?v={$build}"></script>
<script type="text/javascript" src="inc/jquery-core.min.js.php?v={$build}"></script>
<script type="text/javascript" src="inc/jquery.cookie.js?v={$build}"></script>
<script type="text/javascript" src="inc/jquery-ui.min.js.php?v={$build}"></script>
<script type="text/javascript" src="inc/jquery.select.js?v={$build}"></script>
<script type="text/javascript" src="inc/jquery.colorpicker.js?v={$build}"></script>
<script type="text/javascript" src="inc/jquery.corner.js.php?v={$build}"></script>
<script type="text/javascript" src="plugin/colorbox.js?v={$build}"></script>
<script type="text/javascript" src="skin/acp/roocms.js?v={$build}"></script>
<!-- <script type="text/javascript" src="inc/roocms.js?v={$build}"></script>  -->

{$jscript}

{literal}
<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$(".tabs").tabs({ collapsible: false });
		$(".tabs ul").css("display","block");
		
		/*function timer(to, sec) {
			var step = sec;
			var dly = 1000;
			
			while(step >= 0) {
				setTimeout(function() {
					$(to).text(step);
				}, dly);
				dly = dly + 1000;
				step--;
			}
		}
		
		$.each($('[rel^=timer]'), function() {
			var rel = $(this).attr('rel');
			var par = rel.split('.');
			var sec	= par[1];
			
			timer($(this), sec);
		});*/
	});
</script>
{/literal}
</head>
<body>
{if trim($error) != ""}<div id="error">{$error}</div>{/if}
{if trim($info) != ""}<div id="info">{$info}</div>{/if}
