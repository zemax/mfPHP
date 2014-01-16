<?php
namespace MF\Front;

class HTMLRouter {
	/**
	 * Constructor
	 * 
	 * @return HTMLRouter
	 */
	public function __construct () {
	}
	
	public function rewrite ($path) {
		if (preg_match('`^(.*)\.html$`', $path, $count)) {
			$path = 'page/view/'.$count[1];
		}
		
		return ($path);
	}
}
