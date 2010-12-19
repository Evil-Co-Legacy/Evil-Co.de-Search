<?php
// wcf imports
require_once(WCF_DIR.'lib/page/SortablePage.class.php');

// search imports
require_once(WCF_DIR.'lib/data/search/SearchType.class.php');

/**
 * Shows a list of all search types
 * @author		Johannes Donath
 * @copyright	2010 Punksoft
 * @package		de.evil-co.search.www
 * @subpackage	de.evil-co.search.core
 * @version		1.0.0
 */
class SearchTypeListPage extends SortablePage {
	public $templateName = 'searchTypeList';
	public $defaultSortField = 'typeName';
	public $searchTypes = array();
	public $defaultSearchTypeID = 0;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['defaultSearchTypeID'])) $this->defaultSearchTypeID = intval($_REQUEST['defaultSearchTypeID']);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// get servers
		$this->readSearchTypes();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'searchTypes' => $this->searchTypes,
			'defaultSearchTypeID' => $this->defaultSearchTypeID
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wcf.acp.menu.link.search.typeList');
		
		// check permission.
		//WCF::getUser()->checkPermission('admin.serch');
		
		parent::show();
	}
	
	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField() {
		parent::validateSortField();
		
		switch ($this->sortField) {
			case 'typeID':
			case 'typeName': break;
			default: $this->sortField = $this->defaultSortField;
		}
	}
	
	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_search_type";
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}
	
	/**
	 * Gets all search types
	 */
	protected function readSearchTypes() {
		if ($this->items) {
			$sql = "SELECT
						type.*
					FROM
						wcf".WCF_N."_search_type type
					ORDER BY
						".$this->sortField.' '.$this->sortOrder;
			$result = WCF::getDB()->sendQuery($sql, $this->itemsPerPage, ($this->pageNo - 1) * $this->itemsPerPage);
			
			while ($row = WCF::getDB()->fetchArray($result)) {
				$this->searchTypes[] = new SearchType(null, $row);
			}
		}
	}
}
?>