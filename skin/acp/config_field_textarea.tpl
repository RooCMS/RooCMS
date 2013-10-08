<textarea name="{$field['name']}" id="input_{$field['name']}" class="form-control">{$field['value']}</textarea>
<br />
<font id="p{$field['name']}" class="btn btn-xs">+ увеличить</font>
<font id="m{$field['name']}" class="btn btn-xs">- уменьшить</font>
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