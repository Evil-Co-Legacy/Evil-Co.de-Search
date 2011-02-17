<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Displays all reports
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class APIBlacklistPage extends Abstractpage {
	
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'apiBlacklist';
	
	/**
	 * @see AbstractPage::$neededPermissions
	 */
	public $neededPermissions = 'mod.search.canModerate';
	
	
	/**
	 * Contains all blacklisted ips/hosts
	 * @var array
	 */
	public $blacklistedHosts = array();
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$sql = "SELECT
				IFNULL(ipAddress, hostname) AS target,
				expire
			FROM
				www".WWW_N."_api_key_blacklist
			WHERE
				expire >= ".TIME_NOW;
		$result = WCF::getDB()->sendQuery($sql);
		
		while($row = WCF::getDB()->fetchArray($result)) {
			$this->blacklistedHosts[] = $row;
		}
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// activate usercpmenu
		require_once(WCF_DIR.'lib/page/util/menu/UserCPMenu.class.php');
		UserCPMenu::getInstance()->setActiveMenuItem('www.user.usercp.menu.link.modcp.apiBlacklist');
		
		parent::show();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'blacklistedHosts'	=>	$this->blacklistedHosts
		));
	}
}
?>