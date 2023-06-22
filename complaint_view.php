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
	  <?php include 'sidebar_user.php';?>
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
							
                                <h4 class="header-title">View Complaints</h4>
                                <div class="data-tables">
                                    <table id="dataTable" class="table table-striped table-bordered">
										
                                        <thead class="bg-light text-capitalize">
                                            <tr>
                                                <th>Complaint No</th>
												<th>Section</th>
												<th>Floor</th>
                                                <th>Floor Coordinator Name</th>
                                                <th>Floor Coordinator Designation</th>
												<th>Location in Floor</th>
												<th>Nature of Complaint</th>
												<th>Remark</th>
												<th>Complaint Date</th>
												<th>Status</th>
												<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php 
										
										$sql = "select c.complaint_no,s.section_name,c.floor_no,f.floor_coordinator_name,f.floor_coordinator_designation,c.location_in_floor,c.nature_of_complaint,c.complaint_date,c.flag,c.remarks from complaint_tbl c,floor_tbl f,section_tbl s where c.section_code=s.section_code and c.floor_coordinator_empno=f.floor_coordinator_empno";
										$floor_co_data = mysqli_query($db, $sql);  
    
										if ($floor_co_data->num_rows > 0) {
										  // output data of each row
										while($row = mysqli_fetch_assoc($floor_co_data)) {
											
											Print "<tr><td>".$row["complaint_no"]."</td>";
											Print "<td>".$row["section_name"]."</td>";
											Print "<td>".$row["floor_no"]."</td>";
											Print "<td>".$row["floor_coordinator_name"]."</td>";
											Print "<td>".$row["floor_coordinator_designation"]."</td>";
											Print "<td>".$row["location_in_floor"]."</td>";
											Print "<td>".$row["nature_of_complaint"]."</td>";
											Print "<td>".$row["remarks"]."</td>";
											Print "<td>".$row["complaint_date"]."</td>";
											
											if($row["flag"]==0)
												Print "<td><span class='status-p bg-primary'>Pending</span></td>";
											elseif($row["flag"]==1)
												Print "<td><span class='status-p bg-info'>Process</span></td>";
											elseif($row["flag"]==2)
												Print "<td><span class='status-p bg-success'>Completed</span></td>";
											else
												Print "<td><span class='status-p bg-danger'>Rejected</span></td>";
											
											
											Print '<td>
                                                        <ul class="d-flex justify-content-center">
                                                            <li class="mr-3"><a href="#" class="text-secondary"><i class="fa fa-edit" title="Edit"></i></a></li>
															 <li class="mr-3"><a href="#" class="text-secondary"><i class="fa fa-tasks" aria-hidden="true" title="Process"></i></a></li>
                                                            <!--<li><a href="#" class="text-danger"><i class="ti-trash" title="Delete"></i></a></li>-->
                                                        </ul>
                                                    </td></tr>';
										}
										} else {
										  Print "<tr><td> No Records Found</td></tr>";
										}
																		
										?>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- data table end -->
                </div>
            </div>
        </div>
        <!-- main content area end -->
        <!-- footer area start-->
        <?php include 'footer_bar.php';?>
        <!-- footer area end-->
    </div>
    <!-- page container area end -->
    <!-- jquery latest version -->
    <script src="assets/js/vendor/jquery-2.2.4.min.js"></script>
    <!-- bootstrap 4 js -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>

    <!-- Start datatable js -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <!-- others plugins -->
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>

</html>
