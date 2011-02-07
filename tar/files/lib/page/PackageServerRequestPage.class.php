<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Displays a server request
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class PackageServerRequestPage extends AbstractPage {
	
	/**
	 * @see Page::$templateName
	 */
	public $templateName = 'packageServerRequest';
	
	/**
	 * Contains the object ID of the request
	 * @var integer
	 */
	public $requestID = 0;
	
	/**
	 * Contains the data of request
	 * @var array
	 */
	public $request = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['requestID'])) $this->requestID = intval($_REQUEST['requestID']);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_package_server_request
			WHERE
				requestID = ".$this->requestID;
		$this->request = WCF::getDB()->getFirstRow($sql);
		
		if (!WCF::getDB()->countRows()) throw new IllegalLinkException;
		
		// permission checks
		if ($this->request['authorID'] != WCF::getUser()->userID) WCF::getUser()->checkPermission('mod.search.canModerate');
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
		
		WCF::getTPL()->assign('request', $this->request);
	}
}
?>