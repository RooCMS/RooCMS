{* Шаблон прикрепленных сообщений *}
<div id="attachedimages">
	{foreach from=$attachimg item=img}
		<div class="aimage ui-state-default">
			<img src="/upload/images/thumb/{$img['filename']}" border="0" height="50" class="unit_image">
			<font class="delphoto" id="del.{$img['id']}">X</font>
			<!-- <div class="unit_image_options" style="height: 50px;">
				<input type="text" name="alt" value="{$img['alt']}" class="f_input_m" style="margin-bottom: 5px;" placeholder="alt текст">
				<div align="right"><font class="f_submit">Сохранить</font></div>
			</div> -->
			<input type="hidden" name="sort[{$img['id']}]" value="{$img['sort']}">
		</div>
	{/foreach}
</div>

<script>
{literal}
$(document).ready(function(){

	$('.delphoto').css('display', 'block');

	$('#attachedimages').sortable({
		connectWith: '#attachedimages',
		opacity: 0.8,
		stop: function(event, ui) {
			var i = 1;
			$(this).children(".aimage").each(function() {
				$(this).children("input[type=hidden]").val(i);
				i++;
			});
		}
	});
	
	$('#attachedimages').disableSelection();

	//$('.unit_image_options').hide();

	/*$('.aimage').click(function() {
		if($(this).find('.unit_image_options').css('display') == 'none') {
			$(this).animate({'margin-right':'250px'},1400,function(){
				$(this).find('.unit_image_options').fadeIn('slow');
				$(this).css('margin-right','0px');
			});
		}
	});*/
	
	/*$('.unit_image').click(function() {
		if($(this).find('.unit_image_options').css('display') != 'none') {
			$(this).parent().find('.unit_image_options').fadeOut(1400);
		}
	});*/
	
	$('font[id^=del]').click(function() {
		var tid = $(this).attr('id');
		var ids = tid.split('.');
		var id	= ids[1];
		
		var aimage = $(this).parent('.aimage');
		aimage.find('img').animate({'opacity':'0'}, 300, function() {
            aimage.load('/acp.php?act=ajax&part=image_delete&id='+id, function() {
			  aimage.fadeIn(700).delay(300).fadeOut(700);
			});
        });
	});
});
{/literal}
</script>
	