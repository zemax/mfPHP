<?php
if (!defined('MF_EXCEPTION_CLASS_NOT_FOUND')) define ('MF_EXCEPTION_CLASS_NOT_FOUND', 'MF_EXCEPTION_CLASS_NOT_FOUND');
if (!defined('MF_EXCEPTION_ACTION_NOT_FOUND')) define ('MF_EXCEPTION_ACTION_NOT_FOUND', 'MF_EXCEPTION_ACTION_NOT_FOUND');

class MF_Exception_Handler {
	private static $instance;
	
	/**
	 * Return MF_Exception_Handler Singleton
	 *
	 * @return MF_Exception_Handler
	 */
	public static function getInstance () {
		if (!isset(self::$instance)) {
			self::setInstance(new MF_Exception_Handler ());
		}
		
		return (self::$instance);
	}
	
	/**
	 * Set the Exception Handler
	 * 
	 * @param $handler MF_Exception_Handler
	 */
	public static function setInstance ($handler) {
		self::$instance = $handler;
	}
	
	/**
	 * Handle Exception
	 * 
	 * @param $e Exception
	 */
	public function handle ($e) {
		switch ($e->getMessage()) {
			case MF_EXCEPTION_CLASS_NOT_FOUND:
				$stack = $e->getTrace();
				if ($stack[1]['class'] == 'MF_Front_Controller') {
					$this->handle404($e);
				}
				else {
					$this->handleDefault($e);
				}
				break;
				
			case MF_EXCEPTION_ACTION_NOT_FOUND:
				$this->handle404($e);
				break;
				
			default:
				$this->handleDefault($e);
		}
	}
	
	protected function handleDefault ($e) {
		MF_Response::setContentType(MF_Response::$contentTypeTEXT);
		print_r($e);
		die();
	}
	
	protected function handle404 ($e) {
		MF_Response::setHTTPStatus(404);
		$this->handleDefault($e);
	}
}
