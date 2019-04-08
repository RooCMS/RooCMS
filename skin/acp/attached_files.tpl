{* Attached Files template *}

<div class="attached_files">
{foreach from=$attachfile item=file}
	<p class="bg-light p-1"  id="filea-{$file['id']}">
		<a href="/upload/files/{$file['file']}" id="afile-{$file['id']}" target="_blank" class="btn btn-link p-0">
			{if file_exists("skin/acp/img/icon/32/{$file['fileext']}.png")}<img src="skin/acp/img/icon/32/{$file['fileext']}.png" border="0" width="32" height="32" alt="">{else}<span class="fas fa-file-code fa-fw fa-2x"></span>{/if}{$file['filetitle']}
		</a>
		<span id="fileoption-{$file['id']}" class="float-right"><span class="hover-cursor" id="delfile-{$file['id']}" rel="tooltip" title="Удалить" data-placement="left"><span class="far fa-trash-alt fa-fw"></span></span></span>
	</p>
{/foreach}
</div>

{literal}
<script>
	$(document).ready(function(){
		$('span[id^=delfile]').click(function() {
			var attrdata = $(this).attr('id');
			var arrdata = attrdata.split('-');
			var id = arrdata[1];

			$("#afile-"+id).animate({'opacity':'0'}, 300, function() {
				$("#fileoption-"+id).load('/acp.php?act=ajax&part=delete_attached_file&id='+id, function() {
					$("#filea-"+id).animate({'opacity':'0'}, 750, function() {
						$("#filea-"+id).hide(600).delay(900).remove();
						//$("#filea-"+id).delay(900).remove();
					});
				});
			});
		});
	});
</script>
{/literal}
