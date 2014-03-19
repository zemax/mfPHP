<?php
/**
* @version 2.1.5 2009-01-15
* - static keyword
* - updated isJetableEmail
*
* @version 2.1.4 2008-05-14
* - added isValidEmail
* - added isJetableEmail
*
* @version 2.1.3 2008-01-31
* - added sizedNumber
*
* @version 2.1.2 2007-12-13
* - replaced self:: by StringManager:: for PHP4 compatibility
*
* @version	2.1.1 2007-07-18
* - added autodetect mb_string
* - added optionnal delimiters array in toCapitalize
* - replaced StringManager:: by self::
*
* @version	2.1.0 2007-06-13
*/

namespace MF;

class String {
	/**
	 * Return lowercase string
	 *
	 * @param $str		String
	 * 
	 * @return String
	 */
	public static function toLowerCase ($str) {
		if (extension_loaded('mbstring')) {
			$str = mb_strtolower($str, 'UTF-8');
		}
		else {
			$str = preg_replace(
				array('/Á/','/À/','/Â/','/Ä/','/É/','/È/','/Ê/','/Ë/','/Í/','/Ì/','/Î/','/Ï/','/Ó/','/Ò/','/Ô/','/Ö/','/Ú/','/Ù/','/Û/','/Ü/','/Ç/'), 
				array('á','à','â','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','ô','ö','ú','ù','û','ü','ç'), 
				$str);
				
			$str = strtolower($str);
		}
		
		return ($str);
	}
	
	/**
	 * Return uppercase string
	 *
	 * @param $str		String
	 * 
	 * @return String
	 */
	public static function toUpperCase ($str) {
		if (extension_loaded('mbstring')) {
			$str = mb_strtoupper($str, 'UTF-8');
		}
		else {
			$str = preg_replace(
				array('/á/','/à/','/â/','/ä/','/é/','/è/','/ê/','/ë/','/í/','/ì/','/î/','/ï/','/ó/','/ò/','/ô/','/ö/','/ú/','/ù/','/û/','/ü/','/ç/'),
				array('Á','À','Â','Ä','É','È','Ê','Ë','Í','Ì','Î','Ï','Ó','Ò','Ô','Ö','Ú','Ù','Û','Ü','Ç'), 
				$str);
		
			$str = strtoupper($str);
		}
		
		return ($str);
	}
	
	/**
	 * Return capitalized string
	 *
	 * @param $str		String
	 * 
	 * @return String
	 */
	public static function toCapitalize ($str, $delimiters = array(' ', '-', '/')) {
		$str = self::toLowerCase($str);
		
		$delimiters_count 	= count($delimiters);
		for ($i = 0; $i < $delimiters_count; $i++) {
			$a = explode($delimiters[$i], $str);
			$l = count($a);
			
			if (extension_loaded('mbstring')) {
				for ($j = 0; $j < $l; $j++) {
					$a[$j] = self::toUpperCase(mb_substr($a[$j], 0, 1, 'UTF-8')).mb_substr($a[$j], 1, mb_strlen($a[$j], 'UTF-8') - 1, 'UTF-8');
				}
			}
			else {
				for ($j = 0; $j < $l; $j++) {
					$a[$j] = self::toUpperCase(substr($a[$j], 0, 1)).substr($a[$j], 1, strlen($a[$j]) - 1);
				}
			}
			
			$str = join($delimiters[$i], $a);
		}
		
		return ($str);
	}
	
	/**
	 * Transform an (UTF-8) variable into an ISO-8859-15 String
	 *
	 * @param $v		String
	 * 
	 * @return String
	 */
	public static function toLatin1($v, $from = 'UTF-8') {
		return (mb_convert_encoding($v, 'ISO-8859-15', $from));
	}
	
	/**
	* Return a string from a number with additionnal "0"
	* 
	* @param $s		Int, String		The number
	* @param $l		Int				Length
	* 
	* @return String
	*/
	public static function sizedNumber ($s, $l) {
		$s = ''.$s;
		while (strlen($s) < $l) {
			$s = '0'.$s;
		}
		
		return ($s);
	}
	
	/**
	 * Trim a string
	 * 
	 * @param $str		String
	 * 
	 * @return String
	 */
	public static function trim ($str) {
		return (trim($str));
	}
	
