<?php 

session_start();
include 'connection.php';
?>
<?php include 'header.php';?>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- preloader area start -->
   <!--  <div id="preloader">
        <div class="loader"></div>
    </div> -->
    <!-- preloader area end -->
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
                                 <?php
        
                    if(isset($_SESSION['message']))
                        { 
                            echo $_SESSION['message']; 
                            unset($_SESSION['message']);
                        }?>
                                
							<div style="float:right">
								<a class="btn btn-success mb-3" href="section_incharge.php" role="button">Add Section Incharge</a>
							</div>
								<h4 class="header-title">Section Incharge Details</h4>

                                <div class="data-tables">

                                     <?php // Perform a SELECT query
                                    $query = "SELECT * FROM section_incharge_tbl";
                                   $result = mysqli_query($db,$query);
                                        

                                    if ($result->num_rows > 0) {
                                        ?>
                                    <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
                                        <thead class="bg-light text-capitalize">
                                            <tr>
                                                <th>Section Code</th>
                                                <th>Section Incharge Code</th>
                                                <th>Section Incharge Name</th>
                                                <th>Section Incharge Designation</th>
                                                <th>Creation Date</th>
                                               
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                                            <tr>
                                                <td style="width:200px;"><?php echo $row['section_code']?></td>
                                                <td><?php echo $row['sect_emp_code']?></td>

                                                <td><?php echo $row['sect_emp_name']?></td>
                                                <td><?php echo $row['sect_emp_desig']?></td>
                                                <td><?php echo $row['creation_date']?></td>
                                                
                                                
                                            </tr>
                                           <?php }?> 
                                        </tbody>
                                    </table>
                                <?php } else {

                                    echo 'No data Available';
                                } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- data table end -->
                    
                    
                </div>
            </div>
        </div>
        <!-- main content area end -->
        <?php include 'footer_bar.php'; ?>
    </div>
    <?php include 'footer.php';?>
</body>

</html>
