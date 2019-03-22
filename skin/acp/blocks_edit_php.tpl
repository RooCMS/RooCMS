{* Edit PHP block template *}
<div class="card-header">
	<script type="text/javascript" src="plugin/codemirror.php?mode=php"></script>

	Редактируем PHP блок "{$data['title']}"
</div>
<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=update&block={$data['id']}" role="form">
	<div class="card-body">
		<div class="form-group row">
			<label for="inputAlias" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Alias: <small><i class="fa fa-question-circle fa-fw" rel="tooltip" title="Значение должно быть уникальным" data-placement="left"></i></small>
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="alias" id="inputAlias" class="form-control" value="{$data['alias']}" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputTitle" class="col-md-5 col-lg-4 form-control-plaintext text-right">
			Заголовок:
			</label>
			<div class="col-md-7 col-lg-8">
				<input type="text" name="title" id="inputTitle" class="form-control" value="{$data['title']}" required>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Поиск<br /><span class="badge badge-dark">Ctrl</span> + <span class="badge badge-dark">F</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Cлед.результат<br /><span class="badge badge-dark">Ctrl</span> + <span class="badge badge-dark">G</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Пред.результат<br /><span class="badge badge-dark">Ctrl</span> + <span class="badge badge-dark">Shift</span> + <span class="badge badge-dark">G</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Заменить<br /><span class="badge badge-dark">Ctrl</span> + <span class="badge badge-dark">Shift</span> + <span class="badge badge-dark">F</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Заменить все<br /><span class="badge badge-dark">Ctrl</span> + <span class="badge badge-dark">Shift</span> + <span class="badge badge-dark">R</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Во весь экран<br /><span class="badge badge-dark">F11</span></div>
		</div>
		<div class="form-group row">
			<div class="col-12">
				<textarea id="content" class="form-control" name="content" wrap="off">{$data['content']}</textarea>
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-12">
				<input type="hidden" name="id" value="{$data['id']}" readonly>
				<input type="hidden" name="oldalias" value="{$data['alias']}" readonly>
				<input type="submit" name="update_block" class="btn btn-lg btn-success" value="Обновить блок">
			</div>
		</div>
	</div>
</form>


<script>
	{literal}
	(function($) {
		$(window).on('load', function() {
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
		});
	})(jQuery);
	{/literal}
</script>
