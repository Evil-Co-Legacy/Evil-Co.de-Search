{include file='documentHeader'}
	<head>
		<title>{lang}www.search.searchResult{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{assign var='allowSpidersToIndexThisPage' value=false}
		{include file='headInclude' sandbox=false}
		
		<!-- Include search javascript literal -->
		<script type="text/javascript" src="{@RELATIVE_WWW_DIR}js/search.js"></script>
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
		 {include file='header' sandbox=false}
		 
		 <div id="main">
		 	<ul class="breadCrumbs">
				<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img alt="" src="{icon}indexS.png{/icon}" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
			</ul>
		 
		 	<div class="mainHeadline">
				<img src="{icon}indexL.png{/icon}" alt="" />
				<div class="headlineContainer">
					<h2>{lang}www.search.searchResult{/lang}</h2>
				</div>
			</div>
		 
		 	<div id="resultList">
		 		{if !$items}
		 			<div class="info">
		 				<p id="noResultsInfo">{lang}www.search.result.noResults{/lang}</p>
		 				
		 				{if $suggestions|count}
		 					<div class="noResultSuggestions">
		 						<p class="noResultSuggestionsHeading">{lang}www.search.result.noResults.suggestions{/lang}</p>
		 						<p class="noResultSuggestionsList">
		 							{implode from=$suggestions item='suggestion'}
		 								<a href="index.php?form=Search&amp;query=&quot;{$suggestion.query|urlencode}&quot;&amp;searchType={$searchTypeID}{@SID_ARG_2ND}">{$suggestion.query}</a>
		 							{/implode}
		 						</p>
		 					</div>
		 				{/if}
		 			</div>
		 		{else}
		 			<div class="contentHeader">
						{pages print=true assign=pagesLinks link="index.php?form=Search&pageNo=%d&query=$encodedQuery&searchType=$searchTypeID"|concat:SID_ARG_2ND_NOT_ENCODED}
					</div>
					
					{foreach from=$results item='result'}
						<div class="result">
							<div class="message content result{if $result->isDisabled} disabled{/if}">
								<div class="messageInner container-{cycle name='results' values='1,2'}">
									
									<h3 class="subHeadline">
										{if $result->getDetailTemplate()}<a href="index.php?page=ResultDetail&amp;resultID={@$result->getResultID()}&amp;searchType={@$searchType->typeID}{@SID_ARG_2ND}">{@$result->getTitle()}</a>{else}{@$result->getTitle()}{/if}
									</h3>
						
									<div class="messageBody">
										<p>{@$result->getDescription()}</p>
									</div>
									
									<div class="messageFooter">
										<div class="smallButtons">
											<ul>
												<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /></a></li>
												<li class="detailsButton"><a href="index.php?page=ResultDetail&amp;resultID={@$result->getResultID()}&amp;searchType={@$searchType->typeID}{@SID_ARG_2ND}" title="{lang}www.search.detail{/lang}"><img src="{icon}detailS.png{/icon}" alt="" /> <span>{lang}www.search.detail{/lang}</span></a>
												{if $result->getAdditionalButtons() != ''}{@$result->getAdditionalButtons()}{/if}
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