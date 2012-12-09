<center>
	<table width="99%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="270" align="left" valign="top">
				<ul id="acp_submenu">
					<li class="part">Системная информация</li>
					<li{if !isset($smarty.get.part)} class="sel"{/if}><img src="{$SKIN}/img/ico_report.png" width="16" height="16" border="0" alt="" class="img"> <a href="{$SCRIPT_NAME}">Сводка по сайту</a></li>
					<li{if isset($smarty.get.part) && $smarty.get.part == "serverinfo"} class="sel"{/if}><img src="{$SKIN}/img/ico_server.png" width="16" height="16" border="0" alt="" class="img"> <a href="{$SCRIPT_NAME}?part=serverinfo">Информация о сервере</a></li>
					<li{if isset($smarty.get.part) && $smarty.get.part == "phpext"} class="sel"{/if}><img src="{$SKIN}/img/ico_property.png" width="16" height="16" border="0" alt="" class="img"> <a href="{$SCRIPT_NAME}?part=phpext">Установленные PHP расширения</a></li>
					<li{if isset($smarty.get.part) && $smarty.get.part == "inivars"} class="sel"{/if}><img src="{$SKIN}/img/ico_serverprop.png" width="16" height="16" border="0" alt="" class="img"> <a href="{$SCRIPT_NAME}?part=inivars">Значение PHP переменных</a></li>
					<li{if isset($smarty.get.part) && $smarty.get.part == "fileinfo"} class="sel"{/if}><img src="{$SKIN}/img/ico_docs.png" width="16" height="16" border="0" alt="" class="img"> <a href="{$SCRIPT_NAME}?part=fileinfo">Файлы и форматы</a></li>
					<li{if isset($smarty.get.part) && $smarty.get.part == "license"} class="sel"{/if}><img src="{$SKIN}/img/ico_license.png" width="16" height="16" border="0" alt="" class="img"> <a href="{$SCRIPT_NAME}?part=license">Лицензия RooCMS</a></li>
				</ul>
			</td>
			<td align="left" valign="top">
				<div id="tabs">
					<ul style="display: none;">
						<li><a href="#maininfo">{$part_title}</a></li>
						<!-- <div class="right" style="margin-top: 4px;"><nobr><a href="{$SCRIPT_NAME}?act=help" class="opt"><img src="{$SKIN}/img/ico_helpm.png" width="16" height="16" border="0" alt="" class="iconlink">Справка</a></nobr></div> -->
					</ul>
					<div id="maininfo">
						<noscript><h2>{$part_title}</h2></noscript>

						{$content}
					</div>
				</div>
			</td>
		</tr>
	</table>
</center>