<?php
/**
 * ReturnObject
 * Simple value Object for return
 * 
 * @author Maxime
 */
class MF_Return_Object {
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
	 * @return MF_Return_Object
	 */
	public static function returnException ($e) {
		switch ($e->getMessage()) {
			case 'database_error':
				return (new MF_Return_Object(0, $e->getMessage(), $e->getFile().' : line '.$e->getLine()."\n".MF_SQL::getInstance()->errorCode().' : '.MF_SQL::getInstance()->errorMessage()));
				break;
				
			default:
				return (new MF_Return_Object(0, $e->getMessage(), $e->getFile().' : line '.$e->getLine()));
		}
	}
}
