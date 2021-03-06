{include file='documentHeader'}
	<head>
		<title>{lang}{PAGE_TITLE}{/lang}</title>
		{include file='headInclude' sandbox=false}

		<!-- Include search javascript literal -->
		<script type="text/javascript">
			var languages = new Array();

			languages['www.search.hideAdvancedSearchOptions'] = '{lang}www.search.hideAdvancedSearchOptions{/lang}';
			languages['www.search.showAdvancedSearchOptions'] = '{lang}www.search.showAdvancedSearchOptions{/lang}';
			languages['www.search.result.noResults'] = '{lang}www.search.result.noResults{/lang}';
		</script>
		<script type="text/javascript" src="{@RELATIVE_WWW_DIR}js/search.js"></script>
		<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/Suggestion.class.js"></script>

		<!-- And now ... for the thausends of people! LEEETS GET READY FOR CHEATS! -->
		<script type="text/javascript" src="{@RELATIVE_WWW_DIR}js/konami.js"></script>
		<script type="text/javascript">
			//konami = new Konami()
			// konami.code = function() {
			//	if ($j('#advancedSearchFieldName') != null) {
			//		//$j('#query').val('com.woltlab.wbb');
			//		$j('#advancedSearchFieldName').val('com.woltlab.wbb');
			//		document.getElementsByTagName('form')[0].submit();
			//	}
			//}

			//konami.load()
		</script>
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
		 {include file='header' sandbox=false}

		 <div id="main">
		 	<div class="mainHeadline">
				<img src="{icon}indexL.png{/icon}" alt="" />
				<div class="headlineContainer">
					<h2>{lang}{PAGE_TITLE}{/lang}</h2>
					<p>{lang}{PAGE_DESCRIPTION}{/lang}</p>
				</div>
			</div>
			
			{if $userMessages|isset}{@$userMessages}{/if}

		 	{if $tpl.get.error|isset}
		 		<div class="contentHeader">
		 			<p class="error">{lang}www.search.error{/lang}</p>
		 		</div>
		 	{/if}

		 	<form action="index.php?form=Search" method="post">
		 		<div class="searchField">
		 			<input type="text" name="query" id="query" value="{lang}www.search.searchFieldValue{/lang}" onclick="search.clearField()"{if $this->user->enableInstantSearch} onkeyup="search.changedQueryField()"{/if} class="emptySearchField indexSearchField" />
		 			<input type="submit" accesskey="s" value="{lang}www.search.submit{/lang}" {if $this->user->enableInstantSearch}onclick="search.pageNo = 1; search.submitSearch(); return false;"{/if} class="indexSearchSubmit" />
		 		</div>
		 			
			 	{@SID_INPUT_TAG}

		 		<div id="advancedSearch">
		 			<fieldset>
		 				<legend><a href="javascript:openList('advancedSearchList')"><img alt="" src="{icon}minusS.png{/icon}" id="advancedSearchListImage" /></a> {lang}www.search.advancedSearch{/lang}</legend>

		 				<div id="advancedSearchList">
			 				{if $searchTypes|count > 0}
				 				<div class="formElement">
				 					<div class="formFieldLabel">
				 						<label for="searchType">{lang}www.search.advancedSearch.searchType{/lang}</label>
				 					</div>
				 					<div class="formField">
				 						<select name="searchType" onchange="if(this.value > 0) search.changeAdvancedSearch(this.value);" id="searchType">
				 							{foreach from=$searchTypes item='type'}
				 								{if $type->isDefault}{assign var='defaultSearchType' value=$type}{/if}
				 								<option value="{$type->typeID}"{if $type->isDefault} selected="selected"{/if}>{lang}www.search.advancedSearch.searchType.{$type->typeName}{/lang}</option>
				 							{/foreach}
				 						</select>
				 					</div>
				 					<div class="formFieldDesc">
				 						<p>{lang}www.search.advancedSearch.searchType.description{/lang}</p>
				 					</div>
				 				</div>
			 				{/if}

			 				<div id="advancedSearchInner">
			 					{if $defaultSearchType|isset}
				 					{foreach from=$type->getAdvancedSearchFields() item='searchField'}
				 						<div class="formElement">
				 							<div class="formFieldLabel">
				 								<label for="advancedSearchField{$searchField|ucfirst}">{lang}www.search.advancedSearch.searchType.{$defaultSearchType->typeName}.{$searchField}.label{/lang}</label>
				 							</div>
				 							<div class="formField">
				 								<input type="text" class="inputText" name="advancedSearch[{$searchField}]" id="advancedSearchField{$searchField|ucfirst}"/>
				 							</div>
				 							<div class="formFieldDesc">
				 								<p>{lang}www.search.advancedSearch.searchType.{$defaultSearchType->typeName}.{$searchField}.description{/lang}</p>
				 							</div>
				 						</div>
				 					{/foreach}
				 				{else}
				 					&nbsp;<!-- W00t? No default search type?! Bad Administrator! BAAAAAD! Er ... EVIIIIIIL -->
				 				{/if}
			 				</div>

			 				<div class="formElement">
			 					<div class="formFieldLabel">
			 						<label for="itemsPerPage">{lang}www.search.advancedSearch.itemsPerPage{/lang}</label>
			 					</div>
			 					<div class="formField">
			 						<select name="itemsPerPage" id="itemsPerPage"{if $this->user->enableInstantSearch} onchange="search.submitSearch()"{/if}>
			 							<option value="5">5</option>
			 							<option value="10">10</option>
			 							<option selected="selected" value="20">20</option>
			 							<option value="50">50</option>
			 							<option value="75">75</option>
			 							<option value="100">100</option>
			 						</select>
			 					</div>
			 					<div class="formFieldDesc">
			 						<p>{lang}www.search.advancedSearch.itemsPerPage.description{/lang}</p>
			 					</div>
			 				</div>
		 				</div>

		 				<script type="text/javascript">
		 					initList('advancedSearchList', false);
		 				</script>
		 			</fieldset>
		 		</div>
		 	</form>

		 	<div id="results" style="display: none;" class="contentBox">
		 		<h3 class="subHeadline">{lang}www.search.instantResults{/lang}</h3>
		 		<div class="messageBody" id="resultsInner">

		 		</div>
		 	</div>
		 	
		 	{cycle values='container-1,container-2' print=false advance=false}
			<div class="border infoBox">
				{if $usersOnlineTotal|isset}
					<div class="{cycle}">
						<div class="containerIcon"><img src="{icon}membersM.png{/icon}" alt="" /></div>
						<div class="containerContent">
							<h3>{if $this->user->getPermission('user.usersOnline.canView')}<a href="index.php?page=UsersOnline{@SID_ARG_2ND}">{lang}www.usersOnlineList.page.title{/lang}</a>{else}{lang}pdb.usersOnlineList.page.title{/lang}{/if}</h3> 
							<p class="smallFont">{lang}www.usersOnlineList.page.detail{/lang}</p>
							{if $usersOnline|count}
								<p class="smallFont">
									{implode from=$usersOnline item=userOnline}<a href="index.php?page=User&amp;userID={@$userOnline.userID}{@SID_ARG_2ND}">{@$userOnline.username}</a>{/implode}
								</p>
								{if $usersOnlineMarkings|count}
									<p class="smallFont">
										{lang}wcf.usersOnline.marking.legend{/lang} {implode from=$usersOnlineMarkings item=usersOnlineMarking}{@$usersOnlineMarking}{/implode}
									</p>
								{/if}
							{/if}
						</div>
					</div>
				{/if}
				{if $searchStatistics|count > 1}
					<div class="{cycle}">
						<div class="containerIcon"><img src="{icon}statsL.png{/icon}" alt="" style="width: 24px; height: 24px;" /></div>
						<div class="containerContent">
							<h3>{lang}www.index.statistics{/lang}</h3>
							<p class="smallFont">{lang}www.index.statistics.detail.{$searchStatistics.searchTypeName}{/lang}</p>
						</div>
					</div>
				{/if}
			</div>
		 </div>

		 {include file='footer' sandbox=false}
	</body>
</html>