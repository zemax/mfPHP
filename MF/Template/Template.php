<?php
/**
* Template
* Generate output from a file or string with Macro replacement
* 
* @version	1.0.2
* - cleanup code
* 
* @version	1.0.1
* - added replaceHR
*
* @version	1.0.0
* 
* @author	Maxime Cousinou
* @author	Romain Laurent
**/

namespace MF\Template;

use \Exception as Exception;

class Template {
	private $macrosPrefix;
	private $startDelimiter;
	private $endDelimiter;
	private $content;
	
	/**
	 * Constructor
	 *
	 * @return Template
	 */
	public function __construct ($macrosPrefix = 'Macro_', $startDelimiter = '{[', $endDelimiter = ']}') {
		$this->startDelimiter 	= $startDelimiter;
		$this->endDelimiter 	= $endDelimiter;
		$this->macrosPrefix 	= $macrosPrefix;
		
		$this->content = '';
	}
	
/****************************************************************************************************
* GET CONTENT 
****************************************************************************************************/
	
	/**
	 * Return content
	 * 
	 * @return String
	 */
	public function getContent () {
		return ($this->content);
	}
	
/****************************************************************************************************
* SET CONTENT 
****************************************************************************************************/
	
	/**
	 * Get content from a file
	 *
	 * @param $filename			String
	 * 
	 * @return Boolean
	 */
	public function setContentFromFile ($filename) {
		if (file_exists($filename)) {
			return ($this->content = file_get_contents($filename));
		}
		else {
			return (false);
		}
	}
	
	/**
	 * Get content from a string
	 *
	 * @param $string			String
	 * 
	 * @return Boolean
	 */
	public function setContentFromString ($string) {
		$this->content = $string;
		
		return (true);
	}
	
/****************************************************************************************************
* PARSE CONTENT 
****************************************************************************************************/
	
	/**
	 * Replace macro in content
	 * Macro syntax is {[$macro]}
	 * 
	 * @param $macroFullString		String		Macro to replace
	 * @param $replacement			String		Value for replace
	 * @param $limit				Int 		Replacements limit
	 */
	public function replaceMacro ($macroFullString, $replacement, $limit = 0) {
		if (empty($limit)) {
			$this->content = str_replace(
								$this->startDelimiter.$macroFullString.$this->endDelimiter, 
								$replacement, 
								$this->content);
		}
		else {
			$this->content = preg_replace(
								'`'.preg_quote($this->startDelimiter.$macroFullString.$this->endDelimiter).'`i', 
								$replacement, 
								$this->content, 
								$limit);
		}
	}
	
	/**
	 * Return Macro content
	 * 
	 * @param $id				String 
	 * @param $params			Array 
	 * 
	 * @return String
	 */
	public function getReplacement ($macroName, $params, $data) {
		$className 		= $this->macrosPrefix.$macroName;
		try {
			$macroInstance 	= new $className($params, $data);
			return ($macroInstance->getContent());
		}
		catch (Exception $e) {
			return ($this->startDelimiter.$macroName.$this->endDelimiter);
		}
	}
	
	/**
	 * Replace all macros with Macros contents
	 * and return the number of replacements
	 * 
	 * @param $data		Array
	 * 
	 * @return Int
	 */
	public function parseMacros ($data = array()) {
		preg_match_all('`'.preg_quote($this->startDelimiter).'(.*?)'.preg_quote($this->endDelimiter).'`i', $this->content, $out);
		
		$l = count($out[1]);
		for ($i=0; $i<$l; $i++) {
			$macroFullString = $out[1][$i];
			
			if (preg_match('`(.*)\((.*?)\)`i', $macroFullString, $out2)) {
				$macroName 	= $out2[1];
				$params 	= explode(',', $out2[2]);
			}
			else {
				$macroName 	= $macroFullString;
				$params 	= array();
			}
			
			$this->replaceMacro($macroFullString, 
								$this->getReplacement($macroName, $params, $data), 
								1);
		}
		
		return ($l);
	}
	
	/**
	 * Replace <hr> with <div class="hr"> in content
	 */
	public function replaceHR () {
		$this->content = preg_replace(	'`<hr */*>`i', 
										'<div class="hr"></div>', 
										$this->content);
	}
}
