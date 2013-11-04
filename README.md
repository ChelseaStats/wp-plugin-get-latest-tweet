#### wp-plugin-get-latest-tweet

wp plugin to get the latest tweet and show it with a simple function call.

prerequisites:

1) you have set up an app on dev.twitter.com to get your API keys and secrets
2) your host allows `file_put_contents` and `file_get_contents` (for caching the tweet).


to use:

    <?php if (function_exists(getTweet)) { 
    getTweet(); 
    }
    else { 
    print 'Did you know you can follow Hediip on twitter @youraccount'; 
    } 
    ?>
