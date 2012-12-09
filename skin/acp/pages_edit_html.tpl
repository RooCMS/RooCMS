<script type="text/javascript" src="plugin/ckeditor.php"></script>
<form method="post" action="{$SCRIPT_NAME}?act=pages&part=update&page={$data['sid']}" enctype="multipart/form-data">
	<div id="tabs">
		<ul style="display: none;">
			<li><a href="#edithtml">Редактор HTML страницы</a></li>
		</ul>
		<div id="edithtml">
			<noscript><h2>Редактор HTML страницы</h2></noscript>
			<div style="float: right;margin-right: 1px;">
				<a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$data['sid']}" class="opt"><img src="{$SKIN}/img/ico_page_settings_edit.png" width="16" height="16" border="0" alt="" class="iconlink">Редактировать теги</a>
			</div>
			<div style="float: left;text-align: right;margin-right: 5px;">
				<b>ID #:
				<br />Название страницы:
				<br />Алиас страницы:
				<br />Мета описание:
				<br />Мета ключевые слова:
				<br />Время последнего обновления:
				</b>
			</div>
			<div  align="left">
				{$data['sid']}
				<br />{$data['title']}
				<br />{$data['alias']}
				<br />{$data['meta_description']}
				<br />{$data['meta_keywords']}
				<br />{$data['lm']}
			</div>
			<textarea id="content_field" class="f_textarea" name="content">{$data['content']}</textarea>
			<br />{$attachedimages}
			<br />{$imagesupload}
			<script>{literal}CKEDITOR.replace( 'content_field', {toolbar: 'RooCMS'});{/literal}</script>
			<div align="right"><input type="submit" name="update_page" class="f_submit" value="Обновить страницу"></div>
		</div>
	</div>
</form>