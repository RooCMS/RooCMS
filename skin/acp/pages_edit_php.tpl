{* Шаблон редактирования PHP страницы *}
<script type="text/javascript" src="plugin/codemirror.php"></script>

<div class="panel-heading">
	<q>{$data['title']}</q>
	<p class="pull-right"><a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$data['sid']}" class="btn btn-default btn-xs"><i class="fa fa-pencil-square-o fa-fw"></i> Редактировать теги</a></p>
</div>
<form method="post" action="{$SCRIPT_NAME}?act=pages&part=update&page={$data['sid']}" enctype="multipart/form-data">
	<div class="panel-body">
		<div class="row">
			<div class="col-lg-12">
				<dl class="dl-horizontal">
					<dt>ID #:</dt>
					<dd>{$data['sid']}</dd>

					<dt>Название страницы:</dt>
					<dd><a href="index.php?page={$data['alias']}" target="_blank">{$data['title']}</a></dd>

					<dt>Алиас страницы:</dt>
					<dd>{$data['alias']}</dd>

					{if $data['meta_description'] != ""}
						<dt>Мета описание:</dt>
						<dd>{$data['meta_description']}</dd>
					{/if}

					{if $data['meta_keywords'] != ""}
						<dt>Мета ключевые слова:</dt>
						<dd>{$data['meta_keywords']}</dd>
					{/if}

					<dt>Последнее обновление:</dt>
					<dd>{$data['lm']}</dd>
				</dl>
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
				<input type="submit" name="update_page" class="btn btn-success" value="Обновить страницу">
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