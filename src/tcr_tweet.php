<?php
/*
Plugin Name: TCR Tweeter
Description: get tweet and put it into the template
License: GPL
Version: 1.0.1
Plugin URI: http://thecellarroom.net
Author: The Cellar Room Limited
Author URI: http://www.thecellarroom.net
Copyright (c) 2013 The Cellar Room Limited
*/
include_once('twitteroauth.php');

function writeLinks($tweet){
	
	//for links
	$tweet = preg_replace('!http://([a-zA-Z0-9./-]+[a-zA-Z0-9/-])!i', '<a href="\\0" target="_blank">\\0</a>', $tweet);
	
	//for mentions
	$tweet = preg_replace("/\B[@]([a-zA-Z0-9_]{1,20})/i", '<a target="_blank" class="twtr-atreply" href="http://twitter.com/$1">@$1</a>',$tweet);
	
	//for hashtags
	$tweet = preg_replace("/(^|\s+)#(\w+)/i", '<a target="_blank" class="twtr-hashtag" href="http://twitter.com/search\?q=%23$1>#$1</a>', $tweet);
		
	// the order of the regexes is very imporant.
	return $tweet;
	
}


function getTweet() {

 $consumerKey 		= 'x';
 $consumerSecret 	= 'x';
 $accessToken 		= 'x';
 $accessTokenSecret = 'x';

require_once 'twitteroauth.php';

$connection = new TwitterOAuth($consumerKey,$consumerSecret,$accessToken,$accessTokenSecret);
$statuses = $connection->get('statuses/home_timeline', array('count' => 1));

foreach($statuses as $status): $tweet = $status->text; endforeach;

			if (file_exists('twitter_result.txt')) {
				$data = unserialize(file_get_contents('twitter_result.txt'));
				if ($data['timestamp'] > time() - 15 * 60) {
					$twitter_result = $data['twitter_result'];
				}
			}
			
			if (!$twitter_result) { // cache doesn't exist or is older than 10 mins
				$twitter_result = $tweet;
			
				$data = array ('twitter_result' => $twitter_result, 'timestamp' => time());
				file_put_contents('twitter_result.txt', serialize($data));
			}
			

 print writeLinks($twitter_result);	
}
?>
