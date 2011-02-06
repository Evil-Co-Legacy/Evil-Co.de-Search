<?php
// wcf imports
require_once(WCF_DIR.'lib/data/search/SearchResult.class.php');

/**
 * Represents a search result
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class PackageResult extends SearchResult {
	
	/**
	 * Contains and caches all requirements of this package
	 * @var array
	 */
	protected $requirements = null;
	
	/**
	 * Contains and caches all optionals of this package
	 * @var array
	 */
	protected $optionals = null;
	
	/**
	 * Contains and caches all instructions of this package
	 * @var array
	 */
	protected $instructions = null;
	
	/**
	 * Contains true if we should not generate trees
	 * @var boolean
	 */
	protected $disableTrees = false;
	
	/**
	 * Creates a new search result instance
	 * @param	array	$row
	 */
	public function __construct($row, $disableTrees = false) {
		$this->disableTrees = $disableTrees;
		
		parent::__construct($row);
	}
	
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		
		if (!$this->disableTrees) {
			$this->getRequirements();
			$this->getOptionals();
			$this->getInstructions();
		}
	}

	/**
	 * @see SearchResult::getResultID()
	 */
	public function getResultID() {
		return $this->packageID;
	}

	/**
	 * @see SearchResult::getTitle()
	 */
	public function getTitle() {
		return $this->name;
	}

	/**
	 * @see SearchResult::getDescription()
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @see SearchResult::getAdditionalButtons()
	 */
	public function getAdditionalButtons() {
		return WCF::getTPL()->fetch('packageSearchTypeButtons');
	}

	/**
	 * @see SearchResult::getDetailTemplate()
	 */
	public function getDetailTemplate() {
		return 'packageResult';
	}
	
	/**
	 * Returnes true if the download feature is available for this package
	 */
	public function isDownloadAvailable() {
		if (empty($this->licenseName)) return false;
		if (empty($this->licenseUrl)) return false;
		return true;
	}
	
	/**
	 * Returnes true if the mirror feature is available for this package
	 */
	public function isMirrorAvailable() {
		if (!$this->mirrorEnabled) return false;
		return true;
	}

	/**
	 * @see SearchResult::getByID()
	 */
	public static function getByID($resultID) {
		$sql = "SELECT
				package.packageID,
				package.packageName,
				packageLanguage.name AS name,
				packageLanguage.description,
				server.serverID,
				server.serverAlias,
				server.serverUrl,
				version.version,
				version.versionID,
				version.isUnique,
				version.standalone,
				version.plugin,
				version.packageUrl,
				version.author,
				version.authorUrl,
				version.licenseName,
				version.licenseUrl,
				mirror.isEnabled AS mirrorEnabled
			FROM
				www".WWW_N."_package package
			LEFT JOIN
				www".WWW_N."_package_version version
			ON
				package.lastVersionID = version.versionID
			LEFT JOIN
				www".WWW_N."_package_version_to_language packageLanguage
			ON
				version.versionID = packageLanguage.versionID
			LEFT JOIN
				www".WWW_N."_package_server server
			ON
				version.serverID = server.serverID
			LEFT JOIN
				www".WWW_N."_package_mirror AS mirror
			ON
				(package.packageID = package.packageID AND version.versionID = mirror.versionID)
			WHERE
				(
						packageLanguage.languageID = ".WCF::getLanguage()->getLanguageID()."
					OR
						packageLanguage.isFallback = 1
				)
			AND
				package.packageID = ".$resultID;
		return new PackageResult(WCF::getDB()->getFirstRow($sql));
	}
	
	/**
	 * Returnes an array with all requirements
	 */
	public function getRequirements() {
		if ($this->requirements === null) {
			$data = array();
			
			$sql = "SELECT
					targetPackageID AS packageID,
					targetVersionID AS versionID,
					packageLanguage.name AS name,
					packageLanguage.description,
					version.version
				FROM
					www".WWW_N."_package_version_requirement requirement
				LEFT JOIN
					www".WWW_N."_package_version version
				ON
					requirement.targetVersionID = version.versionID
				LEFT JOIN
					www".WWW_N."_package_version_to_language packageLanguage
				ON
					version.versionID = packageLanguage.versionID
				WHERE
					(
							packageLanguage.languageID = ".WCF::getLanguage()->getLanguageID()."
						OR
							packageLanguage.isFallback = 1
					)
				AND
					requirement.packageID = ".$this->packageID;
			$result = WCF::getDB()->sendQuery($sql);
			
			while($row = WCF::getDB()->fetchArray($result)) {
				$data[] = new PackageResult($row, true);
			}
			
			$this->requirements = $data;
		}
			
		return $this->requirements;
	}
	
	/**
	 * Returnes an array with all 
	 */
	public function getOptionals() {
		if ($this->optionals === null) {
			$data = array();
			
			$sql = "SELECT
					targetPackageID AS packageID,
					targetVersionID AS versionID,
					packageLanguage.name AS name,
					packageLanguage.description,
					version.version
				FROM
					www".WWW_N."_package_version_optional optional
				LEFT JOIN
					www".WWW_N."_package_version version
				ON
					optional.targetVersionID = version.versionID
				LEFT JOIN
					www".WWW_N."_package_version_to_language packageLanguage
				ON
					version.versionID = packageLanguage.versionID
				WHERE
					(
							packageLanguage.languageID = ".WCF::getLanguage()->getLanguageID()."
						OR
							packageLanguage.isFallback = 1
					)
				AND
					optional.packageID = ".$this->packageID;
			$result = WCF::getDB()->sendQuery($sql);
			
			while($row = WCF::getDB()->fetchArray($result)) {
				$data[] = new PackageResult($row, true);
			}
			
			$this->optionals = $data;
		}
		
		return $this->optionals;
	}
	
	/**
	 * Returnes a pip list
	 */
	public function getInstructions() {
		if ($this->instructions === null) {
			$sql = "SELECT
					pipList
				FROM
					www".WWW_N."_package_version_instruction
				WHERE
					versionID = ".$this->versionID."
				AND
					instructionType = 'install'"; // TODO: we should add support for all types
			$row = WCF::getDB()->getFirstRow($sql);
			
			$this->instructions = explode(',', $row['pipList']);
		}
		
		return $this->instructions;
	}
	
	/**
	 * Adds the missing __isset method to wcf
	 * @param	string	$variable
	 */
	public function __isset($variable) {
		if (isset($this->data[$variable])) return true;
		return false;
	}
}
?>