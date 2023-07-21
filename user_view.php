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
            
            <?php include 'body.php';?>
				
			<div class="main-content-inner">
                <!-- sales report area start -->
                <div class="sales-report-area mt-5 mb-5">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="single-report mb-xs-30">
                                <div class="s-report-inner pr--20 pt--30 mb-3">
								
                                    <div class="icon"><i class="fa fa-building-o"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Total Complaints</h4>
                                        <!--<p>24 H</p>-->
                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
									
									<?php
									  $floor_no=$_SESSION['floor_no'];	
									  $sql = "select count(*) as total_complaints  from complaint_tbl c where c.floor_no='$floor_no'";
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
                                <div class="s-report-inner pr--20 pt--30 mb-3">
                                    <div class="icon"><i class="fa fa-tasks teal-color"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Pending Complaints</h4>
                                         <!--<p>24 H</p>-->
                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
                                        <?php 
									  $floor_no=$_SESSION['floor_no'];	
									  $sql = "select count(*) as pending_complaints  from complaint_tbl c where c.flag ='0' and c.floor_no='$floor_no'";
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
                                <div class="s-report-inner pr--20 pt--30 mb-3">
                                    <div class="icon"><i class="fa fa-lock"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Solved Complaints</h4>
                                       <!--<p>24 H</p>-->
                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
									<?php
									  $floor_no=$_SESSION['floor_no'];										
									  $sql = "select count(*) as completed_complaints  from complaint_tbl c where c.flag='2' and c.floor_no='$floor_no'";
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
                                <div class="s-report-inner pr--20 pt--30 mb-3">
                                    <div class="icon"><i class="fa fa-ban"></i></div>
                                    <div class="s-report-title d-flex justify-content-between">
                                        <h4 class="header-title mb-0">Rejected Complaints</h4>
                                       <!--<p>24 H</p>-->
                                    </div>
                                    <div class="d-flex justify-content-between pb-2">
									<?php
									   $floor_no=$_SESSION['floor_no'];	
									  $sql = "select count(*) as rejected_complaints  from complaint_tbl c where c.flag='3' and c.floor_no='$floor_no'";
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
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="header-title mb-0">Overview</h4>
                                    <select class="custome-select border-0 pr-3">
                                        <option selected>Last 24 Hours</option>
                                        <option value="0">01 July 2018</option>
                                    </select>
                                </div>
                                <div id="verview-shart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 coin-distribution">
                        <div class="card h-full">
                            <div class="card-body">
                                <h4 class="header-title mb-0">Coin Distribution</h4>
                                <div id="coin_distribution"></div>
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
</body>

</html>
