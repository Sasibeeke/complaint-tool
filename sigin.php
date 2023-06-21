<?php
require('connection.php'); 
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {


        // echo  urlencode($_POST['password']);die();
	 $myusername = mysqli_real_escape_string($db,($_REQUEST['email']));
     $mypassword = mysqli_real_escape_string($db,($_REQUEST['password'])); 
	// echo $myusername.'==user'.$mypassword;die();
     // $sql = "SELECT id FROM login WHERE username = '$myusername' and password = '".$mypassword."'";
     $sql = "SELECT sno FROM register WHERE email = '$myusername' and password = '".$mypassword."'";
      //echo $sql;die();
     //echo $sql;die();
     $result = mysqli_query($db,$sql);
     //$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
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