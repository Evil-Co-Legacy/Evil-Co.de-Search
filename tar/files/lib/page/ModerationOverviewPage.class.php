<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Displays a moderation overview
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ModerationOverview extends AbstractPage {
	
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
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'outstandingServerRequests'		=>	$this->outstandingServerRequests
		));
	}
}
?>