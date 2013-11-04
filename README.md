#### wp-plugin-get-latest-tweet

wp plugin to get the latest tweet and show it with a simple function call.

prerequisites:

1. you have set up an app on dev.twitter.com to get your API keys and secrets
2. your host allows `file_put_contents` and `file_get_contents` (for caching the tweet).
3. <del>You have a copy of <a href="http://github.com/kutf/twitter-oauth">Abraham's twitteroauth and oauth files</a> in a folder with this file installed in `wp-plugins`</del>
4. <del>I muight package it up for you at some point</del>


to use:

    <?php if (function_exists(getTweet)) { 
    getTweet(); 
    }
    else { 
    print 'Did you know you can follow Hediip on twitter @youraccount'; 
    } 
    ?>

probably best to wrap it in a `<span>` or a `<div>` so it can then be styled accordingly.
