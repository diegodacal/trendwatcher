<?php
header('Content-type: text/html; charset=utf-8');	

function watchYoutube(){
	include "db.php";

	$response = file_get_contents("https://gdata.youtube.com/feeds/api/standardfeeds/BR/on_the_web?alt=json&time=today");
	$response = json_decode($response, TRUE);
	
	foreach ($response['feed']['entry'] as $trend){
		
		$source =  mysqli_real_escape_string($dbhandle, "youtube");
		$title =  mysqli_real_escape_string($dbhandle, $trend['title']['$t']);
		$url =  mysqli_real_escape_string($dbhandle, $trend['link'][0]['href']);
		$category = mysqli_real_escape_string($dbhandle, $trend['category'][1]['label']);
		date_default_timezone_set('America/Sao_Paulo');
		$date = date("Y-m-d");
		
		$sql="SELECT source,title,date FROM trends
				WHERE source='$source' AND title='$title' AND date='$date'";
		
		set_time_limit(600);
		
		$exist = mysqli_query($dbhandle,$sql);
		
		if (mysqli_num_rows($exist) > 0){
			echo "EXIST <br>";
			$sql="UPDATE trends SET relevancy = '$relevancy'
			WHERE source='$source' AND title='$title' AND date='$date'";
			rundb($sql);
		} else {
			echo "NON-ECZISTE <br>";
			$sql="INSERT INTO trends (source, title , date, url, category)
			VALUES ('$source', '$title' , '$date', '$url', '$category')";
			rundb($sql);
		}
	}
	
}

function rundb($sql){
	include "db.php";
	set_time_limit(600);
	if (!mysqli_query($dbhandle,$sql))
	  {
	  die('Error: ' . mysqli_error($dbhandle));
	  }
	mysqli_close($dbhandle);
}

watchYoutube();

?>