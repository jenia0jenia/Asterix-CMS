<p class="a_field">
	{$field.title}:<br />
	<select name="{$field.sid}[]" multiple="multiple" style="width:300px" size="8">
	{foreach from=$field.value item=value}
		<option value="{$value.value}"{if $value.selected} selected="selected"{/if}>{$value.title}{if !strlen($value.title)}[пусто]{/if}</option>
	{/foreach}
	</select>
	<br /><small class="grey">Чтобы выбрать несколько наименований, используйте клавишу Ctrl</small>
</p>
