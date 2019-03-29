{* Breadcrumb *}
{if !empty($breadcrumb)}
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb bg-transparent w-100 mb-n1">
			<li class="breadcrumb-item"><a href="{$SCRIPT_NAME}"><i class="fas fa-fw fa-home"></i></a></li>
			{foreach from=$breadcrumb item=bc key=i name=breadcrumb}
				<li class="breadcrumb-item{if $smarty.foreach.breadcrumb.last} active{/if}">
					<a href="{$SCRIPT_NAME}?{if isset($bc['alias']) && trim($bc['alias']) != ""}page={$bc['alias']}{/if}{if isset($bc['part']) && trim($bc['part']) != ""}{if (isset($bc['alias']) && trim($bc['alias']) != "")}&{/if}part={$bc['part']}{/if}{if isset($bc['act']) && trim($bc['act']) != ""}{if (isset($bc['alias']) && trim($bc['alias']) != "") || (isset($bc['part']) && trim($bc['part']) != "")}&{/if}act={$bc['act']}{/if}">{$bc['title']}</a>
				</li>
			{/foreach}
		</ol>
	</nav>
{/if}