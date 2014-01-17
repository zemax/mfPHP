<?php
/**
 * Created by PhpStorm.
 * User: Maxime
 * Date: 17/01/14
 * Time: 14:31
 */

namespace MF\Social;

include_once 'twitteroauth\twitteroauth.php';

class Twitter {
	public function __construct($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret) {
		$this->consumerKey 			= $consumerKey;
		$this->consumerSecret 		= $consumerSecret;
		$this->accessToken 			= $accessToken;
		$this->accessTokenSecret 	= $accessTokenSecret;
	}

/****************************************************************************************************
 * ACTIONS
 ****************************************************************************************************/

	public function getUserTweets($user, $count = 20, $include_rts = false, $ignore_replies = true) {
		$connection = new \TwitterOAuth(
			$this->consumerKey,
			$this->consumerSecret,
			$this->accessToken,
			$this->accessTokenSecret
		);

		$tweets = $connection->get(
			"https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=" . $user . "&count=" . $count . "&include_rts=" . $include_rts . "&exclude_replies=" . $ignore_replies
		);

		return $tweets;
	}
}
