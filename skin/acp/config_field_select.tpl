<select name="{$field['name']}"  id="input_{$field['name']}" class="selectpicker" data-size="auto" data-width="50%">
	{foreach from=$field['variants'] item=option}
		<option value="{$option['value']}" {$option['selected']}>{$option['title']}</option>
	{/foreach}
</select>