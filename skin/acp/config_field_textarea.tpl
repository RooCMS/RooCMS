<textarea name="{$field['name']}" id="{$field['name']}" class="f_textarea">{$field['value']}</textarea>
<br />
<font class="ta_resize" id="p{$field['name']}">+ увеличить</font> 
<font class="ta_resize" id="m{$field['name']}">- уменьшить</font>
{literal}
<script>
	$("#p{/literal}{$field['name']}{literal}").click(function(){
		$("#{/literal}{$field['name']}{literal}").animate({width: "+=150px", height: "+=250px"},350);
	});
	$("#m{/literal}{$field['name']}{literal}").click(function(){
		$("#{/literal}{$field['name']}{literal}").animate({width: "-=150px", height: "-=250px"},350);
	});
</script>
{/literal}