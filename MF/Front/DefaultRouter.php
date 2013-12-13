<?php
class MF_Front_DefaultRouter {
	/**
	 * Constructor
	 * 
	 * @return MF_Front_DefaultRouter
	 */
	public function __construct () {
	}
	
	public function rewrite ($path) {
		return ($path);
	}
}
