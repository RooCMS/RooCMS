{* Template view mailing letter *}
<div class="container mb-4">
	<div class="row">
		<div class="col-12">
			<h1>Почтовое уведомление</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="card airmail" style="min-height: 50vh;">
				<div class="card-body">
					<div class="d-flex flex-column flex-lg-row border-bottom mb-2">
						<h4 class="card-title{if empty($letter)} arial{/if}">
							{if !empty($letter)}
								{$letter['title']}
							{else}
								{*<i class="fas fa-fw fa-toilet-paper text-muted"></i>*} {*&#x2592;&#x2592;&#x2592;&#x2592;&#x2592;&#x2592; &#x2592;&#x2592;&#x2592;&#x2592;&#x2592;&#x2592;*}
								&#9618;&#9618;&#9618;&#9618;&#9618;&#9618; &#9618;&#9618;&#9618;&#9618;&#9618;&#9618;
							{/if}
						</h4>
						<span class="small mt-2 mb-3 mb-lg-0 ml-lg-auto{if empty($letter)} arial{/if}">
							{if !empty($letter)}
								{$letter['date']}
							{else}

								&#9618;&#9618;.&#9618;&#9618;.&#9618;&#9618;&#9618;&#9618;
							{/if}
						</span>
					</div>

					<div class="card-text">
						{if !empty($letter)}
							{$letter['message']}
							<hr />
							<small>Для: {$letter['nickname']} < {$letter['email']} ></small>
						{else}
							<p>
								{assign var=text value="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."}
								{$text|topsecret}
							</p>

							<p>
								{assign var=text value="Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam,eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit,sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?"}
								{$text|topsecret}
							</p>

							<p>
								{assign var=text value="At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat."}
								{$text|topsecret}
							</p>
							<div class="alert alert-danger mb-0">
								Извините, у Вас нет доступа к содержимому данного письма.
							</div>
						{/if}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>