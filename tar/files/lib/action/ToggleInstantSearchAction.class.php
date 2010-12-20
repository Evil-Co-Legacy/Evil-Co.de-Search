<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

/**
 * Toggles the instant search feature
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class ToggleInstantSearchAction extends AbstractAction {

	/**
	 * @see	Action::execute()
	 */
	public function execute() {
		parent::execute();

		// set session variable
		WCF::getSession()->register('disableInstantSearch', !WCF::getSession()->getVar('disableInstantSearch'));

		// redirect
		HeaderUtil::redirect('index.php?page=Index'.SID_ARG_2ND_NOT_ENCODED);

		// fire event
		$this->executed();
	}
}
?>