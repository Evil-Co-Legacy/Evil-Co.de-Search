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
	 * Contains a count of max bad logins
	 * @var integer
	 */
	const MAX_BAD_LOGIN_COUNT = 5;

	/**
	 * Contains an amount of seconds wich the ip is still in database until the ban expires
	 * @var integer
	 */
	const BAD_LOGIN_EXPIRE = 1800;
	
	/**
	 * @see	AbstractPage::$templateName
	 */
	public $templateName = 'api';

	/**
	 * Contains a list of valid actions
	 * If the given action does not match to one of this elements an IllegalLinkException will appear
	 * @var	array<string>
	 */
	public $validActions = array(/* 'getadvancedsearchfields', */ 'search', 'getresult');

	/**
	 * Valid API types
	 * @var array<string>
	 */
	public $validTypes = array('json', 'xml');

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
		// set correct case
		$this->action = strtolower($this->action);
		$this->type = strtolower($this->type);
		
		// send correct content-type
		header('Content-Type: '.($this->type == 'xml' ? 'application/xml' : ($this->type == 'json' ? 'application/json' : 'text/html')));

		// call readParameters method to get needed variables
		$this->readParameters();

		// validate action
		if (!in_array($this->action, $this->validActions) or !in_array($this->type, $this->validTypes)) throw new IllegalLinkException;
		
		// check API-Key blacklist
		$sql = "SELECT
				COUNT(*) AS count
			FROM
				www".WWW_N."_api_key_blacklist
			WHERE
				(
						ipAddress = '".escapeString(WCF::getSession()->ipAddress)."'
					OR
						hostname = '".escapeString(gethostbyaddr(WCF::getSession()->ipAddress))."'
				)
			AND
				banEnabled = 1
			AND
				expire >= ".TIME_NOW;
		$row = WCF::getDB()->getFirstRow($sql);
		
		// send banned message
		if ($row['count'] > 0) throw new NamedUserException("Access to API denied: Banned");
		
		// check for API-Key
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			// send auth headers
			header('WWW-Authenticate: Basic realm="'.PAGE_TITLE.' API"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'You must send a valid API-Key to use our API!';
			exit;
		} else {
			// check login
			$sql = "SELECT
					COUNT(*) AS count
				FROM
					www".WWW_N."_api_key key
				LEFT JOIN
					www".WWW_N."_api_key_whitelist whitelist
				ON
					key.keyID = whitelist.keyID
				WHERE
					publicKey = '".escapeString($_SERVER['PHP_AUTH_USER'])."'
				AND
					secretKey = '".escapeString($_SERVER['PHP_AUTH_PW'])."'
				AND
					(
							whitelist.ipAddress = '".escapeString(WCF::getSession()->ipAddress)."'
						OR
							whitelist.hostname = '".escapeString(gethostbyaddr(WCF::getSession())->ipAddress)."'
					)";
			$row = WCF::getDB()->getFirstRow($sql);
			
			// wrong key
			if ($row['count'] <= 0) {
				// update blacklist
				$sql = "SELECT
						*
					FROM
						www".WWW_N."_api_key_blacklist
					WHERE
						ipAddress = '".escapeString(WCF::getSession()->ipAddress)."'
					OR
						hostname = '".escapeString(gethostbyaddr(WCF::getSession()->ipAddress))."'";
				$row = WCF::getDB()->getFirstRow($sql);
				
				if (WCF::getDB()->countRows() > 0) {
					// update counts
					$sql = "UPDATE
							www".WWW_N."_api_key_blacklist
						SET
							badLoginCount = ".($row['badLoginCount'] + 1).",
							timestamp = ".TIME_NOW.",
							expire = ".(TIME_NOW + self::BAD_LOGIN_EXPIRE)."
							".($row['badLoginCount'] >= self::MAX_BAD_LOGIN_COUNT ? ", banEnabled = 1" : "")."
						WHERE
							banID = ".$row['banID'];
					WCF::getDB()->sendQuery($sql);
				} else {
					// insert new ban
					$sql = "INSERT INTO
							www".WWW_N."_api_key_blacklist (ipAddress, host, badLoginCount, timestamp, expire, banEnabled)
						VALUES
							('".escapeString(WCF::getSession()->ipAddress)."',
							 NULL,
							 1,
							 ".TIME_NOW.",
							 ".(TIME_NOW + self::BAD_LOGIN_EXPIRE).",
							 ".(self::MAX_BAD_LOGIN_COUNT == 1 ? 1 : 0).")";
					
					if (gethostbyaddr(WCF::getSession()->ipAddress) != WCF::getSession()->ipAddress) {
						$sql .= ", (NULL,
							    '".escapeString(gethostbyaddr(WCF::getSession()->ipAddress))."',
							    1,
							    ".TIME_NOW.",
							    ".(TIME_NOW + self::BAD_LOGIN_EXPIRE).",
							    ".(self::MAX_BAD_LOGIN_COUNT == 1 ? 1 : 0).")";
					}
					
					WCF::getDB()->sendQuery($sql);
				}
				
				throw new PermissionDeniedException;
			}
		}
		// correct key ... let's go
		
		// create function name
		$functionName = $this->type.ucfirst($this->action);

		// create templateName
		$this->templateName .= ucfirst($this->type).ucfirst($this->action);

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
	 * Handles search requests in XML syntax (We'll use this for instant search)
	 */
	protected function xmlSearch() {
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
			
			if (!$searchType->typeID) throw new IllegalLinkException;

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
			// throw new NamedUserException(WCF::getLanguage()->get('www.search.error'));
			WCF::getTPL()->assign('error', true);
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
				
				if (!$searchType->typeID) throw new IllegalLinkException;
	
				// validate given type
				if ($searchType->typeID != 0) {
					// validate type
					if (file_exists(WWW_DIR.'lib/data/search/'.$className.'.class.php')) {
						// include type
						require_once(WWW_DIR.'lib/data/search/'.$className.'.class.php');
	
						// create new type instance
						$searchType = new $className($searchType->typeID);
	
						// execute query
						$tempSearchResults = $searchType->search($query, $page, $itemsPerPage);
						$searchResults = array();
						
						foreach($tempSearchResults as $result) {
							$result->readTrees();
							$searchResults[] = $result->getData();	
						}
						
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
	 * Reads information about a result from database and assignes it to template
	 * @throws IllegalLinkException
	 * @throws SystemException
	 */
	protected function xmlGetresult() {
		$this->templateName = 'apiXmlGetResult';
		
		// validate query
		if (!isset($_REQUEST['searchType']) or !isset($_REQUEST['resultID'])) throw new IllegalLinkException;
		
		// include searchTypes
		require_once(WCF_DIR.'lib/data/search/SearchType.class.php');
		
		// extract vars
		$searchTypeID = intval($_REQUEST['searchType']);
		$resultID = intval($_REQUEST['resultID']);
		
		// search type
		$searchType = new SearchType($searchTypeID);
		
		// validate
		if (!$searchType->typeID) throw new IllegalLinkException;
		
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
			
		$searchType = new $className($searchType->typeID);
			
		$className = $searchType->getSearchResultClass();
		
		// create new instance
		$searchResult = new $className(array(), true);
		
		// check for detail template
		if (!$searchResult->getDetailTemplate()) throw new IllegalLinkException;
		
		// create result
		$result = call_user_func(array($className, 'getByID'), $resultID);
		
		// validate
		if (!$result->getResultID()) throw new IllegalLinkException;
		
		// assign result
		WCF::getTPL()->assign('result', $result);
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