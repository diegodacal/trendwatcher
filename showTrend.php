<?php 
include "header.php"; 
?>

<?php
	include "core.php";
	if( isset($_POST['trend']) ){
		include "db.php";
		$trendArray = splitTrend(removeCommonWords(mysqli_real_escape_string($dbhandle, $_POST['trend'])));
		$sql="SELECT * FROM trends 
				WHERE ";
		$flag = false;
		foreach ($trendArray as $trend){
			if (!$flag){
				$sql.= "title LIKE '%" . $trend . "%' ";
				$flag = true;
			} else {
				$sql.= "OR title LIKE '%" . $trend . "%' ";
			}
		}
		$sql .= "
				ORDER BY date ASC"; 	
		//echo $sql;
		
		$result = rundb($sql,true);
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
		} else {
			echo "No trends available. :(";
		}
	} else {
		echo "algo deu erado. :/";
	}
	
	/* Create string from Twitter Content */
	$sql="SELECT * FROM content 
			WHERE trend = '$trend'";
	$result = rundb($sql,true);
	if ($result->num_rows > 0){
		$twitterContent = "";
		while($row = $result->fetch_array(MYSQL_ASSOC)){
				/*clean row goes here*/
				$twitterContent .= mysqli_real_escape_string($dbhandle, $row['text']) . " ";
		}
		/* html portion para criar wordcloud */
		?>
		<script>
			$(document).ready(function(){
				generatewordcloud();
			});
		
		</script>
		<?php
	} else {
		echo "<script>texttoelement('not enough data for a wordcloud :(','#wordcloud')</script>";
		/*javascript para inserir esse text no box*/
	}
?>
<h2><?php  echo $_POST['trend']; ?></h2>
<section id="timeline">
	<canvas id="timelinechart" width="auto" height="250"></canvas>
</section>

<section id="wordcloud">
	<h3>Wordcloud</h3>
</section>
<section id="speed">
	<h3>Speed</h3>
</section>
<section id="similar">
	<h3>Similar Trends</h3>
</section>

<form id="viewTrend" action="showTrend.php" method="POST">
	<input id="trendInput" type="hidden" name="trend" value="" />
</form>



<script>

$(document).ready(function(){
	
	var similar = new Array();
	var timeline = new Array();
	var wordcloud = new Array();
	var sources = new Array();
	var theTrend = "<?php echo $_POST['trend']; ?>" ;
	json = JSON.parse('<?php  echo $json; ?>');
	$.each(json.entries, function(i,entry){
		/*create similar trends array*/
		if ($.inArray(entry.title, similar) < 0){
			similar.push(entry.title);
		}
		/*create sources array*/
		sourceFound = false;
		$.each(sources, function(){
			if (this.source === entry.source){
				sourceFound = true;
				this.count = this.count+1;
				//alert(JSON.stringify(sources));
				return false;
			}
		});
		if (sourceFound == false){
			sources.push({source : entry.source, count:1});
			//alert(JSON.stringify(sources));
		}
		/*create timeline array*/
		dateFound = false;
		$.each(timeline, function(){
			if (this.date === entry.date){
				dateFound = true;
				if (entry.title == theTrend){
					this.trend = this.trend+1;
				} else {
					this.similar = this.similar+1;
				}
				//alert(JSON.stringify(timeline));
				return false;
			}
		});
		if (dateFound == false){
			if (entry.title == theTrend){
				timeline.push({date : entry.date, trend:1, similar:0});
			} else {
				timeline.push({date : entry.date, trend:0, similar:1});
			}
			//alert(JSON.stringify(timeline));
		}
		//similar.push({"":entry.title,""});
	});

	/* plot similar trends on screen */
	$.each(similar,function(i,simila){
		$("#similar").append("<a class='trendLink' href='#'>"+simila+"</a>");
		$("#similar").append("<br/>");
	});
	
	/*plot timeline on chart*/

var d = [];
var t = [];
var s = [];

if (timeline.length > 1) {
    // Assuming dates are 'yyyy-mm-dd'. Sort by date in ascending order.
    timeline.sort(function (s1, s2) {
        return s2.date < s1.date;
    });
    // Adds a new point in the three arrays.
    var addSerie = function (date, trend, similar) {
        // NOTE: Maybe the way I'm using to get the dd/mm/yyyy is a little bit obscure,
        // you can use your way here if you see it more clear.
        d.push(date.toISOString().slice(0, 10).split('-').reverse().join('-'));
        t.push(trend);
        s.push(similar);
    }
    // Insert the first serie
    addSerie(new Date(timeline[0].date), timeline[0].trend, timeline[0].similar);
    // Then the rest
    for (var i = 1; i < timeline.length; i++) {
        var d1 = new Date(timeline[i - 1].date);
        d1.setDate(d1.getDate() + 1);
        var d2 = new Date(timeline[i].date);
        // Generate the empty gap dates starting on the next date
        while (d1 < d2) {
            addSerie(d1, 0, 0);
            d1 = new Date(d1);
            d1.setDate(d1.getDate() + 1);
        }
        addSerie(d2, timeline[i].trend, timeline[i].similar);
    }
}
var data = {
	labels : d,
	datasets : [
		{
			fillColor : "#f38630",
			strokeColor : "#FA6900",
			pointColor : "#FA6900",
			pointStrokeColor : "#FA6900",
			data : t
		},
		{
			fillColor : "rgba(12,194,132,0.4)",
			strokeColor : "#69d2e7",
			pointColor : "#69d2e7",
			pointStrokeColor : "#69d2e7",
			data : s
		},
	]
}
var ctx = document.getElementById('timelinechart').getContext('2d');
new Chart(ctx).Line(data);

});

</script>

<?php include "footer.php"; ?>