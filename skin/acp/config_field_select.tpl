<select name="{$field['name']}" class="f_input">
	{foreach from=$field['variants'] item=option}
		<option value="{$option['value']}" {$option['selected']}>{$option['title']}</option>
	{/foreach}
</select>