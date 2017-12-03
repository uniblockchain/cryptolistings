<?php
// Filters Exchanges Timelines and filters Tweets for Keywords
// Saves relevant Tweets in DB
// Diese Seite kann nur vom Admin aufgerufen werden

session_start();

// Handle Post Requests
if($_POST['user'] = $loginUser && $_POST['pass'] == $loginPass) {
	$_SESSION['login'] = 1;
}

// Access
if($_SESSION['login'] == 1 || $_GET['update'] == 1) {
	// Ok
	// Admin oder Cron Job
} else {
	// Access Denied
	echo 'Access Denied';
	exit;
}


 

// Klasse laden und instanzieren
include 'db.php';
$dbModel = new dbModel();

ini_set('display_errors', 1);


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


// Exchanges
$exchanges = array("quoine_sg", "bitfinex", "coinone_info", "bithumbexchange", "hitbtc", "bittrexexchange", "gdax", "btercom", "krakenfx", "bitstamp", "bitflyer", "binance_2017", "korbitbtc", "wexnz", "btc38com", "cex_io", "liqui_exchange", "geminidotcom", "okcoin", "yobitexchange", "lakebtc", "exmo_com", "allcoinex", "tidex_exchange", "gatecoin", "cryptopia_nz", "livecoin_net", "poloniex", "waltonchain", "liskhq", "zcashco", "augurproject", "_pivx", "tenxwallet", "komodoplatform", "golemproject", "vertcoin", "saltlending");

// Last 100 Tweets in DB
$last_100_saved_tweets = $dbModel->get_last_100();

foreach($last_100_saved_tweets as $tweet){
	$last_100_saved_tweets_string = $last_100_saved_tweets_string." ".$tweet['message'];
}

	// Loop über alle Exchanges und für jede Exchange Tweets abrufen
	foreach ($exchanges as $exchange) {
	    echo $exchange."<br>";

							/** Perform a GET request and echo the response **/
							/** Note: Set the GET field BEFORE calling buildOauth(); **/
							// https://developer.twitter.com/en/docs/tweets/timelines/api-reference/get-statuses-user_timeline.html
							$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
								// Parameter
								$screen_name = $exchange;
								$count = "6";	// Anzahl Tweets
								$include_rts = "1";
								$trim_user = "0";
								$exclude_replies = "1";
								
							$getfield = '?screen_name='.$screen_name.'&include_rts='.$include_rts.'&trim_user='.$trim_user.'&exclude_replies='.$exclude_replies.'&count='.$count;
							$requestMethod = 'GET';
							// Get Twitter Data of Exchange -> Request
							$twitter = new TwitterAPIExchange($settings);
							$timelineData = $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();

							// Decode JSON
							$timelineData = json_decode($timelineData);

							// Loop Trough Data of this Exchange
							foreach($timelineData as $tweet){

								// Existiert dieser Tweet bereits in DB?
								// Suche in String mit allen Tweets aus DB
								echo '<br>Existiert "'.$tweet->text.'" in DB ($last_100_saved_tweets_string)?';
								if(strpos($last_100_saved_tweets_string, $tweet->text) !== false ) {
									// Tweet bereits in DB
									echo '->Ja';
								} else {
									// Tweet ist nicht in DB
									echo '->Nein';

									// Gehört Tweet in DB? Check Tweet for Keywords
									if(stripos($tweet->text, 'list') !== false || stripos($tweet->text, 'trade') !== false || stripos($tweet->text, 'support') !== false || stripos($tweet->text, 'now live') !== false || stripos($tweet->text, 'announce') !== false || stripos($tweet->text, 'available') !== false || stripos($tweet->text, 'add') !== false && stripos($tweet->text, 'maintenance') !== false) { 

											echo ' --> Relevanter Tweet. Save in DB<br>';

											// Format Create Date
											$created_at2 = substr($tweet->created_at, 4);
											$year = substr($created_at2, -4);
											$created_at2 = substr($created_at2, 0, -20);
											$created_at2 = $created_at2." ".$year;
											$created_at2 = date("Y-m-d", strtotime($created_at2));

											// Save into DB
											$dbModel->save_tweet($tweet->id, $tweet->created_at, $created_at2, $tweet->text);

											// Send eMail
											// send_notification($tweet->text);


									} else {
										echo ' --> Nicht relevanter Tweet.<br>';
									}

								}
							
							}




	echo "<br>-------------------------------------------------------------------------------------------------------------<br>";

	// Slow Down Requests -> Wie oft darf API angefragt werden?
	usleep(100000); // 0.1 Sek Sleep

	}

	// Get all Tweets from DB -> Tweets anzeigen zur kontrolle
	$all_saved_tweets = $dbModel->get_saved_tweets();
	echo '<br>All Saved Tweets: ';
	print_r($all_saved_tweets);
	echo '<br>';

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

	</head>
<body>
	<div class="content">

			<h1>Tweets</h1>
			<?php
				// Echo all Tweets from DB
				foreach($all_saved_tweets as $tweet){
					echo '<div class="tweet" id="'.$tweet['tweet_id'].'">';
						if($_SESSION['login'] == 1) {
							echo '<div class="delete" id="delete_'.$tweet['tweet_id'].'">X</div>';
						}
					echo '</div>';
				}
			?>
			<p>Ende</p>

	</div>
</body>
</html>