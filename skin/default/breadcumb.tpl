{* Хлебные крошки *}
{if !empty($breadcumb)}
	<ul class="breadcrumb small">
		<li>
			<a href="/index.php"><span class="fa fa-fw fa-folder-o fa-lg"></span>Главная</a>
		</li>
		{foreach from=$breadcumb item=bc key=i name=breadcumb}
			<li{if $smarty.foreach.breadcumb.last} class="active"{/if}>
				{if !$smarty.foreach.breadcumb.last || isset($smarty.get.id)}
					<a href="{$SCRIPT_NAME}?{if isset($bc['alias']) && trim($bc['alias']) != ""}page={$bc['alias']}{/if}{if isset($bc['act']) && trim($bc['act']) != ""}{if isset($bc['alias']) && trim($bc['alias']) != ""}&{/if}act={$bc['act']}{/if}{if isset($bc['part']) && trim($bc['part']) != ""}{if (isset($bc['alias']) && trim($bc['alias']) != "") || (isset($bc['act']) && trim($bc['act']) != "")}&{/if}part={$bc['part']}{/if}">{$bc['title']}</a>{else}{$bc['title']}
				{/if}
			</li>
		{/foreach}
	</ul>
{/if}