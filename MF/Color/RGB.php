<?php
namespace MF\Color;

use MF\String;

class RGB {
	private $red 	= 0;
	private $green 	= 0;
	private $blue 	= 0;
	
	/**
	 * Constructor
	 * 
	 * @return RGB
	 */
	public function __construct ($html = '#000000') {
		$this->red 		= hexdec(substr($html, 1, 2));
		$this->green 	= hexdec(substr($html, 3, 2));
		$this->blue 	= hexdec(substr($html, 5, 2));
	}
	
	public function multiply ($ratio) {
		$this->red 		= min(255, round($this->red * $ratio));
		$this->green 	= min(255, round($this->green * $ratio));
		$this->blue 	= min(255, round($this->blue * $ratio));
	}
	
	public function getHTML () {
		return ('#'	.String::sizedNumber(dechex($this->red), 2)
					.String::sizedNumber(dechex($this->green), 2)
					.String::sizedNumber(dechex($this->blue), 2)
		);
	}
}
