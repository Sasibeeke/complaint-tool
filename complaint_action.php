<?php
require('connection.php'); 
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {

if(isset($_POST['submit'])){

	$complaint_no = mysqli_real_escape_string($db,($_REQUEST['complaint_no']));
	$action_taken = mysqli_real_escape_string($db,($_REQUEST['action_taken']));
    $remark = mysqli_real_escape_string($db,($_REQUEST['remark'])); 
    $action_taken_by = mysqli_real_escape_string($db,($_SESSION['username']));
	if($action_taken=='F')
		$flag = 1;
	if($action_taken=='R')
		$flag = 3;		
	
    $sql_trans = "INSERT INTO complaint_transaction (complaint_no,action_taken,action_taken_by,action_date,remark) VALUES ('$complaint_no','$action_taken','$action_taken_by',now(),'$remark')";
	if (mysqli_query($db, $sql_trans)) {
		
		$sql_master = "update complaint_tbl set flag='$flag' where complaint_no='$complaint_no'";
        if (mysqli_query($db, $sql_master)){   
			if($action_taken=='F')
				$_SESSION['message'] = "<div class='alert alert-info' role='alert'>Your Complaint Forwarded Successfully.</div>";
            if($action_taken=='R')
				$_SESSION['message'] = "<div class='alert alert-info' role='alert'>Your Complaint Rejected.</div>";
			 
			header("location: complaint_view.php"); 
		}
	  }else {
            
             echo "Error: " . $sql_trans . "<br>" . mysqli_error($db);
		     }
      }
}
   else{

	echo 'Invalid request method';
}



?>