<?php
/**
 * Created by PhpStorm.
 * User: Maxime
 * Date: 17/01/14
 * Time: 12:43
 */

namespace MF\Social;

class Pinterest {
	public function __construct($mashapeApiKey) {
		$this->mashapeApiKey = $mashapeApiKey;
	}

	/****************************************************************************************************
	 * ACTIONS
	 ****************************************************************************************************/

	public function getPins($user, $page = 1) {
		$headers = array(
			"X-Mashape-Authorization: ".$this->mashapeApiKey
		);

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, "https://ismaelc-pinterest.p.mashape.com/" . $user . "/pins?page=" . $page);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt ($ch, CURLOPT_HEADER, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($ch);

		$curl_info = curl_getinfo($ch);
		$header_size = $curl_info["header_size"];
		$body = substr($response, $header_size);

		$pins = json_decode($body);

		return $pins->body;
	}
}
