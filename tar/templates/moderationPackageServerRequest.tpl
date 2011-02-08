{include file="documentHeader"}
	<head>
		<title>{lang}www.moderation.packageServerRequest.title{/lang} - {lang}wcf.user.usercp{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{include file='headInclude' sandbox=false}
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

		{include file='header' sandbox=false}

		<div id="main">
			
			{include file="userCPHeader"}
			
			<div class="border tabMenuContent">
				<div class="container-1">
					<h3 class="subHeadline">{lang}www.moderation.packageServerRequest.title{/lang}</h3>
					
					<div class="contentHeader">
						<div class="largeButtons">
							<ul>
								<li><a href="index.php?page=ModerationPackageServerRequest{if !$tpl.get.showOthers|isset}&amp;showOthers=1{/if}{@SID_ARG_2ND}"><img src="packageServerM.png" alt="" /> <span>{lang}www.moderation.packageServerRequest.showOthers{/lang}</span></a></li>
							</ul>
						</div>
					</div>
					
					<div class="border titleBarPanel">
						<div class="containerHead">
							<h3>{lang}www.moderation.packageServerRequest.requestCount{/lang}</h3>
						</div>
						<table class="tableList">
							<thead>
								<tr>
									<th><div><span class="emptyHead">{lang}www.moderation.packageServerRequest.alias{/lang}</span></div></th>
									<th><div><span class="emptyHead">{lang}www.moderation.packageServerRequest.author{/lang}</span></div></th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$requests item='request'}
									<tr>
										<td><a href="index.php?page=PackageServerRequest&amp;requestID={$request.requestID}{@SID_ARG_2ND}">{$request.serverAlias}</a> ({$request.serverUrl})</td>
										<td>{if $request.authorID}<a href="index.php?page=User&amp;userID={$request.authorID}{@SID_ARG_2ND}">{$request.authorName}</a>{else}&nbsp;{/if}</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		
		{include file='footer' sandbox=false}
	</body>
</html>