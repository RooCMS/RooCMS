		</div>
	</div>
</div>

{if !isset($no_footer)}
	<div class="navbar navbar-dark bg-dark fixed-bottom d-none d-md-block" id="footer">
		<div class="row align-items-center">
			<div class="col-sm-12 col-lg-3  text-left footer">
				<div class="progress my-sm-2" rel="tooltip" title="{$progress}% Завершено" data-placement="top">
					<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" aria-valuenow="{$progress}" aria-valuemin="0" aria-valuemax="100" style="width: {$progress}%">
						<span class="sr-only">{$progress}% Завершено</span>
					</div>
				</div>
			</div>

			<div class="col-sm-3 col-lg-2  text-left footer">
				<small>
					<i class="far fa-chart-bar"></i> Обращений к БД: <b>{$db_querys}</b>
				</small>
			</div>
			<div class="col-sm-3 col-lg-2  text-left footer">
				<small>
					<i class="fas fa-tachometer-alt"></i> Использовано памяти : <span style="cursor: help;" title="{round($debug_memusage/1024/1024, 2)} Мб макс" class="text-nowrap"><b>{round($debug_memory/1024/1024, 2)} Мб</b></span>
				</small>
			</div>
			<div class="col-sm-3 col-lg-2 text-left footer">
				<small>
					<i class="far fa-clock"></i> Время работы скрипта : <b class="text-nowrap">{$debug_timer} мс</b>
				</small>
			</div>
			<div class="col-sm-3 col-lg-3 text-right footer">
				<small class="copyright">
					{$copyright}<br />Версия {$smarty.const.ROOCMS_FULL_VERSION}
				</small>
			</div>
		</div>
	</div>

	<div class="container-fluid d-block d-md-none">
		<div class="row">
			<div class="col-12 align-self-end pt-2" style="margin-bottom: -4rem;">
				<div class="progress border border-warning mt-3" rel="tooltip" title="{$progress}% Завершено" data-placement="top" style="height: 2rem;">
					<div class="progress-bar progress-bar-striped bg-warning" role="progressbar" aria-valuenow="{$progress}" aria-valuemin="0" aria-valuemax="100" style="width: {$progress}%">
						{$progress}% Завершено
					</div>
				</div>
				<span class="text-nowrap">
					<br /><i class="far fa-chart-bar fa-fw"></i> Обращений к БД: <b>{$db_querys}</b>
					<br /><i class="fas fa-tachometer-alt fa-fw"></i> Использовано памяти :
					<br /><b>{round($debug_memory/1024/1024, 2)} Мб ({round($debug_memusage/1024/1024, 2)} Мб макс)</b>
					<br /><i class="far fa-clock fa-fw"></i> Время работы скрипта : <b>{$debug_timer} мс</b>
				</span>

				<br />
				<br /><span class="text-nowrap">{$copyright}<br />Версия {$smarty.const.ROOCMS_FULL_VERSION}</span>
			</div>
		</div>
	</div>
{/if}
</body>
</html>