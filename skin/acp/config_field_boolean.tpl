<div class="btn-group btn-group-toggle roocms-crui" data-toggle="buttons">
	<label class="btn btn-light{if $field['value'] == "true"} active{/if}" for="flag_{$field['name']}_true">
		<input type="radio" name="{$field['name']}" value="true" id="flag_{$field['name']}_true"{if $field['value'] == "true"} checked{/if}> <i class="far fa-fw fa-check-circle text-success"></i> Да
	</label>
	<label class="btn btn-light{if $field['value'] == "false"} active{/if}" for="flag_{$field['name']}_false">
		<input type="radio" name="{$field['name']}" value="false" id="flag_{$field['name']}_true"{if $field['value'] == "false"} checked{/if}> <i class="far fa-fw fa-circle text-danger"></i> Нет
	</label>
</div>
