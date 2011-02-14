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
	 * @param	pointer	$outputBuffer
	 * @param	boolean	$writeXmlHeader
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
	 * @param unknow$data
	 * @param unknown_type $outputBuffer
	 * @param unknown_type $writeXmlHeader
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
	 * @param	pointer	$outputBuffer
	 * @param	boolean	$writeXmlHeader
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
	 * @param	pointer	$outputBuffer
	 * @param	boolean	$writeXmlHeader
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
}
?>