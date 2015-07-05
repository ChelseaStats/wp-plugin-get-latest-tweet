<?php
/*
Plugin Name: TCR Tweeter
Description: Automatically tweets new post titles and links to a Twitter account by @author.
License: GPL
Version: 1.0.2
Plugin URI: http://thecellarroom.uk
Author: The Cellar Room Limited
Author URI: http://www.thecellarroom.uk
Copyright (c) 2013 The Cellar Room Limited
*/

include_once('twitteroauth.php');

defined( 'ABSPATH' ) or die();

/*************************************************************************/

if ( ! class_exists( 'tcr_class_tweeter' ) ) :

		class tcr_class_tweeter {

			function __construct() {

				/* Register Actions this is what triggers the post */
				add_action( 'new_to_publish'        , array ( $this, 'tcr_tweeter' ) , 10, 1 );
				add_action( 'draft_to_publish'      , array ( $this, 'tcr_tweeter' ) , 10, 1 );
				add_action( 'auto-draft_to_publish' , array ( $this, 'tcr_tweeter' ) , 10, 1 );
				add_action( 'pending_to_publish'    , array ( $this, 'tcr_tweeter' ) , 10, 1 );
				add_action( 'publish_future_post'   , array ( $this, 'tcr_future'  ) , 10, 1 );

			}

			function tcr_tweeter( $post_id ) {
				$post = get_post( $post_id );
				if ( $post->post_type == 'post' && $post->post_status == 'publish' ) {
					/* get the post that's being published */
					$post_title = $post->post_title;
					/* author needs a twitterid in their meta data*/
					$author = get_the_author_meta( 'twitterid', $post->post_author );
					/* get the permalink */
					$url = get_permalink( $post_id );
					/* and shorten it */
					$short_url = $this->goBitly( $url );
					//check to make sure the tweet is within the 140 char limit
					//if not, shorten and place ellipsis and leave room for link.
					if ( strlen( $post_title ) + strlen( $short_url ) > 100 ) {
						$total_len       = strlen( $post_title ) + strlen( $short_url );
						$over_flow_count = $total_len - 100;
						$post_title      = substr( $post_title, 0, strlen( $post_title ) - $over_flow_count - 3 );
						$post_title .= '...';
					}
					//add in the shortened bit.ly link
					$message = "New: " . $post_title . " - " . $short_url . " by @" . $author . " #hashtag";
					//call the tweet function to tweet out the message
					$this->goTweet( $message );
					//call the mail function to mail out the message
					$this->tcr_email( $message );

				}
			}


			function tcr_future( $post_id ) {
				$post = get_post( $post_id );
				/* get the post that's being published */
				$post_title = $post->post_title;
				/* author needs a twitterid in their meta data*/
				$author = get_the_author_meta( 'twitterid', $post->post_author );
				/* get the permalink */
				$url = get_permalink( $post_id );
				/* and shorten it */
				$short_url = $this->goBitly( $url );
				//check to make sure the tweet is within the 140 char limit
				//if not, shorten and place ellipsis and leave room for link.
				if ( strlen( $post_title ) + strlen( $short_url ) > 100 ) {
					$total_len       = strlen( $post_title ) + strlen( $short_url );
					$over_flow_count = $total_len - 100;
					$post_title      = substr( $post_title, 0, strlen( $post_title ) - $over_flow_count - 3 );
					$post_title .= '...';
				}
				//add in the shortened bit.ly link
				$message = "new: " . $post_title . " - " . $short_url . " by @" . $author . " #hashtag";
				//call the tweet function to tweet out the message
				$this->goTweet( $message );
				//call the mail function to mail out the message
				$this->tcr_email( $message );
			}

			/* do the tweet */
			function goTweet( $message ) {
				$connection = new TwitterOAuth(
					'xxx',
					'xxx',
					'xxx-xxx',
					'xxx' );
				$connection->get( 'account/verify_credentials' );
				$connection->post( 'statuses/update', array ( 'status' => $message ) );
			}


			/* do the email (a copy of what is tweeted)*/
			function tcr_email( $message ) {

				$email = 'wordpress@xxx.co.uk';
				$title = get_bloginfo( 'title' );
				wp_mail( $email, 'New: ' . $title, $message );
			}

			function goBitly( $url ) {

				/* YOUR BITLY TOKEN */
				$token = '';
				$encoded_url = urlencode( $url );
				$site_url    = ( "https://api-ssl.bitly.com/v3/shorten?format=txt&access_token=$token&longUrl=$encoded_url" );
				$ch = curl_init($site_url);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
				$bitly_return = curl_exec($ch);

				if ( $bitly_return !='' || $bitly_return !='ALREADY_A_BITLY_LINK') {
					$url = $bitly_return;
				}

				return $url;
			}

		}

		new tcr_class_tweeter;
endif;
