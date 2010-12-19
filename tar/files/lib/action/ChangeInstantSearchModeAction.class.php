<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

class ChangeInstantSearchModeAction extends AbstractAction {
	
	public function execute() {
		parent::execute();
		
		if (isset($_COOKIE['disableInstantSearch'])) {
			setcookie('disableInstantSearch', '', (time() - 30));
		} else {
			setcookie('disableInstantSearch', '1', 0);
		}
		
		HeaderUtil::redirect('index.php?page=Index'.SID_ARG_2ND_NOT_ENCODED);
	}
}
?>