	/**
	 * Replace accentued characters with non-accentued ones
	 * 
	 * @param $str		String
	 * 
	 * @return String
	 */
	public static function replaceAccents ($str) {
		$str = preg_replace(
					array (
						'/Á/','/À/','/Â/','/Ä/',
						'/É/','/È/','/Ê/','/Ë/',
						'/Í/','/Ì/','/Î/','/Ï/',
						'/Ó/','/Ò/','/Ô/','/Ö/',
						'/Ú/','/Ù/','/Û/','/Ü/',
						'/Ç/',
						
						'/á/','/à/','/â/','/ä/',
						'/é/','/è/','/ê/','/ë/',
						'/í/','/ì/','/î/','/ï/',
						'/ó/','/ò/','/ô/','/ö/',
						'/ú/','/ù/','/û/','/ü/',
						'/ç/'
					),
					array (
						'A','A','A','A',
						'E','E','E','E',
						'I','I','I','I',
						'O','O','O','O',
						'U','U','U','U',
						'C',
						
						'a','a','a','a',
						'e','e','e','e',
						'i','i','i','i',
						'o','o','o','o',
						'u','u','u','u',
						'c'
					),
					$str
				);
				
		return ($str);
	}
	
	/**
	 * Clean an email
	 *
	 * @param $str		String
	 * 
	 * @return String
	 */
	public static function cleanEmail ($str) {
		$str = self::trim($str);
		$str = self::toLowerCase($str);
		$str = self::replaceAccents($str);
		$str = preg_replace('/[^a-zA-Z0-9@_\.\-\+]/', '', $str);
		
		return ($str);
	}
	
	/**
	 * Clean a string (for filenames, ID, etc...)
	 * - spaces and ' replaced by "-"
	 * - Accents stripped
	 * - Only keep 0-9A-Za-z_.-
	 * 
	 * @param $str		String
	 * 
	 * @return String
	 */
	public static function cleanString ($str) {
		$str = self::trim($str);
		$str = self::replaceAccents($str);
		$str = preg_replace(
					array (
						"/'/",
						"/ /"
					),
					array (
						'-',
						'-'
					),
					$str
				);
		$str = preg_replace('/[^0-9A-Za-z_.-]/', '', $str);
		$str = preg_replace('/--*/', '-', $str);
		$str = preg_replace('/-*$/', '', $str);
		
		return ($str);
	}
	
	/**
	 * Transform a String to an ID (clean string + cut)
	 *
	 * @param $str		String
	 * @param $size		Int
	 * @param $prefix	String
	 * @param $suffix	String
	 * 
	 * @return String
	 */
	public static function makeID ($str, $size = 50, $prefix = '', $suffix = '') {
		$str = self::toLowerCase($str);
		$str = self::cleanString($str);
		$str = preg_replace('/\./', '-', $str);
		$str = substr($str, 0, $size);
		
		return ($prefix.$str.$suffix);
	}
	
	/**
	 * Build a random string with distinguables characters
	 *
	 * @param $code_length	Int
	 * 
	 * @return String
	 */
	public static function generateCode ($code_length) {
		$chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
		$l = strlen($chars);
		$__code__ = '';
		for ($i = 0; $i < $code_length; $i++) {
			$__code__ = $__code__.$chars[rand(0, $l-1)];
		}
		
		return ($__code__);
	}
	
	/**
	 * Return true if the email syntax is correct
	 * 
	 * @param $str		String
	 * 
	 * @return Boolean
	 */
	public static function isValidEmail ($str) {
		return (preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$/', $str));
	}
	
	/**
	 * Return true if the email is in the "jetable" blacklist
	 * 
	 * @param $str		String
	 * 
	 * @return Boolean
	 */
	public static function isJetableEmail ($str) {
		$str = self::cleanEmail($str);
		$a = explode('@', $str);
		$domain = array_pop($a);
		
		switch ($domain) {
			case 'jetable.org':
			case 'jetable.net':
			case 'jetable.com':
			case 'spambox.us':
			case 'yopmail.com':
			case 'yopmail.fr':
			case 'ephemail.net':
			case 'iximail.com':
			case 'kasmail.com':
			case 'haltospam.com':
			case 'brefemail.com':
			case 'trashmail.net':
			case 'link2mail.net':
				return (true);
				break;
				
			case 'yahoo.fr':	
			case 'yahoo.com':
				return (preg_match('/-/', $str));
				break;
				
			case 'gmail.com':
			case 'mail.google.com':
				return (preg_match('/\+/', $str));
				break;

			default:
				return (false);
		}
	}
}
