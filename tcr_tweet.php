<?php
/*
Plugin Name: TCR Tweeter
Description: get tweet and put it into the template
License: GPL
Version: 1.0.1
Plugin URI: http://thecellarroom.net
Author: The Cellar Room Limited
Author URI: http://www.thecellarroom.net
Copyright (c) 2015 The Cellar Room Limited
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
	$accessTokenSecret  = '0';	
	
	$connection = new TwitterOAuth($consumerKey,$consumerSecret,$accessToken,$accessTokenSecret);
	$statuses = $connection->get('statuses/home_timeline', array('count' => 1, 'include_rts' => false, 'exclude_replies' => true) );
	
	foreach($statuses as $status):
	$tweet = $status->text;
	endforeach;

	$file	= 'wp-content/plugins/tcr_tweet/tweet.txt';
	$data = array ('tweet' => $tweet, 'timestamp' => time());
	file_put_contents($file, serialize($data));

	return $tweet;
}


function getTimecomp () {
	
 	return intval(time() - (15 * 60));
}


function getTweet() {

			$file	= 'wp-content/plugins/tcr_tweet/tweet.txt';
			if (file_exists($file)) {
				
				$data = unserialize(file_get_contents($file));

					if ( ($data['timestamp'] - getTimecomp()) > 0 ) {
						
							// there is a cached tweet
							$tweet = '<span class="cached">'. $data['tweet'].'</span>';
	
					} else {
							// tweet has expired or is missing
							$tweet = '<span class="expired">'.fetchTweet().'</span>';
					}
					
			}
			 else {
				 	// file does not exist
					$tweet = '<span class="new">'.fetchTweet().'</span>';
			}

 print writeLinks($tweet);	
}
?>
