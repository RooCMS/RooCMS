{* Форма загрузки изображений *}
<div id="imagesupload" style="width: 50%;" class="clear">
	<br />&nbsp;<b>Загрузить изображание</b>: <input type="file" name="images[]" class="f_input" size="47">
	<div id="moreimages"></div>
	<span style="cursor: pointer; background-color: #F2F4FF;" id="addimg">добавить ещё изображение</span>
	<script>
		$('#addimg').click(function() {
			$('<input type="file" name="images[]" size="47" class="f_input"></br>').appendTo('#moreimages');
		});
	</script>
</div>

	