<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Displays all license blacklist requests
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class LicenseBlacklistRequestsPage extends AbstractPage {
	
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'licenseBlacklistRequests';
	
	/**
	 * @see AbstractPage::$neededPermissions
	 */
	public $neededPermissions = 'mod.search.canModerate';
	
	/**
	 * Contains all requested licenses for blacklist
	 * @var array
	 */
	public $licenses = array();
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_package_license_blacklist_request
			".(!isset($_REQUEST['showAll']) ? "WHERE state = 'waiting'" : '');
		$result = WCF::getDB()->sendQuery($sql);
		
		while($row = WCF::getDB()->fetchArray($result)) {
			$this->licenses[] = $row;
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// activate usercpmenu
		require_once(WCF_DIR.'lib/page/util/menu/UserCPMenu.class.php');
		UserCPMenu::getInstance()->setActiveMenuItem('www.user.usercp.menu.link.modcp.licenseBlacklistRequests');
		
		parent::show();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'licenses'		=>	$this->licenses
		));
	}
}
?>