{include file='documentHeader'}
	<head>
		<title>{lang}www.reportPackage.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{assign var='allowSpidersToIndexThisPage' value=true}
		{include file='headInclude' sandbox=false}
		
		<!-- Include search javascript literal -->
		<script type="text/javascript" src="{@RELATIVE_WWW_DIR}js/search.js"></script>
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
		 {include file='header' sandbox=false}
		 
		 <div id="main">
		 	<ul class="breadCrumbs">
				<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img alt="" src="{icon}indexS.png{/icon}" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
				<li><a href="index.php?page=ResultDetail&amp;searchTypeName=PackageType&amp;resultID={$package->getResultID()}"><img alt="" src="{icon}detailM.png{/icon}" /> <span>{$package->getTitle()}</span></a> &raquo;</li>
			</ul>
		 
		 	<div class="mainHeadline">
				<img src="{icon}indexL.png{/icon}" alt="" />
				<div class="headlineContainer">
					<h2>{lang}www.reportPackage.title{/lang}</h2>
				</div>
			</div>
			
			<form action="index.php?form=ReportPackage" method="post">
				<fieldset>
					<legend>{lang}www.reportPackage.generalInformation{/lang}</legend>
					
					<div class="formElement">
						<p class="formFieldLabel">{lang}www.reportPackage.generalInformation.resultName{/lang}</p>
						<p class="formField">{$package->getTitle()}</p>
					</div>
					
					<div class="formElement">
						<div class="formFIeldLabel">
							<label for="reason">{lang}www.reportPackage.generalInformation.reason{/lang}</label>
						</div>
						<div class="formField">
							<textarea id="reason" name="reason" cols="40" rows="10" style="width: 98%;">{$reason}</textarea>
						</div>
					</div>
					
					{include file='captcha'}
				</fieldset>
				
				<div class="formSubmit">
		 			<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" {if $this->user->enableInstantSearch}onclick="search.pageNo = 1; search.submitSearch(); return false;"{/if} />
		 			<input type="hidden" name="packageID" value="{$package->getResultID()}" />
			 		{@SID_INPUT_TAG}
		 		</div>
			</form>
		</div>
	</body>
</html>