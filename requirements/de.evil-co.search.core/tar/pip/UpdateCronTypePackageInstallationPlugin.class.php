<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/package/plugin/AbstractXMLPackageInstallationPlugin.class.php');

/**
 * Implements a class for the cron update type package installation plugin (PiP)
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class UpdateCronTypePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	public $tagName = 'cron_update_type';
	public $tableName = 'updatecrontype';

    /**
     * @see PackageInstallationPlugin::install()
     */
    public function install() {
		parent::install();

		if (!$xml = $this->getXML()) {
			return;
		}

		// Create an array with the data blocks (import or delete) from the xml file.
		$validateServerXML = $xml->getElementTree('data');

		// Loop through the array and install or uninstall items.
		foreach ($validateServerXML['children'] as $key => $block) {
		    if (count($block['children'])) {
				// Handle the import instructions
				if ($block['name'] == 'import') {
				    // Loop through items and create or update them.
				    foreach ($block['children'] as $type) {
						// Extract item properties.
						foreach ($type['children'] as $child) {
						    if (!isset($child['cdata'])) continue;
						    $type[$child['name']] = $child['cdata'];
						}

						// default values
						$typeName = '';
						$searchTable = '';

						// get values
						if (isset($type['file'])) $file = $type['file'];

						if (empty($file)) {
						    throw new SystemException("Required 'file' attribute is missing", 13023);
						}

						$sql = "INSERT INTO
									wcf".WCF_N."_cron_update_type (packageID, file)
								VALUES (".$this->installation->getPackageID().", '".escapeString($file)."')";
						WCF::getDB()->sendQuery($sql);
					}
				}

				// Handle the delete instructions.
				else if ($block['name'] == 'delete' && $this->installation->getAction() == 'update') {
				    // Loop through items and delete them.
				    $nameArray = array();
				    foreach ($block['children'] as $type) {
						// Extract item properties.
						foreach ($type['children'] as $child) {
						    if (!isset($child['cdata'])) continue;
						    $type[$child['name']] = $child['cdata'];
						}

						if (empty($type['file'])) {
						    throw new SystemException("Required 'file' attribute is missing", 13023);
						}

						$nameArray[] = $type['file'];
				    }

				    if (count($nameArray)) {
						$sql = "DELETE FROM
							    	wcf".WCF_N."_cron_update_type
								WHERE
							    	packageID = ".$this->installation->getPackageID()."
								AND
							    	file IN ('".implode("','", array_map('escapeString', $nameArray))."')";
						WCF::getDB()->sendQuery($sql);
				    }
				}
			}
    	}
    }
}
?>