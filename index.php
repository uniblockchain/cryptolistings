<?php

// Twitter API
require_once('TwitterAPIExchange.php');

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
include("twitter_config.php");	// Gitignore - Privat
$settings = array(
    'oauth_access_token' => $oauth_access_token,
    'oauth_access_token_secret' => $oauth_access_token_secret,
    'consumer_key' => $consumer_key,
    'consumer_secret' => $consumer_secret
);

	// Klasse laden und instanzieren
	include 'db.php';
	$dbModel = new dbModel();

	// Get all Tweets from DB
	$all_saved_tweets = $dbModel->get_saved_tweets();

?>


<!doctype html>
<html class="no-js" lang="EN">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>cryptolistings.info</title>


        <link rel="stylesheet" href="style/normalize.css">
        <link rel="stylesheet" href="style/style.css">


		<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
		<script sync src="https://platform.twitter.com/widgets.js"></script>

		<script src="js/script.js"></script>

		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-109135597-1"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', 'UA-109135597-1');
		</script>



	</head>
<body>
	<header>
		<a href="about.html">About</a>
	</header>

	<div class="content" id="content">
			<p>
				Tweets concerning Altcoin Listings from the biggest exchanges.
			</p>
			<?php
				// Echo all Tweets from DB
				foreach($all_saved_tweets as $tweet){
					echo '<div class="tweet" id="'.$tweet['tweet_id'].'"></div>';
				}
			?>
			<img id="loader" src="img/loader.gif">
	</div>
</body>
</html>