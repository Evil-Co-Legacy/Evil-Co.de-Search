<?php

/**
 * Generates type outputs automaticly
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class APIUtil {
	
	/**
	 * Contains the name of api xml start tag
	 * @var string
	 */
	const API_XML_TAG = 'api';
	
	/**
	 * Contains allowed types
	 * @var array<string>
	 */
	protected static $allowedTypes = array('xml', 'json', 'var_dump', 'print_r');
	
	/**
	 * Contains content-types for all valid types
	 * @var array<string>
	 */
	protected static $typeHeaders = array(
		'xml'		=>	'application/xml',
		'json'		=>	'application/json',
		'var_dump'	=>	'text/plain',
		'print_r'	=>	'text/plain'
	);
	
	/**
	 * Generates a dynamic output
	 * @param	string	$type
	 * @param	array	$data
	 * @param	boolean	$outputBuffer
	 * @param	boolean	$writeXmlHeader
	 * @return mixed
	 * @throws SystemException
	 */
	public static function generate($type, $data, $outputBuffer = false, $writeXmlHeader = true) {
		// convert type case
		$type = strtolower($type);
		
		// type validation
		if (!in_array($type, self::$allowedTypes)) throw new SystemException("Use of undefined type '".$type."'");
		
		// generate method name
		$methodName = 'generate'.ucfirst($type);
		
		// call generator method
		return call_user_func(array('static', $methodName), $data, $outputBuffer, $writeXmlHeader);
	}
	
	/**
	 * Generates dynamic XML strings
	 * @param	array	$data
	 * @param	pointer	$outputBuffer
	 * @param	boolean	$writeXmlHeader
	 * @return mixed
	 */
	public static function generateXml($data, $outputBuffer = false, $writeXmlHeader = true) {
		if (!$outputBuffer) {
			unset($outputBuffer);
			$outputBuffer = "";
			$sendOutput = true;
		} else
			$sendOutput = false;
		
		// start output catching
		ob_start();
		if ($writeXmlHeader) {
			echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			echo "<".self::API_XML_TAG.">\n";
		}
		
		foreach($data as $key => $data) {
			echo "\t<".$key.">";
			
			if (is_array($data)) {
				// send newline
				echo "\n";
				
				// generate child xml
				$buffer = self::generateXml($data, true, false);
				
				// split buffer
				$bufferArray = explode("\n", $buffer);
				
				// prefix every line with \t
				foreach($bufferArray as $buffer) echo "\t".$buffer."\n";
				
				// send newline
				echo "\n\t";
			} else {
				// cast to string
				$data = (string) $data;
				
				// send output
				echo "<![CDATA[".$data."]]>";
			}
			
			echo "</".$key.">\n";
		}
		
		if ($writeXmlHeader) {
			echo "</".self::API_XML_TAG.">";
		}
		$outputBuffer = ob_get_clean();
		// // ob_end_clean();
		
		if ($sendOutput)
			echo $outputBuffer;
		else
			return $outputBuffer;
	}
	
	/**
	 * Generates dynamic json strings
	 * @param	array	$data
	 * @param	boolean	$outputBuffer
	 * @param	boolean	$writeXmlHeader
	 * @return mixed
	 */
	protected static function generateJson ($data, $outputBuffer = false, $writeXmlHeader = true) {
		if ($outputBuffer === null)
			echo json_encode($data);
		else
			return json_encode($data);
	}
	
	/**
	 * Generates dynamic var_dump strings
	 * @param	array	$data
	 * @param	boolean	$outputBuffer
	 * @param	boolean	$writeXmlHeader
	 * @return mixed
	 * @throws SystemException
	 */
	protected static function generateVar_dump($data, $outputBuffer = false, $writeXmlHeader = true) {
		if (!$outputBuffer)
			var_dump($data);
		else {
			ob_start();
			echo var_dump($data);
			$outputBuffer = ob_get_clean();
			// ob_end_clean();;
			return $outputBuffer;
		}
	}
	
	/**
	 * Generates dynamic print_r 
	 * @param	array	$data
	 * @param	boolean	$outputBuffer
	 * @param	boolean	$writeXmlHeader
	 * @return mixed
	 * @throws SystemException
	 */
	protected static function generatePrint_r($data, $outputBuffer = false, $writeXmlHeader = true) {
		if (!$outputBuffer)
			print_r($data);
		else {
			ob_start();
			echo print_r($data);
			$outputBuffer = ob_get_clean();
			// ob_end_clean();;
			return $outputBuffer;
		}
	}
	
	/**
	 * Returnes true if the given type is valid
	 * @param	string	$type
	 * @throws SystemException
	 */
	public static function isValidType($type) {
		return (in_array($type, self::$allowedTypes));
	}
	
	/**
	 * Returnes the content type for given output type
	 * @param	string	$type
	 * @throws SystemException
	 */
	public static function getContentType($type) {
		// validate
		if (!self::isValidType($type)) throw new SystemException("Invalid type '".$type."'");
		
		// get content type
		return self::$typeHeaders[$type];
	}
	
	/**
	 * Returnes true if the given ip address is banned
	 * @param	string	$ipAddress
	 */
	public static function isBanned($ipAddress) {
		// check API-Key blacklist
		$sql = "SELECT
				COUNT(*) AS count
			FROM
				www".WWW_N."_api_key_blacklist
			WHERE
				(
						ipAddress = '".escapeString($ipAddress)."'
					OR
						hostname = '".escapeString(gethostbyaddr($ipAddress))."'
				)
			AND
				banEnabled = 1
			AND
				expire >= ".TIME_NOW;
		$row = WCF::getDB()->getFirstRow($sql);
		
		// banned ip?
		if ($row['count'] > 0) return true;
		
		return false;
	}
	
	/**
	 * Returnes false if the given keys are not valid and an integer if the given key is valid
	 * @param	string	$publicKey
	 * @param	string	$secretKey
	 * @return mixed
	 * @throws SystemException
	 */
	public static function checkLogin($publicKey, $secretKey) {
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_api_key
			WHERE
				publicKey = '".escapeString($publicKey)."'
			AND
				secretKey = '".escapeString($secretKey)."'";
		$row = WCF::getDB()->getFirstRow($sql);
		
		// no valid key
		if (!WCF::getDB()->countRows()) return false;
		
		// return keyID
		return $row['keyID'];
	}
	
	/**
	 * Returnes true if the whitelist is enabled
	 * @param	integer	$keyID
	 * @return	boolean
	 * @throws SystemException
	 */
	public static function isWhiteListEnabled($keyID) {
		// check for enabled whitelist
		$sql = "SELECT
				COUNT(*) AS count
			FROM
				www".WWW_N."_api_key_whitelist
			WHERE
				keyID = ".$keyID;
		$row = WCF::getDB()->getFirstRow($sql);
		
		return ($row['count'] > 0);
	}
	
	/**
	 * Returnes true if the given address is on whitelist (This additionaly executes the isWhiteListEnabled method. Please do not use isWhiteListEnabled in combination with this method)
	 * @param	integer	$keyID
	 * @param	string	$ipAddress
	 * @return boolean
	 * @throws SystemException
	 */
	public static function checkWhiteList($keyID, $ipAddress) {
		if (!self::isWhiteListEnabled($keyID)) return true;
		
		$sql = "SELECT
				COUNT(*) AS count
			FROM
				www".WWW_N."_api_key_whitelist
			WHERE
				(
						whitelist.ipAddress = '".escapeString($ipAddress)."'
					OR
						whitelist.hostname = '".escapeString(gethostbyaddr($ipAddress))."'
				)
			AND
				keyID = ".$keyID;
		$row = WCF::getDB()->getFirstRow($sql);
		
		return ($row['count'] > 0);
	}
	
	/**
	 * Updates the blacklist
	 * @param	string	$ipAddress
	 * @param	boolean	$mode
	 * @return void
	 */
	public static function updateBlackList($ipAddress, $mode = true) {
		$sql = "SELECT
				*
			FROM
				www".WWW_N."_api_key_blacklist
			WHERE
				ipAddress = '".escapeString($ipAddress)."'
			OR
				hostname = '".escapeString(gethostbyaddr($ipAddress))."'";
		$row = WCF::getDB()->getFirstRow($sql);
		
		if (WCF::getDB()->countRows() > 0) {
			// update counts
			$sql = "UPDATE
					www".WWW_N."_api_key_blacklist
				SET
					badLoginCount = ".($mode ? ($row['badLoginCount'] + 1) : ($row['badLoginCount'] - 1)).",
					timestamp = ".TIME_NOW.",
					expire = ".(TIME_NOW + self::BAD_LOGIN_EXPIRE)."
					".($row['badLoginCount'] >= self::MAX_BAD_LOGIN_COUNT ? ", banEnabled = 1" : "")."
				WHERE
					banID = ".$row['banID'];
			WCF::getDB()->sendQuery($sql);
		} elseif ($mode) {
			// insert new ban
			$sql = "INSERT INTO
					www".WWW_N."_api_key_blacklist (ipAddress, hostname, badLoginCount, timestamp, expire, banEnabled)
				VALUES
					('".escapeString($ipAddress)."',
					 NULL,
					 1,
					 ".TIME_NOW.",
					 ".(TIME_NOW + self::BAD_LOGIN_EXPIRE).",
					 ".(self::MAX_BAD_LOGIN_COUNT == 1 ? 1 : 0).")";
			
			if (gethostbyaddr($ipAddress) != $ipAddress) {
				$sql .= ", (NULL,
					    '".escapeString(gethostbyaddr($ipAddress))."',
					    1,
					    ".TIME_NOW.",
					    ".(TIME_NOW + self::BAD_LOGIN_EXPIRE).",
					    ".(self::MAX_BAD_LOGIN_COUNT == 1 ? 1 : 0).")";
			}
			
			WCF::getDB()->sendQuery($sql);
		}
	}
}
?>