<?php
/**
* Response
* 
* @version 0.1.2
*/

namespace MF;

class Response {
	public static $contentTypeHTML 			= 'text/html';
	public static $contentTypeTEXT 			= 'text/plain';
	public static $contentTypeJAVASCRIPT 	= 'text/javascript';
	public static $contentTypeCSS 			= 'text/css';
	public static $contentTypeCSV 			= 'text/csv';
	public static $charsetUTF8 				= 'utf-8';
	public static $charsetISO 				= 'iso-8859-1';
	
	/**
	 * Send no cache header
	 */
	public static function setNoCache () {
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	}
	
	/**
	 * Send redirection header
	 * 
	 * @param $url
	 */
	public static function setRedirect ($url) {
		header('Location: '.$url);
		exit();
	}
	
	/**
	 * Send Content Type header
	 * 
	 * @param $type		String
	 * @param $charset	String
	 */
	public static function setContentType ($type, $charset = '') {
		if (!empty($charset)) {
			header('Content-Type: '.$type.'; charset='.$charset);
		}
		else {
			header('Content-Type: '.$type);
		}
	}

	/**
	 * Send P3P Policy
	 *
	 * @param $p3p		String
	 */
	public static function setP3P ($p3p = 'CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"') {
		header('P3P: '.$p3p);
	}

	/**
	 * Send a String with gzip
	 * 
	 * @param $output	String
	 * @param $gzip		Boolean
	 */
	public static function sendOutput ($output, $gzip = false) {
		if ($gzip) {
			ob_start("ob_gzhandler");
		}
		
		 print ($output);
	}
	
	public static function setHTTPStatus ($code = 200) {
		switch ($code) {
			case 404:
				header("HTTP/1.0 404 Not Found");
				break;
				
			default:
		}
	}
}
