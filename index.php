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
        <?php 
		if($_SESSION['role']==1)
			include 'sidebar_user.php';
		elseif($_SESSION['role']==2)
			include 'sidebar_incharge.php';
		else
			include 'sidebar.php';
		?>
		
        <!-- main content area start -->
        <div class="main-content">
            
            <?php include 'body.php';?>

            <div class="main-content-inner">
                <!-- sales report area start -->
                <div class="sales-report-area mt-5 mb-5">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="single-report mb-xs-30">
                                <div class="s-report-inner  pt--30 mb-3">
								
                                    <div class="icon"><i class="fa fa-building-o"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Total Complaints</h4>
                                        <!--<p>24 H</p>-->
                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
									
									<?php 
									  $sql = "select count(*) as total_complaints  from complaint_tbl c";
									  $result = mysqli_query($db, $sql);
									  $row = mysqli_fetch_assoc($result);
									  $total_complaints=$row['total_complaints']; 	
									?>
									
                                        <h2><?php echo $total_complaints; ?></h2>
                                        
                                    </div> 
                                </div>
                                <!--<canvas id="coin_sales1" height="100"></canvas>-->
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="single-report mb-xs-30">
                                <div class="s-report-inner  pt--30 mb-3">
                                    <div class="icon"><i class="fa fa-tasks teal-color"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Pending Complaints</h4>
                                         <!--<p>24 H</p>-->
                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
                                        <?php 
									  $sql = "select count(*) as pending_complaints  from complaint_tbl c where c.flag in ('0','1')";
									  $result = mysqli_query($db, $sql);
									  $row = mysqli_fetch_assoc($result);
									  $pending_complaints=$row['pending_complaints']; 	
									?>
										
										<h2><?php echo $pending_complaints; ?></h2>
                                       
                                    </div>
                                </div>
                                <!-- <canvas id="coin_sales2" height="100"></canvas> -->
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="single-report">
                                <div class="s-report-inner  pt--30 mb-3">
                                    <div class="icon"><i class="fa fa-lock"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Solved Complaints</h4>
                                       <!--<p>24 H</p>-->
                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
									<?php 
									  $sql = "select count(*) as completed_complaints  from complaint_tbl c where c.flag='2'";
									  $result = mysqli_query($db, $sql);
									  $row = mysqli_fetch_assoc($result);
									  $completed_complaints=$row['completed_complaints']; 	
									?>
									
                                        <h2><?php echo $completed_complaints; ?></h2>
                                      
                                    </div>
                                </div>
                                <!-- <canvas id="coin_sales3" height="100"></canvas>-->
                            </div>
                        </div>
						<div class="col-md-3">
                            <div class="single-report">
                                <div class="s-report-inner  pt--30 mb-3">
                                    <div class="icon"><i class="fa fa-ban"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Rejected Complaints</h4>
                                       <!--<p>24 H</p>-->
                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
									<?php 
									  $sql = "select count(*) as rejected_complaints  from complaint_tbl c where c.flag='3'";
									  $result = mysqli_query($db, $sql);
									  $row = mysqli_fetch_assoc($result);
									  $rejected_complaints=$row['rejected_complaints']; 	
									?>
									
                                        <h2><?php echo $rejected_complaints; ?></h2>
                                      
                                    </div>
                                </div>
                                <!-- <canvas id="coin_sales3" height="100"></canvas>-->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- sales report area end -->
                <!-- overview area start -->
                <div class="row">
                    <div class="col-xl-9 col-lg-8">
                        <div class="card">
                            <div class="card-body">
                              
                                 <div id="chart_div" style="width:auto;height: 500px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 coin-distribution">
                        <div class="card h-full">
                            <div class="card-body">
                            </div>
                        </div>
                    </div>
                   </div>

                <!-- overview area end -->
                          
            </div>
        </div>
        <!-- main content area end -->
        <?php include 'footer_bar.php';?>
    </div>
    <!-- page container area end -->
    <?php include 'body_head.php';?>
   <?php include 'footer.php';?>
   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
   <script type="text/javascript">
    // Load the Visualization API and the corechart package
    google.charts.load('current', {'packages':['corechart']});

    // Set a callback to run when the Google Visualization API is loaded
    google.charts.setOnLoadCallback(drawChart);

    // Callback function to create and populate the chart
    function drawChart() {
      // Your complaint table data
      var pendingCount  = <?php echo $pending_complaints;?>; // Example count of completed complaints
      var rejectedCount = <?php echo $rejected_complaints;?>;  // Example count of rejected complaints
      var completedCount =  <?php echo $completed_complaints;?>;;   // Example count of pending complaints
      // var totalCount =  <?php echo $total_complaints; ?>;   // Example count of pending complaints

      // Create the data table
      var data = google.visualization.arrayToDataTable([
        ['Status', 'Count'],
        ['Completed', completedCount],
        ['Rejected', rejectedCount],
        ['Pending', pendingCount]
      ]);

      // Set chart options
      var options = {
        title: 'Complaint Graph',
        pieHole: 0.4,
      };

      // Instantiate and draw the chart
      var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }
  </script>
</body>

</html>
