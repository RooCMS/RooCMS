{* Шаблон редактирования PHP блока *}
<div class="panel-heading">
	<script type="text/javascript" src="plugin/codemirror.php?mode=php"></script>

	Редактируем PHP блок "{$data['title']}"
</div>
<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=update&block={$data['id']}" role="form" class="form-horizontal">
	<div class="panel-body">
		<div class="form-group">
			<label for="inputAlias" class="col-lg-3 control-label">
			Alias: <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение должно быть уникальным" data-placement="left"></span></small>
			</label>
			<div class="col-lg-9">
				<input type="text" name="alias" id="inputAlias" class="form-control" value="{$data['alias']}" required>
			</div>
		</div>
		<div class="form-group">
			<label for="inputTitle" class="col-lg-3 control-label">
			Заголовок:
			</label>
			<div class="col-lg-9">
				<input type="text" name="title" id="inputTitle" class="form-control" value="{$data['title']}" required>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-6 col-md-4 col-lg-2"><span class="btn btn-default btn-xs">Ctrl</span> + <span class="btn btn-default btn-xs">F</span> - поиск</div>
			<div class="col-sm-6 col-md-4 col-lg-2"><span class="btn btn-default btn-xs">Ctrl</span> + <span class="btn btn-default btn-xs">G</span> - след.результат</div>
			<div class="col-sm-6 col-md-4 col-lg-2"><span class="btn btn-default btn-xs">Ctrl</span> + <span class="btn btn-default btn-xs">Shift</span> + <span class="btn btn-default btn-xs">G</span> - пред.результат</div>
			<div class="col-sm-6 col-md-4 col-lg-2"><span class="btn btn-default btn-xs">Ctrl</span> + <span class="btn btn-default btn-xs">Shift</span> + <span class="btn btn-default btn-xs">F</span> - заменить</div>
			<div class="col-sm-6 col-md-4 col-lg-2"><span class="btn btn-default btn-xs">Ctrl</span> + <span class="btn btn-default btn-xs">Shift</span> + <span class="btn btn-default btn-xs">R</span> - заменить все</div>
			<div class="col-sm-6 col-md-4 col-lg-2"><span class="btn btn-default btn-xs">F11</span> - во весь экран</div>
		</div>
		<div class="form-group">
			<div class="col-lg-12">
				<textarea id="content" class="form-control" name="content" wrap="off">{$data['content']}</textarea>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<div class="row">
			<div class="col-md-12">
				<input type="hidden" name="id" value="{$data['id']}" readonly>
				<input type="hidden" name="oldalias" value="{$data['alias']}" readonly>
				<input type="submit" name="update_block" class="btn btn-success" value="Обновить блок">
			</div>
		</div>
	</div>
</form>

{literal}
<script>
	var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
		lineNumbers: true,
		lineWrapping: true,
		matchBrackets: true,
		mode: "text/x-php",
		indentUnit: 4,
		enterMode: "keep",
        	tabMode: "shift",
		gutters: ["breakpoints","CodeMirror-linenumbers"],
		extraKeys: {
			"F11": function(cm) {
				cm.setOption("fullScreen", !cm.getOption("fullScreen"));
			},
			"Esc": function(cm) {
				if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
			}
		}
	});

	editor.on("gutterClick", function(cm, n) {
	  var info = cm.lineInfo(n);
	  cm.setGutterMarker(n, "breakpoints", info.gutterMarkers ? null : makeMarker());
	});

	function makeMarker() {
	  var marker = document.createElement("div");
	  marker.style.color = "#822";
	  marker.innerHTML = "●";
	  return marker;
	}
</script>
{/literal}