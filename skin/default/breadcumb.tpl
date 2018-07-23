{* Хлебные крошки *}
{if !empty($breadcumb)}

	<div class="btn-group btn-breadcrumb breadcrumb-default">
		<a href="{$SCRIPT_NAME}" class="btn btn-default"><i class="glyphicon glyphicon-home"></i></a>
		{*<div class="visible-lg-block">
			<div class="btn btn-warning btn-derecha"><i class="glyphicon glyphicon-star"></i><i class="glyphicon glyphicon-star"></i><i class="glyphicon glyphicon-star"></i></div>
			<div class="btn btn-danger btn-derecha">*</div>
		</div>*}
		{foreach from=$breadcumb item=bc key=i name=breadcumb}
			{if !$smarty.foreach.breadcumb.last}
				<a href="{$SCRIPT_NAME}?{if isset($bc['alias']) && trim($bc['alias']) != ""}page={$bc['alias']}{/if}{if isset($bc['part']) && trim($bc['part']) != ""}{if (isset($bc['alias']) && trim($bc['alias']) != "")}&{/if}part={$bc['part']}{/if}{if isset($bc['act']) && trim($bc['act']) != ""}{if (isset($bc['alias']) && trim($bc['alias']) != "") || (isset($bc['part']) && trim($bc['part']) != "")}&{/if}act={$bc['act']}{/if}" class="btn btn-default visible-lg-block visible-md-block">{$bc['title']}</a>
			{/if}
			{if $smarty.foreach.breadcumb.last} {*&& !isset($smarty.get.id)*}
				{*<div class="btn btn-default visible-xs-block hidden-xs visible-sm-block ">...</div>*}
				<div class="btn btn-default active"><b>{$bc['title']}</b></div>
			{/if}
		{/foreach}
		{$module->load("search")}


	</div>

	{*<ul class="breadcrumb small">
		<li>
			<a href="{$SCRIPT_NAME}"><span class="glyphicon glyphicon-home"></span></a>
		</li>
		{foreach from=$breadcumb item=bc key=i name=breadcumb}
			<li{if $smarty.foreach.breadcumb.last} class="active"{/if}>
				{if !$smarty.foreach.breadcumb.last || isset($smarty.get.id)}
					<a href="{$SCRIPT_NAME}?{if isset($bc['alias']) && trim($bc['alias']) != ""}page={$bc['alias']}{/if}{if isset($bc['part']) && trim($bc['part']) != ""}{if (isset($bc['alias']) && trim($bc['alias']) != "")}&{/if}part={$bc['part']}{/if}{if isset($bc['act']) && trim($bc['act']) != ""}{if (isset($bc['alias']) && trim($bc['alias']) != "") || (isset($bc['part']) && trim($bc['part']) != "")}&{/if}act={$bc['act']}{/if}">{$bc['title']}</a>{else}{$bc['title']}
				{/if}
			</li>
		{/foreach}
	</ul>*}
{/if}