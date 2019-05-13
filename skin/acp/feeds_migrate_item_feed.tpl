{* Template Migrate Feed Unit *}

<div class="card-header">
	Переносим "{$item['title']}"
</div>

<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=migrate_item&item={$item['id']}&page={$item['sid']}" role="form">
	<div class="card-body">
		<div class="row">
			<div class="col-sm-12 lead">
					Переносим публикацию: <span class="mark">"{$item['title']}"</span>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-5 text-right">
				из ленты
				<br /><span class="form-control">{$feeds[$item['sid']]['title']}</span>
			</div>
			<div class="col-sm-2 text-center">
				<i class="fas fa-fw fa-angle-double-right fa-4x"></i>
			</div>
			<div class="col-sm-5">
				в ленту
				<br />
				<select name="to" id="inputFeeds" class="selectpicker" required data-header="Ленты" data-size="auto" data-live-search="true" data-width="100%">
					{foreach from=$feeds item=f}
						<option value="{$f['id']}" data-subtext="{$f['alias']}" {if $f['id'] == $item['sid']}selected disabled="disabled" {/if}>{$f['title']}</option>
					{/foreach}
				</select>
			</div>
		</div>
	</div>
	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12 text-center">
				<input type="hidden" name="from" value="{$item['sid']}" readonly>
				<input type="hidden" name="item" value="{$item['id']}" readonly>
				<input type="submit" name="migrate_item" class="btn btn-success" value="Переместить">
			</div>
		</div>
	</div>
</form>

