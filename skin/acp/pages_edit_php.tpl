<script type="text/javascript" src="plugin/codemirror.php"></script>

<form method="post" action="{$SCRIPT_NAME}?act=pages&part=update&page={$data['sid']}" enctype="multipart/form-data">
	<div id="tabs">
		<ul style="display: none;">
			<li><a href="#edithtml">Редактор PHP страницы</a></li>
		</ul>
		<div id="edithtml">
			<noscript><h2>Редактор PHP страницы</h2></noscript>
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
			<font class="rem">Подсказка:
				<br />Ctrl+F - поиск / Ctrl+G - след.результат / Shift+Ctrl+G - след.результат / Shift+Ctrl+F - заменить / Shift+Ctrl+R - заменить все</font>
			<textarea id="content" class="f_textarea" name="content" wrap="off">{$data['content']}</textarea>

				<script>
				{literal}
				  var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
					lineNumbers: true,
					matchBrackets: true,
					mode: "text/x-php",
					indentUnit: 4,
					indentWithTabs: true,
					enterMode: "keep",
					lineWrapping: true,
					tabMode: "shift",
					onGutterClick: function(cm, n) {
					  var info = cm.lineInfo(n);
					  if (info.markerText)
						cm.clearMarker(n);
					  else
						cm.setMarker(n, "<span style=\"color: #900\">●</span> %N%");
					},
					onCursorActivity: function() {
						editor.matchHighlight("CodeMirror-matchhighlight");
					}
				  });
				{/literal}
				</script>
			<div align="right"><input type="submit" name="update_page" class="f_submit" value="Обновить страницу"></div>
		</div>
	</div>
</form>