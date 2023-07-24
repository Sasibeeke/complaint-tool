<?php
require('connection.php'); 
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {


        // echo  urlencode($_POST['password']);die();
	 $complaint_no = mysqli_real_escape_string($db,($_REQUEST['id']));
	 $action_taken = mysqli_real_escape_string($db,($_REQUEST['action_taken']));
     $remark = mysqli_real_escape_string($db,($_REQUEST['remarks'])); 
	 $action_taken_by = mysqli_real_escape_string($db,($_SESSION['username']));
	// echo $myusername.'==user'.$mypassword;die();
     // $sql = "SELECT id FROM login WHERE username = '$myusername' and password = '".$mypassword."'";
	$sql_transction = "INSERT INTO complaint_transaction (complaint_no,action_taken,action_taken_by,action_date,remark) VALUES ('$complaint_no','$action_taken','$action_taken_by',now(),'$remark')";
    if (mysqli_query($db, $sql_transction)) {
		
		if($action_taken=='C'){
			$flag = '2';
		}else{
			$flag = '3';
		}
		 $sql = "update complaint_tbl set flag='$flag' where complaint_no ='$complaint_no'";
		 $result = mysqli_query($db,$sql);
		 //$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		 //$count = mysqli_num_rows($result);
		 if(mysqli_affected_rows($db)>0){

				$_SESSION['message'] = "<div class='alert alert-success alert-dismissible fade show' role='alert'>Complaint Updated Successfully!<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
													<span class='fa fa-times'></span>
												</button></div>"; 
				header("location: pending_complaint_view.php");
		 }else{

			  $_SESSION['message'] = "<div class='alert alert-danger' role='alert'>Due to some issue complaint not updated,Please try after some time!</div>"; 
				header("location: pending_complaint_view.php");
		 }
      
	}else{
            echo "Error: " . $sql_transction . "<br>" . mysqli_error($db);
	}	
		 

         
      }else{

	echo 'Invalid request method';
}



?>