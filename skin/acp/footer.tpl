	</div>
</div>

{if !isset($no_footer)}
	<div class="navbar navbar-dark bg-deepdark fixed-bottom d-none d-md-block" id="footer">
		<div class="row align-items-center">
			<div class="col-sm-3 col-lg-3  text-left footer">
				{if $exist_errors}
					<a href="{$SCRIPT_NAME}?act=logs&part=lowerrors" class="btn btn-sm btn-outline-danger"><i class='fas fa-exclamation-triangle fa-fw'></i> Обнаружены ошибки</a>
				{else}
					{if $smarty.const.DEBUGMODE}<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Включен режим разработки!</span>{/if}
				{/if}
			</div>

			<div class="col-sm-3 col-lg-2  text-left footer">
				<small>
					<i class="far fa-chart-bar"></i> Обращений к БД: <b>{$db_querys}</b>
				</small>
			</div>
			<div class="col-sm-3 col-lg-2  text-left footer">
				<small>
					<span class="text-nowrap"><i class="fas fa-tachometer-alt"></i> Использовано памяти:</span> <span style="cursor: help;" title="{round($debug_memusage/1024/1024, 2)} Мб макс" class="text-nowrap font-weight-bold">{round($debug_memory/1024/1024, 2)} Мб</span>
				</small>
			</div>
			<div class="col-sm-3 col-lg-2 text-left footer">
				<small>
					<span class="text-nowrap"><i class="far fa-clock"></i> Время работы скрипта:</span> <b class="text-nowrap">{$debug_timer} мс</b>
				</small>
			</div>
			<div class="col-sm-12 col-lg-3 text-right footer">
				<small class="copyright">
				    {$copyright}<br />Версия <b title="{$smarty.const.ROOCMS_VERSION_ID}">{$smarty.const.ROOCMS_FULL_VERSION}</b>
				</small>
			</div>
		</div>
	</div>

	<div class="container-fluid d-block d-md-none">
		<div class="row">
			<div class="col-12 align-self-end pt-2" style="margin-bottom: -4rem;">
				{if $smarty.const.DEBUGMODE}<span class="badge badge-warning"><span class="fas fa-exclamation-triangle"></span> Включен режим разработки!</span>{/if}

				{if $exist_errors}
					<br /> <a href="{$SCRIPT_NAME}?act=logs&part=lowerrors" class="btn btn-sm btn-outline-danger"><i class='fas fa-exclamation-triangle fa-fw'></i> Ошибки</a>
				{/if}
				<br />
				<span class="text-nowrap">
				<br /><i class="far fa-chart-bar fa-fw"></i> Обращений к БД: <b>{$db_querys}</b>
				<br /><i class="fas fa-tachometer-alt fa-fw"></i> Использовано памяти :
					<br /><b>{round($debug_memory/1024/1024, 2)} Мб ({round($debug_memusage/1024/1024, 2)} Мб макс)</b>
				<br /><i class="far fa-clock fa-fw"></i> Время работы скрипта : <b>{$debug_timer} мс</b>
				</span>

				<br />
				<br /><span class="text-nowrap">{$copyright}<br />Версия <b title="{$smarty.const.ROOCMS_VERSION_ID}">{$smarty.const.ROOCMS_FULL_VERSION}</b></span>
			</div>
		</div>
	</div>
{/if}
{*{if $smarty.const.DEBUGMODE}
	{debug}
{/if}*}
</body>
</html>