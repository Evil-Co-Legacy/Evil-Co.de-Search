<?php
require_once(WWW_DIR.'lib/system/session/WWWSession.class.php');
require_once(WWW_DIR.'lib/data/user/WWWUserSession.class.php');
require_once(WWW_DIR.'lib/data/user/WWWGuestSession.class.php');
require_once(WCF_DIR.'lib/system/session/CookieSessionFactory.class.php');

class WWWSessionFactory extends CookieSessionFactory {
	protected $guestClassName = 'WWWGuestSession';
	protected $userClassName = 'WWWUserSession';
	protected $sessionClassName = 'WWWSession';
}
?>
