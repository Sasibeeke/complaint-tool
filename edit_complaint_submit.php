<?php
require('connection.php'); 
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {

if(isset($_POST['submit'])){


        // echo  urlencode($_POST['password']);die();
	$complaint_no = mysqli_real_escape_string($db,($_REQUEST['complaint_no']));
	$section_code = mysqli_real_escape_string($db,($_REQUEST['section_code']));
   // $floor_coordinator_empno = mysqli_real_escape_string($db,($_REQUEST['floor_coordinator_empno'])); 
    $location_in_floor = mysqli_real_escape_string($db,($_REQUEST['location_in_floor']));
	$complaint_description = mysqli_real_escape_string($db,($_REQUEST['complaint_description']));
	//$nature_of_complaint="ABC";
	//$flag = 0;	
	// here our table name is register
	
	
    $sql = "update complaint_tbl set section_code='$section_code',location_in_floor='$location_in_floor',description='$complaint_description' where complaint_no='$complaint_no'";
          
    //echo $sql;die;
    if (mysqli_query($db, $sql)) {
         // session_register("myusername");
          //   $last_id = mysqli_insert_id($db);
           //  echo "New record created successfully. Last inserted ID is: " . $last_id;
             $_SESSION['message'] = "<div class='alert alert-info' role='alert'>Your Complaint Updated Successfully.</div>";
             header("location: complaint_view.php"); 
      }else {
            
             echo "Error: " . $sql . "<br>" . mysqli_error($db);
		     }
      }
}
   else{

	echo 'Invalid request method';
}



?>