	<div class="control-group acms_panel_groups acms_panel_group_{$group_key}">
		<label class="control-label" for="field_{$field.sid}">{$field.title}</label>
		<div class="controls">
			<textarea id="field_{$field.sid}" class="" name="{$field.sid}" style="height:200px;"{if $field.required} required="required"{/if}>{$field.value}</textarea>
		</div>
	</div>
