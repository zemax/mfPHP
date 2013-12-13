<?php
class MF_Front_HTMLRouter {
	/**
	 * Constructor
	 * 
	 * @return MF_Front_HTMLRouter
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
