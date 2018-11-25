{* Основной шаблон управления лентами *}

<div class="col-sm-3 col-md-2">
	<div class="row hidden-xs">
		<div class="panel panel-default">

			<div class="panel-heading visible-lg">
				Управление лентами
			</div>
			<div class="list-group">
				{if isset($smarty.get.part) && ($smarty.get.part == 'control' || $smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item')}
					<a href="{$SCRIPT_NAME}?act=feeds&part=create_item&page={$smarty.get.page}" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "create_item"} active{/if}"><i class="fa fa-fw fa-volume-up"></i> Добавить запись</a>
					<a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$smarty.get.page}" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "settings"} active{/if}"><i class="fa fa-fw fa-cog"></i> Настройки ленты</a>
				{else}
					<a href="{$SCRIPT_NAME}?act=structure&part=create&type=feed" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><i class="fa fa-fw fa-plus-circle"></i> Создать ленту</a>
				{/if}
			</div>

			{if !empty($subfeeds)}
				<div class="panel-heading">
					<span class="visible-lg">Вложенные ленты</span>
				</div>
				<div class="list-group">
					{foreach from=$subfeeds item=subfeed}
						<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$subfeed['id']}" class="list-group-item"><i class="fa fa-fw fa-newspaper-o"></i> {$subfeed['title']}</a>
					{/foreach}
				</div>
			{/if}

			{if isset($smarty.get.part) && ($smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item')}
				<div class="panel-heading visible-lg">
					&bull;&bull;&bull;
				</div>
				<div class="list-group">
					<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$smarty.get.page}" class="list-group-item"><i class="fa fa-fw fa-arrow-left"></i>  Вернуться в ленту</a>
				</div>
			{/if}
		</div>
	</div>

	<div class="btn-group btn-group-sm btn-group-justified visible-xs submenu-xs">
		{if isset($smarty.get.part) && ($smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item')}
			<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$smarty.get.page}" class="btn btn-default"><i class="fa fa-fw fa-arrow-left"></i></a>
		{/if}

		{if isset($smarty.get.part) && ($smarty.get.part == 'control' || $smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item')}
			<a href="{$SCRIPT_NAME}?act=feeds&part=create_item&page={$smarty.get.page}" class="btn btn-default{if isset($smarty.get.part) && $smarty.get.part == "create_item"} active{/if}"><i class="fa fa-fw fa-volume-up"></i> Добавить запись</a>
			<a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$smarty.get.page}" class="btn btn-default{if isset($smarty.get.part) && $smarty.get.part == "settings"} active{/if}"><i class="fa fa-fw fa-cog"></i> Настройки ленты</a>
		{else}
			<a href="{$SCRIPT_NAME}?act=structure&part=create&type=feed" class="btn btn-default{if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><i class="fa fa-fw fa-plus-circle"></i> Создать ленту</a>
		{/if}
	</div>
</div>
<div class="col-sm-9 col-md-10">
	<div class="panel panel-default">
		{$content}
	</div>
</div>