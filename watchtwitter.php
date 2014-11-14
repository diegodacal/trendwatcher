<?php
header('Content-type: text/html; charset=utf-8');	
include "core.php";

function searchTrend($trend){
	require_once('lib/TwitterAPIExchange.php');
	include "config.php";

	$url = 'https://api.twitter.com/1.1/search/tweets.json';
	$getfield = '?q=' . $trend . '&result_type=recent&count=100';
	$lastTweet = returnLastTweet($trend);
	if ($lastTweet != ""){
		$getfield .= "&since_id=" . $lastTweet;
	}
	echo $getfield;
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($settings);
	
	$response = $twitter->setGetfield($getfield)
				 ->buildOauth($url, $requestMethod)
				 ->performRequest();  
	return json_decode($response);
}

function saveLastTweet($trend, $lastTweet){
	include "db.php";
	$sql="UPDATE trends 
			SET lasttweet='$lastTweet' WHERE title='$trend'";
	rundb($sql,false);
}
function returnLastTweet($trend){
	include "db.php";
	date_default_timezone_set('America/Sao_Paulo');
	$date = date("Y-m-d");
	$sql = "SELECT lasttweet FROM trends
			WHERE title='$trend' AND date='$date'";
	echo $date;
	$dbNodes = mysqli_query($dbhandle,$sql);
	while($row = mysqli_fetch_array($dbNodes)){
		return $row['lasttweet'];
	}
}
function watchTwitter(){
	require_once('lib/TwitterAPIExchange.php');
	include "config.php";
	include "db.php";

	$url = 'https://api.twitter.com/1.1/trends/place.json';
	$getfield = '?id=23424768';
	echo $getfield;
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($settings);

	$response = $twitter->setGetfield($getfield)
				 ->buildOauth($url, $requestMethod)
				 ->performRequest();  
	$output = json_decode($response);
	$output = $output[0];
	date_default_timezone_set('America/Sao_Paulo');
	$date = date("Y-m-d");
	foreach ($output->trends as $trend){
		$title = $trend->name;
		$url = mysqli_real_escape_string($dbhandle, $trend->url);
		$source =  mysqli_real_escape_string($dbhandle, "twitter");
		if ($trend->promoted_content) { $promoted = 1; } else { $promoted = 0;}
		
		$sql="SELECT source,title,promoted,date FROM trends
				WHERE source='$source' AND title='$title' AND promoted='$promoted' AND date='$date'";
		
		set_time_limit(600);
		
		$exist = rundb($sql,true);
		
		if (mysqli_num_rows($exist) > 0){
			echo "EXIST <br>";
			$sql="UPDATE trends SET relevancy = relevancy + 1
			WHERE source='$source' AND title='$title' AND date='$date' AND promoted = '$promoted'";
			rundb($sql,false);
		} else {
			echo "NON-ECZISTE <br>";
			$sql="INSERT INTO trends (source, title , date, url, promoted)
			VALUES ('$source', '$title' , '$date', '$url', '$promoted')";
			rundb($sql,false);
		}
		$twitterfeed = searchTrend($title);
		if (!empty($twitterfeed)){
			$flagfirst = true;
			$previous = "";
			$speed = 0;
			$counter = 0;
			foreach ($twitterfeed->statuses as $tweet){
				$created_at = str_replace("+0000 ", "+0200 ", $tweet->created_at);
				$created_at = date( 'Y-m-d H:i:s', strtotime($created_at));
				$source = mysqli_real_escape_string($dbhandle, "twitter");
				$text = mysqli_real_escape_string($dbhandle, $tweet->text);
				
				if ($flagfirst){
					$previous = new DateTime($created_at);
					$flagfirst = false;
					$counter++;
				} else {
					$now = new DateTime($created_at);
					$difference = date_diff($now,$previous);
					echo "diff: " . $difference->s . "*<br>";
					if ($difference->s == 0){
						$counter++;
					} else {
						//echo "diff::: " . $difference->s . ";) <br>";
						 if ($speed == 0) {
							$speed = $counter/$difference->s; 
						} else { 
							$speed = ($speed + ($counter/$difference->s))/2;
							$previous = $now;
						}
						$counter = 0;
					}
				}
				savelasttweet($title,$tweet->id_str);
				
				$sql = "INSERT INTO content (source, date, trend, text)
				VALUES('$source','$created_at','$title','$text')";
				rundb($sql,false);
			}
			echo "trend speed: " . number_format($speed, 2) . "!";
		}
	}
	mysqli_close($dbhandle);
}

watchTwitter();
?>