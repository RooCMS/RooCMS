{* Форма загрузки изображений *}
<link rel="stylesheet" type="text/css" href="plugin/uploadify/uploadify.min.css" media="screen" />
<script type="text/javascript" src="plugin/uploadify/jquery.uploadify.min.js"></script>
<p>
	Загрузить изображение: <small><span class="fa fa-info fa-fw" rel="tooltip" title="Можно загружать изображения форматов: {foreach from=$allow_images_type item=type name=itype}{$type['ext']}{if !$smarty.foreach.itype.last}, {/if}{/foreach}" data-placement="right"></span></small>

	<input type="file" name="imagesup" id="image_upload">

	<span id="images"></span>
</p>
{literal}
<script>
	$(document).ready(function() {
		// uploadufy
		$("#image_upload").uploadify({
			'swf'			: 'plugin/uploadify/uploadify.swf',
			'uploader'		: 'plugin/multiupload.php',
			'buttonText'		: '<i class="fa fa-fw fa-file-image-o"></i> Выберите файл...',
			'fileTypeDesc' 		: 'Image Files',
			'fileTypeExts' 		: '{/literal}{foreach from=$allow_images_type item=type name=itype}*.{$type['ext']}{if !$smarty.foreach.itype.last}; {/if}{/foreach}{literal}',
			'width'			: 230,
			'height'		: 36,
			//'debug'			: true,
			'removeCompleted' 	: false,
			'itemTemplate' 		: 	'<div id="${fileID}" class="alert alert-success">' +
							'<i class="fa fa-fw fa-picture-o"></i> ' +
							'<span class="fileName">${fileName} (${fileSize})</span><span class="data"></span>' +
							'</div>',
			'onUploadSuccess'	: function(file, data, response) {
				$('<input type="hidden" name="images[]" value="cache/images/'+data+'">').appendTo('#images');
			}
		});

	});
</script>
{/literal}


