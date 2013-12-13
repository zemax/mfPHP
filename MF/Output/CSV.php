<?php
class MF_Output_CSV {
	var $delimiter;
	var $separator;
	var $newline;
	
	public function __construct($delimiter = '"', $separator = ',', $newline = "\r\n") {
		$this->delimiter 	= $delimiter;
		$this->separator 	= $separator;
		$this->newline 		= $newline;
	}
	
	public function printCSV ($array, $titles = array(), $filename = 'export.csv') {
		MF_Response::setContentType(MF_Response::$contentTypeCSV, MF_Response::$charsetISO);
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		
		if (!empty($titles)) {
			foreach ($titles as $key => $value) {
				$titles[$key] = $this->quote($value);
			}
			print(implode($this->separator, $titles).$this->newline);
		}
		
		foreach ($array as $item) {
			foreach ($item as $key => $value) {
				$item[$key] = $this->quote($value);
			}
			print(implode($this->separator, $item).$this->newline);
		}
	}
	
	private function quote ($value) {
		return (MF_String::toISO($this->delimiter.str_replace($this->delimiter, str_repeat($this->delimiter, 2), $value).$this->delimiter));
	}
}
