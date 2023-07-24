<?php
require('connection.php'); 
session_start();





        // echo  urlencode($_POST['password']);die();
	// $name = mysqli_real_escape_string($db,($_REQUEST['name']));
	// $email = mysqli_real_escape_string($db,($_REQUEST['email']));
    // $password = mysqli_real_escape_string($db,($_REQUEST['password'])); 
    // $confirmpassword = mysqli_real_escape_string($db,($_REQUEST['confirmpassword']));
	// $role = mysqli_real_escape_string($db,($_REQUEST['role']));
	// $floor_no = mysqli_real_escape_string($db,($_REQUEST['floor_no']));
	// $section_code = mysqli_real_escape_string($db,($_REQUEST['section_code']));	
	// echo $floor_no.' ==floor_no=== '.$section_code.'  ===section_code== '.$password.'cnf'.$confirmpassword;die();

      // Performing insert query execution
        // here our table name is register
    $sql = "select COUNT(*) as count from complaint_tbl c where c.flag=3 ";
        
    $result = mysqli_query($db, $sql);

      //echo mysqli_insert_id($db);
      //echo $sql;
     // die();
    // echo $sql;die();
   while($row = mysqli_fetch_assoc($result)) {
	   echo $row['count'];
   }
    
    
    // if (mysqli_query($db, $sql)) {
         ////session_register("myusername");
             // $last_id = mysqli_insert_id($db);
            ////echo "New record created successfully. Last inserted ID is: " . $last_id;
             // $_SESSION['error'] = "<div class='alert alert-info' role='alert'>User Registered successfully.Please Enter Login Details here!</div>";
             // header("location: login.php"); 
      // }else {
            
             // echo "Error: " . $sql . "<br>" . mysqli_error($db);
		     // }
 
   

?>