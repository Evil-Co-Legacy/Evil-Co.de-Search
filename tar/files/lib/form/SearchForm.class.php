<?php
// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/data/search/SearchType.class.php');

/**
 * Displays search results for given query
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class SearchForm extends MultipleLinkPage {

	/**
	 * @see	AbstractPage::$templateName
	 */
	public $templateName = 'search';

	/**
	 * Contains advanced search fields
	 * @var	array
	 */
	public $advancedSearchFields = array();

	/**
	 * Contains the search type ID
	 * @var	integer
	 */
	public $searchType = null;
	
	/**
	 * Contains the search type name
	 * @var string
	 */
	public $searchTypeName = null;

	/**
	 * Contains the search query
	 */
	public $query = "";

	/**
	 * Contains a list of all results
	 * @var	array
	 */
	public $searchResults = array();
	
	/**
	 * Contains a list of suggestions
	 * @var array<string>
	 */
	public $suggestions = array();

	/**
	 * @see	Page::readData();
	 * @throws SystemException
	 */
	public function readData() {
		// read parameters
		if (isset($_REQUEST['query'])) $this->query = StringUtil::trim($_REQUEST['query']);
		if (isset($_REQUEST['searchType'])) $this->searchType = intval($_REQUEST['searchType']);
		if (isset($_REQUEST['searchTypeName'])) $this->searchTypeName = StringUtil::trim($_REQUEST['searchTypeName']);
		if (isset($_REQUEST['advancedSearch'])) $this->advancedSearchFields = array_map(array('StringUtil', 'trim'), $_REQUEST['advancedSearch']);
		if (isset($_REQUEST['itemsPerPage']) and intval($_REQUEST['itemsPerPage']) <= 100) $this->itemsPerPage = intval($_REQUEST['itemsPerPage']);

		// validate
		$this->searchType = new SearchType($this->searchType, null, $this->searchTypeName);
		$className = $this->searchType->typeName;

		// validate
		if ($this->searchType->typeID == 0) {
			HeaderUtil::redirect('index.php?page=Index&error=1&errorMessage=invalidSearchType'.SID_ARG_2ND_NOT_ENCODED);
			exit;
		}

		// validate search type
		if (!file_exists(WWW_DIR.'lib/data/search/'.$className.'.class.php'))
				throw new SystemException('Classfile \''.$className.'.class.php\' not found.');
			else
				require_once(WWW_DIR.'lib/data/search/'.$className.'.class.php');

		// create new search type instance
		$this->searchType = new $className($this->searchType->typeID);

		// handle advanced fields
		if (count($this->advancedSearchFields)) {
			foreach($this->advancedSearchFields as $field => $value) {
				if (!in_array($field, $this->searchType->getAdvancedSearchFields()) or empty($value)) {
					unset($this->advancedSearchFields[$field]); // delete bad fields
				}
			}
		}

		// validate query field
		if (empty($this->query) && !count($this->advancedSearchFields)) {
			HeaderUtil::redirect('index.php?page=Index&error=1&errorMessage=noQuerySet'.SID_ARG_2ND_NOT_ENCODED);
			exit;
		}

		// handle normal searches and advanced searches
		if (!count($this->advancedSearchFields)) {
			$this->searchResults = $this->searchType->search($this->query, $this->pageNo, $this->itemsPerPage);
		} else {
			$this->searchResults = $this->searchType->advancedSearch($this->query, $this->advancedSearchFields, $this->pageNo, $this->itemsPerPage);
		}
		
		// get suggestions
		if (!count($this->searchResults)) $this->suggestions = $this->searchType->getSuggestions($this->query);

		// call parent method
		parent::readData();
	}

	/**
	 * Returnes a count of all search results
	 * @see	MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();

		// get count from searchType instance
		return ($this->searchType ? $this->searchType->getResultCount() : 0);
	}

	/**
	 * @see	Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		// assign needed variables
		WCF::getTPL()->assign(array(
			'results'			=>	$this->searchResults,
			'encodedQuery'			=>	urlencode($this->query),
			'query'				=>	$this->query,
			'searchType'			=>	$this->searchType,
			'searchTypeID'			=>	$this->searchType->typeID,
			'suggestions'			=>	$this->suggestions
		));

		// assign additional footer options (Back to index page link)
		WCF::getTPL()->append('additionalFooterOptions', '<li><a href="index.php?page=Index'.SID_ARG_2ND.'">'.WCF::getLanguage()->get('www.search.result.back').'</a>');
	}
}
?>