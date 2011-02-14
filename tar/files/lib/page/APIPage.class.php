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
	 * Contains the current type
	 * @var	string
	 */
	public $type = 'xml';

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
	 * @throws APIException
	 */
	public function show() {
		// set correct case
		$this->action = strtolower($this->action);
		$this->type = strtolower($this->type);
		
		// validate type
		if (!APIUtil::isValidType($this->type)) throw new APIException('xml', "Invalid type '%s'", $this->type);
		
		// send correct content-type
		header('Content-Type: '.APIUtil::getContentType($this->type));

		// validate action
		if (!method_exists($this, $this->action)) throw new APIException($this->type, "Invalid API method '%s'", $this->action);
		
		// check blacklist
		if (APIUtil::isBanned(WCF::getSession()->ipAddress)) throw new APIException($this->type, "Access to API denied: %s", 'Banned');
		
		// check for API-Key
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			// send auth headers
			header('WWW-Authenticate: Basic realm="'.PAGE_TITLE.' API"');
			header('HTTP/1.0 401 Unauthorized');
			
			throw new APIException($this->type, "You need an API-Key to access this API");
		} else {
			try {
				// check login
				if (($apiKeyID = APIUtil::checkLogin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) === false) throw new APIException($this->type, "Invalid API-Key");
				
				// check whitelist
				if (!APIUtil::checkWhiteList($apiKeyID, WCF::getSession()->ipAddress)) throw new APIException($this->type, "Your IP-Address/hostname isn't on keys whitelist");
			} Catch (APIException $ex) {
				// update blacklist (+1)
				APIUtil::updateBlackList($ipAddress, true);
				
				// show exception
				$ex->show();
			}
		}
		// correct key ... let's go

		// call readData method for given type and action
		$this->{$this->action}();
	}

	/**
	 * Handles search requests in XML syntax (We'll use this for instant search)
	 * @throws APIException
	 */
	protected function search() {
		// include searchTypes
		require_once(WCF_DIR.'lib/data/search/SearchType.class.php');

		// create needed arrays
		$searchResults = array();

		// validate
		if (!isset($_REQUEST['query']) or empty($_REQUEST['query']) or !isset($_REQUEST['searchType'])) throw new APIException($this->type, "Request Error: %s", 'Missing arguments');
		
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
			
		if (!$searchType->typeID) throw new APIException($this->type, "Request Error: %s", 'Invalid search type');
				
		// validate type
		if (!file_exists(WWW_DIR.'lib/data/search/'.$className.'.class.php')) throw new APIException($this->type, "API Error: %s", "Can't locate search type");
			
		// include type
		require_once(WWW_DIR.'lib/data/search/'.$className.'.class.php');

		// create new type instance
		$searchType = new $className($searchType->typeID);

		// execute query
		$searchResults = $searchType->search($query, $page, $itemsPerPage);

		// calculate result count
		$pageData = $this->calculateNumberOfPages($searchType->getResultCount(), $page, $itemsPerPage);
		
		// create needed variables
		$searchResultOutput = array();
	
		foreach($searchResults as $result) {
			$searchResultOutput[] = $result->getPublicArray();
		}
		
		// generate output
		APIUtil::generate($this->type, array('pageData' => $pageData, 'results' => $searchResultOutput));
	}

	/**
	 * Reads information about a result from database and assignes it to template
	 * @throws APIException
	 * @throws SystemException
	 */
	protected function getresult() {
		// validate query
		if (!isset($_REQUEST['searchType']) or !isset($_REQUEST['resultID'])) throw new APIException($this->type, "Request error: %s", 'Missing arguments');
		
		// include searchTypes
		require_once(WCF_DIR.'lib/data/search/SearchType.class.php');
		
		// extract vars
		$searchTypeID = intval($_REQUEST['searchType']);
		$resultID = intval($_REQUEST['resultID']);
		
		// search type
		$searchType = new SearchType($searchTypeID);
		
		// validate
		if (!$searchType->typeID) throw new APIException($this->type, "Request error: %s", 'Invalid search type');
		
		$className = $searchType->typeName;
		
		if (!file_exists(WWW_DIR.'lib/data/search/'.$className.'.class.php'))
			throw new SystemException('Classfile \''.$className.'.class.php\' not found.');
		else
			require_once(WWW_DIR.'lib/data/search/'.$className.'.class.php');

		// create new search type instance
		$searchType = new $className($searchType->typeID);
		
		// get search result class
		if (!file_exists(WWW_DIR.'lib/data/search/'.$searchType->getSearchResultClass().'.class.php'))
			throw new SystemException('Classfile \''.$searchType->getSearchResultClass().'.class.php\' not found.');
		else
			require_once(WWW_DIR.'lib/data/search/'.$searchType->getSearchResultClass().'.class.php');
			
		// create searchType
		$searchType = new $className($searchType->typeID);
			
		// get className
		$className = $searchType->getSearchResultClass();
		
		// create result
		$result = call_user_func(array($className, 'getByID'), $resultID);
		
		// validate
		if (!$result->getResultID()) throw new APIException($this->type, "No object with given ID found");
		
		// generate result output
		APIUtil::generate($this->type, $result->getPublicArray());
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