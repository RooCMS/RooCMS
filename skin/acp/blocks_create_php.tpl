{* Шаблон создания HTML блока *}
<script type="text/javascript" src="plugin/codemirror.php"></script>

<h3>Новый PHP блок</h3>

<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=create&type=php" role="form" class="form-horizontal">
	<div class="form-group">
	    <label for="inputAlias" class="col-lg-3 control-label">
    		Alias: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Значение должно быть уникальным" data-placement="auto"></span></small>
	    </label>
	    <div class="col-lg-9">
			<input type="text" name="alias" id="inputAlias" class="form-control" required>
		</div>
	</div>
	<div class="form-group">
	    <label for="inputTitle" class="col-lg-3 control-label">
    		Заголовок:
	    </label>
	    <div class="col-lg-9">
			<input type="text" name="title" id="inputTitle" class="form-control" required>
		</div>
	</div>

    <div class="row">
    	<div class="col-lg-12">
		    <label for="content" class="control-label">
    			Код блока: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Код блока на языке PHP" data-placement="auto"></span></small>
		    </label>
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
        	<textarea id="content" class="form-control" name="content" wrap="off"></textarea>
    	</div>
    </div>

	<div class="row">
    	<div class="col-lg-12 text-right">
        	<input type="submit" name="create_block" class="btn btn-success" value="Создать блок">
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