	
	
	
	<div class="control-group acms_panel_groups acms_panel_group_{$group_key}">


		<div class="tabbable tabs-left">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#lA" data-toggle="tab">Section 1</a></li>
				<li class=""><a href="#lB" data-toggle="tab">Section 2</a></li>
				<li class=""><a href="#lC" data-toggle="tab">Section 3</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="lA">
					<p>I'm in Section A.</p>
				</div>
				<div class="tab-pane" id="lB">
					<p>Howdy, I'm in Section B.</p>
				</div>
				<div class="tab-pane" id="lC">
					<p>What up girl, this is Section C.</p>
				</div>
			</div>
		</div>	
	
	
		<label class="control-label" for="field_{$field.sid}">{$field.title}</label>
		<div class="controls">
		{if $field.value.path}
			<ul class="thumbnails">
				<li>
					<a href="#" id="{$field.sid}_first" class="thumbnail" style="width:auto; height:auto; min-width:150px; min-height:100px; background-size:contain;">
						<img src="{$field.value.path}" alt="" style="max-width:200px; max-height:100px;">
					</a>
				</li>
			{foreach from=$field.value.pre item=pre key=key}
				<li>
					<a href="#" class="thumbnail">
						{assign var=$val value=$field.value}
						<img src="{$field.value.$key}" alt="" style="max-height:100px;" />
					</a>
				</li>
			{/foreach}
			</ul>
		{/if}
			<input type="hidden" name="{$field.sid}_old_id" value="{$field.value.old|escape}" />
			<input type="text" name="{$field.sid}_title" value="{$field.value.title|escape}" placeholder="Альтернативный текст" /><br />
			<input type="file" name="{$field.sid}" id="{$field.sid}_id"{if $field.required} required="required"{/if} OnChange="

function onChangeImagefile( image_field_id, image_id ){
    var imagefile = document.getElementById( image_field_id );
 
    // HTML5 FileAPI: Firefox 3.6+, Chrome 6+
	if(typeof(FileReader)!='undefined'){
		var reader = new FileReader();
		reader.onload = function(e){
			$('#'+image_id).css('background','url(' + e.target.result + ') center center no-repeat');
			$('#'+image_id).find('img').remove()
		}
		reader.readAsDataURL(imagefile.files.item(0));
	}
}

onChangeImagefile( '{$field.sid}_id', '{$field.sid}_first' );

			" />
{if $field.value.path}
			<span style="display:block; font-size:0.8em;">
				загружен файл: <a href="{$field.value.path}" target="_blank">{$field.value.path}</a><br />
				размер: {$field.value.size|number_format:0:",":" "} байт<br />
				тип mime: {$field.value.type}<br />
			</span>
			<label class="checkbox" for="{$field.sid}_delete">удалить файл
				<input type="checkbox" name="{$field.sid}_delete" id="{$field.sid}_delete" value="1" />
			</label>
{/if}
		</div>
	</div>
