<?php
/**
* Build HTML and TEXT versions of an email from files with fields customisation
*
* @version 1.0.0 2009-01-15
*
* @author Maxime Cousinou
*/

namespace MF\Email;

class Files {
	private $path		= '';
	private $base_path	= '';
	private $file		= 'email';
	private $title		= '';
	
	/**
	 * Constructor
	 * 
	 * @param $path		String	The files path
	 * @param $file		String	The files name
	 * 
	 * @return Files
	 */
	public function __construct ($path, $file = 'email', $base_path = '') {
		$this->path = $path;
		$this->file = $file;
		
		if (empty($base_path)) {
			$this->base_path = realpath(dirname(__FILE__).'/../../../');
		}
		else {
			$this->base_path = $base_path;
		}
	}
	
	/**
	 * Return email's title from HTML's title tag
	 * Must launch getHTML before launching this
	 *
	 * @return String
	 */
	public function getTITLE () {
		if (!empty($this->title)) {
			return ($this->title);
		}
		else {
			return ('Invitation');
		}
	}
	
	/**
	 * Return HTML version
	 * Substitute images src with BASE_URL + path
	 *
	 * @param $fields	Array	Associative array of ids as keys and substitutions as values
	 * 
	 * @return String
	 */
	public function getHTML ($fields = array()) {
		$dir 	= $this->base_path;
		
		$html 	= file_get_contents ($dir.'/'.$this->path.$this->file.'.html');
		$html 	= preg_replace('/src="images/i', 'src="'.BASE_URL.$this->path.'images', $html);
		
		foreach ($fields AS $key => $value) {
			$html = preg_replace('/{\['.$key.'\]}/i', $value, $html);
		}
		
		preg_match_all('`<title>(.*?)</title>`i', $html, $out);
		
		if (!empty($out[1][0])) {
			$this->title = $out[1][0];
		}
		
		return ($html);
	}
	
	/**
	 * Return TEXT version
	 *
	 * @param $fields	Array	Associative array of ids as keys and substitutions as values
	 * 
	 * @return String
	 */
	public function getTEXT ($fields = array()) {
		$dir 	= $this->base_path;
		
		$text = file_get_contents ($dir.'/'.$this->path.$this->file.'.txt');
		
		foreach ($fields AS $key => $value) {
			$text = preg_replace('/{\['.$key.'\]}/i', $value, $text);
		}
		
		return ($text);
	}
}
