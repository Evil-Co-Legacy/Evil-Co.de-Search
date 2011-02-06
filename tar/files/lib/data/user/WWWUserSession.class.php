<?php
require_once(WWW_DIR.'lib/data/user/AbstractWWWUserSession.class.php');

class WWWUserSession extends AbstractWWWUserSession {
	
	/**
	 * Returns the avatar of this user.
	 * @return	DisplayableAvatar
	 */
	public function getAvatar() {
		return $this->avatar;
	}
}
?>