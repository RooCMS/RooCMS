{* Module template: last_feed *}
<style>
	#carousel-last-feed .carousel-caption {
		left: 8% !important;
		right: 8% !important;
		bottom: 15px;
		padding: 10px !important;
		background: rgba(0, 0, 0, 0.3);
	}

	#carousel-last-feed .carousel-control {
		width: 5% !important;
		font-size: 15px !important;
	}

	#carousel-last-feed .glyphicon {
		font-size: 15px !important;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<div id="carousel-last-feed" class="carousel slide" data-ride="carousel" data-interval="6500">
			<div class="carousel-inner" role="listbox">
				{foreach from=$feeds item=feed name=lastfeed}
					<div class="item{if $smarty.foreach.lastfeed.first} active{/if}">
						<a href="{$SCRIPT_NAME}?page={$feed['alias']}&id={$feed['id']}">
							{if isset($feed['image'][0])}
								{foreach from=$feed['image'] item=image}
									<img src="upload/images/{$image['thumb']}" border="0" alt="{$image['alt']}">
								{/foreach}
							{/if}
							<div class="carousel-caption">
								{$feed['title']}
							</div>
						</a>
					</div>
				{/foreach}
			</div>

			<!-- Controls -->
			<a class="left carousel-control" href="#carousel-last-feed" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				<span class="sr-only">Prev</span>
			</a>
			<a class="right carousel-control" href="#carousel-last-feed" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		</div>
	</div>
</div>

