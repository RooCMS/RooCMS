<div class="btn-group" data-toggle="buttons">
  <label class="btn btn-default{if $field['value'] == "true"} active{/if}" for="flag_{$field['name']}_true">
	<input type="radio" name="{$field['name']}" value="true" id="flag_{$field['name']}_true"{if $field['value'] == "true"} checked{/if}> <span class="text-success"><span class="icon-fixed-width icon-check"></span> Да</span>
  </label>
  <label class="btn btn-default{if $field['value'] == "false"} active{/if}" for="flag_{$field['name']}_false">
	<input type="radio" name="{$field['name']}" value="false" id="flag_{$field['name']}_true"{if $field['value'] == "false"} checked{/if}> <span class="text-danger"><span class="icon-fixed-width icon-check-empty"></span> Нет</span>
  </label>
</div>