<?php
// wcf imports
require_once(WCF_DIR.'lib/system/session/UserSession.class.php');

/**
 * This class implements a user session
 * @author akkarin
 */
class AbstractWWWUserSession extends UserSession {

	/**
	 * Contains the count of outstanding group applications
	 * @var	integer
	 */
	protected $outstandingGroupApplications = null;
	
	/**
	 * Contains the count of outstanding moderations
	 * @var integer
	 */
	protected $outstandingModerations = null;
	
	/**
	 * Contains a list of outstanding notifications
	 * @var	array
	 */
	protected $outstandingNotifications = null;
	
	/**
	 * Contains a list of outstanding invitations
	 * @var	array
	 */
	protected $invitations = null;
	
	/**
	 * Returnes true if the user is a group leader
	 */
	public function isGroupLeader() {
		return false;
	}
	
	/**
	 * Retrunes true if the user has moderator permissions
	 */
	public function isModerator() {
		return false;
	}
	
	/**
	 * Returnes the number of outstanding group applications
	 */
	public function getOutstandingGroupApplications() {
		return 0;
	}
	
	/**
	 * Returnes the number of outstanding moderations
	 */
	public function getOutstandingModerations() {
		return 0;
	}
	
	/**
	 * @see	PM::getOutstandingNotifications()
	 */
	public function getOutstandingNotifications() {
		return array();
	}
	
	/**
	 * @see	PM::getOutstandingNotifications()
	 */
	public function getInvitations() {
		return array();
	}
	
	/**
	 * Returnes the avatar of the current user
	 */
	public function getAvatar() {
		return null;
	}
}
?>
