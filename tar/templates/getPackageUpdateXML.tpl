<?xml version="1.0" encoding="UTF-8"?>
<section name="packages">
	{foreach from=$resultList item='result'}
		<package name="{$result->packageName}">
			<packageInformation>
				<packageName><![CDATA[{$result->getTitle()}]]></packageName>
				<packageDescription><![CDATA[{$result->getDescription()}]]></packageDescription>
				{if $result->plugin != ''}<plugin><![CDATA[{$result->plugin}]]></plugin>{/if}
				{if $result->standalone}<standalone>1</standalone>{/if}
				{if $result->isUnique}<isUnique>1</isUnique>{/if}
			</packageInformation>
			
			{if $result->licenseName != '' || $result->licenseUrl != ''}
				<licenseInformation>
					{if $result->licenseName != ''}<license><![CDATA[{$result->licenseName}]]></license>{/if}
					{if $result->licenseUrl != ''}<licenseUrl><![CDATA[{$result->licenseUrl}]]></licenseUrl>{/if}
				</licenseInformation>
			{/if}
			
			{if $result->author != '' || $result->authorUrl}
				<authorInformation>
					{if $result->author != ''}<author><![CDATA[{$result->author}]]></author>{/if}
					{if $result->authorUrl != ''}<authorUrl><![CDATA[{$result->authorUrl}]]></authorUrl>{/if}
				</authorInformation>
			{/if}
			
			<versions>
				{foreach from=$result->getVersions() item='version'}
					<version name="{$version.version}">
						{if $result->updateInstructions|count}
							<fromVersions>
								{foreach from=$result->updateInstructions item='pipList' key='fromVersion'}
									<fromVersion><![CDATA[{$fromVersion}]]></fromVersion>
								{/foreach}
							</fromVersions>
						{/if}
						
						{if $result->getRequirements()|count}
							<requiredPackages>
								{foreach from=$result->getRequirements() item='requirement'}
									<requiredPackage{if $requirement->version != ''} minversion="{$requirement->version}"{/if}>{$requirement->packageName}</requiredPackage>
								{/foreach}
							</requiredPackages>
						{/if}
						
						{* TODO: Add excluded packages here *}
						
						{* I think this should not be hardcoded ... *}
						<updateType><![CDATA[update]]></updateType>
						
						{* TODO: Add this
						<timestamp><![CDATA[{$version->timestamp}]]></timestamp>
						*}
						<timestamp><![CDATA[{TIME_NOW}]]></timestamp>
						
						{* TODO: Add this
						<versionType><![CDATA[{$version->type}]]></versionType>
						*}
						
						<file><![CDATA[{$version.downloadUrl}]]></file>
					</version>
				{/foreach}
			</versions>
		</package>
	{/foreach}
</section>