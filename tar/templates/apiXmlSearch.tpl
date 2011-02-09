<?xml version="1.0" encoding="UTF-8"?>
<api>
	{if $error|isset}
		<error>{lang}www.search.error{/lang}</error>
	{else}
		<resultList>
			{foreach from=$results item='result'}
				<result objectID="{@$result->getResultID()}">
					<title><![CDATA[{$result->getTitle()}]]></title>
					<description><![CDATA[{$result->description}]]></p>
					{capture assign='apiTemplate'}apiXmlSearch{$result|get_class}{/capture}
					{include file=$apiTemplate}
				</result>
			{/foreach}
		</resultList>
	{/if}
</api>