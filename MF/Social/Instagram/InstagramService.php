<?php
/**
 * Created by PhpStorm.
 * User: Maxime
 * Date: 17/01/14
 * Time: 15:23
 */

namespace MF\Social\Instagram;


class InstagramService {
	public function __construct($accessToken) {
		$this->accessToken = $accessToken;
	}

	public static function getAccessTokenURL($clientId, $redirectURL) {
		$url = 'https://instagram.com/oauth/authorize/'
					.'?client_id='.$clientId
					.'&redirect_uri='.urlencode($redirectURL)
					.'&response_type=token';

		return $url;
	}

	/****************************************************************************************************
	 * ACTIONS
	 ****************************************************************************************************/

	public function searchByTag($tag, $max_id = '', $min_id = '') {
		$url = 'https://api.instagram.com/v1/tags/'.$tag.'/media/recent'
			.'?access_token='.$this->accessToken;

		if (!empty($max_id)) {
			$url .= '&max_id='.$max_id;
		}

		if (!empty($min_id)) {
			$url .= '&min_id='.$min_id;
		}

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($ch);

		$grams = json_decode($response);

		return $grams;
	}
}
