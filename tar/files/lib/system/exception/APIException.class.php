<?php

/**
 * This exception will thrown if an API-Error occoures
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class APIException extends Exception implements PrintableException {
	
	/**
	 * Contains exceptions type
	 * @var string
	 */
	protected $type = '';
	
	/**
	 * Contains an additional header
	 * @var string
	 */
	protected $header = '';
	
	/**
	 * Creates a new instance of APIException.
	 * Note: You can add additional argument to use c-style printf strings
	 * @param	string	$type
	 * @param	string	$message
	 * @param	mixed	$messageArg1
	 * @param	mixed	$messageArg2
	 * @param	mixed	...
	 */
	public function __construct($type, $message) {
		// save type
		$this->type = StringUtil::toLower($type);
		
		// get arguments
		$arguments = func_get_args();
		
		// kick first argument
		unset($arguments[0]);
		$arguments = array_merge(array(), $arguments); // Just for resorting
		
		// get code
		$code = 0;
		for($i = 0; $i < strlen($message); $i++) {
			$code += hexdec(sha1($message{$i}));
		}
		
		// replace message
		$message = call_user_func_array('sprintf', $arguments);
		
		// construct exception
		parent::__construct($message, $code);
	}
	
	/**
	 * Sets the additional header
	 * @param	string	$header
	 */
	public function setHeader($header) {
		$this->header = $header;
	}
	
	/**
	 * Displays the error
	 */
	public function show() {
		@header("Content-type: ".($this->type == 'xml' ? 'application/xml' : ($this->type == 'json' ? 'application/json' : 'text/plain')));
		if (!empty($this->header)) @header($this->header);
		
		switch($this->type) {
			case 'xml':
				echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
				echo "<api>";
				echo "\t<errorMessage><![CDATA[".$this->getMessage()."]]></errorMessage>\n";
				echo "\t<errorCode><![CDATA[".$this->getCode()."]]></errorCode>\n";
				echo "</api>";
				break;
			case 'json':
				echo json_encode(array('errorMessage' => $this->getMessage(), 'errorCode' => $this->getCode()));
				break;
			default:
				echo str_replace(':', '\:', $this->getMessage()).':'.$this->getCode();
				break;
		}
	}
}
?>