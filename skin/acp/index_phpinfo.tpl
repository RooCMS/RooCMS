{* PHP Info *}

<h3>{$part_title}</h3>

<div id="phpinfo" class="text-center table-responsive">
	{$phpinfo}
</div>

{literal}
<script>
	$(document).ready(function() {
		$("#phpinfo").find("table").removeAttr("width").addClass("table table-condensed text-left table-striped table-bordered table-hover");
	});
</script>
{/literal}
