<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Displays the license information and sends the tar archive
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class PackageMirrorPage extends AbstractPage {
	
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
				version.*,
				package.packageName,
				mirror.isEnabled AS mirrorEnabled
			FROM
				www".WWW_N."_package_version version
			LEFT JOIN
				www".WWW_N."_package AS package
			ON
				version.packageID = package.packageID
			LEFT JOIN
				www".WWW_N."_package_mirror AS mirror
			ON
				(package.packageID = package.packageID AND version.versionID = mirror.versionID)
			WHERE
				version.versionID = ".$this->versionID;
		$this->version = WCF::getDB()->getFirstRow($sql);
		
		// validate
		if (!WCF::getDB()->countRows() or empty($this->version['licenseName']) or empty($this->version['licenseUrl']) or !$this->version['mirrorEnabled']) throw new IllegalLinkException;
		
		if (!isset($_REQUEST['licenseAccepted'])) {
			WCF::getTPL()->assign(array('licenseName' => $this->version['licenseName'], 'licenseUrl' => $this->version['licenseUrl'], 'versionID' => $this->versionID));
			WCF::getTPL()->display('mirrorPackage');
			exit;
		}
		
		// validate
		if (!file_exists(WWW_DIR.'mirror/version'.$this->versionID.'.tar.gz') or !is_readable(WWW_DIR.'mirror/version'.$this->versionID.'.tar.gz')) throw new NamedUserException('An error occoured: The mirror file does not exist! Please retry later.');
		
		// send headers
		header("Content-type: application/x-tar");
		header("Content-Disposition: attachment; filename=\"".$this->version['packageName']."_".str_replace(' ', '_', $this->version['version'])."\"");
		header("Content-length: ".filesize(WWW_DIR.'mirror/version'.$this->versionID.'.tar.gz'));
		header("Cache-control: private");
		
		// send file
		readfile(WWW_DIR.'mirror/version'.$this->versionID.'.tar.gz');
		exit;
	}
}
?>