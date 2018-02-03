<textarea name="{$field['name']}" id="input_{$field['name']}" class="form-control" spellcheck>{$field['value']}</textarea>

<span id="p{$field['name']}" class="btn btn-default btn-xs"><i class="fa fa-fw fa-plus"></i> Увеличить</span>
<span id="m{$field['name']}" class="btn btn-default btn-xs"><i class="fa fa-fw fa-minus"></i> Уменьшить</span>

<script>
	{literal}
	$(document).ready(function(){
		$("#p{/literal}{$field['name']}{literal}").click(function(){
			$("#input_{/literal}{$field['name']}{literal}").animate({height: "+=250px"},350);
		});
		$("#m{/literal}{$field['name']}{literal}").click(function(){
			$("#input_{/literal}{$field['name']}{literal}").animate({height: "-=250px"},350);
		});
	});
	{/literal}
</script>
