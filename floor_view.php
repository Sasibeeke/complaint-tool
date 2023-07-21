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
						
                                <h4 class="header-title">View Floor Coordinators</h4>
                                <div class="data-tables">
								 <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
                                     	
                                        <thead class="bg-light text-capitalize">
                                            <tr>
												<th>Sno</th>
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
										$sno=1;
										$sql = "select * from floor_tbl";
										$floor_co_data = mysqli_query($db, $sql);  
										
										if ($floor_co_data->num_rows > 0) {
										  // output data of each row
										while($row = mysqli_fetch_assoc($floor_co_data)) {
											Print "<tr><td>".$sno."</td>";
											Print "<td>".$row["floor_no"]."</td>";
											Print "<td>".$row["floor_coordinator_empno"]."</td>";
											Print "<td>".$row["floor_coordinator_name"]."</td>";
											Print "<td>".$row["floor_coordinator_designation"]."</td>";
											Print "<td>".$row["location_in_floor"]."</td>";
											Print "<td>".$row["creation_date"]."</td></tr>";
											$sno++;
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
    <?php include 'footer.php';?>
</body>

</html>
