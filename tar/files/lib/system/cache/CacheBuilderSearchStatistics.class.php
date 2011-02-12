<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches index statistics
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class CacheBuilderSearchStatistics implements CacheBuilder {
	
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($name, $searchTypeID) = explode('-', $cacheResource['cache']);
		
		// search type
		$searchType = new SearchType($searchTypeID);
		
		// get class name
		$className = $this->searchType->typeName;
		
		// include class
		if (!file_exists(WWW_DIR.'lib/data/search/'.$className.'.class.php'))
				throw new SystemException('Classfile \''.$className.'.class.php\' not found.');
			else
				require_once(WWW_DIR.'lib/data/search/'.$className.'.class.php');

		// create new search type instance
		$searchType = new $className($searchType->typeID);
		
		return array_merge(array('searchTypeName' => $className), $searchType->getStatistics());
	}
}
?>