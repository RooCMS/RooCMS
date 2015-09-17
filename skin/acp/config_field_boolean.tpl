<div class="btn-group boolui-roocms" data-toggle="buttons">
	<label class="btn btn-default{if $field['value'] == "true"} active{/if}" for="flag_{$field['name']}_true">
		<input type="radio" name="{$field['name']}" value="true" id="flag_{$field['name']}_true"{if $field['value'] == "true"} checked{/if}> <span class="text-success"><i class="fa fa-fw fa-check-square-o"></i> Да</span>
	</label>
	<label class="btn btn-default{if $field['value'] == "false"} active{/if}" for="flag_{$field['name']}_false">
		<input type="radio" name="{$field['name']}" value="false" id="flag_{$field['name']}_true"{if $field['value'] == "false"} checked{/if}> <span class="text-danger"><i class="fa fa-fw fa-square-o"></i> Нет</span>
	</label>
</div>