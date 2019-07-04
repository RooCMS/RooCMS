<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" id="RooCMS">
<head>
<title>{$site['title']}</title>
<meta name="robots"			content="no-index,no-follow,all" />
<meta name="revisit-after"		content="365 days" />
<meta name="Document-state" 		content="dynamic" />
<meta name="Resource-type" 		content="document" />
<meta name="Author" 			content="alex Roosso @ {$site['domain']}" lang="ru" />
<meta name="Copyright" 			content="RooCMS @ {$site['domain']}" lang="ru" />
<meta name="viewport" 			content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
<meta name="theme-color"		content="#3D4F61">
<meta http-equiv="Content-Type" 	content="{$charset}" />
<meta http-equiv="Content-language"	content="ru" />
<meta http-equiv="Cache-Control" 	content="no-cache" />

<link href="favicon.ico" rel="icon" 		type="image/x-icon" />
<link href="favicon.ico" rel="shortcut icon" 	type="image/x-icon" />

<base href="{if trim($site['domain']) != ""}{$site['protocol']}://{$site['domain']}{else}http{if isset($smarty.server.HTTPS)}s{/if}://{$smarty.server.SERVER_NAME}{$smarty.server.SCRIPT_NAME|replace:'index.php':''}{/if}" /><!--[if IE]></base><![endif]-->

<!-- Style -->
<link rel="stylesheet" type="text/css" href="plugin/fancybox/jquery.fancybox.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="plugin/bootstrap/css/bootstrap.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="plugin/font-awesome/css/font-awesome.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="plugin/bs-select/css/bootstrap-select.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="plugin/bs-datepicker/css/bootstrap-datepicker.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="plugin/bs-colorpicker/colorpicker.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="plugin/bs-tagsinput/css/bootstrap-tagsinput.min.css{$build}" media="screen" />
<link rel="stylesheet" type="text/css" href="{$SKIN}/css/style.min.css{$build}" media="screen" />

<!-- JS -->
<script type="text/javascript" src="plugin/jquery-core.min.js{$build}"></script>
<script type="text/javascript" src="plugin/jquery-migrate.min.js{$build}"></script>
<script type="text/javascript" src="plugin/fancybox/jquery.fancybox.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bootstrap/js/bootstrap.bundle.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bs-custom-file-input/bs-custom-file-input.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bs-select/js/bootstrap-select.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bs-select/js/i18n/defaults-ru_RU.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bs-datepicker/js/bootstrap-datepicker.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bs-datepicker/js/locales/bootstrap-datepicker.ru.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bs-colorpicker/colorpicker.min.js{$build}"></script>
<script type="text/javascript" src="plugin/bs-tagsinput/js/bootstrap-tagsinput.min.js{$build}"></script>
<script type="text/javascript" src="{$SKIN}/js/roocms.min.js{$build}"></script>
<script type="text/javascript" src="plugin/jquery.roocms.crui.min.js{$build}"></script>

{$jscript}

</head>
<body>

{if isset($cpmenu)}{$cpmenu}{/if}

{if !empty($error) || !empty($info)}
	<div class="position-absolute w-100 d-flex flex-column px-3" style="z-index: 999;">
		{if !empty($error)}
			{foreach from=$error item=e name=noterror}
				<div class="toast ml-auto border border-warning" role="alert" data-autohide="false" aria-live="assertive" aria-atomic="true">
					<div class="toast-header">
						<strong class="mr-auto text-danger"><i class="fas fa-exclamation-triangle fa-fw"></i> Ошибка</strong>
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
				{$delay = 2500 * $smarty.foreach.notinfo.iteration}
				<div class="toast ml-auto border border-info" role="alert" data-delay="{$delay}" data-autohide="true" aria-live="assertive" aria-atomic="true">
					<div class="toast-header">
						<strong class="mr-auto text-info"><i class="fas fa-info-circle fa-fw"></i> Уведомление</strong>
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

<div class="container-fluid">
	<div class="row">