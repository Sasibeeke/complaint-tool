<?php
require('connection.php'); 
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {

if(isset($_POST['submit'])){
        // echo  urlencode($_POST['password']);die();
	 $section_name = mysqli_real_escape_string($db,($_REQUEST['section_name']));

    $currentDate = date('Y-m-d');
      $sql = "INSERT INTO section_tbl (section_name,creation_date) VALUES ('$section_name','$currentDate')";

      //echo $sql;die();
    if(mysqli_query($db, $sql)) {
         $last_id = mysqli_insert_id($db);
            $_SESSION['message'] = "<div class='alert alert-info' role='alert'>Section Name Added Successfully!</div>";
             //redirect is remaining//
             header("location: view_section.php"); 
      }else {
            
             echo "Error: " . $sql . "<br>" . mysqli_error($db);
           }
      
	}

}else{

	echo 'invalid request method';
}



?>