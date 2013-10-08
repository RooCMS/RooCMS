{* Основной шаблон управления блоками *}
<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
	{*<ul class="nav nav-list">*}
		<li class="nav-header">Опции</li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "html"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=blocks&part=create&type=html"><span class="icon-fixed-width icon-plus-sign-alt"></span> Создать новый <span class="label label-primary">HTML</span> блок</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "php"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=blocks&part=create&type=php"><span class="icon-fixed-width icon-plus-sign-alt"></span> Создать новый <span class="label label-primary">PHP</span> блок</a></li>
	</ul>
</div>
<div class="col-md-10 thumbnail">
	<div class="caption">
    	{$content}
	</div>
</div>