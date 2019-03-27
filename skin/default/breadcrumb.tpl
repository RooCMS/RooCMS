{* Хлебные крошки *}
{if !empty($breadcrumb)}

	<nav aria-label="breadcrumb">
		<ol class="breadcrumb bg-transparent border w-100 mt-1">
			<li class="breadcrumb-item"><a href="{$SCRIPT_NAME}"><i class="fas fa-fw fa-home"></i></a></li>
			{foreach from=$breadcrumb item=bc key=i name=breadcrumb}
				<li class="breadcrumb-item{if $smarty.foreach.breadcrumb.last} active{/if}">
					{if !$smarty.foreach.breadcrumb.last || isset($smarty.get.id)}
						<a href="{$SCRIPT_NAME}?{if isset($bc['alias']) && trim($bc['alias']) != ""}page={$bc['alias']}{/if}{if isset($bc['part']) && trim($bc['part']) != ""}{if (isset($bc['alias']) && trim($bc['alias']) != "")}&{/if}part={$bc['part']}{/if}{if isset($bc['act']) && trim($bc['act']) != ""}{if (isset($bc['alias']) && trim($bc['alias']) != "") || (isset($bc['part']) && trim($bc['part']) != "")}&{/if}act={$bc['act']}{/if}">{$bc['title']}</a>
					{else}
						{$bc['title']}
					{/if}
				</li>
			{/foreach}
		</ol>
	</nav>

	{* {$module->load("search")} *}
{/if}