{* Шаблон параметров ленты *}

<h3>Параметры ленты</h3>

<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=update_settings&page={$feed['id']}" role="form" class="form-horizontal">
	<div class="form-group">
	    <label for="inputRss" class="col-lg-3 control-label">
    		RSS вывод: <small><span class="icon-info icon-fixed-width" rel="tooltip" title="Включить/Выключить RSS ленту" data-placement="right"></span></small>
	    </label>
	    <div class="col-lg-9">
			<div class="btn-group" data-toggle="buttons">
			  <label class="btn btn-default{if $feed['rss'] == 1} active{/if}">
			    <input type="radio" name="rss" value="1" id="flag_rss_on"{if $feed['rss'] == 1} checked{/if}> Вкл
			  </label>
			  <label class="btn btn-default{if $feed['rss'] == 0} active{/if}">
			    <input type="radio" name="rss" value="0" id="flag_rss_off"{if $feed['rss'] == 0} checked{/if}> Выкл
			  </label>
			</div>
		</div>
	</div>
	<div class="form-group">
	    <label for="inputItems" class="col-lg-3 control-label">
    		Кол-во новостей на страницу: <small><span class="icon-info icon-fixed-width" rel="tooltip" title="Устанавливает кол-во новостей выводимых на странице. По-умолчанию:10. При значении 0 используется значение по-умолчанию." data-placement="right"></span></small>
	    </label>
	    <div class="col-lg-9">
			<input type="text" name="items_per_page" id="inputItems" class="form-control" value="{$feed['items_per_page']}">
		</div>
	</div>
	<div class="form-group">
	    <div class="col-lg-9 col-md-offset-3">
			<input type="submit" name="update_settings" class="btn btn-success" value="Сохранить настройки">
		</div>
	</div>
</form>