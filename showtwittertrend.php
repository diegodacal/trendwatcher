<?php include "header.php"; ?>

<?php

include "core.php";
include "db.php";

if( isset($_GET['t']) ){
	/*print trend page title*/
	echo "<h2>".$_GET['t']."</h2>";
	
	/*select twitter trend from db*/
	include "db.php";
	$trend = mysqli_real_escape_string($dbhandle, $_GET['t']);
	$sql="SELECT * FROM trends 
			WHERE title='$trend' AND source='twitter'
			ORDER BY date ASC";
	//echo $sql;
	$result = rundb($sql,true);
	
	/*Build JSON with twitter trend*/
	if ($result->num_rows > 0){
		while($row = $result->fetch_array(MYSQL_ASSOC)){
			$json[] = array(	"id"=>$row['id'], 
								"source"=>$row['source'], 
								"title"=>mysqli_real_escape_string($dbhandle, $row['title']), 
								"description"=>mysqli_real_escape_string($dbhandle, $row['description']), 
								"date"=>$row['date'], 
								"url"=>$row['url'], 
								"category"=>$row['category'], 
								"tags"=>$row['tags'], 
								"promoted"=>$row['promoted'], 
								"relevancy"=>$row['relevancy']);
		}
		$json = '{"entries":'. json_encode($json).'}';
		$json = mysqli_real_escape_string($dbhandle, $json);
		echo "<section id='timeline'><canvas id='timelinechart' width='auto' height='300'></canvas></section>";
	} else {
		echo "<section id='timeline'><h3>timeline</h3>No trends available. :(</section>";
	}
	
	/*Build string for Wordcloud*/
	$sql="SELECT * FROM content 
			WHERE trend='$trend'
			ORDER BY date
			LIMIT 1000";
			//oportunidade para pegar o conteudo de apenas um dia
	$result = rundb($sql,true);
	if ($result->num_rows > 0){
		$twitterContent = "";
		while($row = $result->fetch_array(MYSQL_ASSOC)){
				$twitterContent .= mysqli_real_escape_string($dbhandle, $row['text']) . " ";
		}
	} else {
		echo "<section id='timeline'><h3>wordcloud</h3>No data for a wordcloud. :(</section>";
	}
}
?>



<?php include "footer.php"; ?>