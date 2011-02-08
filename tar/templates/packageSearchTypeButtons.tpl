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