<span  class="buttonset">
	<input type="radio" name="{$field['name']}" value="true" id="flag_{$field['name']}_true"{if $field['value'] == "true"} checked{/if}><label for="flag_{$field['name']}_true" style="color: #003300;">Да / Yes</label>
	<input type="radio" name="{$field['name']}" value="false" id="flag_{$field['name']}_false"{if $field['value'] == "false"} checked{/if}><label for="flag_{$field['name']}_false" style="color: #330000;">Нет / No</label>
</span>
