<form method="post" action="{$SCRIPT_NAME}?act=structure&part=create">
	<div id="tabs">
		<ul style="display: none;">
			<li><a href="#structure">Добавить страницу</a></li>
		</ul>
		<div id="structure">
			<noscript><h2>Добавить страницу</h2></noscript>
			<table width="99%" border="0" cellpadding="1" cellspacing="0">
				<tr>
					<td width="30%" align="left" valign="middle">
						<b>Название страницы:</b>
						<span class="rem"><br />Будет использовано в мета теге title.</span>
					</td>
					<td width="70%" align="right" valign="top">
						<input type="text" name="title" class="f_input" required>
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="middle">
						<b>Alias страницы:</b>
						<span class="rem"><br />Должен быть обязательно уникальным.</span>
					</td>
					<td width="70%" align="right" valign="top">
						<input type="text" name="alias" class="f_input" required>
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="middle">
						<b>Мета описание страницы:</b>
					</td>
					<td width="70%" align="right" valign="top">
						<input type="text" name="meta_description" class="f_input">
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="middle">
						<b>Ключевые слова страницы:</b>
					</td>
					<td width="70%" align="right" valign="top">
						<input type="text" name="meta_keywords" class="f_input">
					</td>
				</tr>
                <tr>
                    <td width="30%" align="left" valign="middle">
                        <b>NOINDEX:</b>
                        <span class="rem"><br />Запрет на индексацию страницы поисковыми роботами.</span>
                    </td>
                    <td width="70%" align="right" valign="top">
                        <span  class="buttonset">
                            <input type="radio" name="noindex" value="0" id="flag_noindex_false" checked><label for="flag_noindex_false" style="color: #330000;">Индексировать страницу</label>
                            <input type="radio" name="noindex" value="1" id="flag_noindex_true"><label for="flag_noindex_true" style="color: #003300;">Неиндексировать страницу</label>
                        </span>
                    </td>
                </tr>
				<tr>
					<td width="30%" align="left" valign="middle">
						Порядок расположения страницы в структуре:
					</td>
					<td width="70%" align="right" valign="top">
						<input type="text" name="sort" class="f_input" value="0">
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="middle">
						<b>Тип страницы:</b>
						<span class="rem"><br />Вы не сможете в последствии изменить тип страницы.</span>
					</td>
					<td width="70%" align="right" valign="top">
						<select name="type" class="f_input" required>
							{foreach from=$page_types key=ptype item=ptitle}
								<option value="{$ptype}"{if isset($smarty.get.type) && $smarty.get.type == $ptype} selected{/if}>{$ptitle}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="middle">
						<b>Расположение страницы в структуре:</b>
					</td>
					<td width="70%" align="right" valign="top">
						<select name="parent_id" class="f_input" required>
							{foreach from=$tree item=p}
								<option value="{$p['id']}">{section name=level loop=$p['level']}&middot; {/section} {$p['title']} ({$p['alias']}) </option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="top">

					</td>
					<td width="70%" align="right" valign="top">
						<input type="hidden" name="empty" value="1">
						<input type="submit" name="create_unit" class="f_submit" value="Создать страницу">
					</td>
				</tr>
			</table>
		</div>
	</div>
</form>