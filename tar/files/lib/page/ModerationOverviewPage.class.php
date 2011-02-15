<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Displays a moderation overview
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ModerationOverviewPage extends AbstractPage {
	
	/**
	 * @see Page::$templateName
	 */
	public $templateName = 'moderationOverview';
	
	/**
	 * @see AbstractPage::$neededPermissions
	 */
	public $neededPermissions = 'mod.search.canModerate';
	
	/**
	 * Contains a count of outstanding server requests
	 * @var integer
	 */
	public $outstandingServerRequests = 0;
	
	/**
	 * Contains a count of disabled packages
	 * @var integer
	 */
	public $disabledPackages = 0;
	
	/**
	 * Contains a count of outstanding package reports
	 * @var integer
	 */
	public $outstandingPackageReports = 0;
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$sql = "SELECT
				COUNT(*) AS count
			FROM
				www".WWW_N."_package_server_request
			WHERE
				state = 'waiting'";
		$row = WCF::getDB()->getFirstRow($sql);
			
		$this->outstandingServerRequests = $row['count'];
		
		// get reports
		$sql = "SELECT
				COUNT(*) AS count
			FROM
				www".WWW_N."_package_report report
			WHERE
				report.state = 'new'";
		$row = WCF::getDB()->getFirstRow($sql);
			
		$this->outstandingPackageReports = $row['count'];
		
		// get disabled packages
		$sql = "SELECT
				COUNT(*) AS count
			FROM
				www".WWW_N."_package
			WHERE
				isDisabled = 1";
		$row = WCF::getDB()->getFirstRow($sql);
		
		$this->disabledPackages = $row['count'];
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'outstandingServerRequests'		=>	$this->outstandingServerRequests,
			'outstandingPackageReports'		=>	$this->outstandingPackageReports,
			'disabledPackages'			=>	$this->disabledPackages
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// activate usercpmenu
		require_once(WCF_DIR.'lib/page/util/menu/UserCPMenu.class.php');
		UserCPMenu::getInstance()->setActiveMenuItem('www.user.usercp.menu.link.modcp.overview');
		
		parent::show();
	}
}
?>