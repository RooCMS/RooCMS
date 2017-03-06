{* Основной шаблон конфигурации *}
<script src="{$SKIN}/jquery.booluiroocms.min.js"></script>

<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
		{*<ul class="nav nav-list">*}
		<li class="nav-header">Сайт</li>
		{if !empty($parts['global'])}
			{foreach from=$parts['global'] item=part}
				<li{if $thispart == $part['name']} class="active"{/if}><a href="{$SCRIPT_NAME}?act=config&part={$part['name']}"><i class="fa fa-fw fa-{$part['ico']}"></i> {$part['title']}</a></li>
			{/foreach}
		{/if}
		<li class="nav-header">Компоненты</li>
		{if !empty($parts['component'])}
			{foreach from=$parts['component'] item=part}
				<li{if $thispart == $part['name']} class="active"{/if}><a href="{$SCRIPT_NAME}?act=config&part={$part['name']}"><i class="fa fa-fw fa-{$part['ico']}"></i> {$part['title']}</a></li>
			{/foreach}
		{/if}
	</ul>
</div>
<div class="col-md-10">
	<div class=" panel panel-default" id="{$this_part['name']}">
		<div class="panel-heading">
			<i class="fa fa-fw fa-{$this_part['ico']}"></i> {$this_part['title']}
		</div>
		<div class="panel-body">
			<form method="post" action="{$SCRIPT_NAME}?act=config" enctype="multipart/form-data" role="form" class="form-horizontal">
				{foreach from=$this_part['options'] item=option}
					<div class="form-group" title="$config->{$option['option_name']}">
						<label for="input_{$option['option_name']}" class="col-lg-3 control-label{if $option['value'] != $option['default_value']} text-primary{/if}">
							{$option['title']}: {if $option['description'] != ""}<small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="{$option['description']}" data-placement="left"></span></small>{/if}
						</label>
						<div class="col-lg-9">
							{$option['option']}
						</div>
					</div>
				{/foreach}
				<div class="row">
					<div class="col-md-9 col-md-offset-3">
						<input type="submit" name="update_config" class="btn btn-success" value="Сохранить настройки">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

{literal}
	<script>
		$(document).ready(function(){
			$('span[id^=dci]').click(function() {
				var attrdata = $(this).attr('id');
				var arrdata = attrdata.split('-');
				var option = arrdata[1];

				$("#dci-"+option).load('/acp.php?act=ajax&part=delete_config_image&option='+option, function() {
					$("#dci-"+option).animate({'opacity':'0.2'}, 750, function() {
						$("#dci-"+option).hide(600).delay(900).remove();
					});
				});

			});
		});
	</script>
{/literal}