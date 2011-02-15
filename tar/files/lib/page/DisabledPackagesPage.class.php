<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

// www imports
require_once(WWW_DIR.'lib/data/search/PackageResult.class.php');

/**
 * Displays all disabled packages
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class DisabledPackagesPage extends AbstractPage {
	
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'disabledPackages';

	/**
	 * @see AbstractPage::$neededPermissions
	 */
	public $neededPermissions = 'mod.search.canModerate';
	
	/**
	 * Contains all disabled packages
	 * @var array<PackageResult>
	 */
	public $disabledPackages = array();
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$sql = "SELECT
				package.packageID,
				package.packageName,
				package.isDisabled,
				packageLanguage.name AS name,
				packageLanguage.description,
				server.serverID,
				server.serverAlias,
				server.serverUrl,
				version.versionID,
				version.version,
				version.isUnique,
				version.standalone,
				version.plugin,
				version.packageUrl,
				version.author,
				version.authorUrl,
				version.licenseName,
				version.licenseUrl,
				version.downloadUrl,
				mirror.isEnabled AS mirrorEnabled,
				packageLanguage.isFallback,
				packageLanguage.languageID
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
				package.isDisabled = 1";
		$result = WCF::getDB()->sendQuery($sql);
		
		// create needed array
		$resultList = array();
		$tempList = array();
		$bestValues = array();
		$fallbacks = array();
		
		// loop while fetching rows
		while ($row = WCF::getDB()->fetchArray($result)) {
			$resultList[] = new PackageResult($row, true);
		}
		
		foreach($resultList as $key => $result) {
			if ($result->isFallback)
				$fallbacks[$result->getResultID()] = $key;
			elseif ($result->languageID == WCF::getLanguage()->getLanguageID())
				$bestValues[$result->getResultID()] = $key;
		}
		
		foreach($fallbacks as $resultID => $key) {
			if (isset($bestValues[$resultID]))
				$tempList[] = $resultList[$bestValues[$resultID]];
			else
				$tempList[] = $resultList[$key];
		}
		
		$this->disabledPackages = $tempList;
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// activate usercpmenu
		require_once(WCF_DIR.'lib/page/util/menu/UserCPMenu.class.php');
		UserCPMenu::getInstance()->setActiveMenuItem('www.user.usercp.menu.link.modcp.disabledPackages');
		
		parent::show();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'disabledPackages'	=>	$this->disabledPackages
		));
	}
}
?>