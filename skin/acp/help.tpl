{* Шаблон управления помощью сайта *}
{if $smarty.const.DEVMODE}
<div class="col-sm-3 col-md-2">
	<div class="row hidden-xs">
		<div class="panel panel-default">
			<div class="panel-heading visible-lg">
				Разработчику
			</div>
			<div class="list-group">
				<a href="{$SCRIPT_NAME}?act=help&part=create_part" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "create_part"} active{/if}"><span class="fa fa-fw fa-plus-circle"></span> Добавить новый раздел</a>
			</div>
		</div>
	</div>
	<div class="btn-group btn-group-sm btn-group-justified visible-xs submenu-xs">
		<a href="{$SCRIPT_NAME}?act=help&part=create_part" class="btn btn-default {if isset($smarty.get.part) && $smarty.get.part == "create_part"} active{/if}"><span class="fa fa-fw fa-plus-circle"></span> Добавить раздел</a>
	</div>
</div>
{/if}
<div class="col-sm-{if $smarty.const.DEVMODE}9 col-md-10{else} col-sm-12{/if}">
	<div class=" panel panel-default">
		{$content}
	</div>
</div>