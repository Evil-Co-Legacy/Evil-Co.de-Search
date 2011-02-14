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
	public static function generate($type, $data, &$outputBuffer = null, $writeXmlHeader = true) {
		// convert type case
		$type = StringUtil::toLower($type);
		
		// type validation
		if (!in_array($type, self::$allowedTypes)) throw new SystemException("Use of undefined type '".$type."'");
		
		// generate method name
		$methodName = 'generate'.ucfirst($type);
		
		// call generator method
		$this->{$methodName}($data, $outputBuffer, $writeXmlHeader);
	}
	
	/**
	 * Generates dynamic XML strings
	 * @param	array	$data
	 * @param	pointer	$outputBuffer
	 * @param	boolean	$writeXmlHeader
	 */
	public static function generateXml($data, &$outputBuffer = null, $writeXmlHeader = true) {
		if ($outputBuffer === null) {
			unset($outputBuffer);
			$outputBuffer = "";
			$sendOutput = true;
		}
		
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
				
				// create buffer
				$buffer = "";
				
				// generate xml string
				self::generateXml($data, $buffer, false);
				
				// split buffer
				$bufferArray = explode("\n", $buffer);
				
				// prefix every line with \t
				foreach($bufferArray as $buffer) echo "\t".$buffer;
				
				// send newline
				echo "\n\t";
			} else {
				// cast to string
				$data = (string) $data;
				
				// send output
				echo $data;
			}
			
			echo "</".$key.">";
		}
		
		if ($writeXmlHeader) {
			echo "</".self::API_XML_TAG.">";
		}
		$outputBuffer = ob_get_clean();
		ob_end();
		
		if ($sendOutput) {
			echo $outputBuffer;
		}
	}
	
	/**
	 * Generates dynamic json strings
	 * @param unknow$data
	 * @param unknown_type $outputBuffer
	 * @param unknown_type $writeXmlHeader
	 */
	protected static function generateJson ($data, &$outputBuffer = null, $writeXmlHeader = true) {
		if ($outputBuffer === null)
			echo json_encode($data);
		else
			$outputBuffer = json_encode($data);
	}
	
	/**
	 * Generates dynamic var_dump strings
	 * @param	array	$data
	 * @param	pointer	$outputBuffer
	 * @param	boolean	$writeXmlHeader
	 * @throws SystemException
	 */
	protected static function generateVar_dump($data, &$outputBuffer = null, $writeXmlHeader = true) {
		if ($outputBuffer === null)
			var_dump($data);
		else {
			ob_start();
			echo var_dump($data);
			$outputBuffer = ob_get_clean();
			ob_end();
		}
	}
	
	/**
	 * Generates dynamic print_r 
	 * @param	array	$data
	 * @param	pointer	$outputBuffer
	 * @param	boolean	$writeXmlHeader
	 * @throws SystemException
	 */
	protected static function generatePrint_r($data, &$outputBuffer = null, $writeXmlHeader = true) {
		if ($outputBuffer === null)
			print_r($data);
		else {
			ob_start();
			echo print_r($data);
			$outputBuffer = ob_get_clean();
			ob_end();
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
}
?>