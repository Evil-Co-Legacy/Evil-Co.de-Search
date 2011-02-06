<?php
// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');

/**
 * Displays a list of all servers
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class PackageServerListPage extends AbstractPage {
	
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'packageServerList';
	
	/**
	 * Contains the list of all servers
	 * @var array
	 */
	public $packageServerList = array();
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_package_server";
		$result = WCF::getDB()->sendQuery($sql);
		
		while($row = WCF::getDB()->fetchArray($result)) {
			$this->packageServerList[] = $row;
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		require_once(WCF_DIR.'lib/page/util/menu/PageMenu.class.php');
		PageMenu::setActiveMenuItem('www.header.menu.packageServerList');
		
		parent::show();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign('packageServerList', $this->packageServerList);
	}
}
?>