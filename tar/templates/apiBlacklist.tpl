{include file="documentHeader"}
	<head>
		<title>{lang}www.moderation.apiBlacklist.title{/lang} - {lang}wcf.user.usercp{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{include file='headInclude' sandbox=false}
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

		{include file='header' sandbox=false}

		<div id="main">
			
			{include file="userCPHeader"}
			
			<div class="border tabMenuContent">
				<div class="container-1">
					<h3 class="subHeadline">{lang}www.moderation.apiBlacklist.title{/lang}</h3>
					
					<div class="border titleBarPanel">
						<div class="containerHead">
							<h3>{lang}www.moderation.apiBlacklist.targetCount{/lang}</h3>
						</div>
						<table class="tableList">
							<thead>
								<tr>
									<th{* colspan="2" *}><div><span class="emptyHead">{lang}www.moderation.apiBlacklist.target{/lang}</span></div></th>
									<th><div><span class="emptyHead">{lang}www.moderation.apiBlacklist.expire{/lang}</span></div></th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$blacklistedHosts item='host'}
									<tr>
										<td>{$host.target}</td>
										<td>{@$host.expire|datediff}</td>
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