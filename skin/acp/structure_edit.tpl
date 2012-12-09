<form method="post" action="{$SCRIPT_NAME}?act=structure&part=edit&id={$data['id']}">
	<div id="tabs">
		<ul style="display: none;">
			<li><a href="#structure">Редактировать страницу</a></li>
		</ul>
		<div id="structure">
			<noscript><h2>Редактировать страницу</h2></noscript>
			<table width="99%" border="0" cellpadding="1" cellspacing="0">
				<tr>
					<td width="30%" align="left" valign="middle">
						<b>Название страницы:</b>
						<span class="rem"><br />Будет использовано в теге title</span>
					</td>
					<td width="70%" align="right" valign="top">
						<input type="text" name="title" class="f_input" value="{$data['title']}" required>
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="middle">
						<b>Alias страницы:</b>
						<span class="rem"><br />{if $data['id'] == 1}<b style="color: red;">Нельзя изменять алиас главной страницы!</b>{else}Должен быть обязательно уникальным.{/if}</span>
					</td>
					<td width="70%" align="right" valign="top">
						<input type="text" name="alias" class="f_input" value="{$data['alias']}" required{if $data['id'] == 1} readonly{/if}>
						<input type="hidden" name="old_alias" value="{$data['alias']}">
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="middle">
						<b>Мета описание страницы:</b>
					</td>
					<td width="70%" align="right" valign="top">
						<input type="text" name="meta_description" class="f_input" value="{$data['meta_description']}">
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="middle">
						<b>Ключевые слова страницы:</b>
					</td>
					<td width="70%" align="right" valign="top">
						<input type="text" name="meta_keywords" class="f_input" value="{$data['meta_keywords']}">
					</td>
				</tr>
                <tr>
                    <td width="30%" align="left" valign="middle">
                        <b>NOINDEX:</b>
                        <span class="rem"><br />Запрет на индексацию страницы поисковыми роботами.</span>
                    </td>
                    <td width="70%" align="right" valign="top">
                        <span  class="buttonset">
                            <input type="radio" name="noindex" value="0" id="flag_noindex_false"{if $data['noindex'] == 0} checked{/if}><label for="flag_noindex_false" style="color: #330000;">Индексировать страницу</label>
                            <input type="radio" name="noindex" value="1" id="flag_noindex_true"{if $data['noindex'] == 1} checked{/if}><label for="flag_noindex_true" style="color: #003300;">Неиндексировать страницу</label>
                        </span>
                    </td>
                </tr>
				<tr>
					<td width="30%" align="left" valign="middle">
						Порядок расположения страницы в структуре:
					</td>
					<td width="70%" align="right" valign="top">
						<input type="text" name="sort" class="f_input" value="{$data['sort']}">
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="middle">
						<b>Тип страницы:</b>
						<span class="rem"><br />Невозможно изменить после создания.</span>
					</td>
					<td width="70%" align="right" valign="middle">
						<div style="width: 99%;text-align: left;"><span style="padding-left: 7px;"><b>{$page_types[$data['type']]}</b></span></div>
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="middle">
						<b>Расположение страницы в структуре:</b>
						<span class="rem"><br />Если у данной страницы есть подчиненные элементы, они перенесуться вместе с ней.<br />Вы не сможете поместить страницу в её дочерние элементы.</span>
					</td>
					<td width="70%" align="right" valign="top">
						{if $data['id'] != 1}
							<select name="parent_id" class="f_input" required>
								{foreach from=$tree item=p}
									<option value="{$p['id']}" required {if $p['id'] == $data['parent_id']}selected{/if}>{section name=level loop=$p['level']}&middot; {/section} {$p['title']} ({$p['alias']}) </option>
								{/foreach}
							</select>
						{else}
						<div style="width: 99%;text-align: left;"><span style="padding-left: 7px;"><b>Корневая страница</b></span></div>
						<input type="hidden" name="parent_id" value="{$data['parent_id']}">
						{/if}
						<input type="hidden" name="now_parent_id" value="{$data['parent_id']}">
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="top">

					</td>
					<td width="70%" align="right" valign="top">
						<input type="hidden" name="empty" value="1">
						<input type="submit" name="update_unit" class="f_submit" value="Сохранить страницу">
					</td>
				</tr>
			</table>
		</div>
	</div>
</form>