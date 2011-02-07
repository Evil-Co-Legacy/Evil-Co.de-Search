<?php
// wcf imports
require_once(WCF_DIR.'lib/form/AbstractForm.class.php');

/**
 * Displays a form that adds 
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class SubmitPackageServerForm extends AbstractForm {
	
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'submitPackageServer';
	
	/**
	 * Contains an alias for server
	 * @var string
	 */
	public $serverAlias = '';
	
	/**
	 * Contains the URL to server
	 * @var string
	 */
	public $serverUrl = '';
	
	/**
	 * Contains the homepage of server
	 * @var string
	 */
	public $homepage = '';
	
	/**
	 * Contains the description of server
	 * @var string
	 */
	public $description = '';
	
	/**
	 * Contains the default state for new server requests
	 * @var string
	 */
	const DEFAULT_STATE = 'waiting';
	
	/**
	 * @see Page::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_REQUEST['serverAlias'])) $this->serverAlias = StringUtil::trim($_REQUEST['serverAlias']);
		if (isset($_REQUEST['serverUrl'])) $this->serverUrl = StringUtil::trim($_REQUEST['serverUrl']);
		if (isset($_REQUEST['homepage'])) $this->homepage = StringUtil::trim($_REQUEST['homepage']);
		if (isset($_REQUEST['description'])) $this->description = StringUtil::trim($_REQUEST['description']);
	}
	
	/**
	 * @see Page::validate()
	 * @throws UserInputException
	 */
	public function validate() {
		parent::validate();
		
		// server alias
		if (empty($this->serverAlias)) throw new UserInputException('serverAlias', 'empty');
		
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_package_server
			WHERE
				serverAlias = '".escapeString($this->serverAlias)."'";
		WCF::getDB()->sendQuery($sql);
		
		if (WCF::getDB()->countRows()) throw new UserInputException('serverAlias', 'notUnique');
		
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_package_server_request
			WHERE
				serverAlias = '".escapeString($this->serverAlias)."'";
		WCF::getDB()->sendQuery($sql);
		
		if (WCF::getDB()->countRows()) throw new UserInputException('serverAlias', 'notUnique');
		
		// server url
		if (empty($this->serverUrl)) throw UserInputException('serverUrl', 'empty');
		
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_package_server
			WHERE
				serverUrl = '".escapeString($this->serverUrl)."'";
		WCF::getDB()->sendQuery($sql);
		
		if (WCF::getDB()->countRows()) throw new UserInputException('serverUrl', 'notUnique');
		
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_package_server_request
			WHERE
				serverUrl = '".escapeString($this->serverUrl)."'";
		WCF::getDB()->sendQuery($sql);
		
		if (WCF::getDB()->countRows()) throw new UserInputException('serverUrl', 'notUnique');
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		$sql = "INSERT INTO
				www".WWW_N."_package_server_request (serverAlias, serverUrl, homepage, description, authorID, authorName, state)
			VALUES
				('".escapeString($this->serverAlias)."', '".escapeString($this->serverUrl)."', '".escapeString($this->homepage)."', '".escapeString($this->description)."', ".WCF::getUser()->userID.", '".escapeString(WCF::getUser()->username)."')";
		WCF::getDB()->sendQuery($sql);
		
		// redirect to url
		WCF::getTPL()->assign(array(
			'url' => (WCF::getUser()->userID ? 'index.php?page=PackageServerRequest&requestID='.WCF::getDB()->getInsertID().SID_ARG_2ND : 'index.php?page=PackageServerList'.SID_ARG_2ND),
			'message' => WCF::getLanguage()->get('www.submitPackageServer.redirect'),
			'wait' => 5
		));
		WCF::getTPL()->display('redirect');
		exit;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariabes();
		
		WCF::getTPL()->assign(array(
			'serverAlias'		=>	$this->serverAlias,
			'serverUrl'		=>	$this->serverUrl,
			'homepage'		=>	$this->homepage,
			'description'		=>	$this->description
		));
	}
}
?>