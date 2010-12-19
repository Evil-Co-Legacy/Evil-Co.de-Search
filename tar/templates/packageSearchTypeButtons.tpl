<li>
	<a id="download{@$result.ID}" href="javascript:void(0)" title="{lang}www.search.result.download{/lang}"><img src="{icon}downloadS.png{/icon}" alt="" /> <span>{lang}www.search.result.download{/lang}</span></a>

	<div class="hidden" id="download{@$result.ID}Menu">
		<div class="pageMenu">
			<ul>
				<li>
					{foreach from=$result.result.versions item='version'}
						{if !$result.result.isDeleted}
							<a href="api.php?action=Download&amp;packageID={@$result.ID}&amp;version={@$version.version|urlencode}{@SID_ARG_2ND}">{$version.version}</a>
						{else}
							<a name="{$version.version}"><strike>{$version.version}</strike></a>
						{/if}
					{/foreach}
				</li>
			</ul>
		</div>
	</div>
	
	<script type="text/javascript">
		//<![CDATA[
		popupMenuList.register('download{@$result.ID}');
		//]]>
	</script>
</li>

<li>
	<a id="mirror{@$result.ID}" href="javascript:void(0)" title="{lang}www.search.result.mirror{/lang}"><img src="{icon}mirrorS.png{/icon}" alt="" /> <span>{lang}www.search.result.mirror{/lang}</span></a>

	<div class="hidden" id="mirror{@$result.ID}Menu">
		<div class="pageMenu">
			<ul>
				<li>
					{foreach from=$result.result.versions item='version'}
						{if !$version.notMirrored|isset}
							<a href="api.php?action=Mirror&amp;packageID={@$result.ID}&amp;version={@$version.version|urlencode}{@SID_ARG_2ND}">{$version.version}</a>
						{else}
							<a name="{$version.version}"><strike>{$version.version}</strike></a>
						{/if}
					{/foreach}
				</li>
			</ul>
		</div>
	</div>
	
	<script type="text/javascript">
		//<![CDATA[
		popupMenuList.register('mirror{@$result.ID}');
		//]]>
	</script>
</li>