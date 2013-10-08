{* Основной шаблон конфигурации *}
<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
	{*<ul class="nav nav-list">*}
		<li class="nav-header">Компоненты</li>
			{if !empty($parts['component'])}
			{foreach from=$parts['component'] item=part}
				<li{if $thispart == $part['name']} class="active"{/if}><a href="{$SCRIPT_NAME}?act=config&part={$part['name']}"><span class="icon-fixed-width icon-{$part['ico']}"></span> {$part['title']}</a></li>
			{/foreach}
			{/if}
		<li class="nav-header">Модули</li>
			{if !empty($parts['mod'])}
			{foreach from=$parts['mod'] item=part}
				<li{if $thispart == $part['name']} class="active"{/if}><a href="{$SCRIPT_NAME}?act=config&part={$part['name']}"><span class="icon-fixed-width icon-{$part['ico']}"></span> {$part['title']}</a></li>
			{/foreach}
			{/if}
		<li class="nav-header">Виджеты</li>
			{if !empty($parts['widget'])}
			{foreach from=$parts['widget'] item=part}
				<li{if $thispart == $part['name']} class="active"{/if}><a href="{$SCRIPT_NAME}act=config&part={$part['name']}"><span class="icon-fixed-width icon-{$part['ico']}"></span> {$part['title']}</a></li>
			{/foreach}
			{/if}
	</ul>
</div>
<div class="col-md-10 thumbnail">
	<div class="caption" id="{$this_part['name']}">
    	<form method="post" action="{$SCRIPT_NAME}?act=config" role="form" class="form-horizontal">
        	{foreach from=$this_part['options'] item=option}
				<div class="form-group" title="$config->{$option['option_name']}">
				    <label for="input_{$option['option_name']}" class="col-lg-3 control-label">
    					{$option['title']}: {if $option['description'] != ""}<small><span class="icon-info icon-fixed-width" rel="tooltip" title="{$option['description']}" data-placement="left"></span></small>{/if}
				    </label>
				    <div class="col-lg-9">
						{$option['option']}
					</div>
				</div>
    	    {/foreach}
			<div class="row">
    			<div class="col-lg-9 col-md-offset-3">
					<input type="hidden" name="empty" value="1" readonly>
        			<input type="submit" name="update_config" class="btn btn-success" value="Сохранить настройки">
    			</div>
			</div>
    	</form>
	</div>
</div>