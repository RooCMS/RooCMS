{* Основной шаблон управления блоками *}
<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
		<li class="nav-header">Опции</li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "html"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=blocks&part=create&type=html"><span class="fa fa-fw fa-cube"></span> Создать новый <strong>HTML</strong> блок</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "php"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=blocks&part=create&type=php"><span class="fa fa-fw fa-cube"></span> Создать новый <strong>PHP</strong> блок</a></li>
	</ul>
</div>
<div class="col-md-10">
	<div class=" panel panel-default">
    		{$content}
	</div>
</div>