<div class="container visible-xs" id="logo-xs">
	<div class="row">
    		<div class="col-md-12 text-center">
        		<a href="{$SCRIPT_NAME}"><img src="{$SKIN}/img/logo.png" border="0" class="logo-xs"></a>
    		</div>
	</div>
</div>

<div class="navbar navbar-fixed-top navbar-inverse" role="navigation">

	<div class="navbar-header text-center">
		<a class="navbar-brand hidden-xs" href="{$SCRIPT_NAME}"><img src="{$SKIN}/img/logo_acp.png" border="0" id="logo"></a>
		<button type="button" class="navbar-btn btn btn-primary visible-xs btn-block" data-toggle="collapse" data-target=".navbar-collapse">
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
		<ul class="nav navbar-nav navbar-right" style="margin-right: 2px;">

			{foreach from=$menu_items_right item=menu_item}
				{if $menu_item['role'] == "navlink"}
					<li class="hidden-xs {if isset($smarty.get.act) && $smarty.get.act == $menu_item['act']} active{/if}" rel="tooltip" title="{$menu_item['text']}" data-placement="left">
						<a href="{$menu_item['link']}" target="{$menu_item['window']}">
							<span class="{$menu_item['icon']}"></span>
						</a>
					</li>
					<li class="visible-xs {if isset($smarty.get.act) && $smarty.get.act == $menu_item['act']} active{/if}" rel="tooltip" title="{$menu_item['text']}" data-placement="left">
						<a href="{$menu_item['link']}" target="{$menu_item['window']}">
							<span class="{$menu_item['icon']}"></span> {$menu_item['text']}
						</a>
					</li>
				{elseif  $menu_item['role'] == "dropdown"}
					<li class="dropdown">
						<a href="#" class="dropdown-toggle visible-lg visible-xs" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="{$menu_item['icon']}"></span> {$menu_item['text']} <span class="caret"></span></a>
						<a href="#" class="dropdown-toggle hidden-lg hidden-xs" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" rel="tooltip" title="{$menu_item['text']}" data-placement="left" data-container="body"><span class="{$menu_item['icon']}"></span><span class="caret"></span></a>
						<ul class="dropdown-menu">
							{foreach from=$menu_item item=item}
								{if is_array($item)}
									{if $item['role'] == "davider"}
										<li role="separator" class="divider"></li>
									{elseif $item['role'] == "header"}
										<li class="dropdown-header">{$item['text']}</li>
									{elseif $item['role'] == "navlink"}
										<li {if isset($smarty.get.act) && $smarty.get.act == $item['act']}class="active"{/if}>
											<a href="{$item['link']}" target="{$item['window']}">
												<span class="{$item['icon']}"></span> {$item['text']}
											</a>
										</li>
									{/if}
								{/if}
							{/foreach}
						</ul>
					</li>
				{/if}
			{/foreach}
		</ul>
	</div>
</div>