<?php

require('connection.php'); 
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {

if(isset($_POST['submit'])){
    //print_r($_POST);die();
    //   echo 'welcomess';die();
	 $section_code = mysqli_real_escape_string($db,($_REQUEST['section_code']));
     $employee_code = mysqli_real_escape_string($db,($_REQUEST['employee_code']));
     $employee_name = mysqli_real_escape_string($db,($_REQUEST['employee_name']));
     $employee_designation = mysqli_real_escape_string($db,($_REQUEST['employee_designation']));

        $currentDate = date('Y-m-d');
      $sql = "INSERT INTO section_incharge_tbl (section_code,sect_emp_code,sect_emp_name,sect_emp_desig,creation_date) VALUES('$section_code','$employee_code','$employee_name','$employee_designation','$currentDate')";

      //echo $sql;die();
    if(mysqli_query($db, $sql)) {
         $last_id = mysqli_insert_id($db);
            $_SESSION['message'] = "<div class='alert alert-info' role='alert'>Section Incharge Added successfully!</div>";
             //redirect is remaining//
             header("location: view_sectionincharge.php"); 
      }else {
            
             echo "Error: " . $sql . "<br>" . mysqli_error($db);
           }
      
}

}else{

	echo 'invalid request method';
}



?>