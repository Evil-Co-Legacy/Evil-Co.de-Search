{include file='documentHeader'}
	<head>
		<title>{lang}www.packageDetail.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{include file='headInclude' sandbox=false}
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
		 {include file='header' sandbox=false}

		 <div id="main">
			{include file=$detailTemplate}
		</div>
		
		{include file='footer' sandbox=false}
	</body>
</html>