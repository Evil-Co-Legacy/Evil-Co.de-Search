<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Displays all reports
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class PackageReportsPage extends Abstractpage {
	
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'packageReports';
	
	/**
	 * @see AbstractPage::$neededPermissions
	 */
	public $neededPermissions = 'mod.search.canModerate';
	
	
	/**
	 * Contains all package reports
	 * @var array
	 */
	public $packageReports = array();
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$sql = "SELECT
				report.*,
				packageLanguage.name AS packageName,
				packageLanguage.languageID,
				packageLanguage.isFallback
			FROM
				www".WWW_N."_package_report report
			LEFT JOIN
				www".WWW_N."_package package
			ON
				report.packageID = package.packageID
			LEFT JOIN
				www".WWW_N."_package_version_to_language packageLanguage
			ON
				package.lastVersionID = packageLanguage.versionID
			WHERE
				(
						packageLanguage.languageID = ".WCF::getLanguage()->getLanguageID()."
					OR
						packageLanguage.isFallback = 1
				)";
		$result = WCF::getDB()->sendQuery($sql);
		
		$resultList = array();
		
		// create needed array
		$resultList = array();
		$tempList = array();
		$bestValues = array();
		$fallbacks = array();
		
		// loop while fetching rows
		while ($row = WCF::getDB()->fetchArray($result)) {
			$resultList[] = $row;
		}
		
		foreach($resultList as $key => $result) {
			if ($result['isFallback'])
				$fallbacks[$result['reportID']] = $key;
			elseif ($result['languageID'] == WCF::getLanguage()->getLanguageID())
				$bestValues[$result['languageID']] = $key;
		}
		
		foreach($fallbacks as $resultID => $key) {
			if (isset($bestValues[$resultID]))
				$tempList[] = $resultList[$bestValues[$resultID]];
			else
				$tempList[] = $resultList[$key];
		}
		
		$this->packageReports = $tempList;
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// activate usercpmenu
		require_once(WCF_DIR.'lib/page/util/menu/UserCPMenu.class.php');
		UserCPMenu::getInstance()->setActiveMenuItem('www.user.usercp.menu.link.modcp.packageReports');
		
		parent::show();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'packageReports'	=>	$this->packageReports
		));
	}
}
?>