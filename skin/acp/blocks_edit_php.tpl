<div id="tabs">
	<ul style="display: none;">
		<li><a href="#addnewitem">Редактировать PHP блок</a></li>
	</ul>
	<div id="addnewitem">
		<noscript><h2>Редактировать PHP блок</h2></noscript>
		<script type="text/javascript" src="plugin/codemirror.php"></script>
		<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=update&block={$data['id']}">
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
							<br /><b>Код блока</b>
							<br /><font class="rem">Исполнительный код блока на языке PHP</font>
							<font class="rem">Подсказка:
								<br />Ctrl+F - поиск / Ctrl+G - след.результат / Shift+Ctrl+G - след.результат / Shift+Ctrl+F - заменить / Shift+Ctrl+R - заменить все</font>
							<br /><textarea id="content" class="f_textarea" name="content">{$data['content']}</textarea>
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
