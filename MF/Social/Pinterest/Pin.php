<?php
/**
 * Created by PhpStorm.
 * User: Maxime
 * Date: 20/01/14
 * Time: 15:37
 */

namespace MF\Social\Pinterest;


class Pin {
	function __construct($feedData) {
		$this->source = $feedData->domain;

		$this->content = $feedData->desc;

		$this->media = $feedData->src;

		$this->url = 'http://www.pinterest.com'.$feedData->href;

//		$this->rawData = $feedData;
	}
}
