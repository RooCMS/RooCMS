<div id="tabs">
	<ul style="display: none;">
		<li><a href="#addnewitem">Редактировать HTML блок</a></li>
	</ul>
	<div id="addnewitem">
		<noscript><h2>Редактировать HTML блок</h2></noscript>
		<script type="text/javascript" src="plugin/ckeditor.php"></script>
		<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=update&block={$data['id']}" enctype="multipart/form-data">
				<table width="99%" border="0" cellpadding="1" cellspacing="0">
					<tr>
						<td width="30%" align="left" valign="top">
							<b>Alias</b>*
							<br /><font class="rem">Должен быть обязательно уникальным.</font>
						</td>
						<td width="70%" align="right" valign="top">
							<input type="text" name="alias" class="f_input" value="{$data['alias']}" required>
						</td>
					</tr>
					<tr>
						<td width="30%" align="left" valign="top">
							<b>Заголовок</b>*
							<br /><font class="rem">Название</font>
						</td>
						<td width="70%" align="right" valign="top">
							<input type="text" name="title" class="f_input" value="{$data['title']}" required>
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="left" valign="top">
							<br /><b>Код блока</b>*
							<br /><font class="rem">Исполнительный код блока на языке HTML</font>
							<br /><textarea id="content" class="f_textarea" name="content">{$data['content']}</textarea>
							<br />{$attachedimages}
							<br />{$imagesupload}
							<script>{literal}CKEDITOR.replace( 'content', {toolbar: 'RooCMS'});{/literal}</script>
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="right" valign="top">
							<input type="hidden" name="id" value="{$data['id']}">
							<input type="hidden" name="oldalias" value="{$data['alias']}">
							* - поля являтся обязательными для заполнения <input type="submit" class="f_submit" name="update_block" value="Обновить блок">
						</td>
					</tr>
				</table>
		</form>
	</div>
</div>
