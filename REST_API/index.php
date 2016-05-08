<?php
if(isset($_GET['slug'])) {
	$slug = $_GET['slug'];
}else{
	$slug = '';
}

if(isset($_GET['id'])) {
	$id = $_GET['id'];
}else{
	$id = '';
}

if(isset($_POST['auth'])) {
	$auth = $_POST['auth'];
}else{
	$auth = true;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Accept");
header('Access-Control-Allow-Origin: *');
$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
	
if( $auth ){
	$env = file('./.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$dbh = new PDO('mysql:host=localhost;charset=utf8;dbname='.$env[0], $env[1], $env[2]);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	if( $slug == 'cars' ) {	
		include_once('cars.php');
	}
	else if( $slug == 'timeslots' ) {
		include_once('timeslots.php');
	}
	else if( $slug == 'reservations' ) {
		include_once('reservations.php');
	}else{
		$data = array( 'errors' => array( array( 'type' => 'url', 'message' => 'Bad request ' ) ) );
		$output = json_encode($data);
		header($protocol . ' ' . '404');
	}
	
	$dbh = null;

}else{
	$data = array( 'errors' => array( array( 'type' => 'authorization', 'message' => 'Unauthorized HTTP responses' ) ) );
	$output = json_encode($data);
	header($protocol . ' ' . '401');
}

echo $output;
?>