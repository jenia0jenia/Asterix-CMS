	<div class="control-group acms_panel_groups acms_panel_group_{$group_key}">
		<label class="control-label" for="field_{$field.sid}">{$field.title}</label>
		<div class="controls">
			<select name="{$field.sid}"{if $field.required} size=8{/if} class="" style="width:80%; display:block;" id="field_{$field_sid}"{if $field.required} required="required"{/if}>
			{if !$field.required}
				<option value="0">- пусто -</option>
			{/if}
			{foreach from=$field.value item=val}
				<option value="{$val.value}"{if $val.selected} selected="selected"{/if}>
				{section name=pre start=1 loop=$value.tree_level max=$value.tree_level}
					&nbsp;&nbsp;&nbsp;&nbsp;&#8680;
				{/section}
					{$val.title}{if !strlen($val.title)}[пусто]{/if}
				</option>
			{/foreach}
			</select>
		</div>
	</div>
