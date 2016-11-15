<?php  // Post submission handler for ajax Email a Friend form

if( $_POST ) {
	$to = $_POST['friendemail'];
	$subject = get_the_title($id);
	$msg = $_POST['message'];
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'From: ' . $_POST['youremail'] . "\r\n";
	mail( $to, $subject, $msg, $headers );
}
