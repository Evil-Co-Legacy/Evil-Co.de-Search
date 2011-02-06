<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/data/search/SearchType.class.php');

class IndexPage extends AbstractPage {
	public $templateName = 'index';
	
	public $searchTypes = array();
	public $defaultSearchTypeID = 0;
	
	public function readData() {
		parent::readData();
		
		$sql = "SELECT
					search_type.*
				FROM
					wcf".WCF_N."_search_type search_type,
					wcf".WCF_N."_package_dependency package_dependency
				WHERE
					search_type.isDisabled = 0
				AND
					search_type.packageID = package_dependency.dependency
				AND 
					package_dependency.packageID = ".PACKAGE_ID;
		$result = WCF::getDB()->sendQuery($sql);
		
		while($row = WCF::getDB()->fetchArray($result)) {
			$className = $row['typeName'];
			
			if (!file_exists(WWW_DIR.'lib/data/search/'.$className.'.class.php'))
				throw new SystemException('Classfile \''.$className.'.class.php\' not found.');
			else
				require_once(WWW_DIR.'lib/data/search/'.$className.'.class.php');
			
			$this->searchTypes[] = new $className(null, $row);
			if ($row['isDefault']) $this->defaultSearchTypeID = $row['typeID'];
		}
	}
	
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array('searchTypes' => $this->searchTypes, 'showAdvancedSearchOptions' => true, 'defaultSearchTypeID' => $this->defaultSearchTypeID));
		// WCF::getTPL()->append('additionalFooterOptions', '<li><a href="index.php?action=ChangeInstantSearchMode'.SID_ARG_2ND.'">'.WCF::getLanguage()->get('www.index.'.(!isset($_COOKIE['disableInstantSearch']) ? 'disable' : 'enable').'InstantSearch'));
	}
}
?>