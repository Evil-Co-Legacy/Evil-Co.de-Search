{include file='documentHeader'}
	<head>
		<title>{lang}www.downloadPackage.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{capture append='specialStyles'}
			<style type="text/css">
				.formField {
					margin: 0 !important;
				}
				
				.buttonBar {
					padding-top: 13px;
					padding-bottom: 3px;
				}
			</style>
		{/capture}
		{include file='headInclude' sandbox=false}
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
		 {include file='header' sandbox=false}
		 
		 <div id="main">
		 	<ul class="breadCrumbs">
				<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img alt="" src="{icon}indexS.png{/icon}" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
			</ul>
		 
		 	<div class="mainHeadline">
				<img src="{icon}packageServerL.png{/icon}" alt="" />
				<div class="headlineContainer">
					<h2>{lang}www.downloadPackage.title{/lang}</h2>
				</div>
			</div>
			
			{if $userMessages|isset}{@$userMessages}{/if}
			
			<form action="index.php?page=DownloadPackage" method="post">
				<div class="border content">
					<div class="container-1">
						<h3 class="subHeadline">{lang}www.downloadPackage.license{/lang}</h3>
						<p>{lang}www.downloadPackage.pleaseReadLicense{/lang}</p>
					
						<div class="buttonBar">
							<div class="formElement">
								<div class="formField">
									<label><input type="checkbox" value="1" name="licenseAccepted"> {lang}www.downloadPackage.licenseAccept{/lang}</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="formSubmit">
					<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
					<input type="hidden" name="versionID" value="{$versionID}" />
					{@SID_INPUT_TAG}
				</div>
			</form>
		</div>
		
		{include file='footer' sandbox=false}
	</body>
</html>