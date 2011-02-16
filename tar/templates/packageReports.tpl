{include file="documentHeader"}
	<head>
		<title>{lang}www.moderation.packageReports.title{/lang} - {lang}wcf.user.usercp{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{include file='headInclude' sandbox=false}
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

		{include file='header' sandbox=false}

		<div id="main">
			
			{include file="userCPHeader"}
			
			<div class="border tabMenuContent">
				<div class="container-1">
					<h3 class="subHeadline">{lang}www.moderation.packageReports.title{/lang}</h3>
					
					<div class="packageReportList">
						{foreach from=$packageReports item='report'}
							<div class="message content result">
								<div class="messageInner container-{cycle name='results' values='1,2'}">
									
									<h3 class="subHeadline">
										<a href="index.php?page=ResultDetail&amp;searchTypeName=PackageType&amp;resultID={$report.packageID}{@SID_ARG_2ND}">{if $report.state == 'new'}<strong>{$report.packageName}</strong>{else}{$report.packageName}{/if}</a>
									</h3>
						
									<div class="messageBody">
										<p>{$report.reason}</p>
									</div>
									
									<div class="messageFooter">
										<div class="smallButtons">
											<ul>
												<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /></a></li>
												<li class="pmButton"><a href="index.php?form=PMNew&amp;userID={$report.authorID}{@SID_ARG_2ND}" title="{lang}www.packageReport.pm{/lang}"><img src="{icon}pmEmptyS.png{/icon}" alt="" /> <span>{lang}www.packageReport.pm{/lang}</span></a></li>
												<li class="reportDeleteButton"><a href="index.php?action=PackageReportDelete&amp;reportID={$report.reportID}{@SID_ARG_2ND}" title="{lang}www.packageReport.delete{/lang}"><img src="{icon}deleteS.png{/icon}" alt="" /> <span>{lang}www.packageReport.delete{/lang}</span></a></li>
											</ul>
										</div>
									</div>
									<hr />
								</div>
							</div>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
		
		{include file='footer' sandbox=false}
	</body>
</html>