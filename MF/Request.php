<?php
/**
 * VarTools
 * Tool for handling variables from $_GET or $_POST
 * 
 * @version 1.0.1 2008-09-03
 * - remove explicit & reference
 *
 * @version	1.0.0 2007-06-13
 * 
 * @author	Maxime Cousinou
 */

class MF_Request {
	/**
	 * Return a copy of variables Arrays ($_GET ou $_POST) without the quotes (even with magic_quotes on)
	 *
	 * @param $a		Array
	 * 
	 * @return array
	 */
	public static function getVarsArray($a) {
		$r = array();
		
		if (get_magic_quotes_gpc()) {
			foreach ($a as $key => $value) {
				if (is_array($value) || is_object($value)) {
					$r[$key] = MF_Request::getVarsArray($value);
				}
				else {
					$r[$key] = stripslashes($value);
				}
			}
		}
		else {
			foreach ($a as $key => $value) {
				$r[$key] = $value;
			}
		}
		
		return ($r);
	}
	
	/**
	 * Return the Front Controller Parameters
	 * 
	 * @return array
	 */
	public static function getParameters () {
		return (MF_Front_Controller::getInstance()->getParameters());
	}
}
