{include file='documentHeader'}
	<head>
		<title>{lang}www.packageServerSubmit.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{include file='headInclude' sandbox=false}
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
		 {include file='header' sandbox=false}
		 
		 <div id="main">
		 	<ul class="breadCrumbs">
				<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img alt="" src="{icon}indexS.png{/icon}" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
				<li><a href="index.php?page=PackageServerList{@SID_ARG_2ND}"><img alt="" src="{icon}packageServerS.png{/icon}" /> <span>{lang}www.packageServerList.title{/lang}</span></a> &raquo;</li>
			</ul>
		 
		 	<div class="mainHeadline">
				<img src="{icon}packageServerSubmitL.png{/icon}" alt="" />
				<div class="headlineContainer">
					<h2>{lang}www.packageServerSubmit.title{/lang}</h2>
					<p>{lang}www.packageServerSubmit.description{/lang}</p>
				</div>
			</div>
			
			{if $userMessages|isset}{@$userMessages}{/if}
			
			{if $errorField}<p class="error">{lang}wcf.global.form.error{/lang}</p>{/if}
			
			<form action="index.php?form=SubmitPackageServer" method="post">
				<fieldset>
					<legend>{lang}www.packageServerSubmit.general{/lang}</legend>
				
					<div class="formElement">
						<div class="formFieldLabel">
							<label for="serverAlias">{lang}www.packageServerSubmit.serverAlias{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" value="{$serverAlias}" name="serverAlias" id="serverAlias" />
							{if $errorField == 'serverAlias'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'notUnique'}{lang}www.packageServerSubmit.serverAlias.notUnique{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc">
							<p>{lang}www.packageServerSubmit.serverAlias.description{/lang}</p>
						</div>
					</div>
					
					<div class="formElement">
						<div class="formFieldLabel">
							<label for="serverUrl">{lang}www.packageServerSubmit.serverUrl{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" value="{$serverUrl}" name="serverUrl" id="serverUrl" />
							{if $errorField == 'serverUrl'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
									{if $errorType == 'notUnique'}{lang}www.packageServerSubmit.serverAlias.notUnique{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc">
							<p>{lang}www.packageServerSubmit.serverUrl.description{/lang}</p>
						</div>
					</div>
				</fieldset>
				
				<fieldset>
					<legend>{lang}www.packageServerSubmit.optional{/lang}</legend>
				
					<div class="formElement">
						<div class="formFieldLabel">
							<label for="serverAlias">{lang}www.packageServerSubmit.homepage{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" value="{$homepage}" name="homepage" id="homepage" />
							{if $errorField == 'homepage'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc">
							<p>{lang}www.packageServerSubmit.homepage.description{/lang}</p>
						</div>
					</div>
					
					<div class="formElement">
						<div class="formFieldLabel">
							<label for="serverAlias">{lang}www.packageServerSubmit.descriptionField{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" value="{$description}" name="description" id="description" />
							{if $errorField == 'description'}
								<p class="innerError">
									{if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
								</p>
							{/if}
						</div>
						<div class="formFieldDesc">
							<p>{lang}www.packageServerSubmit.descriptionField.description{/lang}</p>
						</div>
					</div>
					
					{include file='captcha'}
				</fieldset>
				
				<div class="formSubmit">
					<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
					<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
					{@SID_INPUT_TAG}
				</div>
			</form>
		</div>
		
		{include file='footer' sandbox=false}
	</body>
</html>