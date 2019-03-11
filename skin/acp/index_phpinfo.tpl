{* PHP Info *}
<div class="card">
	<div class="card-header">
		PHP Info
	</div>

	<div class="card-body">
		<div id="phpinfo" class="text-center table-responsive">
			{$phpinfo}
		</div>
	</div>
</div>
{literal}
	<script>
		$(document).ready(function() {
			$("#phpinfo").find("table").removeAttr("width").addClass("table table-condensed text-left table-striped table-bordered table-hover mb-0");
		});
	</script>
{/literal}

