<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows information for external applications (Note: This will used by instant search)
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class APIPage extends AbstractPage {

	/**
	 * @see	AbstractPage::$templateName
	 */
	public $templateName = 'api';

	/**
	 * Contains a list of valid actions
	 * If the given action does not match to one of this elements an IllegalLinkException will appear
	 * @var	array<string>
	 */
	public $validActions = array('GetAdvancedSearchFields', 'Search');

	/**
	 * Valid API types
	 * @var array<string>
	 */
	public $validTypes = array('json', 'xml', 'html');

	/**
	 * Contains the current type
	 * @var	string
	 */
	public $type = 'json';

	/**
	 * @see	Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// read query parameters
		if (isset($_REQUEST['type'])) $this->type = StringUtil::trim($_REQUEST['type']);
	}

	/**
	 * @see	Page::show()
	 * @throws IllegalLinkException
	 */
	public function show() {
		// send correct content-type
		header('Content-Type: '.($this->type == 'xml' ? 'application/xml' : ($this->type == 'json' ? 'application/json' : 'text/html')));

		// call readParameters method to get needed variables
		$this->readParameters();

		// validate action
		if (!in_array($this->action, $this->validActions) or !in_array($this->type, $this->validTypes)) throw new IllegalLinkException;

		// create function name
		$functionName = $this->type.$this->action;

		// create templateName
		$this->templateName .= ucfirst($this->type).$this->action;

		// validate method (We'll catch undefined methods. If the method for the given action and type isn't defined we'll throw an IllegalLinkException)
		if (!method_exists($this, $functionName)) throw new IllegalLinkException;

		// call readData method for given type and action
		$this->{$functionName}();

		// call assignVariables method
		$this->assignVariables();

		// display template
		echo WCF::getTPL()->fetch($this->templateName);
	}

	/**
	 * Handles search requests in HTML syntax (We'll use this for instant search)
	 */
	protected function htmlSearch() {
		// include searchTypes
		require_once(WCF_DIR.'lib/data/search/SearchType.class.php');

		// create needed arrays
		$searchResults = array();

		// check for needed attributes
		if (isset($_REQUEST['query']) and !empty($_REQUEST['query']) and isset($_REQUEST['searchType'])) {
			// read attributes
			$searchType = intval($_REQUEST['searchType']);
			$query = StringUtil::trim($_REQUEST['query']);

			if (isset($_REQUEST['page']))
				$page = intval($_REQUEST['page']);
			else
				$page = 1;

			if (isset($_REQUEST['itemsPerPage']) and intval($_REQUEST['itemsPerPage']) <= 100)
				$itemsPerPage = intval($_REQUEST['itemsPerPage']);
			else
				$itemsPerPage = 20;

			// validate
			$searchType = new SearchType($searchType);
			$className = $searchType->typeName;

			// validate given type
			if ($searchType->typeID != 0) {
				// validate type
				if (file_exists(WWW_DIR.'lib/data/search/'.$className.'.class.php')) {
					// include type
					require_once(WWW_DIR.'lib/data/search/'.$className.'.class.php');

					// create new type instance
					$searchType = new $className($searchType->typeID);

					// execute query
					$searchResults = $searchType->search($query, $page, $itemsPerPage);

					// calculate result count
					$pageData = $this->calculateNumberOfPages($searchType->getResultCount(), $page, $itemsPerPage);

					// assign data
					WCF::getTPL()->assign($pageData);
				} else {
					// print debug message
					// echo '<p class="error">Cannot load search type</p>';
					throw new IllegalLinkException;
				}
			} else {
				// print debug message
				// echo '<p class="error">Invalid SearchType!</p>';
				throw new IllegalLinkException;
			}
		} else {
			// print debug message
			// echo '<p class="error">Invalid query!<br />'; print_r($_REQUEST); echo '</p>';
			throw new NamedUserException(WCF::getLanguage()->get('www.search.error'));
		}

		// assign results
		WCF::getTPL()->assign('results', $searchResults);
	}

	/**
	 * Handles json search requests
	 */
	protected function jsonSearch() {
		// include searchTypes
		require_once(WCF_DIR.'lib/data/search/SearchType.class.php');

		// create needed arrays
		$searchResults = array();

		if (!isset($_REQUEST['evil'])) {
			// check for needed attributes
			if (isset($_REQUEST['query']) and !empty($_REQUEST['query']) and isset($_REQUEST['searchType'])) {
				// read attributes
				$searchType = intval($_REQUEST['searchType']);
				$query = StringUtil::trim($_REQUEST['query']);
	
				if (isset($_REQUEST['page']))
					$page = intval($_REQUEST['page']);
				else
					$page = 1;
	
				if (isset($_REQUEST['itemsPerPage']) and intval($_REQUEST['itemsPerPage']) <= 100)
					$itemsPerPage = intval($_REQUEST['itemsPerPage']);
				else
					$itemsPerPage = 20;
	
				// validate
				$searchType = new SearchType($searchType);
				$className = $searchType->typeName;
	
				// validate given type
				if ($searchType->typeID != 0) {
					// validate type
					if (file_exists(WWW_DIR.'lib/data/search/'.$className.'.class.php')) {
						// include type
						require_once(WWW_DIR.'lib/data/search/'.$className.'.class.php');
	
						// create new type instance
						$searchType = new $className($searchType->typeID);
	
						// execute query
						$searchResults = $searchType->search($query, $page, $itemsPerPage);
	
						// calculate result count
						$pageData = $this->calculateNumberOfPages($searchType->getResultCount(), $page, $itemsPerPage);
	
						// assign data
						WCF::getTPL()->assign($pageData);
					} else {
						// print debug message
						// echo '<p class="error">Cannot load search type</p>';
						$error = 'cannotLoadSearchType';
					}
				} else {
					// print debug message
					// echo '<p class="error">Invalid SearchType!</p>';
					$error = 'invalidSearchType';
				}
			} else {
				// print debug message
				// echo '<p class="error">Invalid query!<br />'; print_r($_REQUEST); echo '</p>';
				$error = 'invalidQuery';
			}
		} else {
			$error = "Don't be evil!";
		}

		// assign results
		WCF::getTPL()->assign('json', json_encode(array(
			'error' => (isset($error) ? $error : false),
			'resultList' => $searchResults
		)));
	}

	/**
	 * Calculates the number of pages and
	 * handles the given page number parameter.
	 */
	public function calculateNumberOfPages($items, $pageNo, $itemsPerPage) {
		// calculate number of pages
		$pages = intval(ceil($items / $itemsPerPage));

		// correct active page number
		if ($pageNo > $pages) $pageNo = $pages;
		if ($pageNo < 1) $pageNo = 1;

		return array('pageNo' => $pageNo, 'pages' => $pages, 'items' => $items, 'itemsPerPage' => $itemsPerPage);
	}
}
?>