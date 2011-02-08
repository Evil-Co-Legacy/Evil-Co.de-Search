<ul class="breadCrumbs">
	<li><a href="index.php?page=Index{@SID_ARG_2ND}"><img alt="" src="{icon}indexS.png{/icon}" /> <span>{lang}{PAGE_TITLE}{/lang}</span></a> &raquo;</li>
</ul>

<div class="mainHeadline">
	<img src="{icon}detailL.png{/icon}" alt="" />
	<div class="headlineContainer">
		<h2>{lang}www.packageDetail.title{/lang}</h2>
		<p>{$result->description}</p>
	</div>
</div>

{if $userMessages|isset}{@$userMessages}{/if}

{if !$result->isDownloadAvailable()}<p class="warning">{lang}www.packageDetail.downloadDisabled{/lang}</p>{/if}
{if !$result->isMirrorAvailable()}<p class="info">{lang}www.packageDetail.mirrorDisabled{/lang}</p>{/if}

<div class="contentHeader">
	{if $result->isDownloadAvailable() || $result->isMirrorAvailable() || $additionalLargeButtons|isset}
		<div class="largeButtons">
			<ul>
				{if $result->isDownloadAvailable()}
					<li>
						<a id="download{@$result->getResultID()}" href="index.php?page=DownloadPackage&amp;packageID={@$result->getResultID()}" title="{lang}www.search.result.download{/lang}"><img src="{icon}downloadM.png{/icon}" alt="" /> <span>{lang}www.search.result.download{/lang}</span></a>
					</li>
				{/if}
				
				{if $result->isMirrorAvailable()}
					<li>
						<a id="mirror{@$result->getResultID()}" href="index.php?page=PackageMirror&amp;packageID={@$result->getResultID()}" title="{lang}www.search.result.mirror{/lang}"><img src="{icon}mirrorM.png{/icon}" alt="" /> <span>{lang}www.search.result.mirror{/lang}</span></a>
					</li>
				{/if}
				
				{if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
			</ul>
		</div>
	{/if}
</div>

<div id="packageInformation">
	{* General Information *}
	<div class="generalPackageInformation">
		<fieldset>
			<legend>{lang}www.packageDetail.generalInformation{/lang}</legend>
			
			<div class="formElement">
				<p class="formFieldLabel">{lang}www.packageDetail.packageName{/lang}</p>
				<p class="formField">{$result->packageName}</p>
			</div>
			
			{if $result->author != ''}
				<div class="formElement">
					<p class="formFieldLabel">{lang}www.packageDetail.author{/lang}</p>
					<p class="formField">{if $result->packageUrl != ''}<a href="{@RELATIVE_WCF_DIR}acp/dereferrer.php?url={$result->packageUrl|rawurlencode}">{$result->author}</a>{else}{$result->author}{/if}</p>
				</div>
			{/if}
			
			{if $result->licenseName}
				<div class="formElement">
					<p class="formFieldLabel">{lang}www.packageDetail.license{/lang}</p>
					<p class="formField">{if $result->licenseUrl != ''}<a href="{@RELATIVE_WCF_DIR}acp/dereferrer.php?url={$result->licenseUrl|rawurlencode}">{$result->licenseName}</a>{else}{$result->licenseName}{/if}</p>
				</div>
			{/if}
			
			<div class="formElement">
				<p class="formFieldLabel">{lang}www.packageDetail.server{/lang}</p>
				<p class="formField"><a href="index.php?page=PackageServerList{@SID_ARG_2ND}#server{@$result->serverID}">{lang}{$result->serverAlias}{/lang}</a></p>
			</div>
			
			<div class="formElement">
				<p class="formFieldLabel">{lang}www.packageDetail.latestVersion{/lang}</p>
				<p class="formField">{$result->version}</p>
			</div>
			
			{if $result->isUnique}
				<div class="formElement">
					<p class="formFieldLabel">{lang}www.packageDetail.isUnique{/lang}</p>
					<p class="formField">{lang}www.packageDetail.isUnique.value{/lang}</p>
				</div>
			{/if}
			
			{if $result->standalone}
				<div class="formElement">
					<p class="formFieldLabel">{lang}www.packageDetail.standalone{/lang}</p>
					<p class="formField">{lang}www.packageDetail.standalone.value{/lang}</p>
				</div>
			{/if}
			
			{if $result->plugin != ''}
				<div class="formElement">
					<p class="formFieldLabel">{lang}www.packageDetail.plugin{/lang}</p>
					<p class="formField">{$result->plugin}</p>
				</div>
			{/if}
		</fieldset>
	</div>
	
	{* Requirements *}
	<div class="packageRequirements">
		{* <fieldset>
			<legend>{lang}www.packageDetail.packageRequirements{/lang}</legend> *}
			
			{assign var='requirements' value=$result->getRequirements()}
			
			<div class="border titleBarPanel">
				<div class="containerHead">
					<div class="containerIcon">
						<a href="javascript:void(0);" onclick="openList('packageRequirementList')">
							<img alt="" id="packageRequirementListImage" src="{icon}plusS.png{/icon}">
						</a>
					</div>
					<div class="containerContent">
						<h3>{lang}www.packageDetail.packageRequirements{/lang}</h3>
					</div>
				</div>
				<div id="packageRequirementList">
					<table class="tableList">
						<thead>
							<tr class="tableHead">
								<th><div><span class="emptyHead">{lang}www.packageDetail.packageRequirements.name{/lang}</span></div></th>
								<th><div><span class="emptyHead">{lang}www.packageDetail.packageRequirements.version{/lang}</span></div></th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$requirements item='requirement'}
								<tr class="{cycle values='container-1,container-2'}">
									<td><a href="index.php?page=ResultDetail&amp;resultID={@$requirement->getResultID()}&amp;searchType={@$searchTypeID}{@SID_ARG_2ND}">{$requirement->getTitle()}</a></td>
									<td>{$requirement->version}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
				<script type="text/javascript">
					//<![CDATA[
					initList('packageRequirementList', false);
					//]]>
				</script>
			</div>
		{* </fieldset> *}
	</div>
	
	{* Optionals *}
	<div class="packageOptionals">
		{* <fieldset>
			<legend>{lang}www.packageDetail.packageOptionals{/lang}</legend> *}
			
			{assign var='optionals' value=$result->getOptionals()}
			
			<div class="border titleBarPanel">
				<div class="containerHead">
					<div class="containerIcon">
						<a href="javascript:void(0);" onclick="openList('packageOptionalsList')">
							<img alt="" id="packageOptionalsListImage" src="{icon}plusS.png{/icon}">
						</a>
					</div>
					<div class="containerContent">
						<h3>{lang}www.packageDetail.packageOptionals{/lang}</h3>
					</div>
				</div>
				<div id="packageOptionalsList">
					<table class="tableList">
						<thead>
							<tr class="tableHead">
								<th><div><span class="emptyHead">{lang}www.packageDetail.packageOptionals.name{/lang}</span></div></th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$optionals item='optional'}
								<tr class="{cycle values='container-1,container-2'}">
									<td><a href="index.php?page=ResultDetail&amp;resultID={@$optional->getResultID()}&amp;searchType={@$searchTypeID}{@SID_ARG_2ND}">{$optional->getTitle()}</a></td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
				<script type="text/javascript">
					//<![CDATA[
					initList('packageOptionalsList', false);
					//]]>
				</script>
			</div>
		{* </fieldset> *}
	</div>
	
	<div class="packageInstructions">
		{* <fieldset>
			<legend>{lang}www.packageDetail.packageInstructions{/lang}</legend> *}
			
			{assign var='instructions' value=$result->getInstructions()}
			
			<div class="border titleBarPanel">
				<div class="containerHead">
					<div class="containerIcon">
						<a href="javascript:void(0);" onclick="openList('packageInstructionList')">
							<img alt="" id="packageInstructionListImage" src="{icon}plusS.png{/icon}">
						</a>
					</div>
					<div class="containerContent">
						<h3>{lang}www.packageDetail.packageInstructions{/lang}</h3>
					</div>
				</div>
				<div id="packageInstructionList">
					<table class="tableList">
						<thead>
							<tr class="tableHead">
								<th><div><span class="emptyHead">{lang}www.packageDetail.packageInstructions.name{/lang}</span></div></th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$instructions item='instruction'}
								<tr class="{cycle values='container-1,container-2'}">
									<td>{$instruction}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
				<script type="text/javascript">
					//<![CDATA[
					initList('packageInstructionList', false);
					//]]>
				</script>
			</div>
		{* </fieldset> *}
	</div>
</div>