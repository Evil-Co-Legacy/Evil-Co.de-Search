<?php
// wcf imports
require_once(WCF_DIR.'lib/acp/package/update/PackageUpdate.class.php');
require_once(WCF_DIR.'lib/system/io/TarWriter.class.php');
require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');

// www imports
require_once(WWW_DIR.'lib/acp/package/DetailedPackageArchive.class.php');

/**
 * Overrides the woltlab PackageUpdate class and writes all data to our package database
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class WWWPackageUpdate extends PackageUpdate {
	
	/**
	 * @see PackageUpdate::refreshPackageDatabaseAutomatically()
	 */
	public static function refreshPackageDatabaseAutomatically() {
		// get update server data
		$updateServers = array();
		
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_package_server
			WHERE
				isDisabled = 0";
		$result = WCF::getDB()->sendQuery($sql);
		
		
		while($row = WCF::getDB()->fetchArray($result)) {
			$updateServers[] = $row;
		}
		
		// loop servers
		foreach ($updateServers as $updateServer) {
			try {
				self::getPackageUpdateXML($updateServer);
			}
			catch (PackageUpdateAuthorizationRequiredException $e) {
				// ignore
			}
			catch (SystemException $e) {
				$sql = "UPDATE
						www".WWW_N."_package_server
					SET
						lastError = '".escapeString($e->getMessage())."'
					WHERE
						serverID = ".$updateServer['serverID'];
				WCF::getDB()->sendQuery($sql);
				
				if (defined('CRON_DEBUG')) print($e);
			}
		}
	}
	
	/**
	 * @see PackageUpdate::getPackageUpdateXML()
	 */
	protected static function getPackageUpdateXML($updateServer) {
		// send request
		$response = self::sendRequest($updateServer['serverUrl']);
		
		// check response
		// check http code
		if ($response['httpStatusCode'] == 401) {
			throw new PackageUpdateAuthorizationRequiredException($updateServer['serverID'], $updateServer['serverUrl'], $response);
		}
		
		if ($response['httpStatusCode'] != 200) {
			throw new SystemException(WCF::getLanguage()->get('wcf.acp.packageUpdate.error.listNotFound') . ' ('.$response['httpStatusLine'].')', 18009);
		}
		
		// parse given package update xml
		$allNewPackages = self::parsePackageUpdateXML($response['content']);
		unset($response);
		
		// save packages
		if (count($allNewPackages)) {
			self::savePackageUpdates($allNewPackages, $updateServer['serverID'], $updateServer['serverUrl']);
		}
		unset($allNewPackages);
	}
	
	/**
	 * @see PackageUpdate::savePackageUpdates()
	 */
	protected static function savePackageUpdates(&$allNewPackages, $packageUpdateServerID, $packageUpdateServerUrl) {
		// find existing packages and delete them
		$packageNames = implode("','", array_map('escapeString', array_keys($allNewPackages)));
		
		// get existing packages
		$existingPackages = array();
		
		$sql = "SELECT
				packageName,
				packageID
			FROM
				www".WWW_N."_package
			WHERE
				serverID = ".$packageUpdateServerID."
			AND
				packageName IN ('".$packageNames."')";
		$result = WCF::getDB()->sendQuery($sql);
		
		while($row = WCF::getDB()->fetchArray($result)) {
			$existingPackages[$row['packageName']] = $row['packageID'];
		}
		
		// get existing versions
		$existingPackageVersions = array();
		if (count($existingPackages) > 0) {
			$sql = "SELECT
					packageID,
					versionID, 
					version
				FROM
					www".WWW_N."_package_version
				WHERE
					packageID IN (".implode(',', $existingPackages).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!isset($existingPackageVersions[$row['packageID']])) $existingPackageVersions[$row['packageID']] = array();
				$existingPackageVersions[$row['packageID']][$row['version']] = $row['versionID'];
			}
		}
		
		// insert updates
		// TODO: Add ecludedPackages to index
		$requirementInserts = $instructionInserts = $optionalInserts = $languageInserts = $mirrorInserts = /* $fromversionInserts = $excludedPackagesInserts = */ '';
		foreach ($allNewPackages as $identifier => $packageData) {
			try {
				if (!isset($existingPackages[$identifier])) {		
					// create new database entry
					$sql = "INSERT INTO
							www".WWW_N."_package (packageName, serverID)
						VALUES
							('".escapeString($identifier)."', '".$packageUpdateServerID."')";
					 
					/*					(packageUpdateServerID, package, packageName, 
										packageDescription, author, authorURL, standalone, plugin)  
						VALUES				(".$packageUpdateServerID.", 
										'".escapeString($identifier)."',
										'".escapeString($packageData['packageName'])."',
										'".escapeString($packageData['packageDescription'])."',
										'".escapeString($packageData['author'])."',
										'".escapeString($packageData['authorURL'])."',
										".$packageData['standalone'].",
										'".escapeString($packageData['plugin'])."')"; */
					WCF::getDB()->sendQuery($sql);
					$packageUpdateID = WCF::getDB()->getInsertID();
				} else {
					$packageUpdateID = $existingPackages[$identifier];
				}
				
				// register version(s) of this update package.
				if (isset($packageData['versions'])) {
					foreach ($packageData['versions'] as $packageVersion => $versionData) {
						if (isset($versionData['file']))
							$packageFile = $versionData['file'];
						else
							$packageFile = FileUtil::addTrailingSlash($packageUpdateServerUrl).'?packageName='.urlencode($identifier).'&packageVersion='.urlencode($packageVersion);
						
						if (!isset($existingPackageVersions[$packageUpdateID]) or !isset($existingPackageVersions[$packageUpdateID][$packageVersion])) {
							// download package
							$packageArchive = new DetailedPackageArchive(FileUtil::downloadFileFromHttp($packageFile));
							$packageArchive->openArchive();
							
							// create new database entry
							$sql = "INSERT INTO
									www".WWW_N."_package_version (	 packageID,
													 version,
													 isUnique,
													 standalone,
													 plugin,
													 packageUrl,
													 author,
													 authorUrl,
													 serverID,
													 licenseName,
													 licenseUrl,
													 downloadUrl)
								VALUES
									(".$packageUpdateID.",
									 '".escapeString($packageVersion)."',
									 ".($packageArchive->getPackageInfo('isUnique') ? 1 : 0).",
									 ".($packageArchive->getPackageInfo('standalone') ? 1 : 0).",
									 '".escapeString($packageArchive->getPackageInfo('plugin'))."',
									 '".escapeString($packageArchive->getPackageInfo('packageURL'))."',
									 '".escapeString($packageArchive->getAuthorInfo('author'))."',
									 '".escapeString($packageArchive->getAuthorInfo('authorUrl'))."',
									 ".$packageUpdateServerID.",
									 '".escapeString($packageData['licenseName'])."',
									 '".escapeString($packageData['licenseUrl'])."',
									 '".escapeString($packageFile)."')";
							WCF::getDB()->sendQuery($sql);
							$packageUpdateVersionID = WCF::getDB()->getInsertID();
						/* } else {
							$packageUpdateVersionID = $existingPackageVersions[$packageUpdateID][$packageVersion];
						} */
						
							// register requirement(s) of this update package version.
							if (isset($versionData['requiredPackages'])) {
								foreach ($versionData['requiredPackages'] as $requiredIdentifier => $required) {
									// add ,
									if (!empty($requirementInserts)) $requirementInserts .= ',';
									
									// add insert
									$requirementInserts .= "(".$packageUpdateVersionID.",
												 ".$packageUpdateID.",
												 0,
												 0,
												 '".escapeString($requiredIdentifier)."',
												'".(!empty($required['minversion']) ? escapeString($required['minversion']) : '')."')";
								}
							}
							
							// register excluded packages of this update package version.
							// TODO: Implement excluded packages
							/* if (isset($versionData['excludedPackages'])) {
								foreach ($versionData['excludedPackages'] as $excludedIdentifier => $exclusion) {
									if (!empty($excludedPackagesInserts)) $excludedPackagesInserts .= ',';
									$excludedPackagesInserts .= "(".$packageUpdateVersionID.", '".escapeString($excludedIdentifier)."',
												'".(!empty($exclusion['version']) ? escapeString($exclusion['version']) : '')."')";
								}
							} */
							
							// register fromversions of this update package version.
							/* if (isset($versionData['fromversions'])) {
								foreach ($versionData['fromversions'] as $fromversion) {
									if (!empty($fromversionInserts)) $fromversionInserts .= ',';
									$fromversionInserts .= "(".$packageUpdateVersionID.", '".escapeString($fromversion)."')";
								}
							} */
							
							$instructions = $packageArchive->getInstructions('install');
							
							// handle instructions
							if (!empty($instructionInserts)) $instructionInserts .= ',';
							$instructionInserts .= "(".$packageUpdateVersionID.",
										 ".$packageUpdateID.",
										 'install',
										 NULL,
										 '".implode(',', array_map('escapeString', array_keys($instructions)))."')";
							
							if (is_array($packageArchive->getInstructions('update'))) {
								foreach($packageArchive->getInstructions('update') as $fromVersion => $update) {
									$instructionInserts .= ",(".$packageUpdateVersionID.",
												  ".$packageUpdateID.",
												  'update',
												  '".escapeString($fromVersion)."',
												  '".implode(',', array_map('escapeString', array_keys($update)))."')";
								}
							}
							
							// handle optionals
							if (is_array($packageArchive->getOptionals())) {
								foreach($packageArchive->getOptionals() as $optional) {
									if (!empty($optionalInserts)) $optionalInserts .= ",";
									$optionalInserts .= "(	".$packageUpdateVersionID.",
												".$packageUpdateID.",
												0,
												0,
												'".escapeString($optional['name'])."',
												'')";
								}
							}
							
							// handle languages
							$packageNames = $packageArchive->getPackageInfo('packageName');
							$packageDescriptions = $packageArchive->getPackageInfo('packageDescription');
							
							foreach($packageNames as $languageCode => $name) {
								// get correct language
								// TODO: We should not use en hardcoded here
								$language = LanguageEditor::getLanguageByCode(($languageCode != 'default' ? $languageCode : 'en'));
								
								if ($language !== null) {
									// add ,
									if (!empty($languageInserts)) $languageInserts .= ",";
									
									$languageInserts .= "(	".$packageUpdateVersionID.",
												".$packageUpdateID.",
												".$language->getLanguageID().",
												'".escapeString($name)."',
												'".(isset($packageDescriptions[$languageCode]) ? escapeString($packageDescriptions[$languageCode]) : '')."',
												".($languageCode == 'default' ? 1 : 0).")";
								}
							}
							
							// handle mirror
							
							// create temp dir
							$tempDir = FileUtil::addTrailingSlash(FileUtil::getTemporaryFilename());
							FileUtil::makePath($tempDir);
							
							// create tar writer
							$tarWriter = new TarWriter(WWW_DIR.'mirror/version'.$packageUpdateVersionID.'.tar.gz');
							
							// get tar
							$tar = $packageArchive->getTar();
							
							// change dir
							chdir($tempDir);
							
							// get content list
							$contentList = $tar->getContentList();
							$workaroundFolders = array();
							
							foreach ($contentList as $key => $val) {
								// extract file
								if ($val['type'] != 'folder') {
									// workaround
									if (!is_dir(dirname($tempDir.$val['filename']))) {
										FileUtil::makePath(dirname($tempDir.$val['filename']));
										$workaroundFolders[] = dirname($tempDir.$val['filename']);
									}
									
									// workaround #2
									if (stripos($val['filename'], '.') === false) {
										FileUtil::makePath($tempDir.$val['filename']);
									}
									
									if (stripos($val['filename'], '.') !== false) $tar->extract($key, $tempDir.$val['filename']);
								} else
									mkdir($tempDir.$val['filename']);
								
								// add file to new tar.gz
								$tarWriter->add($val['filename']);
							}
							
							// create new file
							$tarWriter->create();
							unset($tarWriter);
							
							// delete original file
							$packageArchive->deleteArchive();
							unset($packageArchive);
							
							// delete temp files
							foreach(array_reverse($contentList) as $file) {
								if (is_dir($tempDir.$file['filename']))
									rmdir($tempDir.$file['filename']);
								else
									unlink($tempDir.$file['filename']);
							}
							foreach(array_reverse($workaroundFolders) as $dir) rmdir($dir);
							rmdir($tempDir);
							
							if (!empty($mirrorInserts)) $mirrorInserts .= ",";
							$mirrorInserts .= "(".$packageUpdateID.",
									    ".$packageUpdateVersionID.",
									    ".((!empty($packData['licenseName']) and !empty($packageData['licenseUrl']) and $packageData['disableMirror'] !== true) ? 1 : 0).")";
						}
					}
				}
				
				// get last versionID
				$sql = "SELECT
						versionID
					FROM
						www".WWW_N."_package_version
					WHERE
						packageID = ".$packageUpdateID."
					ORDER BY
						version DESC";
				$row = WCF::getDB()->getFirstRow($sql);
				
				// update lastVersionID field
				$sql = "UPDATE
						www".WWW_N."_package
					SET
						lastVersionID = ".$row['versionID']."
					WHERE
						packageID = ".$packageUpdateID;
				WCF::getDB()->sendQuery($sql);
			} catch(SystemException $e) {
				$sql = "UPDATE
						www".WWW_N."_package_server
					SET
						lastError = '".escapeString($e->getMessage())."'
					WHERE
						serverID = ".$packageUpdateServerID;
				WCF::getDB()->sendQuery($sql);
				
				if (defined('CRON_DEBUG')) print($e);
			}
		}
		
		// save requirements, excluded packages and ...
		// use multiple inserts to save some queries
		if (!empty($requirementInserts)) {
			$sql = "INSERT INTO
					www".WWW_N."_package_version_requirement (versionID, packageID, targetPackageID, targetVersionID, packageName, version) 
				VALUES
					".$requirementInserts;
			WCF::getDB()->sendQuery($sql);
		}
		
		/* if (!empty($excludedPackagesInserts)) {
			$sql = "INSERT INTO			wcf".WCF_N."_package_update_exclusion 
								(packageUpdateVersionID, excludedPackage, excludedPackageVersion) 
				VALUES				".$excludedPackagesInserts."
				ON DUPLICATE KEY UPDATE		excludedPackageVersion = VALUES(excludedPackageVersion)";
			WCF::getDB()->sendQuery($sql);
		} */
		
		if (!empty($instructionInserts)) {
			$sql = "INSERT INTO
					www".WWW_N."_package_version_instruction (versionID, packageID, instructionType, fromVersion, pipList) 
				VALUES
					".$instructionInserts;
			WCF::getDB()->sendQuery($sql);
		}
		
		if (!empty($optionalInserts)) {
			$sql = "INSERT INTO
					www".WWW_N."_package_version_optional (versionID, packageID, targetPackageID, targetVersionID, packageName, version)
				VALUES
					".$optionalInserts;
			WCF::getDB()->sendQuery($sql);
		}
		
		if (!empty($languageInserts)) {
			$sql = "INSERT INTO
					www".WWW_N."_package_version_to_language (versionID, packageID, languageID, name, description, isFallback)
				VALUES
					".$languageInserts;
			WCF::getDB()->sendQuery($sql);
		}
		
		if (!empty($mirrorInserts)) {
			$sql = "INSERT INTO
					www".WWW_N."_package_mirror (packageID, versionID, isEnabled)
				VALUES
					".$mirrorInserts;
			WCF::getDB()->sendQuery($sql);
		}
	}
	
	/**
	 * @see PackageUpdate::parsePackageUpdateXML()
	 */
	protected static function parsePackageUpdateXML($content) {
		// load xml document
		$xmlObj = new XML();
		$xmlObj->loadString($content);
		
		// load the <section> tag (which must be the root element).
		$xml = $xmlObj->getElementTree('section');
		$encoding = $xmlObj->getEncoding();
		unset($xmlObj);
		
		// loop through <package> tags inside the <section> tag.
		$allNewPackages = array();
		foreach ($xml['children'] as $child) {
			// name attribute is missing, thus this package is not valid
			if (!isset($child['attrs']['name']) || !$child['attrs']['name']) {
				throw new SystemException("required 'name' attribute for 'package' tag is missing", 13001);
			}

			// the "name" attribute of this <package> tag must be a valid package identifier.
			if (!Package::isValidPackageName($child['attrs']['name'])) {
				throw new SystemException("'".$child['attrs']['name']."' is not a valid package name.", 18004);
			}
			
			$package = $child['attrs']['name'];
			// parse packages_update.xml and fill $packageInfo.
			$packageInfo = self::parsePackageUpdateXMLBlock($child, $package);
			// convert enconding
			if ($encoding != CHARSET) {
				$packageInfo['packageName'] = StringUtil::convertEncoding($encoding, CHARSET, $packageInfo['packageName']);
				$packageInfo['packageDescription'] = StringUtil::convertEncoding($encoding, CHARSET, $packageInfo['packageDescription']);
				$packageInfo['author'] = StringUtil::convertEncoding($encoding, CHARSET, $packageInfo['author']);
				$packageInfo['authorURL'] = StringUtil::convertEncoding($encoding, CHARSET, $packageInfo['authorURL']);
			}
			
			$allNewPackages[$child['attrs']['name']] = $packageInfo;
		}
		unset($xml);
		
		return $allNewPackages;
	}
	
	/**
	 * @see PackageUpdate::parsePackageUpdateXMLBlock()
	 */
	protected static function parsePackageUpdateXMLBlock($child = array(), $package = '') {
		// define default values
		$packageInfo = array(
			'packageDescription' => '',
			'standalone' => 0,
			'plugin' => '',
			'author' => '',
			'authorURL' => '',
			'licenseName' => '',
			'licenseUrl' => '',
			'disableMirror' => false,
			'versions' => array()
		);
		
		// loop through tags inside the <package> tag.
		foreach ($child['children'] as $packageDefinition) {
			switch (StringUtil::toLowerCase($packageDefinition['name'])) {
				case 'packageinformation':
					// loop through tags inside the <packageInformation> tag.
					foreach ($packageDefinition['children'] as $packageInformation) {
						switch (StringUtil::toLowerCase($packageInformation['name'])) {
							case 'packagename':
								$packageInfo['packageName'] = $packageInformation['cdata'];
								break;
							case 'packagedescription':
								$packageInfo['packageDescription'] = $packageInformation['cdata'];
								break;
							case 'standalone':
								$packageInfo['standalone'] = intval($packageInformation['cdata']);
								break;
							case 'plugin':
								$packageInfo['plugin'] = $packageInformation['cdata'];
								break;
						}
					}
					
					break;
				case 'licenseinformation':
					// loop through tags inside the <licenseInformatioN> tag.
					foreach($packageDefinition['children'] as $licenseInformation) {
						switch (StringUtil::toLowerCase($licenseInformation['name'])) {
							case 'license':
								$packageInfo['licenseName'] = $licenseInformation['cdata'];
								break;
							case 'licenseurl':
								$packageInfo['licenseUrl'] = $licenseInformation['cdata'];
								break;
							case 'packagearchive:disablemirror':
								$packageInfo['disableMirror'] = true;
								break;
						}
					}
				case 'authorinformation':
					// loop through tags inside the <authorInformation> tag.
					foreach ($packageDefinition['children'] as $authorInformation) {
						switch (StringUtil::toLowerCase($authorInformation['name'])) {
							case 'author':
								$packageInfo['author'] = $authorInformation['cdata'];
							break;
							case 'authorurl':
								$packageInfo['authorURL'] = $authorInformation['cdata'];
							break;
						}
					}
					break;
				case 'versions':
					// loop through <version> tags inside the <versions> tag.
					foreach ($packageDefinition['children'] as $versions) {
						$versionNo = $versions['attrs']['name'];
						// loop through tags inside this <version> tag.
						foreach ($versions['children'] as $version) {
							switch (StringUtil::toLowerCase($version['name'])) {
								case 'fromversions':
									// loop through <fromversion> tags inside the <fromversions> block.
									foreach ($version['children'] as $fromversion) {
										$packageInfo['versions'][$versionNo]['fromversions'][] = $fromversion['cdata'];
									}
									break;
								case 'updatetype':
									$packageInfo['versions'][$versionNo]['updateType'] = $version['cdata'];
									break;
								case 'timestamp':
									$packageInfo['versions'][$versionNo]['timestamp'] = $version['cdata'];
									break;
								case 'file':
									$packageInfo['versions'][$versionNo]['file'] = $version['cdata'];
									break;
								case 'requiredpackages':
									// loop through <requiredPackage> tags inside the <requiredPackages> block.
									foreach ($version['children'] as $requiredPackages) {
										$required = $requiredPackages['cdata'];
										$packageInfo['versions'][$versionNo]['requiredPackages'][$required] = array();
										if (isset($requiredPackages['attrs']['minversion'])) {
											$packageInfo['versions'][$versionNo]['requiredPackages'][$required]['minversion'] = $requiredPackages['attrs']['minversion'];
										}
									}
									break;
								case 'excludedpackages':
									// loop through <excludedpackage> tags inside the <excludedpackages> block.
									foreach ($version['children'] as $excludedpackage) {
										$exclusion = $excludedpackage['cdata'];
										$packageInfo['versions'][$versionNo]['excludedPackages'][$exclusion] = array();
										if (isset($excludedpackage['attrs']['version'])) {
											$packageInfo['versions'][$versionNo]['excludedPackages'][$exclusion]['version'] = $excludedpackage['attrs']['version'];
										}
									}
									break;
							}
						}
					}
					break;
			}
		}
		
		// check required tags
		if (!isset($packageInfo['packageName'])) {
			throw new SystemException("required tag 'packageName' is missing for package '".$package."'", 13001);
		}
		if (!count($packageInfo['versions'])) {
			throw new SystemException("required tag 'versions' is missing for package '".$package."'", 13001);
		}
		
		return $packageInfo;
	}
}
?>