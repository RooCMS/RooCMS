<div id="tabs">
	<ul style="display: none;">
		<li><a href="#addnewitem">Новый HTML блок</a></li>
	</ul>
	<div id="addnewitem">
		<noscript><h2>Новый HTML блок</h2></noscript>
		<script type="text/javascript" src="plugin/ckeditor.php"></script>
		<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=create&type=html" enctype="multipart/form-data">
				<table width="99%" border="0" cellpadding="1" cellspacing="0">
					<tr>
						<td width="30%" align="left" valign="top">
							<b>Alias</b>*
							<br /><font class="rem">Должен быть обязательно уникальным.</font>
						</td>
						<td width="70%" align="right" valign="top">
							<input type="text" name="alias" class="f_input" value="" required>
						</td>
					</tr>
					<tr>
						<td width="30%" align="left" valign="top">
							<b>Заголовок</b>*
							<br /><font class="rem">Название</font>
						</td>
						<td width="70%" align="right" valign="top">
							<input type="text" name="title" class="f_input" value="" required>
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="left" valign="top">
							<br /><b>Код блока</b>*
							<br /><font class="rem">Исполнительный код блока на языке HTML</font>
							<br /><textarea id="content" class="f_textarea" name="content"></textarea>
							<br />{$imagesupload}
							<script>{literal}CKEDITOR.replace( 'content', {toolbar: 'RooCMS'});{/literal}</script>
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="right" valign="top">
							* - поля являтся обязательными для заполнения <input type="submit" class="f_submit" name="create_block" value="Создать блок">
						</td>
					</tr>
				</table>
		</form>
	</div>
</div>
