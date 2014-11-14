<?php
date_default_timezone_set('America/Sao_Paulo'); 
header('Content-type: text/html; charset=utf-8');	

/* Run DB */
function rundb($sql,$return){
	include "db.php";
	$dbhandle->set_charset("utf8");
	set_time_limit(600);
	$result = mysqli_query($dbhandle,$sql);
	if (!$result){
	  die('Error: ' . mysqli_error($dbhandle));
	}
	if ($return){
		return $result;
	} else {
		mysqli_close($dbhandle);
	}
}
function splitTrend($trend){
	header('Content-type: text/html; charset=utf-8');	
	return explode(" ", $trend);
}
function clean($string) {
	$unwanted_chars = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
	$string = strtr( $string, $unwanted_chars );
	return preg_replace('/[^ a-zA-Z0-9\']/', '', strtolower($string));
}
function removeCommonWords($input){
	header('Content-type: text/html; charset=utf-8');	
	$commonWords = array('a', 'e', 'o', 'da', 'das', 'do', 'dos', 'de', 'na', 'no', 'em', 'la', 'el', 'lo', 'x', 'on', 'por', 'que', 'te', 'me', 'mas');
	$input = clean($input);
	return preg_replace('!\s+!', ' ', preg_replace('/\b('.implode('|',$commonWords).')\b/','',$input));
}
function dayBefore($date,$arrayDates){
	if (in_array(date('Y-m-d', strtotime("-1 days", strtotime($date))),$arrayDates)){
			return true;
	}
}
function dayAfter($date,$arrayDates){
	if (in_array(date('Y-m-d', strtotime("+1 days", strtotime($date))),$arrayDates)){
		return true;
	} 
}
function lastWeek($date,$arrayDates){
	if (in_array(date('Y-m-d', strtotime("-2 days", strtotime($date))), $arrayDates) || 
			in_array(date('Y-m-d', strtotime("-3 days", strtotime($date))), $arrayDates) || 
			in_array(date('Y-m-d', strtotime("-4 days", strtotime($date))), $arrayDates) ||
			in_array(date('Y-m-d', strtotime("-5 days", strtotime($date))), $arrayDates) || 
			in_array(date('Y-m-d', strtotime("-6 days", strtotime($date))), $arrayDates) || 
			in_array(date('Y-m-d', strtotime("-7 days", strtotime($date))), $arrayDates)) {
		return true;
	}
}
function recurringTrend($trend){
	include "db.php";
	$trend = mysqli_real_escape_string($dbhandle, $trend);
	$sql="SELECT date FROM trends 
			WHERE title='$trend'";
	
	$output = array();
	
	set_time_limit(600);
	$result = mysqli_query($dbhandle,$sql);
	while($row = mysqli_fetch_array($result)){
		$output[] = $row[0];
	}
	return $output;
}


?>