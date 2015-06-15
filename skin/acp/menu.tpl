{literal}
<style>
	#logo {top: 5px; left: 0px;}
	.navbar-brand {width: 110px;}
</style>
{/literal}

<div class="container visible-xs" id="logo-xs">
	<div class="row">
    		<div class="col-md-12 text-center">
        		<a href="{$SCRIPT_NAME}"><img src="{$SKIN}/img/acp_logo_full.png" border="0"></a>
    		</div>
	</div>
</div>

<div class="navbar navbar-fixed-top navbar-inverse" role="navigation">

		<div class="navbar-header text-center">
			<a class="navbar-brand hidden-xs" href="{$SCRIPT_NAME}"><img src="{$SKIN}/img/acp_logo_full.png" border="0" class="absolute" id="logo"></a>
			<button type="button" class="navbar-btn btn btn-danger visible-xs btn-block" data-toggle="collapse" data-target=".navbar-collapse">
				Навигация
			</button>
		</div>

		<div class="collapse navbar-collapse">

			<ul class="nav navbar-nav tshadow">
				{foreach from=$menu_items_left item=menu_item}
				<li class="visible-lg visible-xs{if isset($smarty.get.act) && $smarty.get.act == $menu_item['act']} active{/if}">
					<a href="{$menu_item['link']}" target="{$menu_item['window']}">
						<span class="{$menu_item['icon']}"></span> {$menu_item['text']}
					</a>
				</li>
				<li class="hidden-lg hidden-xs{if isset($smarty.get.act) && $smarty.get.act == $menu_item['act']} active{/if}" rel="tooltip" title="{$menu_item['text']}" data-placement="right">
					<a href="{$menu_item['link']}" target="{$menu_item['window']}">
						<span class="{$menu_item['icon']}"></span>
					</a>
				</li>
				{/foreach}
			</ul>
			<ul class="nav navbar-nav navbar-right" style="margin-right: 5px;">
				{foreach from=$menu_items_right item=menu_item}
				<li class="visible-lg visible-xs{if isset($smarty.get.act) && $smarty.get.act == $menu_item['act']} active{/if}">
					<a href="{$menu_item['link']}" target="{$menu_item['window']}">
						<span class="hidden-sm"><span class="{$menu_item['icon']}"></span> {$menu_item['text']}</span>
					</a>
				</li>
				<li class="hidden-lg hidden-xs{if isset($smarty.get.act) && $smarty.get.act == $menu_item['act']} active{/if}" rel="tooltip" title="{$menu_item['text']}" data-placement="left">
					<a href="{$menu_item['link']}" target="{$menu_item['window']}">
						<span class="{$menu_item['icon']}"></span>
					</a>
				</li>
				{/foreach}
			</ul>
		</div>
</div>