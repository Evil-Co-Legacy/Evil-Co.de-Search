<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/package/plugin/AbstractXMLPackageInstallationPlugin.class.php');

/**
 * Implements a class for the search type package installation plugin (PiP)
 * @author		Johannes Donath
 * @copyright	2010 Punksoft
 * @package		de.evil-co.search.www
 * @subpackage	de.evil-co.search.core
 * @version		1.0.0
 */
class SearchTypePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	public $tagName = 'searchtype';
	public $tableName = 'search_type';

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
						if (isset($type['typename'])) $typeName = $type['typename'];
						if (isset($type['searchtable'])) $searchTable = $type['searchtable'];
			
						if (empty($typeName)) {
						    throw new SystemException("Required 'typename' attribute is missing", 13023);
						}
			
						$sql = "INSERT INTO
									wcf".WCF_N."_search_type (packageID, typeName, searchTable)
								VALUES (".$this->installation->getPackageID().", '".escapeString($typeName)."', '".escapeString($searchTable)."')";
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
				
						if (empty($type['typename'])) {
						    throw new SystemException("Required 'typename' attribute is missing", 13023);
						}
						
						$nameArray[] = $validateServer['typename'];
				    }
				    
				    if (count($nameArray)) {
						$sql = "DELETE FROM
							    	wcf".WCF_N."_search_type
								WHERE
							    	packageID = ".$this->installation->getPackageID()."
								AND
							    	typeName IN ('".implode("','", array_map('escapeString', $nameArray))."')";
						WCF::getDB()->sendQuery($sql);
				    }
				}
			}
    	}
    }
}
?>