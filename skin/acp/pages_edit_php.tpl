{* Шаблон редактирования PHP страницы *}
<script type="text/javascript" src="plugin/codemirror.php"></script>

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
    	<div class="col-lg-12 text-right">
        	<a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$data['sid']}" class="btn btn-link"><span class="icon-edit icon-fixed-width"></span> Редактировать теги</a>
    	</div>
    </div>
    <div class="row">
    	<div class="col-lg-12">
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
        	<input type="submit" name="update_page" class="btn btn-success" value="Обновить страницу">
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