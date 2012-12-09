<div id="tabs">
	<ul style="display: none;">
		<li><a href="#addnewitem">Новый элемент ленты</a></li>
	</ul>
	<div id="addnewitem">
		<noscript><h2>Новый элемент ленты</h2></noscript>
		<script type="text/javascript" src="plugin/ckeditor.php"></script>
		<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=create_item&page={$smarty.get.page}" enctype="multipart/form-data">
				<table width="99%" border="0" cellpadding="1" cellspacing="0">
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
						<td width="30%" align="left" valign="top">
							<b>Дата публикации</b>*
							<br /><font class="rem">Можно указать дату будущим числом. До наступления указанной даты, новость не будет опубликована.</font>
						</td>
						<td width="70%" align="right" valign="top">
							<input type="text" name="date_publications" class="f_input date" value="{$date}" placeholder="дд.мм.гггг" required>
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="left" valign="top">
							<b>Аннотация</b>*
							<br /><font class="rem">Краткое описание</font>
							<br /><textarea id="brief_item" class="f_textarea" name="brief_item"></textarea>
							<script>{literal}CKEDITOR.replace( 'brief_item', {toolbar: 'RooCMS'});{/literal}</script>
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="left" valign="top">
							<b>Текст</b>*
							<br /><font class="rem">Полное содержимое</font>
							<br /><textarea id="full_item" class="f_textarea" name="full_item"></textarea>
							<script>{literal}CKEDITOR.replace( 'full_item', {toolbar: 'RooCMS'});{/literal}</script>
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="left" valign="top">
							{$imagesupload}
						</td>
					</tr>
					<tr>
						<td width="99%" colspan="2" align="right" valign="top">
							* - поля являтся обязательными для заполнения <input type="submit" class="f_submit" name="create_item" value="Создать элемент">
						</td>
					</tr>
				</table>
		</form>
	</div>
</div>
