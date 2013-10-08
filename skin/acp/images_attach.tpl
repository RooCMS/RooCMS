{* Шаблон прикрепленных сообщений *}
<link rel="stylesheet" type="text/css" href="{$SKIN}/jquery-ui-1.10.3.custom.min.css" media="screen" />
<script type="text/javascript" src="{$SKIN}/jquery-ui-1.10.3.custom.min.js"></script>

<p class="images_attach">
{foreach from=$attachimg item=img}
    <span class="thumbnail visible-inline hover-without-underline text-right" width="100" id="a-{$img['id']}">
		<a href="/upload/images/resize/{$img['filename']}" data-lightbox="attached" rel="lightbox"><img src="/upload/images/thumb/{$img['filename']}" border="0" width="100" id="aimage-{$img['id']}"></a>
		<br />
			<span class="btn btn-link btn-xs hover-without-underline delete_image" id="move-{$img['id']}"><span class="icon-move icon-fixed-width"></span></span>
			<span id="option-{$img['id']}"><span class="btn btn-link btn-xs hover-without-underline delete_image" id="del-{$img['id']}" rel="tooltip" title="Удалить" data-placement="left"><span class="icon-trash icon-fixed-width"></span></span></span>
		<input type="hidden" name="sort[{$img['id']}]" value="{$img['sort']}">
	</span>
{/foreach}

{literal}
<script>
	$(document).ready(function(){
		$('span[id^=del]').click(function() {
			var attrdata = $(this).attr('id');
			var arrdata = attrdata.split('-');
			var id = arrdata[1];

			$("#aimage-"+id).animate({'opacity':'0'}, 300, function() {
		        $("#option-"+id).load('/acp.php?act=ajax&part=delete_attached_image&id='+id, function() {
            		$("#a-"+id).animate({'opacity':'0'}, 750, function() {
            			$("#a-"+id).hide(600);
            			$("#a-"+id).delay(900).remove();
					});
				});
	        });
		});

		$('.images_attach').sortable({
			connectWith: '.images_attach',
			opacity: 0.8,
			stop: function(event, ui) {
				var i = 1;
				$(this).children(".thumbnail").each(function() {
					$(this).children("input[type=hidden]").val(i);
					i++;
				});
			}
		});
		$('.images_attach').disableSelection();
	});
</script>
{/literal}
