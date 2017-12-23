{if !isset($no_footer)}
	<div class="navbar navbar-fixed-bottom navbar-inverse hidden-xs hidden-sm" id="footer">
		<div class="col-md-3 text-left footer">
			{if $smarty.const.DEBUGMODE}<nobr><b class="text-warning t10"><span class="fa fa-exclamation-triangle"></span> Включен режим отладки!</b></nobr><br />{/if}
			{if !$smarty.const.DEBUGMODE && $smarty.const.DEVMODE}<nobr><b class="text-error t10"><span class="fa fa-exclamation-triangle"></span> ТРЕБУЕТСЯ ВКЛЮЧИТЬ РЕЖИМ ОТЛАДКИ !!!</b></nobr><br />{/if}
			{if $smarty.const.DEVMODE}<nobr><b class="text-warning t10"><span class="fa fa-exclamation-triangle"></span> Включен режим разработчика!</b></nobr>{/if}
		</div>

		<div class="col-md-2 text-left footer">
			<small>
				<br /><nobr><span class="fa fa-bar-chart-o"></span> Число обращений к БД: <b>{$db_querys}</b></nobr>
			</small>
		</div>
		<div class="col-md-2 text-left footer">
			<small>
				<br /><nobr><span class="fa fa-tachometer"></span> Использовано памяти : <span style="cursor: help;" title="{round($debug_memusage/1024/1024, 2)} Мб макс"><b>{round($debug_memory/1024/1024, 2)} Мб</b></span></nobr>
			</small>
		</div>
		<div class="col-md-2 text-left footer">
			<small>
				<br /><nobr><span class="fa fa-clock-o"></span> Время работы скрипта : <b>{$debug_timer} мс</b></nobr>
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
				{if $smarty.const.DEBUGMODE}<nobr><b class="text-warning"><span class="fa fa-exclamation-triangle"></span> Включен режим отладки!</b></nobr><br />{/if}
				{if !$smarty.const.DEBUGMODE && $smarty.const.DEVMODE}<nobr><b class="text-error"><span class="fa fa-exclamation-triangle"></span> ТРЕБУЕТСЯ ВКЛЮЧИТЬ РЕЖИМ ОТЛАДКИ !!!</b></nobr><br />{/if}
				{if $smarty.const.DEVMODE}<nobr><b class="text-warning"><span class="fa fa-exclamation-triangle"></span> Включен режим разработчика!</b></nobr>{/if}

				<br />
				<nobr>
				<br /><span class="fa fa-bar-chart-o fa-fw"></span> Число обращений к БД: <b>{$db_querys}</b>
					<br /><span class="fa fa-tachometer fa-fw"></span> Использовано памяти : <b>{round($debug_memory/1024/1024, 2)} Мб <span class="hidden-xs">({round($debug_memusage/1024/1024, 2)} Мб макс)</span></b>
				<br /><span class="fa fa-clock-o fa-fw"></span> Время работы скрипта : <b>{$debug_timer} мс</b>
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