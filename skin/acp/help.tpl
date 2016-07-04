{* Шаблон управления помощью сайта *}
<div class="col-sm-2">
	<ul class="nav nav-pills nav-stacked">
		<li class="nav-header">Разделы</li>
		{foreach from=$tree item=gpart key=k}
			{if $gpart['level'] == 1 && $k != 0}
				<li{if isset($smarty.get.u) && $smarty.get.u == $gpart['uname']} class="active"{/if}><a href="{$SCRIPT_NAME}?act=help&u={$gpart['uname']}"><span class="fa fa-fw fa-sticky-note-o"></span> {$gpart['title']}</a></li>
				{*
				{elseif $k != 0}
					<li class="{if isset($smarty.get.u) && $smarty.get.u == $gpart['uname']}active {/if}small"><a href="{$SCRIPT_NAME}?act=help&u={$gpart['uname']}"><span class="fa fa-fw fa-info"></span> {$gpart['title']}</a></li>
				*}
			{/if}
		{/foreach}

		{if $smarty.const.DEVMODE}
			<li class="nav-header">Разработчику</li>
			<li{if isset($smarty.get.part) && $smarty.get.part == "create_part"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=help&part=create_part"><span class="fa fa-fw fa-plus-circle"></span>Добавить новый раздел</a></li>
		{/if}
	</ul>
</div>
<div class="col-sm-10">
	<div class=" panel panel-default">
		{$content}
	</div>
</div>