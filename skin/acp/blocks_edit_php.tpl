{* Шаблон редактирования PHP блока *}
<script type="text/javascript" src="plugin/codemirror.php"></script>

<h3>Редактируем PHP блок "{$data['title']}"</h3>

<form method="post" action="{$SCRIPT_NAME}?act=blocks&part=update&block={$data['id']}" role="form" class="form-horizontal">
	<div class="form-group">
	    <label for="inputAlias" class="col-lg-3 control-label">
    		Alias: <small><span class="icon-info icon-fixed-width" rel="tooltip" title="Должен быть уникальным" data-placement="right"></span></small>
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

    <div class="row">
    	<div class="col-lg-12">
		    <label for="content" class="control-label">
    			Код блока: <small><span class="icon-info icon-fixed-width" rel="tooltip" title="Код блока на языке PHP" data-placement="right"></span></small>
		    </label>
        	<span class="label label-info">Ctrl+F - поиск</span>
        	<span class="label label-info">Ctrl+G - след.результат</span>
        	<span class="label label-info">Shift+Ctrl+G - пред.результат</span>
        	<span class="label label-info">Shift+Ctrl+F - заменить</span>
        	<span class="label label-info">Shift+Ctrl+R - заменить все</span>
    	</div>
    </div>
    <div class="row">
    	<div class="col-lg-12">
        	<textarea id="content" class="form-control" name="content" wrap="off">{$data['content']}</textarea>
    	</div>
    </div>

	<div class="row">
    	<div class="col-lg-12 text-right">
			<input type="hidden" name="id" value="{$data['id']}" readonly>
			<input type="hidden" name="oldalias" value="{$data['alias']}" readonly>
        	<input type="submit" name="update_block" class="btn btn-success" value="Обновить блок">
    	</div>
	</div>
</form>

{literal}
<script>
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
		cm.setMarker(n, "<span style=\"color: #d30\">●</span> %N%");
	},
	onCursorActivity: function() {
		editor.matchHighlight("CodeMirror-matchhighlight");
	}
  });
</script>
{/literal}