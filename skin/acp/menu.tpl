<div class="container-fluid d-sm-none" id="logo-xs">
	<div class="row">
		<div class="col-md-12 text-center">
			<a href="{$SCRIPT_NAME}"><img src="{$SKIN}/img/logo.png" border="0" class="logo-xs"></a>
		</div>
	</div>
</div>

<div class="navbar navbar-expand-sm navbar-dark bg-deepdark fixed-top" role="navigation">

	<a class="navbar-brand d-none d-sm-block" href="{$SCRIPT_NAME}"><img src="{$SKIN}/img/logo_acp.png" border="0" id="logo"></a>
	<button type="button" class="btn btn-outline-light btn-block d-block d-sm-none" data-toggle="collapse" data-target=".navbar-collapse">
		Навигация
	</button>

	<div class="collapse navbar-collapse">
		<ul class="navbar-nav text-shadow">
			{foreach from=$menu_items_left item=menu_item}
				<li class="d-block d-sm-none d-lg-block{if isset($smarty.get.act) && $smarty.get.act == $menu_item['act']} active{/if}">
					<a href="{$menu_item['link']}" target="{$menu_item['window']}" class="nav-link">
						<i class="{$menu_item['icon']}"></i> {$menu_item['text']}
					</a>
				</li>
				<li class="d-none d-sm-block d-md-block d-lg-none{if isset($smarty.get.act) && $smarty.get.act == $menu_item['act']} active{/if}" rel="tooltip" title="{$menu_item['text']}" data-placement="right">
					<a href="{$menu_item['link']}" target="{$menu_item['window']}" class="nav-link">
						<i class="{$menu_item['icon']}"></i>
					</a>
				</li>
			{/foreach}
		</ul>
		<ul class="navbar-nav ml-auto">
			{foreach from=$menu_items_right item=menu_item}
				{if $menu_item['role'] == "navlink"}
					<li class="d-none d-sm-block d-lg-none {if isset($smarty.get.act) && $smarty.get.act == $menu_item['act']} active{/if}" rel="tooltip" title="{$menu_item['text']}" data-placement="left">
						<a href="{$menu_item['link']}" target="{$menu_item['window']}" class="nav-link">
							<span class="{$menu_item['icon']}"></span>
						</a>
					</li>
					<li class="d-block d-sm-none d-lg-block {if isset($smarty.get.act) && $smarty.get.act == $menu_item['act']} active{/if}">
						<a href="{$menu_item['link']}" target="{$menu_item['window']}" class="nav-link">
							<span class="{$menu_item['icon']}"></span> {$menu_item['text']}
						</a>
					</li>
				{elseif  $menu_item['role'] == "dropdown"}
					<li class="nav-item dropdown">
						<a href="#" class="nav-link dropdown-toggle d-block d-sm-none d-lg-block" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="{$menu_item['icon']}"></span> {$menu_item['text']} <span class="caret"></span></a>
						<a href="#" class="nav-link dropdown-toggle d-none d-sm-block d-md-block d-lg-none" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" rel="tooltip" title="{$menu_item['text']}" data-placement="left" data-container="body"><span class="{$menu_item['icon']}"></span><span class="caret"></span></a>
						<ul class="dropdown-menu dropdown-menu-right">
							{foreach from=$menu_item item=item}
								{if is_array($item)}
									{if $item['role'] == "davider"}
										<div class="dropdown-divider"></div>
									{elseif $item['role'] == "header"}
										<h6 class="dropdown-header">{$item['text']}</h6>
									{elseif $item['role'] == "navlink"}
										<a href="{$item['link']}" target="{$item['window']}" class="dropdown-item {if isset($smarty.get.act) && $smarty.get.act == $item['act']}active{/if}">
											<span class="{$item['icon']}"></span> {$item['text']}
										</a>
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