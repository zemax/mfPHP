<?php
/**
* MF_Front_Controller
* Décide du controller à utiliser
*
* @version	1.6.0 2008-09-03
* - use static $instance
* - remove explicit references
*
* @version 	1.5.0 2008-05-21
* - added dispatchPATH
* - added dispatchGET
* - moved class
* - added $module, $action & $parameters
* 
* @version	1.0.0 2007-10-05
*
* @author	Maxime Cousinou
* 
* .htaccess sample for dispatchPath :
* 
* RewriteEngine 	On
* RewriteBase		/_LIB/PHP/STABLE
* RewriteCond %{REQUEST_FILENAME} -f [OR]
* RewriteCond %{REQUEST_FILENAME} -d [OR]
* RewriteCond %{REQUEST_FILENAME} -l
* RewriteRule ^.*$ - [NC,L]
* RewriteRule ^(.*)\.html$ index.php/page/view/$1 [NC,L]
* RewriteRule ^(.*)$ index.php [NC,L]
*/
if (!defined('DEFAULT_CONTROLLER')) define ('DEFAULT_CONTROLLER', 	'page');
if (!defined('DEFAULT_ACTION')) 	define ('DEFAULT_ACTION', 		'index');

class MF_Front_Controller {
	private static $instance;
	
	private $router;
	
	private $module;
	private $action;
	private $parameters;
	
	/**
	 * Constructor
	 * 
	 * @return MF_Front_Controller
	 */
	private function __construct () {
		$this->setRouter(new MF_Front_DefaultRouter());
	}
	
	/**
	 * Return MF_Front_Controller Singleton
	 *
	 * @return MF_Front_Controller
	 */
	public static function getInstance () {
		if (!isset(self::$instance)) {
			self::$instance = new MF_Front_Controller ();
		}
		
		return (self::$instance);
	}
	
	/**
	 * Set the Router
	 * 
	 * @param $router
	 */
	public function setRouter ($router) {
		$this->router = $router;
	}
	
	/**
	 * Get the router
	 * 
	 * @return MF_Front_Router
	 */
	public function getRouter () {
		return ($this->router);
	}
	
	/**
	 * Get the module
	 * 
	 * @return String
	 */
	public function getModule () {
		return ($this->module);
	}
	
	/**
	 * Get the action
	 * 
	 * @return String
	 */
	public function getAction () {
		return ($this->module);
	}
	
	/**
	 * Get the additionnal parameters
	 * 
	 * @return Array
	 */
	public function getParameters () {
		return ($this->parameters);
	}
	
	/**
	 * Get the URI
	 * 
	 * @return String
	 */
	private function getURI () {
		$requestUri = '';
		
		if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
			$requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
		}
		elseif (isset($_SERVER['REQUEST_URI'])) {
			$requestUri = $_SERVER['REQUEST_URI'];
			if (isset($_SERVER['HTTP_HOST']) && strstr($requestUri, $_SERVER['HTTP_HOST'])) {
				$requestUri = preg_replace('#^[^:]*://[^/]*/#', '/', $requestUri);
			}
		}
		elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
			$requestUri = $_SERVER['ORIG_PATH_INFO'];
			if (!empty($_SERVER['QUERY_STRING'])) {
				$requestUri .= '?' . $_SERVER['QUERY_STRING'];
			}
		}
		
		return ($requestUri);
	}
	
	/**
	 * Run the Controller and the Action
	 * 
	 * @param $application		String
	 */
	private function doDispatch ($application = 'Site') {
		try {
			$controllerName = $application.'_'.MF_String::toCapitalize($this->module).'_Controller';
			$actionName		= $this->action;
			
			// CONTROLLER
			$controller 	= new $controllerName();
			
			// ACTION
			if (!method_exists($controller, $actionName)) throw (new Exception(MF_EXCEPTION_ACTION_NOT_FOUND));
			$controller->$actionName();
		}
		catch (Exception $e) {
			MF_Exception_Handler::getInstance()->handle($e);
		}
	}
	
	/**
	 * Process a call with structure :
	 * http://.../index.php?/module/action/param1/.../?get1=...
	 *
	 * @param $application		String
	 */
	public function dispatchGET ($application = 'Site') {
		// Analyse à partir des variables $_SERVER pour cause d'URL_Rewrite
		$p = array (
			'path' 		=> $_SERVER['SCRIPT_NAME'],
			'query' 	=> $_SERVER['QUERY_STRING']
		);
		
		if (!empty($p['query']) && (substr($p['query'], 0, 1) == '/')) {
			$p = parse_url($p['query']);
			
			// MODULE / ACTION
			$split = explode('/', substr($p['path'], 1));
			
			// GET
			if (!empty($p['query'])) {
				parse_str($p['query'], $_GET);
			}
		}
		else {
			$split = array();
		}
		
		$module = (!empty($split[0]))?$split[0]:DEFAULT_CONTROLLER;
		$action = (!empty($split[1]))?$split[1]:DEFAULT_ACTION;
		
		$this->module 		= $module;
		$this->action 		= $action;
		$this->parameters 	= array_splice($split, 2);
		
		// DISPATCH
		$this->doDispatch($application);
		
		return (true);
	}
	
	/**
	 * Process a call with structure :
	 * http://.../index.php/module/action/param1/.../?get1=...
	 *
	 * @param $application		String
	 */
	public function dispatchPATH ($application = 'Site') {
		// Get path called after script name
		$path = substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME']) + 1);

		// Empty ? Direct call to /module/action
		if (empty($path)) {
			$uri 	= $this->getURI();
			$p 		= parse_url($uri);

			$dir 	= str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
			if (substr($dir, -1) != '/') {
				$dir .= '/';
			}
			$path = substr($p['path'], strlen($dir));
		}
		
		$path = $this->getRouter()->rewrite($path);
		
		// MODULE / ACTION
		$split = explode('/', $path);
		
		while(isset($split[count($split) - 1]) && ($split[count($split) - 1] == '')) {
			array_pop($split);
		}
		
		$module = (isset($split[0]))?$split[0]:DEFAULT_CONTROLLER;
		$action = (isset($split[1]))?$split[1]:DEFAULT_ACTION;
		
		$this->module 		= $module;
		$this->action 		= $action;
		$this->parameters 	= array_splice($split, 2);
		
		// DISPATCH
		$this->doDispatch($application);
		
		return (true);
	}
}
