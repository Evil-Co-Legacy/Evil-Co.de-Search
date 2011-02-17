{include file="documentHeader"}
	<head>
		<title>{lang}www.moderation.licenseBlacklistRequest.title{/lang} - {lang}wcf.user.usercp{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{include file='headInclude' sandbox=false}
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

		{include file='header' sandbox=false}

		<div id="main">
			
			{include file="userCPHeader"}
			
			<div class="border tabMenuContent">
				<div class="container-1">
					<h3 class="subHeadline">{lang}www.moderation.licenseBlacklistRequest.title{/lang}</h3>
					
					<div class="contentHeader">
						<div class="largeButtons">
							<ul>
								<li><a href="index.php?page=LicenseBlacklistRequests{if !$tpl.get.showAll|isset}&amp;showAll=1{/if}{@SID_ARG_2ND}"><img src="{icon}blacklistM.png{/icon}" alt="" /> <span>{lang}www.moderation.licenseBlacklistRequest.showAll{/lang}</span></a></li>
							</ul>
						</div>
					</div>
					
					<div class="border titleBarPanel">
						<div class="containerHead">
							<h3>{lang}www.moderation.licenseBlacklistRequest.requestCount{/lang}</h3>
						</div>
						<table class="tableList">
							<thead>
								<tr>
									<th colspan="2"><div><span class="emptyHead">{lang}www.moderation.licenseBlacklistRequest.licenseRegex{/lang}</span></div></th>
									<th><div><span class="emptyHead">{lang}www.moderation.licenseBlacklistRequest.banReason{/lang}</span></div></th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$licenses item='license'}
									<tr>
										<td class="columnIcon"><a href="index.php?action=ModerateLicenseBlacklistRequest&amp;requestID={$license.requestID}&amp;action=accept{@SID_ARG_2ND}"><img src="{icon}blacklistAcceptS.png{/icon}" alt="" title="{lang}www.moderation.licenseBlacklistRequest.accept{/lang}" /></a><a href="index.php?action=ModerateLicenseBlacklistRequest&amp;requestID={$license.requestID}&amp;action=reject{@SID_ARG_2ND}"><img src="{icon}deleteS.png{/icon}" alt="" title="{lang}www.moderation.licenseBlacklistRequest.delete{/lang}" /></a></td>
										<td>{$license.licenseRegex}</td>
										<td>{$license.banReason}</td>
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