{* Основной шаблон управления контентом страниц *}
<div class="col-sm-3 col-md-2">
	<div class="row hidden-xs">
		<div class="panel panel-default">

			<div class="panel-heading visible-lg">
				Управление структурой сайта
			</div>
			<div class="list-group">
				<a href="{$SCRIPT_NAME}?act=structure&part=create" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><span class="fa fa-fw fa-plus-circle"></span> Создать страницу</a>
			</div>
		</div>
	</div>

	<div class="btn-group btn-group-sm btn-group-justified visible-xs submenu-xs">
		<a href="{$SCRIPT_NAME}?act=structure&part=create" class="btn btn-default {if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><span class="fa fa-fw fa-plus-circle"></span> Создать страницу</a>
	</div>
</div>
<div class="col-sm-9 col-md-10">
	<div class=" panel panel-default">
    		{$content}
	</div>
</div>