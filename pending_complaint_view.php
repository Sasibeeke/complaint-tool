<?php 

session_start();
include 'connection.php';

if(!isset($_SESSION["username"])){
header("Location: login.php");
}

?>
<?php include 'header.php';?>


<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
   
    <!-- page container area start -->
    <div class="page-container">
	  <?php include 'sidebar_incharge.php';?>
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
					<?php
        
                    if(isset($_SESSION['message']))
                        { 
                            echo $_SESSION['message']; 
                            //unset($_SESSION['message']);
                        }?>
                                <h4 class="header-title">View Pending Complaints</h4>
                                <div class="data-tables">
                                	<?php 
									$section_code=$_SESSION['section_code'];	
									$sql = "select c.complaint_no,s.section_name,c.floor_no,f.floor_coordinator_name,f.floor_coordinator_designation,c.location_in_floor,
									c.nature_of_complaint,c.complaint_date,c.flag,c.description from complaint_tbl c,floor_tbl f,section_tbl s where c.section_code=s.section_code 
									and c.floor_coordinator_empno=f.floor_coordinator_empno  and c.flag='1' and c.section_code='$section_code'";
									//echo $sql;die();
									$result = mysqli_query($db, $sql);  
									$sno=1;
									if ($result) {?>
                                    <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
										
                                        <thead class="bg-light text-capitalize">
                                            <tr>
                                                <th>Sno</th>
                                                <th>Complaint No</th>
												<th>Section</th>
												 <th>Floor</th> 
                                                <th>Floor Coordinator Name</th>
                                                 <th>Floor Coordinator Designation</th> 
												<th>Location in Floor</th>
												 <th>Nature of Complaint</th> 
												<!-- <th>Remark</th> -->
												<th>Complaint Date</th>
												<th>Status</th>
												<th>Action</th>
											</tr>
                                        </thead>
                                        <tbody>
								<?php 		
										  // output data of each row
										while($row = mysqli_fetch_assoc($result)) {
											
											Print "<tr><td>".$sno."</td>";
											Print "<td>".$row["complaint_no"]."</td>";
											Print "<td>".$row["section_name"]."</td>";
											 Print "<td>".$row["floor_no"]."</td>";
											Print "<td>".$row["floor_coordinator_name"]."</td>";
											 Print "<td>".$row["floor_coordinator_designation"]."</td>";
											Print "<td>".$row["location_in_floor"]."</td>";
											 Print "<td>".$row["nature_of_complaint"]."</td>";
											// Print "<td>".$row["remarks"]."</td>";
											Print "<td>".$row["complaint_date"]."</td>";
											
											if($row["flag"]==1)
												Print "<td><span class='status-p bg-primary'>Pending</span></td>";
											elseif($row["flag"]==2)
												Print "<td><span class='status-p bg-success'>Completed</span></td>";
											else
												Print "<td><span class='status-p bg-danger'>Rejected</span></td>";
											Print '<td>
                <ul class="d-flex justify-content-center">
                   <!--<li class="mr-3"><a href="#" class="text-secondary"> Details</a></li>-->
                   <a href="complaint_details.php?id='.$row['complaint_no'].'"><span class="status-p bg-primary">Details</span></a>
															 </ul>
                 </td>

                 </tr>';

                 $sno++;
										}
									
																		
										?>
            		</tbody>
             </table>
           <?php   	} else {
										  Print "No Data Available";
										}?>
        </div>
       </div>
       </div>
      </div>
     </div>
    </div>
   </div>
        <!-- main content area end -->
        <!-- footer area start-->
     <?php include 'footer_bar.php';?>
        <!-- footer area end-->
</div>
  <?php include 'footer.php';?>
</body>

</html>
