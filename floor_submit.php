<?php
require('connection.php'); 
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {

if(isset($_POST['submit'])){


        // echo  urlencode($_POST['password']);die();
	$floor_no = mysqli_real_escape_string($db,($_REQUEST['floor_no']));
	$floor_coordinator_empno = mysqli_real_escape_string($db,($_REQUEST['floor_coordinator_empno']));
    $floor_coordinator_name = mysqli_real_escape_string($db,($_REQUEST['floor_coordinator_name'])); 
    $floor_coordinator_designation = mysqli_real_escape_string($db,($_REQUEST['floor_coordinator_designation']));
	$location_in_floor = mysqli_real_escape_string($db,($_REQUEST['location_in_floor']));	 
	// here our table name is register
    $sql = "INSERT INTO floor_tbl (floor_no,floor_coordinator_empno,floor_coordinator_name,floor_coordinator_designation,location_in_floor,creation_date) VALUES ('$floor_no','$floor_coordinator_empno','$floor_coordinator_name','$floor_coordinator_designation','$location_in_floor',now())";
          
    
    if (mysqli_query($db, $sql)) {
         // session_register("myusername");
             $last_id = mysqli_insert_id($db);
           //  echo "New record created successfully. Last inserted ID is: " . $last_id;
             $_SESSION['message'] = "<div class='alert alert-info' role='alert'>Success! Record save successfully</div>";
             header("location: floor_form.php"); 
      }else {
            
             echo "Error: " . $sql . "<br>" . mysqli_error($db);
		     }
      }
}
   else{

	echo 'Invalid request method';
}



?>