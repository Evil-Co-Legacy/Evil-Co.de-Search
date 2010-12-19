{if $error|isset}
	<p class="error">{lang}www.search.error{/lang}</p>
{else}
	{if $results|count}
		<div class="contentHeader">
			{pages print=true assign=pagesLinks link="javascript:search.changePage(%d)"}
		</div>
	
		<div class="resultList">
			{foreach from=$results item='result'}
				<div class="result">
					<div class="message content result">
						<div class="messageInner container-{cycle name='results' values='1,2'}">
						
							<h3 class="subHeadline">{$result.title}</h3>
										
							<div class="messageBody">
								<p>{$result.description}</p>
							</div>
							
							<div class="messageFooter">
								<div class="smallButtons">
									<ul>
										<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/upS.png" alt="{lang}wcf.global.scrollUp{/lang}" /></a></li>
										
										{if $result.additionalButtons|isset}
											{include file=$result.additionalButtons}
										{/if}
									</ul>
								</div>
							</div>
							<hr />
						</div>
					</div>
				</div>
			{/foreach}
		</div>
		
		<div class="contentFooter">
			{@$pagesLinks}
		</div>
	{else}
		<p class="info">{lang}www.search.result.noResults{/lang}</p>
	{/if}
{/if}