{* Хлебные крошки *}
{if !empty($mites)}
	<ul class="breadcrumb small">
		<li>
			<a href="/index.php"><span class="fa fa-fw fa-folder-o fa-lg"></span>Главная</a>
		</li>
		{foreach from=$mites item=smites key=i name=mites}
			<li{if $smarty.foreach.mites.last} class="active"{/if}>
				{if !$smarty.foreach.mites.last || isset($smarty.get.id)}<a href="{$SCRIPT_NAME}?page={$smites['alias']}">{$smites['title']}</a>{else}{$smites['title']}{/if}
			</li>
		{/foreach}
	</ul>
{/if}