<?php
header('Content-type: text/html; charset=utf-8');	

function searchTrend($trend){
	require_once('lib/TwitterAPIExchange.php');
	include "config.php";
	include "db.php";

	$url = 'https://api.twitter.com/1.1/search/tweets.json';
	$getfield = '?q=' . $trend . '&result_type=recent&count=100';
	echo $getfield;
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($settings);

	$response = $twitter->setGetfield($getfield)
				 ->buildOauth($url, $requestMethod)
				 ->performRequest();  
	return json_decode($response);
}

function saveString($trend, $date, $content){
	
}



	foreach ($searchResults->statuses) as $tweet){
		echo $tweet->user->name . ", ";
		$twitterID = $tweet->user->id_str;
		$screenName = $tweet->user->screen_name;
		$created_at = str_replace("+0000 ", "+0200 ", $tweet->created_at);
		$created_at = date( 'Y-m-d H:i:s', strtotime($created_at));
		$tweetText = $tweet->text;
		$tweetURL = "https://twitter.com/" . $screenName . "/status/" . $tweet->id_str;
		saveLastTweet($tweet->id_str);
		
		saveTweet($twitterID, $screenName, $created_at, $tweetText, $tweetURL);
		addtoProcess($twitterID, $screenName);	
	}



?>