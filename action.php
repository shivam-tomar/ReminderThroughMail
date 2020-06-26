<?php
// Action page of Infosolution 
if(isset($_POST["send"])){
	$tdate = $_POST['tdate'];
}
if(!empty($tdate))
{
	$host = 'localhost';
	$username = 'root';
	$password = '';
	$dbname = 'infosolutions';

	$conn = new mysqli($host,$username,$password,$dbname);
	
	if(mysqli_connect_errno()){
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	else{
		require 'vendor/autoload.php';
		
		$sql = "SELECT mailid, Name, LoanAmount, Duedate FROM loandata WHERE DueDate >= '$tdate' AND Status = 'Due'";
		if ($result = mysqli_query($conn, $sql)) {
			while ($row = mysqli_fetch_row($result)) {
				# row[0] is the mailid at which we have to send the mail
				$email = new \SendGrid\Mail\Mail(); 
				$email->setFrom("shivame777@gmail.com", "Admin");
				$email->setSubject("Reminder for your Loan Emi");
				$email->addTo("$row[0]", "Customer");
				$email->addContent("text/plain", "Dear customer $row[1] Please pay your loan Emi of $row[2] INR, Due date: $row[3]");
				$sendgrid = new \SendGrid(getenv('Key_Generated_By_Sendgrid'));
				try {
					$response = $sendgrid->send($email);
					echo "Mail Sent to $row[1]";
				} catch (Exception $e) {
					echo 'Caught exception: '. $e->getMessage() ."\n";
				} 
			}
			mysqli_free_result($result);
		}
		else 
			echo "Zero Coustomer's emi is Due";
	}
}
else{
	echo "All required";
	die();
}
$conn->close();
?>
