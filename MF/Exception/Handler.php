<?php
namespace MF\Exception;

if (!defined('MF_EXCEPTION_404')) define ('MF_EXCEPTION_404', 'MF_EXCEPTION_404');
if (!defined('MF_EXCEPTION_CLASS_NOT_FOUND'))  define ('MF_EXCEPTION_CLASS_NOT_FOUND', 'MF_EXCEPTION_CLASS_NOT_FOUND');
if (!defined('MF_EXCEPTION_ACTION_NOT_FOUND')) define ('MF_EXCEPTION_ACTION_NOT_FOUND', 'MF_EXCEPTION_ACTION_NOT_FOUND');

use \Exception as Exception,
	MF\Response;

class Handler {
	private static $instance;
	
	/**
	 * Return Handler Singleton
	 *
	 * @return Handler
	 */
	public static function getInstance () {
		if (!isset(self::$instance)) {
			self::setInstance(new Handler ());
		}
		
		return (self::$instance);
	}
	
	/**
	 * Set the Exception Handler
	 * 
	 * @param $handler Handler
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
			case MF_EXCEPTION_404:
				$this->handle404($e);
				break;

			case MF_EXCEPTION_CLASS_NOT_FOUND:
				$stack = $e->getTrace();
				if ($stack[1]['class'] == 'MF\\Front\\Controller') {
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
		Response::setContentType(Response::$contentTypeTEXT);
		print_r($e);
		die();
	}
	
	protected function handle404 ($e) {
		Response::setHTTPStatus(404);
		$this->handleDefault($e);
	}
}
