{* Edit PHP Page *}
<script type="text/javascript" src="plugin/codemirror.php"></script>

<div class="card-header align-middle">
	<q>{$data['title']}</q>
	<a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$data['sid']}" class="btn btn-outline-primary btn-sm float-right"><span class="fas fa-edit fa-fw"></span> Редактировать информацию</a>
</div>
<form method="post" action="{$SCRIPT_NAME}?act=pages&part=update&page={$data['sid']}" enctype="multipart/form-data">
	<div class="card-body">
		<dl class="row">
			<dt class="col-sm-4">ID #:</dt>
			<dd class="col-sm-8">{$data['sid']}</dd>

			<dt class="col-sm-4">Название страницы:</dt>
			<dd class="col-sm-8"><a href="index.php?page={$data['alias']}" target="_blank"><i class="fas fa-external-link-square-alt fa-fw"></i>{$data['title']}</a></dd>

			<dt class="col-sm-4">Алиас страницы:</dt>
			<dd class="col-sm-8">{$data['alias']}</dd>

			{if $data['meta_description'] != ""}
				<dt class="col-sm-4">Мета описание:</dt>
				<dd class="col-sm-8">{$data['meta_description']}</dd>
			{/if}

			{if $data['meta_keywords'] != ""}
				<dt class="col-sm-4">Мета ключевые слова:</dt>
				<dd class="col-sm-8">{$data['meta_keywords']}</dd>
			{/if}

			<dt class="col-sm-4">Последнее обновление:</dt>
			<dd class="col-sm-8">{$data['lm']}</dd>
		</dl>
		<div class="row">
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Поиск<br /><span class="btn btn-outline-dark btn-sm">Ctrl</span> + <span class="btn btn-outline-dark btn-sm">F</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Cлед.результат<br /><span class="btn btn-outline-dark btn-sm">Ctrl</span> + <span class="btn btn-outline-dark btn-sm">G</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Пред.результат<br /><span class="btn btn-outline-dark btn-sm">Ctrl</span> + <span class="btn btn-outline-dark btn-sm">Shift</span> + <span class="btn btn-outline-dark btn-sm">G</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Заменить<br /><span class="btn btn-outline-dark btn-sm">Ctrl</span> + <span class="btn btn-outline-dark btn-sm">Shift</span> + <span class="btn btn-outline-dark btn-sm">F</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Заменить все<br /><span class="btn btn-outline-dark btn-sm">Ctrl</span> + <span class="btn btn-outline-dark btn-sm">Shift</span> + <span class="btn btn-outline-dark btn-sm">R</span></div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-2">Во весь экран<br /><span class="btn btn-outline-dark btn-sm">F11</span></div>
		</div>
		<div class="form-group">
			<textarea id="content" class="form-control" name="content" wrap="off">{$data['content']}</textarea>
		</div>
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-md-12">
				<input type="submit" name="update_page" class="btn btn-success btn-lg" value="Обновить страницу">
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