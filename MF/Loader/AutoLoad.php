<?php
if (!defined('NL')) define ('NL', "\n");

require_once ('MF/Exception/Handler.php');

class MF_Loader_AutoLoad {
	private static $exceptions = true;
	private static $namespaces;
	
	public static function autoload ($class) {
		$autoload = true;
		
		if (!empty(self::$namespaces)) {
			$autoload = false;
			foreach (self::$namespaces as $namespace) {
				if (substr($class, 0, strlen($namespace)) == $namespace) {
					$autoload = true;
					break;
				}
			}
		}
		
		if ($autoload) {
			try {
				$file = str_replace('_', '/', $class).'.php';
				@include_once($file);
				
			    if (!class_exists($class, false) && !interface_exists($class, false)) {
			    	if (self::$exceptions) {
						throw (new Exception(MF_EXCEPTION_CLASS_NOT_FOUND));
			    	}
			    }
		    }
		    catch (Exception $e) {
		    	MF_Exception_Handler::getInstance()->handle($e);
		    }
		}
	}
	
	public static function register ($args = array()) {
		if (isset($args['exceptions'])) {
			self::$exceptions = $args['exceptions'];
		}
		if (isset($args['namespaces'])) {
			self::$namespaces = array();
			foreach($args['namespaces'] as $namespace) {
				self::$namespaces[] = str_replace('/', '_', $namespace);
			}
		}
		
		if ( (isset($args['__autoload']) && ($args['__autoload'])) || (version_compare(PHP_VERSION, '5.1.2', '<')) ) {
			function __autoload ($class) {
				MF_Loader_AutoLoad::autoload($class);
			}
		}
		else {
			if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
				spl_autoload_register(array('MF_Loader_AutoLoad', 'autoload'), true, true);
			} else {
				spl_autoload_register(array('MF_Loader_AutoLoad', 'autoload'));
			}
		}
	}
}
