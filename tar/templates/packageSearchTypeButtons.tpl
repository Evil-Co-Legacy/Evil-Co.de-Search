{if $this->user->getPermission('mod.search.canModerate')}
	<li class="disablePackageButton">
		<a id="disablePackage{$result->getResultID()}" href="index.php?action=TogglePackage&amp;packageID={$result->getResultID()}" title="{lang}www.search.result.disable{/lang}" onclick="return confirm('{lang}www.search.result.disable.sure{/lang}');"><img src="{icon}{if $result->isDisabled}dis{else}en{/if}abledS.png{/icon}" alt="" /> <span>{lang}wwww.search.result.disable{/lang}</span></a>
	</li>
{/if}

{if $result->isDownloadAvailable()}
	<li class="downloadPackageButton">
		<a id="download{@$result->getResultID()}" href="index.php?page=DownloadPackage&amp;versionID={@$result->versionID}" title="{lang}www.search.result.download{/lang}"><img src="{icon}downloadS.png{/icon}" alt="" /> <span>{lang}www.search.result.download{/lang}</span></a>
	</li>
{/if}

{if $result->isMirrorAvailable()}
	<li class="mirrorPackageButton">
		<a id="mirror{@$result->getResultID()}" href="index.php?page=PackageMirror&amp;versionID={@$result->versionID}" title="{lang}www.search.result.mirror{/lang}"><img src="{icon}mirrorS.png{/icon}" alt="" /> <span>{lang}www.search.result.mirror{/lang}</span></a>
	</li>
{/if}