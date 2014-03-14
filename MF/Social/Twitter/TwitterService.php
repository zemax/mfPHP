<?php
/**
 * Created by PhpStorm.
 * User: Maxime
 * Date: 17/01/14
 * Time: 14:31
 */

namespace MF\Social\Twitter;

include_once 'twitteroauth\twitteroauth.php';

class TwitterService {
	public function __construct($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret) {
		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;
		$this->accessToken = $accessToken;
		$this->accessTokenSecret = $accessTokenSecret;

		$this->connection = new \TwitterOAuth(
			$this->consumerKey,
			$this->consumerSecret,
			$this->accessToken,
			$this->accessTokenSecret
		);
	}

	/****************************************************************************************************
	 * ACTIONS
	 ****************************************************************************************************/

	public function getUserTweets($user, $count = 100, $include_rts = false, $ignore_replies = true) {
		$tweets = $this->connection->get(
			'https://api.twitter.com/1.1/statuses/user_timeline.json'
						.'?screen_name='.$user
						.'&count='.$count
						.'&include_rts='.$include_rts
						.'&exclude_replies='.$ignore_replies
		);

		$return = new \stdClass();
		$return->metadata = array();

		$return->data = array();
		foreach ($tweets as $data) {
			$return->data[] = new Tweet($data);
		}
		return $return;
	}

	public function searchTweets($q, $count = 20) {
		$tweets = $this->connection->get(
			'https://api.twitter.com/1.1/search/tweets.json'
						.'?q='.urlencode($q)
						.'&count='.$count
		);

		$return = new \stdClass();
		$return->metadata = $tweets->search_metadata;

		$return->data = array();
		foreach ($tweets->statuses as $data) {
			$return->data[] = new Tweet($data);
		}
		return $return;
	}
}
