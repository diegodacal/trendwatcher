<?php 
	include "header.php";
	date_default_timezone_set('America/Sao_Paulo'); 
	if (isset($_GET["date"])){
		$workdate = $_GET["date"];
	} else {
		$workdate = date("Y-m-d");
	}
?>


		<section id="stats">
			Showing trends for <?php echo $workdate;  ?>
		</section>
		<section class="source" id="twitter">
			<h2>Twitter</h2>
			<div id="data-twitter">
			
			</div>			
		</section>
		<section class="source" id="twitter">
			<h2>Google Trends</h2>
			<div id="data-gtrends">
			
			</div>	
		</section>
		<section class="source" id="youtube">
			<h2>Youtube</h2>
			<div id="data-youtube">
			
			</div>	
		</section>

		<script>
			function loadDay(data, fonte){
				getIt = $.post('trends.php', { date: data, source: fonte });
				getIt.done(function( data ) {
					$("#data-" + fonte).empty().append(data);
				});
			}
			$(document).ajaxStop( function () {
				$(".trendLink").click(function(){
					$("#trendInput").attr("value", $(this).text());
					$("#viewTrend").submit();
				});
			});
			$(document).ready(function(){
				loadDay("<?php echo $workdate  ?>","twitter");
				loadDay("<?php echo $workdate  ?>","gtrends");
				loadDay("<?php echo $workdate  ?>","youtube");
				$(".ytlink").fancybox({
					maxWidth	: 800,
					maxHeight	: 600,
					fitToView	: false,
					width		: '70%',
					height		: '70%',
					autoSize	: false,
					closeClick	: false,
					openEffect	: 'none',
					closeEffect	: 'none'
				});
			});
		</script>
<?php include "footer.php"; ?>