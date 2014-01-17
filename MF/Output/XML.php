<?php
/**
 * Functions for handling Flash <-> XML/PHP
 * 
 * @version 2.2.1 2009-01-15
 * - static keyword
 * 
 * @version 2.2.0 2008-09-03
 * - returnObject directly print headers and XML from a ReturnObject
 * - printXML now handle string or object
 * - returnMessage is now deprecated
 * - returnArray is now deprecated
 * 
 * @version 2.1.1 2008-05-23
 * - added objects as arrays
 * 
 * @version 2.1.0 2008-04-17
 * - changed arrayToXML to pass item id in attributes
 * 
 * @version 2.0.0 2007-06-13
 * - Les Strings sont supposées être en UTF8
 * 
 * @author 	Maxime Cousinou
 */

namespace MF\Output;

use MF\ReturnObject,
	MF\Response;

if (!defined('NL')) define ('NL', "\n");
 
class XML {
	/**
	 * Display the XML from a ReturnObject
	 *
	 * @param $return	ReturnObject
	 */
	public static function returnObject ($return) {
		Response::setContentType(Response::$contentTypeTEXT, Response::$charsetUTF8);
		
		if ($return->object == '') {
			print (self::getXML($return->success, $return->message));
		}
		else {
			print (self::getXML($return->success, $return->message, $return->object));
		}
	}
	
	/**
	 * Get XML with status, message and an additional information
	 *
	 * @param $success		String
	 * @param $message		String
	 * @param $object		String, Array, Object
	 */
	public static function getXML ($success, $message, $object = '') {
		$buffer = '<status success="'.$success.'" message="'.$message.'"';
		
		if ($object == '') {
			$buffer .= ' />';
		}
		else {
			$buffer .= '>';
			if (is_array($object) || is_object($object)) {
				$buffer .= NL.self::arrayToXML('', $object);
			}
			else {
				$buffer .= '<![CDATA['.$object.']]>';
			}
			$buffer .= '</status>'.NL;
		}
		
		return ($buffer);
	}
	
	/**
	 * Return an XML from a simple value variable
	 *
	 * @param $nodeName		String
	 * @param $nodeValue	String
	 * @param $level		Int
	 * @param $attributes	Array
	 * 
	 * @return String
	 */
	public static function valueToXML ($nodeName, $nodeValue, $level = 0, $attributes = array()) {
		$__output__  = str_repeat('	', $level);
		$__output__ .= '<'.$nodeName;
		foreach ($attributes as $key => $value) {
			$__output__ .= ' '.$key.'="'.$value.'"';
		}
		$__output__ .= '><![CDATA[';
		$__output__ .= $nodeValue;
		$__output__ .= ']]></'.$nodeName.'>'.NL;
		
		return ($__output__);
	}
	
	/**
	 * Return an XML from an array
	 *
	 * @param $nodeName		String
	 * @param $array		Array
	 * @param $level		Int
	 * @param $attributes	Array
	 * 
	 * @return String
	 */
	public static function arrayToXML ($nodeName = '', $array, $level = 0, $attributes = array()) {
		$__output__ = '';
		
		if (!empty($nodeName)) {
			$__output__ .= str_repeat('	', $level);
			$__output__ .= '<'.$nodeName;
			foreach ($attributes as $key => $value) {
				$__output__ .= ' '.$key.'="'.$value.'"';
			}
			$__output__ .= '>'.NL;
		}
		
		foreach ($array as $key => $value) {
			if (is_numeric($key)) {
				$__output__ .= self::varToXML('item', $value, $level + 1, array('id' => $key));
			}
			else {
				$__output__ .= self::varToXML($key, $value, $level + 1);
			}
		}
		
		if (!empty($nodeName)) {
			$__output__ .= str_repeat('	', $level);
			$__output__ .= '</'.$nodeName.'>'.NL;
		}
		
		return ($__output__);
	}
	
	/**
	 * Return an XML from a variable
	 *
	 * @param $nodeName		String
	 * @param $nodeValue	String, Array, Object
	 * @param $level		Int
	 * @param $attributes	Array
	 * 
	 * @return String
	 */
	public static function varToXML ($nodeName, $nodeValue, $level = 0, $attributes = array()) {
		if (is_array($nodeValue) || is_object($nodeValue)) {
			return (self::arrayToXML($nodeName, $nodeValue, $level, $attributes));
		}
		else {
			return (self::valueToXML($nodeName, $nodeValue, $level, $attributes));
		}
	}
}
