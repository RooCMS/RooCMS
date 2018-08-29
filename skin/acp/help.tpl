{* Шаблон управления помощью сайта *}
{if $smarty.const.DEVMODE}
<div class="col-sm-2">
	<ul class="nav nav-pills nav-stacked">
		<li class="nav-header">Разработчику</li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "create_part"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=help&part=create_part"><i class="fa fa-fw fa-plus-circle"></i>Добавить новый раздел</a></li>
	</ul>
</div>
{/if}
<div class="col-sm-{if $smarty.const.DEVMODE}10{else}12{/if}">
	<div class=" panel panel-default">
		{$content}
	</div>
</div>