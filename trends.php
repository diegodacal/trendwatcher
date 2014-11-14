<?php
	function showTrends($date,$source){
		include "db.php";
		include "core.php";
		$sql="SELECT source,title, url, description,date,relevancy FROM trends 
				WHERE source='$source' AND date='$date'
				ORDER BY relevancy DESC";
		set_time_limit(600);
		$result = mysqli_query($dbhandle,$sql); 
		while($row = mysqli_fetch_array($result)){
			$arrayDates = recurringTrend($row['title']);
			?> 
				<article>
					<h3>
						<a class="trendLink" href='#' title='<?php echo strip_tags($row['description']); ?>'><?php echo $row['title']; ?></a>
						
						<?php  
							if ($source=="youtube"){
								echo "<a class='ytlink fancybox.iframe external' href='";
								echo preg_replace('/\\&.*/', '', str_replace("https://www.youtube.com/watch?v=", "http://www.youtube.com/embed/" , $row['url']));
								echo "'><img src='img/play.svg' /></a>";
							} else {
						?>
							<a target="_blank" href="<?php echo $row['url']; ?>" class="external"><img src="img/link.svg" /></a>
						<?php
							}
						?>
					</h3>
					
					<em>
						<?php 
							if (dayBefore($date,$arrayDates)){ echo "<-"; } elseif (lastWeek($date, $arrayDates)) { echo "semana"; }						
							if (dayAfter($date,$arrayDates)){ echo "->"; }	
							echo " | " . $row['relevancy']; 
						?>
					</em>
				</article>
				
				
				
			<?php 
		}
		mysqli_close($dbhandle);
	}
	
	if( isset($_POST['date']) && isset($_POST['source'])){
		date_default_timezone_set('America/Sao_Paulo');
		$date = date("Y-m-d");
		showTrends($_POST['date'], $_POST['source']);
	}
?>