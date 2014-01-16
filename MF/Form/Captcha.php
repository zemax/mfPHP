<?php
namespace MF\Form;

use MF\Response;

class Captcha {
	private $chars = '23456789ABCDEFGHJKMNPQRSTUVXYZ';
	private $fonts = array();
	private $colors = array();
	private $background = array(255, 255, 255);
	
	public function __construct() {
		if (!isset($_SESSION)){
			session_start();
		}
	}
	
	public function setChars($chars) {
		if (!empty($chars)){
			$this->chars = (string) $chars;
		}
	}
	
	public function addFont($font) {
		if (!empty($font)){
			if (is_array($font)){
				foreach($font as $f){
					array_push($this->fonts, $f);
				}
			}
			else {
				array_push($this->fonts, $font);
			}
		}
	}
	
	public function setBackground($color) {
		$this->background = $color;
	}
	
	public function addColor($color) {
		array_push($this->colors, $color);
	}
	
	public function generateValue($length = 4) {
		$l = strlen($this->chars);
		
		$value = '';
		for ($i = 0; $i < $length; $i++){
			$value .= $this->chars[mt_rand(0, $l - 1)];		
		}
		
		$_SESSION['captcha'] = $value;
	}
	
	public function getValue() {
		if (empty($_SESSION['captcha'])) {
			$this->generateValue();
		}
		
		return ($_SESSION['captcha']);
	}
	
	public function compareValue($value, $caseSensitive = false) {
		if ($caseSensitive) {
			return (strcmp($value, $this->getValue()) == 0);
		}
		else {
			return (strcasecmp($value, $this->getValue()) == 0);
		}
	}
	
	private function getColor($image) {
		if (empty($this->colors)) {
			$color = imagecolorallocate($image, rand(0,255), rand(0,255), rand(0,255));
		}
		else {
			$colorIndex = $this->colors[mt_rand(0, count($this->colors)-1)];
			$color = imagecolorallocate($image, $colorIndex[0], $colorIndex[1], $colorIndex[2]);
		}
		return ($color);
	}
	
	private function getFont() {
		$font = $this->fonts[mt_rand(0, count($this->fonts)-1)];
		return ($font);
	}
	
	public function createImage($width = 110, $height = 35, $angleMax = 25, $padding = 8, $spacingRatio = 1.3) {
		$image 	= imagecreate($width, $height);
		$bg 	= imagecolorallocate($image, $this->background[0], $this->background[1], $this->background[2]);

		if (empty($this->fonts)) {
			$this->addFont($path = realpath(dirname(__FILE__)).'/doris.ttf');
		}
		
		$value = $this->getValue();
		$valueLength = strlen($value);
		$valueWidth = $spacingRatio * ($valueLength - 1) + 1;
		
		$hSize = ($width - 2 * $padding) / $valueWidth;
		$vSize = ($height - 2 * $padding);
		
		$size = min($vSize, $hSize);
		$spacing = $spacingRatio * $size;
		$x = 0.5 * ($width - ($size * $valueWidth));
		$y = 0.5 * ($height - $size) + $size;
		
		// Génération des lettres
		for ($i = 0; $i < $valueLength; $i++){
			// font
			$font = $this->getFont();

			// color
			$color = $this->getColor($image);
			
			// angle
			$angle = mt_rand(-$angleMax, $angleMax);
			
			imagettftext($image, $size, $angle, $x, $y, $color, $font, $value[$i]);
			$x += $spacing;
		}
		
		Response::setContentType('image/png');
		Response::setNoCache();
		imagepng($image);
	}
}
