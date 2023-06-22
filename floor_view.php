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
	  <?php include 'sidebar.php';?>
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
							
							<div style="float:right">
								<a class="btn btn-success mb-3" href="floor_form.php" role="button">Add Floor coordinator</a>
							</div>
						
                                <h4 class="header-title">Data Table Default</h4>
                                <div class="data-tables">
                                    <table id="dataTable" class="text-center">
										
                                        <thead class="bg-light text-capitalize">
                                            <tr>
                                                <th>Floor No</th>
                                                <th>Floor Coordinator Empno</th>
                                                <th>Floor Coordinator Name</th>
                                                <th>Floor Coordinator Designation</th>
                                                <th>Location in Floor</th>
												<th>Creation Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php 
										
										$sql = "select * from floor_tbl";
										$floor_co_data = mysqli_query($db, $sql);  
    
										if ($floor_co_data->num_rows > 0) {
										  // output data of each row
										while($row = mysqli_fetch_assoc($floor_co_data)) {
											
											Print "<tr><td>".$row["floor_no"]."</td>";
											Print "<td>".$row["floor_coordinator_empno"]."</td>";
											Print "<td>".$row["floor_coordinator_name"]."</td>";
											Print "<td>".$row["floor_coordinator_designation"]."</td>";
											Print "<td>".$row["location_in_floor"]."</td>";
											Print "<td>".$row["creation_date"]."</td></tr>";
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
