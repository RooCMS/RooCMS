{literal}
<style>
	@media (max-width: 767px) {
		body {margin: 1rem 0 3rem 0;}
	}
	@media (min-width: 768px) {
		body {margin: 7rem 0 7rem 0;}
	}
	@media (min-width: 992px) {
		body {margin: 7rem 0 5rem 0;}
	}
</style>
{/literal}


<div class="container-fluid d-sm-none" id="logo-xs">
	<div class="row">
		<div class="col-md-12 text-center">
			<a href="{$SCRIPT_NAME}"><img src="../skin/acp/img/logo.png" border="0" class="logo-xs" alt="RooCMS"></a>
		</div>
	</div>
</div>

<div class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top d-none d-sm-block" role="navigation">

	<a class="navbar-brand d-none d-sm-block" href="{$SCRIPT_NAME}"><img src="../skin/acp/img/logo_acp.png" border="0" id="logo" alt="RooCMS"></a>

	<div class="collapse navbar-collapse">
		<span class="navbar-text col-3">{if $action == "install"}Установка{elseif $action == "update"}Обновление{/if} RooCMS</span>
		<span class="navbar-text col-6 text-center">{if trim($page_title) != ""}<b class="white">{$page_title}</b>{/if}</span>
		<span class="navbar-text col-3 text-right">Этап:<b class="text-warning"> {$step}</b>/<b class="text-warning">{$steps}</b></span>
	</div>
</div>