	</div>
</div>

{if !isset($no_footer)}
	<div class="navbar navbar-dark bg-dark fixed-bottom d-none d-md-block" id="footer">
		<div class="row align-items-center">
			<div class="col-sm-3 text-left footer">
				{if $exist_errors}
					<a href="{$SCRIPT_NAME}?act=logs&part=lowerrors" class="btn btn-xs btn-danger"><i class='fa fa-exclamation-triangle fa-fw'></i> Обнаружены ошибки</a>
				{else}
					{if $smarty.const.DEBUGMODE}<nobr><b class="text-warning t10"><i class="fa fa-exclamation-triangle"></i> Включен режим разработки!</b></nobr><br />{/if}
				{/if}
			</div>

			<div class="col-sm-2 text-left footer">
				<small>
					<i class="far fa-chart-bar"></i> Число обращений к БД: <b>{$db_querys}</b>
				</small>
			</div>
			<div class="col-md-2 text-left footer">
				<small>
					<i class="fas fa-tachometer-alt"></i> Использовано памяти : <span style="cursor: help;" title="{round($debug_memusage/1024/1024, 2)} Мб макс"><b>{round($debug_memory/1024/1024, 2)} Мб</b></span>
				</small>
			</div>
			<div class="col-md-2 text-left footer">
				<small>
					<i class="far fa-clock"></i> Время работы скрипта : <b>{$debug_timer} мс</b>
				</small>
			</div>
			<div class="col-md-3 text-right footer">
				<small>
					{$copyright}<br />Версия {$smarty.const.ROOCMS_FULL_VERSION}
				</small>
			</div>
		</div>
	</div>

	<div class="container-fluid d-block d-md-none">
		<div class="row">
			<div class="col-sm-9 offset-sm-3 align-self-end" style="padding-top: 1rem;margin-bottom: -4rem;">
				{if $smarty.const.DEBUGMODE}<nobr><b class="text-warning"><span class="fa fa-exclamation-triangle"></span> Включен режим разработки!</b></nobr><br />{/if}

				{if $exist_errors}
					<br /> <a href="{$SCRIPT_NAME}?act=logs&part=lowerrors" class="btn btn-xs btn-danger"><i class='fa fa-exclamation-triangle fa-fw'></i> Ошибки</a>
				{/if}
				<br />
				<nobr>
				<br /><i class="far fa-chart-bar fa-fw"></i> Число обращений к БД: <b>{$db_querys}</b>
				<br /><i class="fas fa-tachometer-alt fa-fw"></i> Использовано памяти : <b>{round($debug_memory/1024/1024, 2)} Мб <span class="hidden-xs">({round($debug_memusage/1024/1024, 2)} Мб макс)</span></b>
				<br /><i class="far fa-clock fa-fw"></i> Время работы скрипта : <b>{$debug_timer} мс</b>
				</nobr>

				<br />
				<br /><nobr>{$copyright}<br />Версия {$smarty.const.ROOCMS_FULL_VERSION}</nobr>
			</div>
		</div>
	</div>
{/if}
</body>
</html>