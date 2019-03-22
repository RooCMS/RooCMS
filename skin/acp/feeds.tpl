{* Template Feeds *}

<div class="col-lg-2">
	<div class="card d-none d-lg-block submenu sticky-top">
		<div class="card-header">
			Управление лентами
		</div>
		<div class="list-group">
			{if isset($smarty.get.part) && ($smarty.get.part == 'control' || $smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item')}
				<a href="{$SCRIPT_NAME}?act=feeds&part=create_item&page={$smarty.get.page}" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "create_item"} active{/if}"><i class="fas fa-fw fa-pen-square"></i> Добавить запись</a>
				<a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$smarty.get.page}" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "settings"} active{/if}"><i class="fas fa-fw fa-cog"></i> Настройки ленты</a>
			{else}
				<a href="{$SCRIPT_NAME}?act=structure&part=create&type=feed" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><i class="fas fa-fw fa-plus-circle"></i> Создать ленту</a>
			{/if}
		</div>

		{if !empty($subfeeds)}
			<div class="card-header">
				Вложенные ленты
			</div>
			<div class="list-group">
				{foreach from=$subfeeds item=subfeed}
					<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$subfeed['id']}" class="list-group-item list-group-item-action text-decoration-none"><i class="fas fa-fw fa-th-list"></i> {$subfeed['title']}</a>
				{/foreach}
			</div>
		{/if}

		{if isset($smarty.get.part) && ($smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item' || $smarty.get.part == 'migrate_item')}
			<div class="card-header">
				&bull;&bull;&bull;
			</div>
			<div class="list-group">
				<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$smarty.get.page}" class="list-group-item list-group-item-action text-decoration-none"><i class="fas fa-fw fa-arrow-left"></i>  Вернуться в ленту</a>
			</div>
		{/if}
	</div>

	<div class="row justify-content-center mb-3">
		<div class="col-auto">
			<div class="btn-group btn-group-sm d-block d-lg-none">
				{if isset($smarty.get.part) && ($smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item' || $smarty.get.part == 'migrate_item')}
					<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$smarty.get.page}" class="btn btn-outline-primary"><i class="fa fa-fw fa-arrow-left"></i></a>
				{/if}

				{if isset($smarty.get.part) && ($smarty.get.part == 'control' || $smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item')}
					<a href="{$SCRIPT_NAME}?act=feeds&part=create_item&page={$smarty.get.page}" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "create_item"} active{/if}"><i class="fas fa-fw fa-pen-square"></i> Добавить запись</a>
					<a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$smarty.get.page}" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "settings"} active{/if}"><i class="fas fa-fw fa-cog"></i> Настройки ленты</a>
				{else}
					<a href="{$SCRIPT_NAME}?act=structure&part=create&type=feed" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><i class="fas fa-fw fa-plus-circle"></i> Создать ленту</a>
				{/if}
			</div>
		</div>
	</div>
</div>
<div class="col-lg-10">
	<div class="card">
		{$content}
	</div>
</div>