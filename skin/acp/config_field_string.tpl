<input type="text" name="{$field['name']}" id="input_{$field['name']}" class="form-control{if $field['type'] == "color"} colorpicker{/if}" value="{$field['value']}"{if $field['type'] == "email"} pattern="^\s*\w+\.*\w*@\w+\.\w+\s*"{elseif $field['type'] == "int" OR $field['type'] == "integer"} pattern="^[ 0-9]+$"{/if}>