{include file='documentHeader'}
	<head>
		<title>{lang}www.packageServerList.title{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
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
					<h2>{lang}www.packageServerList.title{/lang}</h2>
					<p>{lang}www.packageServerList.description{/lang}</p>
				</div>
			</div>
			
			{if $userMessages|isset}{@$userMessages}{/if}
			
			<div class="contentHeader">
				<div class="largeButtons">
					<ul>
						<li>
							<a href="index.php?form=SubmitPackageServer{@SID_ARG_2ND}"><img src="{icon}packageServerSubmitM.png{/icon}" alt="" /> <span>{lang}wwww.packageServerList.submitServer{/lang}</span></a>
						</li>
					</ul>
				</div>
			</div>
			
			<div id="serverList">
				{foreach from=$packageServerList item='server'}
					<div class="server" id="server{@$server.serverID}">
						<div class="message content result{if $server.isDisabled} disabled{/if}">
							<div class="messageInner container-{cycle name='results' values='1,2'}">
									
								<h3 class="subHeadline">{lang}{$server.serverAlias}{/lang}</h3>
						
								<div class="messageBody">
									<div class="formElement">
										<p class="formFieldLabel">{lang}www.packageServerList.url{/lang}</p>
										<p class="formField"><a href="{@RELATIVE_WCF_DIR}acp/dereferrer.php?url={$server.serverUrl|rawurlencode}">{$server.serverUrl}</a>
									</div>
									
									{if $server.homepage != ''}
										<div class="formElement">
											<p class="formFieldLabel">{lang}www.packageServerList.homepage{/lang}</p>
											<p class="formField"><a href="{@RELATIVE_WCF_DIR}acp/dereferrer.php?url={$server.homepage|rawurlencode}">{$server.homepage}</a>
										</div>
									{/if}
									
									{if $server.description != ''}
										<div class="formElement">
											<p class="formFieldLabel">{lang}www.packageServerList.descriptionField{/lang}</p>
											<p class="formField">{lang}{$server.description}{/lang}</p>
										</div>
									{/if}
									
									{if $server.isDisabled}
										<div class="formElement">
											<p class="formFieldLabel">{lang}www.packageServerList.notes{/lang}</p>
											<p class="formField">{lang}www.packageServerList.isDisabled{/lang}</p>
										</div>
									{/if}
								</div>
									
								<div class="messageFooter">
									<div class="smallButtons">
										<ul>
											<li class="extraButton"><a href="#top" title="{lang}wcf.global.scrollUp{/lang}"><img src="{icon}upS.png{/icon}" alt="{lang}wcf.global.scrollUp{/lang}" /></a></li>
											{if $this->user->getPermission('mod.search.canModerate')}
												<li><a href="index.php?action=TogglePackageServer&amp;serverID={@$server.serverID}{@SID_ARG_2ND}"><img src="{icon}{if $server.isDisabled}dis{else}en{/if}abledS.png{/icon}" alt="" /> <span>{lang}www.packageServerList.toggleServer{/lang}</span></a></li>
												<li><a href="index.php?action=DeletePackageServer&amp;serverID={@$server.serverID}{@SID_ARG_2ND}" onclick="return confirm('{lang}www.packageServerList.delete.sure{/lang}');"><img src="{icon}deleteS.png{/icon}" alt="" /> <span>{lang}www.packageServerList.delete{/lang}</span></a></li>
											{/if}
										</ul>
									</div>
								</div>
								<hr />
							</div>
						</div>
					</div>
				{/foreach}
			</div>
		</div>
		
		{include file='footer' sandbox=false}
	</body>
</html>