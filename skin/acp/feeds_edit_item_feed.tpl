<div id="tabs">
	<ul style="display: none;">
		<li><a href="#edititem">Редактировать элемент</a></li>
	</ul>
	<div id="edititem">
		<noscript><h2>Редактировать элемент</h2></noscript>
		<script type="text/javascript" src="plugin/ckeditor.php"></script>
		<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=update_item&item={$item['id']}&page={$item['sid']}" enctype="multipart/form-data">
				<table width="99%" border="0" cellpadding="1" cellspacing="0">
					<tr>
						<td width="30%" align="left" valign="top">
							<b>Заголовок</b>*
							<br /><font class="rem">Название</font>
						</td>
						<td width="70%" align="right" valign="top">
							<input type="text" name="title" class="f_input" value="{$item['title']}" required>
						</td>
					</tr>
					<tr>
						<td width="30%" align="left" valign="top">
							<b>Дата публикации</b>*
							<br /><font class="rem">Можно указать дату будущим числом. До наступления указанной даты, новость не будет опубликована.</font>
						</td>
						<td width="70%" align="right" valign="top">
							<input type="text" name="date_publications" class="f_input date" value="{$item['date_publications']}" placeholder="дд.мм.гггг" required>
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="left" valign="top">
							<b>Аннотация</b>*
							<br /><font class="rem">Краткое описание</font>
							<br /><textarea id="brief_item" class="f_textarea" name="brief_item" required>{$item['brief_item']}</textarea>
							<script>{literal}CKEDITOR.replace( 'brief_item', {toolbar: 'RooCMS'});{/literal}</script>
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="left" valign="top">
							<b>Текст</b>*
							<br /><font class="rem">Полное содержимое</font>
							<br /><textarea id="full_item" class="f_textarea" name="full_item" required>{$item['full_item']}</textarea>
							<script>{literal}CKEDITOR.replace( 'full_item', {toolbar: 'RooCMS'});{/literal}</script>
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="left" valign="top">
							{$attachedimages}
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="left" valign="top">
							{$imagesupload}
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="right" valign="top">
							* - поля являтся обязательными для заполнения <input type="submit" class="f_submit" name="update_item" value="Сохранить элемент">
						</td>
					</tr>
				</table>
		</form>
	</div>
</div>
