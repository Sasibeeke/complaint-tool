<?php
require('connection.php'); 
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {

	 $myusername = mysqli_real_escape_string($db,$_POST['email']);
     $mypassword = mysqli_real_escape_string($db,$_POST['password']); 
	
     $sql = "SELECT id FROM login WHERE username = '$myusername' and password = '$mypassword'";
     // echo $sql;die();
     $result = mysqli_query($db,$sql);
     $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
     $count = mysqli_num_rows($result);

      if($count === 1) {
         // session_register("myusername");
         $_SESSION['username'] = $myusername;
         
         header("location: index.php");
      }else {

      	 //echo 'hi';die();
		
		$_SESSION['error'] = "<div class='alert alert-danger' role='alert'>Oh snap! Invalid login details.</div>"; 
		header("location: login.php");
		     }
	}else{

	echo 'invalid request method';
}



?>