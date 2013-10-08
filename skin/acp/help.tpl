{* Шаблон управления помощью сайта *}
<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
		<li class="nav-header">Разделы</li>
	    {foreach from=$tree item=gpart key=k}
		    {if $gpart['level'] == 1 && $k != 0}
				<li{if isset($smarty.get.u) && $smarty.get.u == $gpart['uname']} class="active"{/if}><a href="{$SCRIPT_NAME}?act=help&u={$gpart['uname']}"><span class="icon-fixed-width icon-info"></span> {$gpart['title']}</a></li>
			{*
			{elseif $k != 0}
				<li class="{if isset($smarty.get.u) && $smarty.get.u == $gpart['uname']}active {/if}small"><a href="{$SCRIPT_NAME}?act=help&u={$gpart['uname']}"><span class="icon-fixed-width icon-info"></span> {$gpart['title']}</a></li>
			*}
			{/if}
		{/foreach}

		{if $smarty.const.DEVMODE}
			<li class="nav-header">Разработчику</li>
            <li{if isset($smarty.get.part) && $smarty.get.part == "create_part"} class="active"{/if}><a href="{$SCRIPT_NAME}?act=help&part=create_part"><span class="icon-fixed-width icon-plus-sign"></span>Добавить новый раздел</a></li>
		{/if}
	</ul>
</div>
<div class="col-md-10 thumbnail">
	<div class="caption">
    	{$content}
	</div>
</div>