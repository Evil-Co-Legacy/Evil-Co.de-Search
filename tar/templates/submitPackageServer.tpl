{include file='documentHeader'}
	<head>
		<title>{lang}www.packageServerSubmit.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
		{include file='headInclude' sandbox=false}
	</head>
	<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
		 {include file='header' sandbox=false}
		 
		 <div id="main">
		 	<ul class="breadCrumbs">
				<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img alt="" src="{icon}indexS.png{/icon}"> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
			</ul>
		 
		 	<div class="mainHeadline">
				<img src="{icon}packageServerAddL.png{/icon}" alt="" />
				<div class="headlineContainer">
					<h2>{lang}www.packageServerSubmit.title{/lang}</h2>
					<p>{lang}www.packageServerSubmit.description{/lang}</p>
				</div>
			</div>
			
			<form action="index.php?form=SubmitPackageServer" method="post">
				<fieldset>
					<legend>{lang}www.packageServerSubmit.general{/lang}</legend>
				
					<div class="formElement">
						<div class="formFieldLabel">
							<label for="serverAlias">{lang}www.packageServerSubmit.serverAlias{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" value="{$serverAlias}" name="serverAlias" id="serverAlias" />
						</div>
						<div class="formFieldDesc">
							<p>{lang}www.packageServerSubmit.serverAlias{/lang}</p>
						</div>
					</div>
					
					<div class="formElement">
						<div class="formFieldLabel">
							<label for="serverUrl">{lang}www.packageServerSubmit.serverUrl{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" value="{$serverUrl}" name="serverUrl" id="serverUrl" />
						</div>
						<div class="formFieldDesc">
							<p>{lang}www.packageServerSubmit.serverUrl{/lang}</p>
						</div>
					</div>
					
					<div class="formElement">
						<div class="formFieldLabel">
							<label for="serverAlias">{lang}www.packageServerSubmit.homepage{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" value="{$homepage}" name="homepage" id="homepage" />
						</div>
						<div class="formFieldDesc">
							<p>{lang}www.packageServerSubmit.homepage{/lang}</p>
						</div>
					</div>
					
					<div class="formElement">
						<div class="formFieldLabel">
							<label for="serverAlias">{lang}www.packageServerSubmit.description{/lang}</label>
						</div>
						<div class="formField">
							<input type="text" class="inputText" value="{$description}" name="description" id="description" />
						</div>
						<div class="formFieldDesc">
							<p>{lang}www.packageServerSubmit.description{/lang}</p>
						</div>
					</div>
					
					<div class="formSubmit">
						<input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
						<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
						{@SID_INPUT_TAG}
					</div>
				</fieldset>
			</form>
		</div>
		
		{include file='footer' sandbox=false}
	</body>
</html>