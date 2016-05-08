<?php
$stmt = $dbh->prepare("SELECT * FROM `carshop_cars` WHERE id >= 0");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if( count($data) > 0 ){
	$output = json_encode($data, JSON_NUMERIC_CHECK);
	header($protocol . ' ' . '200');
}else{
	$data = array( 'errors' => array( array( 'type' => 'data', 'message' => 'No cars are available' ) ) );
	$output = json_encode($data);
	header($protocol . ' ' . '400');
}
