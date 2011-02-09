<?xml version="1.0" encoding="UTF-8"?>
<api>
	{if $error|isset}
		<error>{lang}www.search.error{/lang}</error>
	{else}
		<resultList>
			{foreach from=$results item='result'}
				<result objectID="{@$result->getResultID()}">
					<title><![CDATA[{$result->getTitle()}]]></title>
					<description><![CDATA[{$result->description}]]></description>
					{if $result->getDetailTemplate()}
						{assign var='resultTemplate' value='apiXmlSearch'}
						{append var='resultTemplate' value=$result->getDetailTemplate()|ucfirst}
						
						{include file=$resultTemplate}
					{/if}
				</result>
			{/foreach}
		</resultList>
	{/if}
</api>