<?php
require('connection.php'); 
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {


        // echo  urlencode($_POST['password']);die();
	 $myusername = mysqli_real_escape_string($db,($_REQUEST['email']));
     $mypassword = mysqli_real_escape_string($db,($_REQUEST['password'])); 
	// echo $myusername.'==user'.$mypassword;die();
     // $sql = "SELECT id FROM login WHERE username = '$myusername' and password = '".$mypassword."'";
     $sql = "SELECT * FROM user WHERE email = '$myusername' and password = '".$mypassword."'";
    // echo $sql;die();
     //echo $sql;die();
     $result = mysqli_query($db,$sql);
     //$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
     $count = mysqli_num_rows($result);

      if($count === 1) {
        while($row = mysqli_fetch_assoc($result)) {
			$sno=$row['sno'];$name=$row['name'];$email=$row['email'];$role=$row['role'];$floor_no=$row['floor_no'];$section_code=$row['section_code'];									
		}
		 $_SESSION['username'] = $name;
		 $_SESSION['role'] = $role;
		 $_SESSION['floor_no'] = $floor_no;
		 $_SESSION['section_code'] = $section_code;
		
		 if ($role==1){
			header("location: user_view.php"); 
		 }elseif ($role==2){
			header("location: user_incharge.php"); 
		 }else
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