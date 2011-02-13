<?php
// wcf imports
require_once(WCF_DIR.'lib/form/CaptchaForm.class.php');

// www imports
require_once(WWW_DIR.'lib/data/search/PackageType.class.php');
require_once(WWW_DIR.'lib/data/search/PackageResult.class.php');

/**
 * Displays a form that handles reports
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ReportPackageForm extends CaptchaForm {
	
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'reportPackage';
	
	/**
	 * Contains the object ID of the package that should reported
	 * @var integer
	 */
	public $packageID = 0;
	
	/**
	 * Contains the package object that should reported
	 * @var PackageResult
	 */
	public $package = null;
	
	/**
	 * Contains a reason for the report
	 * @var string
	 */
	public $reason = "";
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['packageID'])) $this->packageID = intval($_REQUEST['packageID']);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		// get package
		$this->package = PackageResult::getByID($this->packageID);
		
		// validate
		if (!$this->package->getResultID()) throw new IllegalLinkException;
		
		$sql = "SELECT
				COUNT(*) AS count
			FROM
				www".WWW_N."_package_report
			WHERE
				packageID = ".$this->package->getResultID();
		$row = WCF::getDB()->getFirstRow($sql);
		
		if ($row['count'] > 0) throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('www.reportPackage.alreadyReported'), array('package' => $this->package));
		
		// call parent
		parent::readData();
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_REQUEST['reason'])) $this->reason = StringUtil::trim($_REQUEST['reason']);
	}
	
	/**
	 * @see Form::validate()
	 * @throws UserInputException
	 */
	public function validate() {
		parent::validate();
		
		// reason
		if (empty($_REQUEST['reason'])) throw new UserInputException('reason');
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		$sql = "INSERT INTO
				www".WWW_N."_package_report (packageID, authorID, authorName, reason)
			VALUES
				(".$this->package->getResultID().", ".WCF::getUser()->userID.", '".escapeString(WCF::getUser()->username)."', '".escapeString($this->reason)."')";
		WCF::getDB()->sendQuery($sql);

		$this->saved();

		// redirect to url
		WCF::getTPL()->assign(array(
			'url' => 'index.php?page=ResultDetail&resultID='.$this->package->getResultID().'&searchTypeName=PackageType'.SID_ARG_2ND,
			'message' => WCF::getLanguage()->getDynamicVariable('www.reportPackage.success'),
			'wait' => 5
		));
		WCF::getTPL()->display('redirect');
		exit;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'package'		=>	$this->package,
			'reason'		=>	$this->reason
		));
	}
}
?>