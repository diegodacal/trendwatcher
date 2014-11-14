<?php
header('Content-type: text/html; charset=utf-8');	

function watchGtrends(){
	include "db.php";
	
	$response = simplexml_load_file('http://www.google.com/trends/hottrends/atom/feed?pn=p18');
	
	
	
	foreach ($response->channel->item as $trend){
		$source =  mysqli_real_escape_string($dbhandle, "gtrends");
		$title =  mysqli_real_escape_string($dbhandle, $trend->title);
		$url =  mysqli_real_escape_string($dbhandle, $trend->link);
		$cleanRel = array ("+", ",");
		$relevancy = str_replace($cleanRel, "", $trend->children('ht', true)->approx_traffic);
		if ($trend->description != ""){
			$description = $trend->description;
		} else {
			$description =  mysqli_real_escape_string($dbhandle, $trend->children('ht', true)->news_item[0]->news_item_title);
		}
		$date = strtotime($trend->pubDate);
		$date = date("Y-m-d", $date);
		
		$sql="SELECT source,title,date FROM trends
				WHERE source='$source' AND title='$title' AND date='$date'";
		
		set_time_limit(600);
		
		$exist = mysqli_query($dbhandle,$sql);
		
		if (mysqli_num_rows($exist) > 0){
			echo "EXIST <br>";
			echo "EXIST <br>";
			$sql="UPDATE trends SET relevancy = '$relevancy'
			WHERE source='$source' AND title='$title' AND date='$date'";
			rundb($sql);
		} else {
			echo "NON-ECZISTE <br>";
			$sql="INSERT INTO trends (source, title , description, date, url, relevancy)
			VALUES ('$source', '$title' , '$description', '$date', '$url', '$relevancy')";
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

watchGtrends();
?>