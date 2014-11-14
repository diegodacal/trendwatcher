<?php 
date_default_timezone_set('America/Sao_Paulo'); 
header('Content-type: text/html; charset=utf-8');	
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="ISO-8859-1"> 
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="javascript/d3.min.js"></script>
		<script src="javascript/d3.layout.cloud.js"></script>
		<script src="javascript/Chart.js"></script>
		<script src="javascript/js.js"></script>
		<link rel="stylesheet" href="javascript/fancybox/jquery.fancybox.css" type="text/css" media="screen" />
		<script type="text/javascript" src="javascript/fancybox/jquery.fancybox.pack.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Didact+Gothic' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="style.css">
		<title>trendwatcher</title>
	</head>
	<body>
		<header> 
			<section id="dates">
				<a href="">...</a>
				<a href="index.php?date=<?php echo date('Y-m-d',strtotime("-6 days")); ?>" data-date="<?php echo date('Y-m-d',strtotime("-6 days")); ?>"><?php echo date('d-M',strtotime("-6 days")); ?></a>
				<a href="index.php?date=<?php echo date('Y-m-d',strtotime("-5 days")); ?>" data-date="<?php echo date('Y-m-d',strtotime("-5 days")); ?>"><?php echo date('d-M',strtotime("-5 days")); ?></a>
				<a href="index.php?date=<?php echo date('Y-m-d',strtotime("-4 days")); ?>" data-date="<?php echo date('Y-m-d',strtotime("-4 days")); ?>"><?php echo date('d-M',strtotime("-4 days")); ?></a>
				<a href="index.php?date=<?php echo date('Y-m-d',strtotime("-3 days")); ?>" data-date="<?php echo date('Y-m-d',strtotime("-3 days")); ?>"><?php echo date('d-M',strtotime("-3 days")); ?></a>
				<a href="index.php?date=<?php echo date('Y-m-d',strtotime("-2 days")); ?>" data-date="<?php echo date('Y-m-d',strtotime("-2 days")); ?>"><?php echo date('d-M',strtotime("-2 days")); ?></a>
				<a href="index.php?date=<?php echo date('Y-m-d',strtotime("-1 days")); ?>" data-date="<?php echo date('Y-m-d',strtotime("-1 days")); ?>"><?php echo date('d-M',strtotime("-1 days")); ?></a>
				<strong><a href="index.php" data-date=" <?php echo date("Y-m-d"); ?>">today</a> </strong>
			</section>
			<section id="trendwatcher">
				<a href="http://www.diegodacal.com.br"><strong>trend</strong>watcher</a>
			</section>
		</header>
