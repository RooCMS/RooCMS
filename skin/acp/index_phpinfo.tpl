{* PHP Info *}
<div class="panel panel-default">
	<div class="panel-heading">
		PHP Info
	</div>

	<div class="panel-body">
		<div id="phpinfo" class="text-center table-responsive">
			{$phpinfo}
		</div>
	</div>
</div>
{literal}
	<script>
		$(document).ready(function() {
			$("#phpinfo").find("table").removeAttr("width").addClass("table table-condensed text-left table-striped table-bordered table-hover");
		});
	</script>
{/literal}

