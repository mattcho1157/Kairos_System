<?php
require_once('email/SMTP.php');
require_once('email/PHPMailer.php');
require_once('email/Exception.php');

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

function sendActivationEmail($address, $username, $token) {
	try {
		$mail = new PHPMailer(true); // Passing `true` enables exceptions
		//settings
		$mail->SMTPDebug = 0; // Enable verbose debug output
		$mail->isSMTP(); // Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true; // Enable SMTP authentication
		$mail->Username = 'digisolutions2019@gmail.com'; // SMTP username
		$mail->Password = 'securepassword123'; // SMTP password
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;

		$mail->setFrom('digisolutions2019@gmail.com', 'Digisol Test');

		//recipient
		$mail->addAddress($address, $username);     // Add a recipient
		$activationlink = 'http://localhost/fia2/index.php?username='.$username.'&token='.$token;
		
		//content
		$mail->isHTML(true); // Set email format to HTML
		$mail->Subject = 'Kairos Account Activation';

		$mail->Body = '
		<h1>KAIROS ACCOUNT ACTIVATION</h1>
		<h3>Username: '.$username.'</h3>
		<p style="font-size: 16px;">Please click this link to activate your account: </p>
		<a style="font-size: 16px; text-decoration: none;" href="'.$activationlink.'">ACTIVATE</a>';

		$mail->AltBody = "KAIROS ACCOUNT ACTIVATION\r\n"."Please click this link to activate your account:\r\n".$activationlink;

		$mail->send();
	} 
	catch(Exception $e) {
		echo 'Message could not be sent.';
		echo 'Mailer Error: '.$mail->ErrorInfo;
	}
}

function sendResubmitprefEmail($username, $reason) {
	try {
		$mail = new PHPMailer(true); // Passing `true` enables exceptions
		//settings
		$mail->SMTPDebug = 0; // Enable verbose debug output
		$mail->isSMTP(); // Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true; // Enable SMTP authentication
		$mail->Username = 'digisolutions2019@gmail.com'; // SMTP username
		$mail->Password = 'securepassword123'; // SMTP password
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;

		$mail->setFrom('digisolutions2019@gmail.com', 'Digisol Test');

		//recipient
		$mail->addAddress('s0157581@terrace.qld.edu.au', 'Admin');     // Add a recipient
		
		//content
		$mail->isHTML(true); // Set email format to HTML
		$mail->Subject = 'Student Kairos Preferences Resubmission Request';

		$mail->Body = '
		<h1>STUDENT KAIROS PREFERENCES RESUBMISSION REQUEST</h1>
		<h3>Student Username: '.$username.'</h3>
		<p style="font-size: 16px;"> Reason: "'.$reason.'"</p>';

		$mail->AltBody = "STUDENT KAIROS PREFERENCES RESUBMISSION REQUEST\r\nStudent Username: ".$username."\r\nReason: ".$reason;

		$mail->send();
	} 
	catch(Exception $e) {
		echo 'Message could not be sent.';
		echo 'Mailer Error: '.$mail->ErrorInfo;
	}
}

?>