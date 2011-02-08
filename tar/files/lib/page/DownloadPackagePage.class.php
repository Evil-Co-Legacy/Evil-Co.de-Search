<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Displays the license information and redirects to download
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class DownloadPackagePage extends AbstractPage {
	
	/**
	 * Contains the object ID of this version
	 * @var integer
	 */
	public $versionID = 0;
	
	/**
	 * Contains the version information
	 * @var array
	 */
	public $version = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['versionID'])) $this->versionID = intval($_REQUEST['versionID']);
	}
	
	/**
	 * @see Page::readData()
	 * @throws IllegalLinkException
	 */
	public function readData() {
		parent::readData();
		
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_package_version
			WHERE
				versionID = ".$this->versionID;
		$this->version = WCF::getDB()->getFirstRow($sql);
		
		// validate
		if (!WCF::getDB()->countRows() or empty($this->version['licenseName']) or empty($this->version['licenseUrl'])) throw new IllegalLinkException;
		
		if (!isset($_REQUEST['licenseAccepted'])) {
			WCF::getTPL()->assign(array('licenseName' => $this->version['licenseName'], 'licenseUrl' => $this->licenseUrl));
			WCF::getTPL()->display('downloadPackage');
			exit;
		}
		
		// redirect
		WCF::getTPL()->assign(array(
			'url' => $this->version['downloadUrl'],
			'message' => WCF::getLanguage()->get('www.download.redirect'),
			'wait' => 5
		));
	}
}
?>