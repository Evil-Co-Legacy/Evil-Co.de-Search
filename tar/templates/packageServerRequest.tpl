{include file='documentHeader'}
	<head>
		<title>{lang}www.packageServerRequest.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{include file='headInclude' sandbox=false}
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
		 {include file='header' sandbox=false}
		 
		 <div id="main">
		 	<ul class="breadCrumbs">
				<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img alt="" src="{icon}indexS.png{/icon}" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
				<li><a href="index.php?page=PackageServerList{@SID_ARG_2ND}"><img alt="" src="{icon}packageServerS.png{/icon}" /> <span>{lang}www.packageServerList.title{/lang}</span></a> &raquo;</li>
			</ul>
		 
		 	<div class="mainHeadline">
				<img src="{icon}packageServerL.png{/icon}" alt="" />
				<div class="headlineContainer">
					<h2>{lang}www.packageServerRequest.title{/lang}</h2>
				</div>
			</div>
			
			{if $userMessages|isset}{@$userMessages}{/if}
			
			{if $this->user->getPermission('mod.search.canModerate') && $request.state == 'waiting' || $additionalLargeButtons|isset}
				<div class="contentHeader">
					<div class="largeButtons">
						<ul>
							<li>
								<a href="index.php?page=ModerateServerRequest&amp;requestID={@$request.requestID}&amp;action=reject{@SID_ARG_2ND}"><img src="{icon}packageServerRequestRejectM.png{/icon}" alt="" /> <span>{lang}www.packageServerRequest.reject{/lang}</span></a>
							</li>
							
							<li>
								<a href="index.php?page=ModerateServerRequest&amp;requestID={@$request.requestID}&amp;action=pending{@SID_ARG_2ND}"><img src="{icon}packageServerRequestPendingM.png{/icon}" alt="" /> <span>{lang}www.packageServerRequest.pending{/lang}</span></a>
							</li>
							
							<li>
								<a href="index.php?page=ModerateServerRequest&amp;requestID={@$request.requestID}&amp;action=accept{@SID_ARG_2ND}"><img src="{icon}packageServerRequestAcceptM.png{/icon}" alt="" /> <span>{lang}www.packageServerRequest.accept{/lang}</span></a>
							</li>
						</ul>
					</div>
				</div>
			{/if}
			
			<div class="requestInformation">
				<fieldset>
					<legend>{lang}www.packageServerRequest.information{/lang}</legend>
					
					<div class="formElement">
						<p class="formFieldLabel">{lang}www.packageServerRequest.serverAlias{/lang}</p>
						<p class="formField">{$request.serverAlias}</p>
					</div>
					
					<div class="formElement">
						<p class="formFieldLabel">{lang}www.packageServerRequest.serverUrl{/lang}</p>
						<p class="formField">{$request.serverUrl}</p>
					</div>
					
					{if $request.homepage != ''}
						<div class="formElement">
							<p class="formFieldLabel">{lang}www.packageServerRequest.homepage{/lang}</p>
							<p class="formField">{$request.homepage}</p>
						</div>
					{/if}
					
					{if $request.description != ''}
						<div class="formElement">
							<p class="formFieldLabel">{lang}www.packageServerRequest.description{/lang}</p>
							<p class="formField">{$request.description}</p>
						</div>
					{/if}
					
					<div class="formElement">
						<p class="formFieldLabel">{lang}www.packageServerRequest.author{/lang}</p>
						<p class="formField"><a href="index.php?page=User&amp;userID={$request.authorID}{@SID_ARG_2ND}">{$request.authorName}</a></p>
					</div>
					
					{if $request.moderatorID}
						<div class="formElement">
							<p class="formFieldLabel">{lang}www.packageServerRequest.moderator{/lang}</p>
							<p class="formField"><a href="index.php?page=User&amp;userID={$request.moderatorID}{@SID_ARG_2ND}">{$request.moderatorName}</a></p>
						</div>
					{/if}
					
					<div class="formElement">
						<p class="formFieldLabel">{lang}www.packageServerRequest.state{/lang}</p>
						<p class="formField">{lang}www.packageServerRequest.state.{$request.state}{/lang}</p>
					</div>
				</fieldset>
			</div>
		</div>
		
		{include file='footer' sandbox=false}
	</body>
</html>