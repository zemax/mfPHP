<?php
/**
* ReturnObject
* Simple value Object for return
*
* @author Maxime
*/
namespace MF;

use \Exception as Exception;

class ReturnObject {
	public $success;
	public $message;
	public $object;
	
	/**
	 * Constructor
	 * 
	 * @param $success		Int
	 * @param $message		String
	 * @param $object		String, Array, Object
	 * 
	 * @return ReturnObject
	 */
	public function __construct ($success, $message, $object = '') {
		$this->success	= $success;
		$this->message	= $message;
		$this->object	= $object;
	}
	
	/**
	 * Basic Return Object for Exception catch
	 * 
	 * @param $e			Exception
	 * 
	 * @return ReturnObject
	 */
	public static function returnException ($e) {
		switch ($e->getMessage()) {
			case 'database_error':
				return (new ReturnObject(0, $e->getMessage(), $e->getFile().' : line '.$e->getLine()."\n".SQL::getInstance()->errorCode().' : '.SQL::getInstance()->errorMessage()));
				break;
				
			default:
				return (new ReturnObject(0, $e->getMessage(), $e->getFile().' : line '.$e->getLine()));
		}
	}
}
