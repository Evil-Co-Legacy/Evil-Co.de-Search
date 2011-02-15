{include file="documentHeader"}
	<head>
		<title>{lang}www.moderation.disabledPackages.title{/lang} - {lang}wcf.user.usercp{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{include file='headInclude' sandbox=false}
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

		{include file='header' sandbox=false}

		<div id="main">
			
			{include file="userCPHeader"}
			
			<div class="border tabMenuContent">
				<div class="container-1">
					<h3 class="subHeadline">{lang}www.moderation.disabledPackages.title{/lang}</h3>
					
					<div class="border titleBarPanel">
						<div class="containerHead">
							<h3>{lang}www.moderation.disabledPackages.packageCount{/lang}</h3>
						</div>
						<table class="tableList">
							<thead>
								<tr>
									<th><div><span class="emptyHead">{lang}www.moderation.disabledPackages.name{/lang}</span></div></th>
									<th><div><span class="emptyHead">{lang}www.moderation.disabledPackages.author{/lang}</span></div></th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$disabledPackages item='result'}
									<tr>
										<td><a href="index.php?page=ResultDetail&amp;resultID={$result->getResultID()}{@SID_ARG_2ND}">{$result->getTitle()}</a></td>
										<td>{if $result->author != ''}{$result->author}{else}&nbsp;{/if}</td>
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