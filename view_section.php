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
           
           
            <!-- page title area start -->
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Dashboard</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="index.html">Home</a></li>
                                <li><span>Datatable</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile pull-right">
                            <img class="avatar user-thumb" src="assets/images/author/avatar.png" alt="avatar">
                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown">Kumkum Rai <i class="fa fa-angle-down"></i></h4>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#">Message</a>
                                <a class="dropdown-item" href="#">Settings</a>
                                <a class="dropdown-item" href="#">Log Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- page title area end -->
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
                                <a href='section.php'><button type="button" class="btn btn-primary btn-lg mb-3" style="float: right;">Add Section</button></a>
                                <h4 class="header-title">Section Details</h4>

                                <div class="data-tables">

                                     <?php // Perform a SELECT query
                                    $query = "SELECT * FROM section_tbl";
                                   $result = mysqli_query($db,$query);
                                        

                                    if ($result->num_rows > 0) {
                                        ?>
                                    <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
                                        <thead class="bg-light text-capitalize">
                                            <tr>
                                                <th>Section Code</th>
                                                <th>Section Name</th>
                                                <th>Date</th>
                                               
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                                            <tr>
                                                <td style="width:200px;"><?php echo $row['section_code']?></td>
                                                <td><?php echo $row['section_name']?></td>

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
