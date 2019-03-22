{* Template configuration *}
<div class="col-lg-2">
	<div class="card d-none d-lg-block submenu sticky-top">
		<div class="card-header">
			Сайт
		</div>
		<div class="list-group">
			{foreach from=$parts['global'] item=part}
				<a href="{$SCRIPT_NAME}?act=config&part={$part['name']}" class="list-group-item list-group-item-action text-decoration-none{if $thispart == $part['name']} active{/if}"><i class="fas fa-fw fa-{$part['ico']}"></i> {$part['title']}</a>
			{/foreach}
		</div>
		<div class="card-header">
			Компоненты
		</div>
		<div class="list-group">
			{foreach from=$parts['component'] item=part}
				<a href="{$SCRIPT_NAME}?act=config&part={$part['name']}" class="list-group-item list-group-item-action text-decoration-none{if $thispart == $part['name']} active{/if}"><i class="fas fa-fw fa-{$part['ico']}"></i> {$part['title']}</a>
			{/foreach}
		</div>
	</div>

	<div class="row justify-content-center mb-3">
		<div class="col-auto">
			<div class="btn-group btn-group-sm d-block d-lg-none">
				{foreach from=$parts['global'] item=part}
					<a href="{$SCRIPT_NAME}?act=config&part={$part['name']}" class="btn btn-outline-primary{if $thispart == $part['name']} active{/if}" title="{$part['title']}"><i class="fas fa-fw fa-{$part['ico']}"></i> </a>
				{/foreach}
				{foreach from=$parts['component'] item=part}
					<a href="{$SCRIPT_NAME}?act=config&part={$part['name']}" class="btn btn-outline-primary{if $thispart == $part['name']} active{/if}" title="{$part['title']}"><i class="fas fa-fw fa-{$part['ico']}"></i> </a>
				{/foreach}
			</div>
		</div>
	</div>
</div>

<div class="col-lg-10">
	<div class="card" id="{$this_part['name']}">
		<div class="card-header">
			<i class="fa fa-lg fa-fw fa-{$this_part['ico']}"></i> {$this_part['title']}
		</div>
		<form method="post" action="{$SCRIPT_NAME}?act=config" enctype="multipart/form-data" role="form">
			<div class="card-body">
				{foreach from=$this_part['options'] item=option}
					<div class="form-group row" title="$config->{$option['option_name']}">
						<label for="input_{$option['option_name']}" class="col-md-4 form-control-plaintext text-right{if $option['value'] != $option['default_value']} text-secondary{/if}">
							{$option['title']}: {if $option['description'] != ""}<small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="{$option['description']}" data-placement="left"></i></small>{/if}
						</label>
						<div class="col-md-8">
							{$option['option']}
						</div>
					</div>
				{/foreach}
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-md-8 offset-md-4">
						<input type="submit" name="update_config" class="btn btn-lg btn-success" value="Сохранить настройки">
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	{literal}
	(function($) {
		$(window).on('load', function() {
			$('[id^=dci]').on('click', function() {
				let attrdata = $(this).attr('id');
				let arrdata = attrdata.split('-');
				let option = arrdata[1];

				$("#ci-"+option).load('/acp.php?act=ajax&part=delete_config_image&option='+option, function() {
					$("#ci-"+option).animate({'opacity':'0.2'}, 750, function() {
						$("#dci-"+option).hide(750).delay(900).remove();
					});
				});
			});
		});
	})(jQuery);
	{/literal}
</script>
