<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/avatar/Gravatar.class.php');
require_once(WCF_DIR.'lib/data/user/avatar/Avatar.class.php');

// www imports
require_once(WWW_DIR.'lib/data/user/AbstractWWWUserSession.class.php');

// little workaround to remove errors from pm system
require_once(WCF_DIR.'lib/data/message/pm/ViewablePM.class.php');

/**
 * Represents a user session
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class WWWUserSession extends AbstractWWWUserSession {
	
	/**
	 * @see UserSession::__construct()
	 */
	public function __construct($userID = null, $row = null, $username = null) {
		$this->sqlSelects .= "	avatar.*,
					GROUP_CONCAT(DISTINCT whitelist.whiteUserID ORDER BY whitelist.whiteUserID ASC SEPARATOR ',') AS buddies,
					GROUP_CONCAT(DISTINCT blacklist.blackUserID ORDER BY blacklist.blackUserID ASC SEPARATOR ',') AS ignoredUser,
					(SELECT COUNT(*) FROM wcf".WCF_N."_user_whitelist WHERE whiteUserID = user.userID AND confirmed = 0 AND notified = 0) AS numberOfInvitations,";
		$this->sqlJoins .= "	LEFT JOIN wcf".WCF_N."_user_whitelist whitelist ON (whitelist.userID = user.userID AND whitelist.confirmed = 1)
					LEFT JOIN wcf".WCF_N."_user_blacklist blacklist ON (blacklist.userID = user.userID)
					LEFT JOIN wcf".WCF_N."_avatar avatar ON (avatar.avatarID = user.avatarID) ";
		parent::__construct($userID, $row, $username);
	}
	
	/**
	 * @see User::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		
		// we'll load the avatar
		if (MODULE_AVATAR == 1 and !$this->disableAvatar and $this->showAvatar) {
			if (MODULE_GRAVATAR == 1 and $this->gravatar) {
				// handle gravatar
				$this->avatar = new Gravatar($this->gravatar);
			} elseif ($this->avatarID) {
				// handle avatar
				$this->avatar = new Avatar(null, $data);
			}
		}
	}
	
	/**
	 * Initialises the user session.
	 */
	public function init() {
		parent::init();

		// reset properties
		$this->outstandingGroupApplications = $this->outstandingModerations = $this->invitations = null;
	}
	
	/**
	 * @see	AbstractPDBUserSession::isGroupLeader()
	 */
	public function isGroupLeader() {
		return $this->getPermission('wcf.group.isGroupLeader');
	}
	
	/**
	 * @see AbstractWWWUserSession::isModerator()
	 */
	public function isModerator() {
		return $this->getPermission('mod.search.canModerate');
	}
	
	/**
	 * @see	AbstractPDBUserSession::getOutstandingGroupApplications()
	 */
	public function getOutstandingGroupApplications() {
		if (MODULE_MODERATED_USER_GROUP == 1) {
			if ($this->outstandingGroupApplications === null) {
				// try to read session variable
				$this->outstandingGroupApplications = WCF::getSession()->getVar('outstandingGroupApplications');
				
				// if no applications are set in session we'll read them
				if ($this->outstandingGroupApplications === null) {
					// set to zero
					$this->outstandingGroupApplications = 0;
					
					// build monster query from hell to read applications
					$sql = "SELECT
								COUNT(*) AS count
							FROM
								wcf".WCF_N."_group_application
							WHERE 
								groupID IN (
									SELECT
										groupID
									FROM
										wcf".WCF_N."_group_leader leader
									WHERE
										leader.leaderUserID = ".$this->userID."
									OR
										leader.leaderGroupID IN (".implode(',', $this->getGroupIDs()).")
								)
							AND
								applicationStatus IN (0,1)";
					$row = WCF::getDB()->getFirstRow($sql);
					
					$this->outstandingGroupApplications = $row['count'];
					
					// register a session variable to remove the monster query from hell above
					WCF::getSession()->register('outstandingGroupApplications', $this->outstandingGroupApplications);
				}
			}
			
			return $this->outstandingGroupApplications;
		}
		
		// If the module isn't enabled we'll return zero
		return 0;
	}
	
	/**
	 * @see AbstractWWWUserSession::getOutstandingModerations()
	 */
	public function getOutstandingModerations() {
		if ($this->outstandingModerations === null) {
			// get server requests
			$sql = "SELECT
					COUNT(*) AS count
				FROM
					www".WWW_N."_package_server_request
				WHERE
					state = 'waiting'";
			$row = WCF::getDB()->getFirstRow($sql);
			
			$this->outstandingModerations = $row['count'];
			
			// get reports
			$sql = "SELECT
					COUNT(*) AS count
				FROM
					www".WWW_N."_package_report report
				WHERE
					report.state = 'new'";
			$row = WCF::getDB()->getFirstRow($sql);
			
			$this->outstandingModerations += $row['count'];
		}
		
		return $this->outstandingModerations;
	}
	
	/**
	 * @see	PM::getOutstandingNotifications()
	 */
	public function getOutstandingNotifications() {
		if ($this->outstandingNotifications === null) {
			require_once(WCF_DIR.'lib/data/message/pm/PM.class.php');
			$this->outstandingNotifications = PM::getOutstandingNotifications(WCF::getUser()->userID);
		}
		
		return $this->outstandingNotifications;
	}
	
	/**
	 * @see	PM::getOutstandingNotifications()
	 */
	public function getInvitations() {
		// if we haven't set the invitations variable we'll load all from database
		if ($this->invitations === null) {
			// create needed array
			$this->invitations = array();
			
			// build another query from hell
			$sql = "SELECT
						user_table.userID, user_table.username
					FROM
						wcf".WCF_N."_user_whitelist whitelist
					LEFT JOIN
						wcf".WCF_N."_user user_table
					ON
						(user_table.userID = whitelist.userID)
					WHERE
						whitelist.whiteUserID = ".$this->userID."
					AND
						whitelist.confirmed = 0
					AND
						whitelist.notified = 0
					ORDER BY
						whitelist.time";
			$result = WCF::getDB()->sendQuery($sql);
			
			while ($row = WCF::getDB()->fetchArray($result)) {
				$this->invitations[] = new User(null, $row);
			}
		}
		
		// return invitation array
		return $this->invitations;
	}
	
	/**
	 * @see	AbstractPDBUserSession::getAvatar()
	 */
	public function getAvatar() {
		return $this->avatar;
	}
}
?>