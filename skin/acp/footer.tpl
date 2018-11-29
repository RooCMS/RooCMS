{if !isset($no_footer)}
	<div class="navbar navbar-fixed-bottom navbar-inverse hidden-xs hidden-sm" id="footer">
		<div class="col-md-3 text-left footer">
			{if $exist_errors}
				<a href="{$SCRIPT_NAME}?act=logs&part=lowerrors" class="btn btn-xs btn-danger"><i class='fa fa-exclamation-triangle fa-fw'></i> Обнаружены ошибки</a>
			{else}
				{if $smarty.const.DEBUGMODE}<nobr><b class="text-warning t10"><i class="fa fa-exclamation-triangle"></i> Включен режим разработки!</b></nobr><br />{/if}
			{/if}
		</div>

		<div class="col-md-2 text-left footer">
			<small>
				<br /><nobr><i class="fa fa-bar-chart-o"></i> Число обращений к БД: <b>{$db_querys}</b></nobr>
			</small>
		</div>
		<div class="col-md-2 text-left footer">
			<small>
				<br /><nobr><i class="fa fa-tachometer"></i> Использовано памяти : <span style="cursor: help;" title="{round($debug_memusage/1024/1024, 2)} Мб макс"><b>{round($debug_memory/1024/1024, 2)} Мб</b></span></nobr>
			</small>
		</div>
		<div class="col-md-2 text-left footer">
			<small>
				<br /><nobr><i class="fa fa-clock-o"></i> Время работы скрипта : <b>{$debug_timer} мс</b></nobr>
			</small>
		</div>
		<div class="col-md-3 text-right footer">
			<small>
				<nobr>{$copyright}<br />Версия {$smarty.const.ROOCMS_FULL_VERSION}</nobr>
			</small>
		</div>
	</div>

	<div class="container visible-xs visible-sm">
		<div class="row">
			<div class="col-xs-12" style="padding-top: 20px;margin-bottom: -40px;">
				{if $smarty.const.DEBUGMODE}<nobr><b class="text-warning"><span class="fa fa-exclamation-triangle"></span> Включен режим разработки!</b></nobr><br />{/if}

				{if $exist_errors}
					<br /> <a href="{$SCRIPT_NAME}?act=logs&part=lowerrors" class="btn btn-xs btn-danger"><i class='fa fa-exclamation-triangle fa-fw'></i> Ошибки</a>
				{/if}

				<br />
				<nobr>
				<br /><i class="fa fa-bar-chart-o fa-fw"></i> Число обращений к БД: <b>{$db_querys}</b>
					<br /><i class="fa fa-tachometer fa-fw"></i> Использовано памяти : <b>{round($debug_memory/1024/1024, 2)} Мб <span class="hidden-xs">({round($debug_memusage/1024/1024, 2)} Мб макс)</span></b>
				<br /><i class="fa fa-clock-o fa-fw"></i> Время работы скрипта : <b>{$debug_timer} мс</b>
				</nobr>

				<br />
				<br /><nobr>{$copyright}<br />Версия {$smarty.const.ROOCMS_FULL_VERSION}</nobr>
			</div>
		</div>
	</div>
{/if}
</div>
</div>
</body>
</html>