{* Хлебные крошки *}
{if !empty($mites)}
	<ul class="breadcrumb small">
		<li>
			<a href="/index.php"><span class="fa fa-fw fa-folder-o fa-lg"></span>Главная</a>
		</li>
		{foreach from=$mites item=smites key=i name=mites}
			<li{if $smarty.foreach.mites.last} class="active"{/if}>
				{if !$smarty.foreach.mites.last || isset($smarty.get.id)}
					<a href="{$SCRIPT_NAME}?{if isset($smites['alias']) && trim($smites['alias']) != ""}page={$smites['alias']}{/if}{if isset($smites['act']) && trim($smites['act']) != ""}{if isset($smites['alias']) && trim($smites['alias']) != ""}&{/if}act={$smites['act']}{/if}{if isset($smites['part']) && trim($smites['part']) != ""}{if (isset($smites['alias']) && trim($smites['alias']) != "") || (isset($smites['act']) && trim($smites['act']) != "")}&{/if}part={$smites['part']}{/if}">{$smites['title']}</a>{else}{$smites['title']}
				{/if}
			</li>
		{/foreach}
	</ul>
{/if}