{* Основной шаблон управления блоками *}
<div class="col-sm-3 col-md-2">
	<div class="row hidden-xs">
		<div class="panel panel-default">

			<div class="panel-heading visible-lg">
				Управление блоками
			</div>
			<div class="list-group">
				<a href="{$SCRIPT_NAME}?act=blocks&part=create&type=html" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "html"} active{/if}"><i class="fa fa-fw fa-cube"></i> Создать <strong>HTML</strong> блок</a>
				<a href="{$SCRIPT_NAME}?act=blocks&part=create&type=php" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "php"} active{/if}"><i class="fa fa-fw fa-cube"></i> Создать <strong>PHP</strong> блок</a>
			</div>
		</div>
	</div>

	<div class="btn-group btn-group-sm btn-group-justified visible-xs submenu-xs">
		<a href="{$SCRIPT_NAME}?act=blocks&part=create&type=html" class="btn btn-default {if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "html"} active{/if}"><i class="fa fa-fw fa-cube"></i> Создать <strong>HTML</strong> блок</a>
		<a href="{$SCRIPT_NAME}?act=blocks&part=create&type=php" class="btn btn-default {if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "php"} active{/if}"><i class="fa fa-fw fa-cube"></i> Создать <strong>PHP</strong> блок</a>
	</div>
</div>
<div class="col-sm-9 col-md-10">
	<div class=" panel panel-default">
		{$content}
	</div>
</div>