{* Create PHP block template *}
<div class="card-header">
	<script type="text/javascript" src="plugin/codemirror.php"></script>

	Новый PHP блок
</div>
<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=create&type=php" role="form">
	<div class="card-body">
		<div class="form-group row">
			<label for="inputAlias" class="col-md-4 form-control-plaintext text-right">
				Alias: <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение должно быть уникальным" data-placement="left"></i></small>
			</label>
			<div class="col-lg-8">
				<input type="text" name="alias" id="inputAlias" class="form-control" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputTitle" class="col-md-4 form-control-plaintext text-right">
				Заголовок:
			</label>
			<div class="col-lg-8">
				<input type="text" name="title" id="inputTitle" class="form-control" required>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Поиск<br /><span class="btn btn-outline-dark btn-sm">Ctrl</span> + <span class="btn btn-outline-dark btn-sm">F</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Cлед.результат<br /><span class="btn btn-outline-dark btn-sm">Ctrl</span> + <span class="btn btn-outline-dark btn-sm">G</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Пред.результат<br /><span class="btn btn-outline-dark btn-sm">Ctrl</span> + <span class="btn btn-outline-dark btn-sm">Shift</span> + <span class="btn btn-outline-dark btn-sm">G</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Заменить<br /><span class="btn btn-outline-dark btn-sm">Ctrl</span> + <span class="btn btn-outline-dark btn-sm">Shift</span> + <span class="btn btn-outline-dark btn-sm">F</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Заменить все<br /><span class="btn btn-outline-dark btn-sm">Ctrl</span> + <span class="btn btn-outline-dark btn-sm">Shift</span> + <span class="btn btn-outline-dark btn-sm">R</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Во весь экран<br /><span class="btn btn-outline-dark btn-sm">F11</span></div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<textarea id="content" class="form-control" name="content" wrap="off"></textarea>
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-md-12">
				<input type="submit" name="create_block" class="btn btn-success" value="Создать блок">
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