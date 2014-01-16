<?php
namespace MF\Front;

class DefaultRouter {
	/**
	 * Constructor
	 * 
	 * @return DefaultRouter
	 */
	public function __construct () {
	}
	
	public function rewrite ($path) {
		return ($path);
	}
}
