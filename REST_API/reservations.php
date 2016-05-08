<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)){
    $postData = file_get_contents("php://input");
    $request = json_decode($postData);
    @$email = $request->email;
    @$token = $request->token;
    @$timeslotId = $request->timeslotId;
}else{
	$token = '';
	$email = '';
	$timeslotId = '';
}

$email = filter_var($email, FILTER_SANITIZE_EMAIL);

if( $token == 'b326b5062b2f0e69046810717534cb09' && is_int($timeslotId) && $timeslotId >= 0 && !filter_var($email, FILTER_VALIDATE_EMAIL) === false && !preg_match('/testerror/',$email) ){

	try {
		$storeData = true;
		$stmt = $dbh->prepare("INSERT INTO carshop_reservations (timeslotId, email) VALUES (:timeslotId, :email);");
		$stmt->execute( array( ':timeslotId' => $timeslotId, ':email' => $email ) );
	}
	catch(PDOException $e)
	{
		$storeData = false;
	}
	
	if( $storeData ){
		$data=array( array( 'success' => array( 'type' => 'stored', 'message' => 'Stored order. Check confirmation on '.$email.'.' ) ) );
		$output = json_encode($data);
		header($protocol . ' ' . '200');	
	}else{
		$data = array( 'errors' => array( array ( 'type' => 'method', 'message' => 'Order not stored.' ) ) );
		$output = json_encode($data);
		header($protocol . ' ' . '500');	
	}
}else if( $email != '' && $timeslotId != '' && $token != '' ){
	$data=array("errors" => array());
	if( $token != 'b326b5062b2f0e69046810717534cb09' ){
		array_push($data["errors"],
			array(
				'type' => 'authorisation',
				'message' => 'Required or invalid token.'
			)
		);
	}
	if( filter_var($email, FILTER_VALIDATE_EMAIL) === false || !$email || preg_match('/testerror/',$email) ){
		array_push($data["errors"],
			array(
				'type' => 'email',
				'message' => 'A valid email is required.'
			)
		);
	}
	if( !is_int($timeslotId) || !($timeslotId >= 0) ){
		array_push($data["errors"],
			array(
				'type' => 'timeslotId',
				'message' => 'A valid timeslot id is required.'
			)
		);
	}
	$output = json_encode($data);
	header($protocol . ' ' . '400');
}else{
	$data = array( 'errors' => array( array( 'type' => 'token', 'message' => 'Required or invalid token' ) ) );	
	$output = json_encode($data);
	header($protocol . ' ' . '498');
}