{include file="documentHeader"}
	<head>
		<title>{lang}www.moderation.overview.title{/lang} - {lang}wcf.user.usercp{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		
		{capture append='specialStyles'}
			<link rel="stylesheet" type="text/css" media="screen" href="{@RELATIVE_WWW_DIR}style/moderationOverview.css" />
		{/capture}
		{include file='headInclude' sandbox=false}
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

		{include file='header' sandbox=false}

		<div id="main">
			
			{include file="userCPHeader"}
			
			<div class="border tabMenuContent">
				<div class="container-1">
					<h3 class="subHeadline">{lang}www.moderation.overview.title{/lang}</h3>
					
					<ul class="moderationOverview">
						<li>
							<img src="{icon}packageServerL.png{/icon}" alt="" />				
							<ul>
								<li><a href="index.php?page=ModerationPackageServerRequest{@SID_ARG_2ND}">{lang}www.moderation.packageServerRequest.count{/lang}</a></li>
							</ul>
						</li>
						<li>
							<img src="{icon}detailL.png{/icon}" alt="" />
							<ul>
								<li><a href="index.php?page=DisabledPackages{@SID_ARG_2ND}">{lang}www.moderation.disabledPackages.count{/lang}</a></li>
								<li><a href="index.php?page=PackageReports{@SID_ARG_2ND}">{lang}www.moderation.packageReports.count{/lang}</a>
							</ul>
						</li>
					</ul>
				</div>
			</div>
			
		</div>
		
		{include file='footer' sandbox=false}
	</body>
</html>