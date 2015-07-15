{* Шаблон прикрепленных сообщений *}
<link rel="stylesheet" type="text/css" href="{$SKIN}/jquery-ui-1.10.3.custom.min.css" media="screen" />
<script type="text/javascript" src="{$SKIN}/jquery-ui-1.10.3.custom.min.js"></script>

<p class="images_attach">
{foreach from=$attachimg item=img}
	<span class="thumbnail visible-inline hover-without-underline text-right" width="100" id="imga-{$img['id']}">
		<a href="/upload/images/{$img['resize']}" data-lightbox="attached" rel="lightbox"><img src="/upload/images/{$img['thumb']}" border="0" width="100" id="aimage-{$img['id']}"></a>
		<br />
			<span class="btn btn-link btn-xs hover-without-underline delete_image pull-left" id="move-{$img['id']}"><span class="fa fa-arrows fa-fw"></span></span>
			<span id="imgoption-{$img['id']}"><span class="btn btn-link btn-xs hover-without-underline delete_image" id="delimage-{$img['id']}" rel="tooltip" title="Удалить" data-placement="left"><span class="fa fa-trash-o fa-fw"></span></span></span>
		<input type="hidden" name="sort[{$img['id']}]" value="{$img['sort']}">
	</span>
{/foreach}

{literal}
<script>
	$(document).ready(function(){
		$('span[id^=delimage]').click(function() {
			var attrdata = $(this).attr('id');
			var arrdata = attrdata.split('-');
			var id = arrdata[1];

			$("#aimage-"+id).animate({'opacity':'0'}, 300, function() {
				$("#imgoption-"+id).load('/acp.php?act=ajax&part=delete_attached_image&id='+id, function() {
					$("#a-"+id).animate({'opacity':'0'}, 750, function() {
						$("#imga-"+id).hide(600).delay(900).remove();
						//$("#imga-"+id).delay(900).remove();
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
		}).disableSelection();
		//$('.images_attach').disableSelection();
	});
</script>
{/literal}
