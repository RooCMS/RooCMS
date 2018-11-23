{* Основной шаблон управления лентами *}

<div class="col-sm-3 col-md-2">
	<div class="row hidden-xs">
		<div class="panel panel-default">

			<div class="panel-heading visible-lg">
				Управление лентами
			</div>
			<div class="list-group">
				{if isset($smarty.get.part) && ($smarty.get.part == 'control' || $smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item')}
					<a href="{$SCRIPT_NAME}?act=feeds&part=create_item&page={$smarty.get.page}" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "create_item"} active{/if}"><span class="fa fa-fw fa-volume-up"></span> Добавить запись</a>
					<a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$smarty.get.page}" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "settings"} active{/if}"><span class="fa fa-fw fa-cog"></span> Настройки ленты</a>
				{else}
					<a href="{$SCRIPT_NAME}?act=structure&part=create&type=feed" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><span class="fa fa-fw fa-plus-circle"></span> Создать ленту</a>
				{/if}
				{if isset($smarty.get.part) && ($smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item')}
					{*<li class="nav-header">Опции</li>*}
					<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$smarty.get.page}" class="list-group-item"><span class="fa fa-fw fa-arrow-left"></span>  Вернуться в ленту</a>
				{/if}
			</div>
		</div>
	</div>

	<div class="btn-group btn-group-sm btn-group-justified visible-xs submenu-xs">
		{if isset($smarty.get.part) && ($smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item')}
			<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$smarty.get.page}" class="btn btn-default"><span class="fa fa-fw fa-arrow-left"></span></a>
		{/if}

		{if isset($smarty.get.part) && ($smarty.get.part == 'control' || $smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item')}
			<a href="{$SCRIPT_NAME}?act=feeds&part=create_item&page={$smarty.get.page}" class="btn btn-default{if isset($smarty.get.part) && $smarty.get.part == "create_item"} active{/if}"><span class="fa fa-fw fa-volume-up"></span> Добавить запись</a>
			<a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$smarty.get.page}" class="btn btn-default{if isset($smarty.get.part) && $smarty.get.part == "settings"} active{/if}"><span class="fa fa-fw fa-cog"></span> Настройки ленты</a>
		{else}
			<a href="{$SCRIPT_NAME}?act=structure&part=create&type=feed" class="btn btn-default{if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><span class="fa fa-fw fa-plus-circle"></span> Создать ленту</a>
		{/if}
	</div>
</div>
<div class="col-sm-9 col-md-10">
	<div class="panel panel-default">
		{$content}
	</div>
</div>