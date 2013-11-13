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

function writeLinks($tweet){
	
	//for http links
	$tweet = preg_replace('!http://([a-zA-Z0-9./-]+[a-zA-Z0-9/-])!i', ' <a href="\\0" target="_blank">\\0</a> ', $tweet);
	
	// for https links because my regex is lame.
	$tweet = preg_replace('!https://([a-zA-Z0-9./-]+[a-zA-Z0-9/-])!i', ' <a href="\\0" target="_blank">\\0</a> ', $tweet);
		
	//for hashtags
	$tweet = preg_replace("/#([a-z_0-9]+)/i", "<a href=\"http://twitter.com/search/$1\">$0</a>", $tweet);
	
	//for mentions
	$tweet = preg_replace("/\B[@]([a-zA-Z0-9_]{1,20})/i", ' <a target="_blank" class="twtr-atreply" href="http://twitter.com/$1">@$1</a> ',$tweet);
	

	// the order of the regexes is very imporant.
	return $tweet;
	
}


function fetchTweet() {
	
	require_once 'twitteroauth.php';
	$consumerKey 		= '0';
	$consumerSecret 	= '0';
	$accessToken 		= '0-0';
	$accessTokenSecret = '0';	
	
	$connection = new TwitterOAuth($consumerKey,$consumerSecret,$accessToken,$accessTokenSecret);
	$statuses = $connection->get('statuses/home_timeline', array('count' => 1, 'include_rts' => false, 'exclude_replies' => true ));
	
	foreach($statuses as $status):
	$twitter_result = $status->text;
	endforeach;

	$file	= 'wp-content/plugins/tcr_tweet/twitter_result.txt';
	$data = array ('twitter_result' => $twitter_result, 'timestamp' => time());
	file_put_contents($file, serialize($data));

	return $twitter_result;
}


function getTweet() {

			$file	= 'wp-content/plugins/tcr_tweet/twitter_result.txt';
			if (file_exists($file)) {
				$data = unserialize(file_get_contents($file));
				if ($data['timestamp'] < time() - (15 * 60)) {
						
						// there is a cached tweet
						$length = $data['twitter_result'];
						
						// but does file have content?
						if ($length.length > 0 ) { 
								// tweet exists
								$tweet_result = $length; 
						} else {
							
							// tweet is missing 
							$twitter_result=fetchTweet();
						}

				} else {
						// tweet has expired
						$twitter_result=fetchTweet();
				}
			} else {
				 	// file does not exist
					$twitter_result=fetchTweet();
			}

 print writeLinks($twitter_result);	
}
?>
