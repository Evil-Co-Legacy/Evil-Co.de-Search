<?php
// wcf imports
require_once(WCF_DIR.'lib/data/search/SearchResult.class.php');

/**
 * Represents a search result
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class PackageResult extends SearchResult {

	/**
	 * @see SearchResult::getResultID()
	 */
	public function getResultID() {
		return $this->packageID;
	}

	/**
	 * @see SearchResult::getTitle()
	 */
	public function getTitle() {
		return $this->name;
	}

	/**
	 * @see SearchResult::getDescription()
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @see SearchResult::getAdditionalButtons()
	 */
	public function getAdditionalButtons() {
		return WCF::getTPL()->fetch('packageSearchTypeButtons');
	}

	/**
	 * @see SearchResult::getDetailTemplate()
	 */
	public function getDetailTemplate() {
		return 'packageResult';
	}
}
?>