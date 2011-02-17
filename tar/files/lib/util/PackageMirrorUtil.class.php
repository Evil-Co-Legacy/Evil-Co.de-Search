<?php

/**
 * Contains util methods for mirrored packages
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class PackageMirrorUtil {
	
	/**
	 * Returnes true if the given license is blacklisted
	 * @param	string	$licenseName
	 * @param	string	$licenseUrl
	 */
	public static function isBlacklistedLicense($licenseName, $licenseUrl) {
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_package_license_blacklist
			WHERE
				isEnabled = 1";
		$result = WCF::getDB()->sendQuery($sql);
		
		while($row = WCF::getDB()->fetchArray($result)) {
			if (preg_match($row['licenseRegex'], $licenseName) or preg_match($row['licenseRegex'], $licenseUrl)) return true;
		}
		
		return false;
	}
}
?>