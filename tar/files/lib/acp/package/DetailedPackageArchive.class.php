<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/package/PackageArchive.class.php');

/**
 * Overrides useless methods that are hardcoded in original class ... thanks
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class DetailedPackageArchive extends PackageArchive {
	
	/**
	 * @see PackageArchive::getLocalizedInformation()
	 */
	protected function getLocalizedInformation($key) { }
}
?>