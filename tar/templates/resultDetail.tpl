{include file='documentHeader'}
	<head>
		<title>{lang}www.resultDetail.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{include file='headInclude' sandbox=false}
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
		 {include file='header' sandbox=false}

		 <div id="main">
		 	<div class="mainHeadline">
				<img src="{icon}detailL.png{/icon}" alt="" />
				<div class="headlineContainer">
					<h2>{lang}www.packageDetail.title{/lang}</h2>
					<p>{lang}www.resultDetail.description{/lang}</p>
				</div>
			</div>
			
			{include file=$detailTemplate}
		</div>
		
		{include file='footer' sandbox=false}
	</body>
</html>