<?php
/**
 * Created by PhpStorm.
 * User: Maxime
 * Date: 20/01/14
 * Time: 15:08
 */

namespace MF\Social\Twitter;

class TwitterAuthor {
	function __construct($feedData) {
		$this->screenName = $feedData->screen_name;
		$this->name = $feedData->name;

//		$this->rawData = $feedData;
	}
}
