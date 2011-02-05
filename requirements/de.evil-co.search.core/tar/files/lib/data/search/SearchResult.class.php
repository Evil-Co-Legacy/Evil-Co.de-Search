<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Provides default methods for search results
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
abstract class SearchResult extends DatabaseObject {

	/**
	 * Creates a new search result instance
	 * @param	array	$row
	 */
	public function __construct($row) {
		// handle result set
		parent::__construct($row);
	}

	/**
	 * Returnes the ID of the current result
	 */
	abstract public function getResultID();

	/**
	 * Returnes the title of the current result
	 */
	abstract public function getTitle();

	/**
	 * Returnes the description of the current result
	 */
	abstract public function getDescription();

	/**
	 * Returnes additional buttons for the current result
	 */
	abstract public function getAdditionalButtons();

	/**
	 * Returnes the template name of detail template for current result
	 * Note: If this returnes false the link to detail page will disappear
	 */
	public function getDetailTemplate() {
		return false;
	}
}
?>