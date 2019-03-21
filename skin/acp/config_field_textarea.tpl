<textarea name="{$field['name']}" id="input_{$field['name']}" class="form-control" spellcheck>{$field['value']}</textarea>

<span id="p{$field['name']}" class="btn btn-outline-primary btn-sm"><i class="fas fa-fw fa-plus"></i> Увеличить</span>
<span id="m{$field['name']}" class="btn btn-outline-primary btn-sm"><i class="fas fa-fw fa-minus"></i> Уменьшить</span>

<script>
	{literal}
	$(document).ready(function(){
		$("#p{/literal}{$field['name']}{literal}").on('click', function(){
			$("#input_{/literal}{$field['name']}{literal}").animate({height: "+=250px"},350);
		});
		$("#m{/literal}{$field['name']}{literal}").on('click', function(){
			$("#input_{/literal}{$field['name']}{literal}").animate({height: "-=250px"},350);
		});
	});
	{/literal}
</script>
