{* Шаблон управления структурой сайта *}
<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
	{*<ul class="nav nav-list">*}
		<li class="nav-header">Управление структурой сайта</li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "create"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=structure&part=create"><span class="fa fa-fw fa-plus-circle"></span> Создать новую страницу</a></li>
	</ul>
</div>
<div class="col-md-10 thumbnail">
	<div class="caption">
    	{$content}
	</div>
</div>