<?php  // Post submission handler for ajax Email a Friend form

/*if( $_POST ) {
	// Here we get all the information from the fields sent over by the form.
	$name = $_POST['yourname'];
	$email = $_POST['youremail'];
	$message = $_POST['message'];

	$to = $_POST['friendemail'];
	$subject = get_the_title( get_the_ID() );
	$message = $_POST['message'];
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'From: ' . $_POST['youremail'] . "\r\n";

	if (filter_var($email, FILTER_VALIDATE_EMAIL)) { // this line checks that we have a valid email address
		mail($to, $subject, $message, $headers); //This method sends the mail.
		echo "Your email was sent!"; // success message
	}else{
		echo "Invalid Email, please provide a correct email.";
	}
}*/


// Here we get all the information from the fields sent over by the form.
$name = $_POST['yourname'];
$email = $_POST['youremail'];
$message = $_POST['message'];
$friendemail = $_POST['friendemail'];
$listingtitle = $_POST['listingtitle'];
 
$to = $friendemail;
//$subject = $name .' Wants to Share This Property With You';
$subject = 'BendHomes.com - '. $listingtitle;
$message =  $message . "\r\n\r\n" .'Shared by: '. $name;
$headers = 'From: BendHomes <noreply@bendhomes.com>' . "\r\n";
 
if (filter_var($email, FILTER_VALIDATE_EMAIL)) { // this line checks that we have a valid email address
	mail($to, $subject, $message, $headers); //This method sends the mail.
	echo "Email sent!"; // success message
}else{
	echo "Invalid Email, please provide a correct email.";
}