{* Шаблон редактирования PHP страницы *}
<script type="text/javascript" src="plugin/codemirror.php"></script>

<p class="pull-right"><a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$data['sid']}" class="btn btn-link"><span class="fa fa-pencil-square-o fa-fw"></span> Редактировать теги</a></p>
<h3><q>{$data['title']}</q></h3>
<form method="post" action="{$SCRIPT_NAME}?act=pages&part=update&page={$data['sid']}" enctype="multipart/form-data">
	<div class="row">
		<div class="col-lg-12">
			<dl class="dl-horizontal">
				<dt>ID #:</dt>
				<dd>{$data['sid']}</dd>

				<dt>Название страницы:</dt>
				<dd><a href="index.php/page-{$data['alias']}" target="_blank">{$data['title']}</a></dd>

				<dt>Алиас страницы:</dt>
				<dd>{$data['alias']}</dd>

				<dt>Мета описание:</dt>
				<dd>{$data['meta_description']}</dd>

				<dt>Мета ключевые слова:</dt>
				<dd>{$data['meta_keywords']}</dd>

				<dt>Последнее обновление:</dt>
				<dd>{$data['lm']}</dd>
			</dl>
		</div>
	</div>
    <div class="row">
    	<div class="col-lg-12">
        	<span class="label label-info">Ctrl+F - поиск</span>
        	<span class="label label-info">Ctrl+G - след.результат</span>
        	<span class="label label-info">Shift+Ctrl+G - пред.результат</span>
        	<span class="label label-info">Shift+Ctrl+F - заменить</span>
        	<span class="label label-info">Shift+Ctrl+R - заменить все</span>
        	<span class="label label-info">F11 - во весь экран</span>
    	</div>
    </div>
    <div class="row">
    	<div class="col-lg-12">
        	<textarea id="content" class="form-control" name="content" wrap="off">{$data['content']}</textarea>
    	</div>
    </div>
	<div class="row">
    	<div class="col-lg-12 text-right">
        	<input type="submit" name="update_page" class="btn btn-success" value="Обновить страницу">
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