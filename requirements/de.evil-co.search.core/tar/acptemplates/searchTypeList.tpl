{include file='header'}

<div class="mainHeadline">
	<img src="{@RELATIVE_WCF_DIR}icon/searchTypeL.png" alt="" />
	<div class="headlineContainer">
		<h2>{lang}wcf.acp.searchTypeList.title{/lang}</h2>
	</div>
</div>

{if $defaultSearchTypeID}
	<p class="success">{lang}wcf.acp.searchTypeList.default.success{/lang}</p>	
{/if}

{if !$searchTypes|count}
	<div class="border content">
		<div class="container-1">
			<p>{lang}wcf.acp.searchTypeList.noTypes{/lang}</p>
		</div>
	</div>
{else}
	<div class="border titleBarPanel">
		<div class="containerHead"><h3>{lang}wcf.acp.searchTypeList.available{/lang}</h3></div>
	</div>
	<div class="border borderMarginRemove">
		<table class="tableList">
			<thead>
				<tr class="tableHead">
					<th class="columnTypeID{if $sortField == 'typeID'} active{/if}" colspan="2"><div><a href="index.php?page=SearchTypeList&amp;pageNo={@$pageNo}&amp;sortField=typeID&amp;sortOrder={if $sortField == 'typeID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.searchTypeList.typeID{/lang}{if $sortField == 'typeID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
					<th class="columnTypeName{if $sortField == 'typeName'} active{/if}"><div><a href="index.php?page=SearchTypeList&amp;pageNo={@$pageNo}&amp;sortField=typeName&amp;sortOrder={if $sortField == 'typeName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.searchTypeList.typeName{/lang}{if $sortField == 'typeName'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$searchTypes item=type}
					<tr class="{cycle values="container-1,container-2"}">
						<td class="columnIcon">
							{if !$type->isDisabled && !$type->isDefault}
								<a href="index.php?action=SearchTypeChangeStatus&amp;field=isDisabled&amp;typeID={@$type->typeID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/enabledS.png" alt="" title="{lang}wcf.acp.searchTypeList.disable{/lang}" /></a>
							{elseif !$type->isDefault}
								<a href="index.php?action=SearchTypeChangeStatus&amp;field=isDisabled&amp;typeID={@$type->typeID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/disabledS.png" alt="" title="{lang}wcf.acp.searchTypeList.enable{/lang}" /></a>
							{else}
								<img src="{@RELATIVE_WCF_DIR}icon/enabledDisabledS.png" alt="" title="{lang}wcf.acp.searchTypeList.disable{/lang}" />
							{/if}
							
							{if !$type->isDefault}
								<a href="index.php?action=SearchTypeChangeStatus&amp;field=isDefault&amp;typeID={@$type->typeID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/defaultS.png" alt="" title="{lang}wcf.acp.searchTypeList.default{/lang}" /></a>
							{/if}
						</td>
						<td class="columnID">{@$type->typeID}</td>
						<td class="columnText">
							{@$type->typeName}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{/if}

{include file='footer'}