<?php 
session_start();
include 'connection.php';

$sql = "select c.complaint_no,s.section_name,s.section_code,c.floor_no,f.floor_coordinator_name,f.floor_coordinator_empno,f.floor_coordinator_designation,c.location_in_floor,c.nature_of_complaint,c.complaint_date,c.flag,c.description from complaint_tbl c,floor_tbl f,section_tbl s where c.section_code=s.section_code and c.floor_coordinator_empno=f.floor_coordinator_empno and c.complaint_no='$_GET[id]'";

  $result = mysqli_query($db, $sql);
  $row = mysqli_fetch_assoc($result);
  $complaint_no = $row['complaint_no'];
  $complaint_date = $row['complaint_date'];
  $section_code = $row['section_code'];
  $section_name = $row['section_name'];
  $floor_no = $row['floor_no'];
  $floor_coordinator_empno = $row['floor_coordinator_empno'];
  $floor_coordinator_name = $row['floor_coordinator_name'];
  $location_in_floor = $row['location_in_floor'];
  $nature_of_complaint = $row['nature_of_complaint'];
  $flag = $row['flag'];
  $remarks = $row['description']; 


?>
<?php include 'header.php';?>

<body>
 
    <div class="page-container">
       <?php 
	   $role=$_SESSION['role'];
	  if($role==1){
		 include 'sidebar_user.php'; 
	  }elseif($role==2){
		include 'sidebar_incharge.php';
	  }else{
		  include 'sidebar.php';
	  }
	   ?>
        <!-- main content area start -->
        <div class="main-content">
           <!-- header area start -->
            <?php include 'body.php';?>
            <!-- header area end -->
           
            <div class="main-content-inner">
                <div class="row">
                    <!-- data table start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                               <div class="invoice-area">
                                    <div class="invoice-head">
                                        <div class="row">
                                            <div class="iv-left col-6">
                                              <span >Complaint No: <?php echo $complaint_no;?></span>
                                            </div>
                                            <div class="iv-right col-6 text-md-right">
                                               <span>Complaint Date:<?php echo $complaint_date;?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="invoice-address">
                                            <h5>Section Name </h5>
                                            <h5>Floor No</h5>
                                            <h5>Floor Coordinator Employee No</h5>
                                            <h5>Floor Coordinator Employee Name</h5>
                                            <h5>Location In Floor</h5>
                                            <h5>Nature Of Complaint</h5>
                                            <h5>Status</h5>
                                            </div>
                                        </div>
                                    <div class="col-md-6 ">
                                    <div class="invoice-address">
                                      <h5><?php echo $section_name;?></h5>
                                       <h5><?php echo $floor_no;?></h5>
                                  <h5><?php echo $floor_coordinator_empno;?></h5>
                                  <h5><?php echo $floor_coordinator_name;?></h5>
                                  <h5><?php echo $location_in_floor;?></h5>
                                   <h5><?php echo $nature_of_complaint;?></h5>
                               <h5><?php if($flag=='1'){
                                  echo 'Pending';
                               }elseif($flag=='2'){
                                echo 'Completed';
                               }else{
                                echo 'Rejected';
                               }
                               ;?></h5>
                                     </div>
                                        </div>
                                    </div>
                                      <hr>
                                    <div class="row">
                                  <div class="col-md-12">
                                    <strong><h5>Complaint Description:</h5></strong><br>
                                    <p><?php echo $remarks;?></p>
                                    </div>
                                </div>
                               
                                </div>
                             </div>
                        </div>
                    </div>
                    <!-- data table end -->
                    

                    <!--for transaction ---->
                   <?php
                    $sql = "select * from complaint_transaction where complaint_no='$_GET[id]'";
                    $result = mysqli_query($db, $sql);
                     // while($row = mysqli_fetch_assoc($result))
                    if($result){
                    foreach($result as $record) {
                     
                       ?>
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                               <div class="invoice-area">
                                    <div class="invoice-head">
                                        <div class="row">
                                            <div class="iv-left col-6">
                                              <span>Complaint No: <?php echo $complaint_no;?></span>
                                            </div>
                                            <div class="iv-right col-6 text-md-right">
                                               <span>Complaint Date:<?php echo $complaint_date;?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="invoice-address">
                                            <!-- <h5>Section Name </h5>
                                            <h5>Floor No</h5>
                                            <h5>Floor Coordinator Employee No</h5>
                                            <h5>Floor Coordinator Employee Name</h5>
                                            <h5>Location In Floor</h5>
                                            <h5>Nature Of Complaint</h5> -->

                                            <h5>Action Taken</h5>
                                            <h5>Action Taken By</h5>
                                            <h5>Action Date</h5>
                                            <h5>Remark</h5>

                                            <!-- <h5>Status</h5> -->
                                            </div>
                                        </div>
                                    <div class="col-md-6 ">
                                    <div class="invoice-address">
                                      <!-- <h5><?php echo $section_name;?></h5>
                                       <h5><?php echo $floor_no;?></h5>
                                      <h5><?php echo $floor_coordinator_empno;?></h5>
                                      <h5><?php echo $floor_coordinator_name;?></h5>
                                      <h5><?php echo $location_in_floor;?></h5>
                                       <h5><?php echo $nature_of_complaint;?></h5>
                                   <h5><?php if($flag=='1'){
                                      echo 'Pending';
                                   };?></h5> -->

                                    <h5><?php

                                    if($record['action_taken']=='F'){
                                        $record_action = 'Forworded';
                                    }elseif($record['action_taken']=='C'){
                                        $record_action = 'Completed';
                                    }else{
                                       $record_action = 'Rejected'; 
                                    }
                                    echo $record_action;?></h5>
                                    <h5><?php echo $record['action_taken_by'];?></h5>
                                    <h5><?php echo $record['action_date'];?></h5>
                                    
                                    <h5><?php echo $record['remark'];?></h5>


                                     </div>
                                        </div>
                                    </div>
                                      <hr>
                                    <div class="row">
                                  <!-- <div class="col-md-12">
                                    <strong><h5>Complaint Description:</h5></strong><br>
                                    <p><?php echo $record['remark'];?></p>
                                    </div> -->
                                </div>
                               
                                </div>
                             </div>
                        </div>
                    </div>
                    <!----ends here ----->
                    <?php }
                }else{

                }




                    ?>
                </div>
            </div>
        </div>


     
        <!-- main content area end -->
        <?php include 'footer_bar.php'; ?>
    </div>
    <?php include 'footer.php';?>


</body>

</html>
