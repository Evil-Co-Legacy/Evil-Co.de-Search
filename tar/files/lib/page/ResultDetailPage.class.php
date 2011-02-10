<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/data/search/SearchType.class.php');

/**
 * Displays a result detail page
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ResultDetailPage extends AbstractPage {
	
	/**
	 * @see AbstractPage::$templateName
	 */
	public $templateName = 'resultDetail';
	
	/**
	 * Contains the ID of the search type that should used
	 * @var integer
	 */
	public $searchTypeID = 0;
	
	/**
	 * Contains the ID of the result that should displayed
	 * @var integer
	 */
	public $resultID = 0;
	
	/**
	 * Contains the search type object
	 * @var SearchType
	 */
	public $searchType = null;
	
	/**
	 * Contains the search result object
	 * @var SearchResult
	 */
	public $result = null;
	
	/**
	 * Contains the detail template
	 * @var string
	 */
	public $detailTemplate = '';
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// read parameters
		if (isset($_REQUEST['searchType'])) $this->searchTypeID = intval($_REQUEST['searchType']);
		if (isset($_REQUEST['resultID'])) $this->resultID = intval($_REQUEST['resultID']);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		// search type
		$this->searchType = new SearchType($this->searchTypeID);
		
		// validate
		if (!$this->searchType->typeID) throw new IllegalLinkException;
		
		$className = $this->searchType->typeName;
		
		if (!file_exists(WWW_DIR.'lib/data/search/'.$className.'.class.php'))
				throw new SystemException('Classfile \''.$className.'.class.php\' not found.');
			else
				require_once(WWW_DIR.'lib/data/search/'.$className.'.class.php');

		// create new search type instance
		$this->searchType = new $className($this->searchType->typeID);
		
		// get search result class
		if (!file_exists(WWW_DIR.'lib/data/search/'.$this->searchType->getSearchResultClass().'.class.php'))
			throw new SystemException('Classfile \''.$this->searchType->getSearchResultClass().'.class.php\' not found.');
		else
			require_once(WWW_DIR.'lib/data/search/'.$this->searchType->getSearchResultClass().'.class.php');
			
		$this->searchType = new $className($this->searchType->typeID);
			
		$className = $this->searchType->getSearchResultClass();
		
		// create new instance
		$searchResult = new $className(array(), true);
		
		// check for detail template
		if (!$searchResult->getDetailTemplate())
			throw new IllegalLinkException;
		else
			$this->detailTemplate = $searchResult->getDetailTemplate();
		
		// create result
		$this->result = call_user_func(array($className, 'getByID'), $this->resultID);
		
		// validate
		if (!$this->result->getResultID()) throw new IllegalLinkException;
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'searchType'		=>	$this->searchType,
			'result'		=>	$this->result,
			'detailTemplate'	=>	$this->detailTemplate
		));
	}
}
?>