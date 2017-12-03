<?php

class dbModel {

    public function __construct() {
    	// DB Config
    	include("db_config.php");	// Gitignore - Privat

		$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
		$opt = [
		    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		    PDO::ATTR_EMULATE_PREPARES   => false,
		];
		$pdo = new PDO($dsn, $user, $pass, $opt);

        $this->db = $pdo;
    }

	// Insert Tweet
	public function save_tweet($tweet_id, $created_at, $created_at2, $message) {
		$sql = "INSERT INTO tweets (tweet_id, created_at, created_at2, message) VALUES (?, ?, ?, ?)";
		$this->db->prepare($sql)->execute([$tweet_id, $created_at, $created_at2, $message]);
		echo 'save_tweet 1';
	}

	// Get Last Tweet
	public function get_last_id() {
		$stmt = $this->db->prepare('SELECT * FROM tweets ORDER BY id DESC LIMIT 1');
		$stmt->execute();
		$last_tweet_id = $stmt->fetch();
		return $last_tweet_id[tweet_id];
	}

	// Get Last 100 Tweets
	public function get_last_100() {
		$stmt = $this->db->prepare('SELECT message FROM tweets ORDER BY id DESC LIMIT 100');
		$stmt->execute();
		$last_100_tweets = $stmt->fetchAll();
		return $last_100_tweets;
	}

	// Get all Tweets (Max 35)
	// Quick Fix: Group By um doppelte (leere) nicht zweimal aufzlisten. Manche Tweets mehrfach in DB da Tweet leer weil manche Emojis nicht abgespeichert werden können
	public function get_saved_tweets() {
		$stmt = $this->db->prepare('SELECT tweet_id FROM tweets WHERE deleted = 0 GROUP BY(tweet_id) ORDER BY created_at2 DESC LIMIT 35');
		$stmt->execute();
		$all_saved_tweets = $stmt->fetchAll();
		return $all_saved_tweets;
	}

	// Delete Tweet
	public function delete_tweet($tweet_id) {
		// Logged User Only
		if($_SESSION['login'] !== 1) {
			$sql = "UPDATE tweets set deleted = 1 WHERE tweet_id = :tweet_id";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':tweet_id', $tweet_id, PDO::PARAM_INT);   
			$stmt->execute();
			echo 'done';
		}
	}

	// Send News Alert Email
	public function send_notification($tweet_text) {

		$empfaenger = "info@cryptolistings.info";
		$betreff = "Cryptolistings Alert";
		$from = "From: Cryptolistings <info@cryptolistings.info>";
		$text = $tweet_text."\n Mehr auf www.cryptolistings.info";
		 
		mail($empfaenger, $betreff, $text, $from);

	}



}


// Ajax Delete Anfrage
if(isset($_POST['funktion']) && $_POST['funktion'] == "deleteTweet") {
	$dbModel = new dbModel($pdo);
	$dbModel->delete_tweet($_POST['tweet_id']);
}





?>