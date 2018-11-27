{* Log Main Template *}
<div class="col-sm-3 col-md-2">
	<div class="row hidden-xs">
		<div class="panel panel-default">

			<div class="panel-heading visible-lg">
				Управление
			</div>
			<div class="list-group">
				<a href="{$SCRIPT_NAME}?act=logs&part=logaction" class="list-group-item{if !isset($smarty.get.part) || (isset($smarty.get.part) && $smarty.get.part == "logaction")} active{/if}"><span class="fa fa-fw fa-file-text-o"></span> Лог действий</a>
				<a href="{$SCRIPT_NAME}?act=logs&part=lowerrors" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "lowerrors"} active{/if}"><span class="fa fa-fw fa-file-code-o"></span> Ошибки PHP</a>
				<a href="{$SCRIPT_NAME}?act=logs&part=syserrors" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "syserrors"} active{/if}"><span class="fa fa-fw fa-file-code-o"></span> Критические ошибки</a>
			</div>
		</div>
	</div>

	<div class="btn-group btn-group-sm btn-group-justified visible-xs submenu-xs">
		<a href="{$SCRIPT_NAME}?act=logs&part=lowerrors" class="btn btn-default {if isset($smarty.get.part) && $smarty.get.part == "lowerrors"} active{/if}"><span class="fa fa-fw fa-file-code-o"></span> Ошибки PHP</a>
	</div>
</div>
<div class="col-sm-9 col-md-10">
	<div class="panel panel-default">
		{$content}
	</div>
</div>