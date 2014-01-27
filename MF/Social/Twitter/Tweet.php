<?php
/**
 * Created by PhpStorm.
 * User: Maxime
 * Date: 20/01/14
 * Time: 11:30
 */

namespace MF\Social\Twitter;

use MF\Date;

class Tweet {
	function __construct($feedData) {
		$this->id = $feedData->id_str;

		$da = date_parse($feedData->created_at);
		$do = new Date();
		$do->setYear($da['year']);
		$do->setMonth($da['month']);
		$do->setDay($da['day']);
		$do->setHours($da['hour']);
		$do->setMinutes($da['minute']);
		$do->setSeconds($da['second']);

		$this->created_at = $do->toSQLDatetime();

		$this->content = $feedData->text;

		if (isset($feedData->entities->media[0]->media_url_https)) {
			$this->media = $feedData->entities->media[0]->media_url_https;
		}

		$this->source = $feedData->source;

		$this->url = 'https://twitter.com/' . $feedData->user->screen_name . '/status/' . $this->id;

		$this->author = new TwitterAuthor($feedData->user);

//		$this->rawData = $feedData;
	}
}
