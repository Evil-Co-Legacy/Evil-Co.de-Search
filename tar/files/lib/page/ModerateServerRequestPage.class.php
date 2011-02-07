<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Sets request states
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ModerateServerRequestPage extends AbstractPage {
	
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
		WCF::getUser()->checkPermission('mod.search.canModerate');
		
		// check for correct action
		$methodName = 'action'.ucfirst(strtolower($this->action));
		if (!method_exists($this, $methodName)) throw new IllegalLinkException;
		
		$this->{$methodName}();
	}
	
	/**
	 * Rejects a request
	 * @throws IllegalLinkException
	 */
	public function actionReject() {
		if (!isset($_REQUEST['reason'])) throw new IllegalLinkException;
		
		// decode reason
		$reason = StringUtil::trim(urldecode($_REQUEST['reason']));
		
		// set state
		$sql = "UPDATE
				www".WWW_N."_package_server_request
			SET
				state = 'rejected',
				moderatorID = ".WCF::getUser()->userID.",
				moderatorName = '".escapeString(WCF::getUser()->username)."'
			WHERE
				requestID = ".$this->requestID;
		WCF::getDB()->sendQuery($sql);
		
		if (MODULE_PM) {
			// send pm
			require_once(WCF_DIR.'lib/data/message/pm/PMEditor.class.php');
			
			PMEditor::create(false, array(array('userID' => $this->request['authorID'], 'username' => $this->request['authorName'])), array(), WCF::getLanguage()->get('www.moderateServerRequest.subject.reject', array('request' => $this->request)), WCF::getLanguage()->get('www.moderateServerRequest.text.reject', array('request' => $this->request, 'reason' => $reason)), WCF::getUser()->userID, WCF::getUser()->username, array('enableSmilies' => true, 'enableHtml' => true, 'enableBBCodes' => true));
		}
		
		// redirect
		HeaderUtil::redirect('index.php?page=PackageServerRequest&requestID='.$this->request['requestID'].SID_ARG_2ND_NOT_ENCODED);
	}
	
	/**
	 * Sets the status of a request to pending
	 */
	public function actionPending() {
		// set state
		$sql = "UPDATE
				www".WWW_N."_package_server_request
			SET
				state = 'pending',
				moderatorID = ".WCF::getUser()->userID.",
				moderatorName = '".escapeString(WCF::getUser()->username)."'
			WHERE
				requestID = ".$this->requestID;
		WCF::getDB()->sendQuery($sql);
		
		if (MODULE_PM) {
			// send pm
			require_once(WCF_DIR.'lib/data/message/pm/PMEditor.class.php');
			
			PMEditor::create(false, array(array('userID' => $this->request['authorID'], 'username' => $this->request['authorName'])), array(), WCF::getLanguage()->get('www.moderateServerRequest.subject.pending', array('request' => $this->request)), WCF::getLanguage()->get('www.moderateServerRequest.text.pending', array('request' => $this->request)), WCF::getUser()->userID, WCF::getUser()->username, array('enableSmilies' => true, 'enableHtml' => true, 'enableBBCodes' => true));
		}
		
		// redirect
		HeaderUtil::redirect('index.php?page=PackageServerRequest&requestID='.$this->request['requestID'].SID_ARG_2ND_NOT_ENCODED);
	}
	
	/**
	 * Sets the status of a request to pending
	 */
	public function actionAccept() {
		// set state
		$sql = "UPDATE
				www".WWW_N."_package_server_request
			SET
				state = 'accepted',
				moderatorID = ".WCF::getUser()->userID.",
				moderatorName = '".escapeString(WCF::getUser()->username)."'
			WHERE
				requestID = ".$this->requestID;
		WCF::getDB()->sendQuery($sql);
		
		// write data to new table
		$sql = "INSERT INTO
				www".WWW_N."_package_server (serverAlias, serverUrl, homepage, description, isDisabled)
			VALUES
				('".escapeString($this->request['serverAlias'])."',
				 '".escapeString($this->request['serverUrl'])."',
				 '".escapeString($this->request['homepage'])."',
				 '".escapeString($this->request['description'])."',
				 0)";
		WCF::getDB()->sendQuery($sql);
		
		if (MODULE_PM) {
			// send pm
			require_once(WCF_DIR.'lib/data/message/pm/PMEditor.class.php');
			
			PMEditor::create(false, array(array('userID' => $this->request['authorID'], 'username' => $this->request['authorName'])), array(), WCF::getLanguage()->get('www.moderateServerRequest.subject.accepted', array('request' => $this->request)), WCF::getLanguage()->get('www.moderateServerRequest.text.accepted', array('request' => $this->request)), WCF::getUser()->userID, WCF::getUser()->username, array('enableSmilies' => true, 'enableHtml' => true, 'enableBBCodes' => true));
		}
		
		// redirect
		HeaderUtil::redirect('index.php?page=PackageServerRequest&requestID='.$this->request['requestID'].SID_ARG_2ND_NOT_ENCODED);
	}
}
?>