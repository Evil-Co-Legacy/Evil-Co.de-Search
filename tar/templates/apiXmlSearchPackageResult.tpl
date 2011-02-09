{if $result->licenseName != '' || $result->licenseUrl != ''}
	<licenseInformation>
			{if $result->licenseName != ''}<licenseName><![CDATA[{$result->licenseName}]]></licenseName>{/if}
			{if $result->licenseUrl != ''}<licenseUrl><![CDATA[{$result->licenseUrl}]]></licenseUrl>{/if}
	</licenseInformation>
{/if}

{if $result->isDownloadAvailable()}
	<downloadEnabled>1</downloadEnabled>
	<downloadUrl><![CDATA[{$result->downloadUrl}]]></downloadUrl>
{else}
	<downloadEnabled>0</downloadEnabled>
{/if}

{if $result->isMirrorAvailable()}
	<mirrorEnabled>1</mirrorEnabled>
	<mirrorUrl><![CDATA[{PAGE_URL}/index.php?page=PackageMirror&versionID={$result->versionID}]]></mirrorUrl>
{else}
	<mirrorEnabled>0</mirrorEnabled>
{/if}

<requirements>
	{foreach from=$result->getRequirements item='requirement'}
		<requirement{if $requirement->getResultID()} objectID="{$requirement->getResultID()}"{/if}>
			<package><![CDATA[{$requirement->getTitle()}]]></package>
			<version><![CDATA[{$requirement->version}]]></version>
		</requirement>
	{/foreach}
</requirements>

<optionals>
	{foreach from=$result->getOptionals() item='optional'}
		<optional{if $optional->getResultID()} objectID="{$optional->getResultID()}"{/if}>
			<package><![CDATA[{$optional->getTitle()}]]>
			{if $optional->version != ''}<version><![CDATA[{$optional->version}]]></version>{/if}
		</optional>
	{/foreach}
</optionals>

<instructions>{implode from=$result->getInstructions() item='instruction'}{$instruction}{/implode}</instructions>

<versions>
	{foreach from=$result->getVersions() item='version'}
		<version isMirrorEnabled="{$version.mirrorEnabled}" isDownloadEnabled="{if $version.licenseName != '' && $version.licenseUrl}1{else}0{/if}">
			<name><![CDATA[{$version.version}]]></name>
			{if $version.licenseName != ''}<licenseName><![CDATA[{$version.licenseName}]]></licenseName>{/if}
			{if $version.licenseUrl != ''}<licenseUrl><![CDATA[{$version.licenseUrl}]]></licenseUrl>{/if}
		</version>
	{/foreach}
</versions>