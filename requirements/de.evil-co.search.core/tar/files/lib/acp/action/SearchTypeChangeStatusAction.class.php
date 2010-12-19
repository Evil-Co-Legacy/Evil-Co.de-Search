<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

class SearchTypeChangeStatusAction extends AbstractAction {
	protected $validFields = array('isDisabled', 'isDefault');
	
	protected $field = '';
	protected $typeID = 0;
	
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['field'])) $this->field = $_REQUEST['field'];
		if (isset($_REQUEST['typeID'])) $this->typeID = intval($_REQUEST['typeID']);
		
		if (!in_array($this->field, $this->validFields) or !$this->typeID) throw new IllegalLinkException;
	}
	
	public function execute() {
		parent::execute();
		
		$sql = "SELECT
					".$this->field."
				FROM
					wcf".WCF_N."_search_type
				WHERE
					typeID = ".$this->typeID;
		$field = WCF::getDB()->getFirstRow($sql);
		
		if (WCF::getDB()->countRows()) {
			$sql = "UPDATE
						wcf".WCF_N."_search_type
					SET
						".$this->field." = ".($field[$this->field] == 1 ? 0 : 1)."
					WHERE
						typeID = ".$this->typeID;
			WCF::getDB()->sendQuery($sql);
		}
		
		HeaderUtil::redirect('index.php?page=SearchTypeList&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		
		$this->executed();
	}
}
?>