{include file='documentHeader'}
	<head>
		<title>{lang}{PAGE_TITLE}{/lang} - {$query}</title>
		{include file='headInclude' sandbox=false}
		
		<!-- Include search javascript literal -->
		<script type="text/javascript" src="{@RELATIVE_WWW_DIR}js/search.js"></script>
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
		 {include file='header' sandbox=false}
		 
		 <div id="main">
		 	<div id="resultList">
		 		{if !$items}
		 			<p class="info">{lang}www.search.result.noResults{/lang}</p>
		 		{else}
		 			<div class="contentHeader">
		 				{assign var='searchTypeID' value=$searchType->typeID}
						{pages print=true assign=pagesLinks link="index.php?form=Search&pageNo=%d&query=$encodedQuery&searchType=$searchTypeID"|concat:SID_ARG_2ND_NOT_ENCODED}
					</div>
					
					{foreach from=$results item='result'}
						<div class="result">
							<div class="message content result">
								<div class="messageInner container-{cycle name='results' values='1,2'}">
									
									<h3 class="subHeadline">
										{$result.title}
									</h3>
						
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
					
					<div class="contentFooter">
						{@$pagesLinks}
					</div>
		 		{/if}
		 	</div>
		 </div>
		 
		 {include file='footer' sandbox=false}
	</body>
</html>