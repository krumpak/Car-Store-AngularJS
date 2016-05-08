<?php
$stmt = $dbh->prepare("SELECT * FROM `carshop_timeslots`");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if( count($data) > 0 ){
	$outputCorr = json_encode($data, JSON_NUMERIC_CHECK);
	
	$replace = array (
		'"reserved":0' => '"reserved":false',
		'"reserved":​1' => '"reserved":true'
	);
	$output = str_replace(array_keys($replace), $replace, $outputCorr);
	
	header($protocol . ' ' . '200');
}else{
	$data = array( 'errors' => array( array( 'type' => 'data', 'message' => 'No Test Drive timeslots are available' ) ) );
	$output = json_encode($data);
	header($protocol . ' ' . '400');
}