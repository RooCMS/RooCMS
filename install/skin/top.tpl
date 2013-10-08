{literal}
<style>
	#logo {top: 5px; left: 0px;}
	.navbar-brand {width: 110px;}
</style>
{/literal}

<div class="container visible-xs" id="logo-xs">
	<div class="row">
    	<div class="col-md-12 text-center">
        	<a href="{$SCRIPT_NAME}"><img src="/skin/acp/img/acp_logo_full.png" border="0"></a>
    	</div>
	</div>
</div>

<div class="navbar navbar-fixed-top navbar-inverse">

		<div class="navbar-header text-center">
			<a class="navbar-brand hidden-xs" href="{$SCRIPT_NAME}"><img src="/skin/acp/img/acp_logo_full.png" border="0" class="absolute" id="logo"></a>
		</div>

		<div class="collapse navbar-collapse navbar-ex-collapse">
        	<p class="navbar-text">{if $action == "install"}Установка{elseif $action == "update"}Обновление{/if} RooCMS</p>
        	<p class="navbar-text">{if trim($page_title) != ""}<b class="white">{$page_title}</b>{/if}</p>
        	<p class="navbar-text pull-right">Этап:<b class="text-warning"> {$step}</b>/<b class="text-warning">{$steps}</b></p>
		</div>
</div>