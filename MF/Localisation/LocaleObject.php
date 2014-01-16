<?php
namespace MF\Localisation;

class LocaleObject {
	var $texts;
	
	public function __construct () {
		
	}
	
	public function getText ($id) {
		if (isset($this->texts[$id])) {
			return ($this->texts[$id]);
		}
		else {
			return ($id);
		}
	}
}
