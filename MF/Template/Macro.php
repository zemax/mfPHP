<?php
class MF_Template_Macro {
	public $parameters;
	public $data;
	
	/**
	 * Constructor
	 *
	 * @param $parameters	Array
	 * 
	 * @return Macro
	 */
	public function __construct ($parameters, $data = array()) {
		$this->parameters 	= $parameters;
		$this->data 		= $data;
	}
	
	/**
	 * Content
	 *
	 * @return String
	 */
	public function getContent () {
		return ('MACRO : '.get_class($this)."\n"
				.print_r($this->parameters, true));
	}
}
