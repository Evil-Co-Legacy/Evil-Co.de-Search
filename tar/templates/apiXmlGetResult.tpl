<?xml version="1.0" encoding="UTF-8"?>
<api>
	<result objectID="{$result->getResultID()}">
		<title><![CDATA[{$result->getTitle()}]]></title>
		<description><![CDATA[{$result->getDescription()}]]></description>
		{assign var='detailTemplateName' value='apiXmlGetResult'}
		{append var='detailTemplateName' value=$result->getDetailTemplate()|ucfirst}
		
		{include file=$detailTemplateName}
	</result>
</api>