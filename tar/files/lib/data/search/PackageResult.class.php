<?php
// wcf imports
require_once(WCF_DIR.'lib/data/search/SearchResult.class.php');

/**
 * Represents a search result
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class PackageResult extends SearchResult {

	/**
	 * @see SearchResult::getResultID()
	 */
	public function getResultID() {
		return $this->packageID;
	}

	/**
	 * @see SearchResult::getTitle()
	 */
	public function getTitle() {
		return $this->name;
	}

	/**
	 * @see SearchResult::getDescription()
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @see SearchResult::getAdditionalButtons()
	 */
	public function getAdditionalButtons() {
		return WCF::getTPL()->fetch('packageSearchTypeButtons');
	}

	/**
	 * @see SearchResult::getDetailTemplate()
	 */
	public function getDetailTemplate() {
		return 'packageResult';
	}
	
	/**
	 * Returnes true if the download feature is available for this package
	 */
	public function isDownloadAvailable() {
		if (empty($this->licenseName)) return false;
		if (empty($this->licenseUrl)) return false;
		return true;
	}
	
	/**
	 * Returnes true if the mirror feature is available for this package
	 */
	public function isMirrorAvailable() {
		if (!$this->mirrorEnabled) return false;
		return true;
	}

	/**
	 * @see SearchResult::getByID()
	 */
	public static function getByID($resultID) {
		$sql = "SELECT
				package.packageID,
				package.packageName,
				packageLanguage.name AS name,
				packageLanguage.description,
				server.serverID,
				server.serverAlias,
				server.serverUrl,
				version.version,
				version.isUnique,
				version.standalone,
				version.plugin,
				version.packageUrl,
				version.author,
				version.authorUrl,
				version.licenseName,
				version.licenseUrl,
				mirror.isEnabled AS mirrorEnabled
			FROM
				www".WWW_N."_package package
			LEFT JOIN
				www".WWW_N."_package_version version
			ON
				package.lastVersionID = version.versionID
			LEFT JOIN
				www".WWW_N."_package_version_to_language packageLanguage
			ON
				version.versionID = packageLanguage.versionID
			LEFT JOIN
				www".WWW_N."_package_server server
			ON
				version.serverID = server.serverID
			LEFT JOIN
				www".WWW_N."_package_mirror AS mirror
			ON
				(package.packageID = package.packageID AND version.versionID = mirror.versionID)
			WHERE
				(
						packageLanguage.languageID = ".WCF::getLanguage()->getLanguageID()."
					OR
						packageLanguage.isFallback = 1
				)
			AND
				package.packageID = ".$resultID;
		return new PackageResult(WCF::getDB()->getFirstRow($sql));
	}
}
?>