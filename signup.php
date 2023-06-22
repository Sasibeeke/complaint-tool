<?php
require('connection.php'); 
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {

if(isset($_POST['submit'])){


        // echo  urlencode($_POST['password']);die();
	$name = mysqli_real_escape_string($db,($_REQUEST['name']));
	$email = mysqli_real_escape_string($db,($_REQUEST['email']));
    $password = mysqli_real_escape_string($db,($_REQUEST['password'])); 
    $confirmpassword = mysqli_real_escape_string($db,($_REQUEST['confirmpassword']));
	$role = mysqli_real_escape_string($db,($_REQUEST['role']));	 
	// echo $name.'==user'.$email.'password=='.$password.'cnf'.$confirmpassword;die();

      // Performing insert query execution
        // here our table name is register
        $sql = "INSERT INTO user (name,email,password,role,creation_date) VALUES ('$name','$email','$password','$role',now())";
        
       // $result = mysqli_query($db, $sql);

      //echo mysqli_insert_id($db);
      //echo $sql;
     // die();
     //echo $sql;die();
    
    
    
    if (mysqli_query($db, $sql)) {
         // session_register("myusername");
             $last_id = mysqli_insert_id($db);
           //  echo "New record created successfully. Last inserted ID is: " . $last_id;
             $_SESSION['error'] = "<div class='alert alert-info' role='alert'>User Registered successfully.Please Enter Login Details here!</div>";
             header("location: login.php"); 
      }else {
            
             echo "Error: " . $sql . "<br>" . mysqli_error($db);
		     }
      }
}
   else{

	echo 'invalid request method';
}



?>