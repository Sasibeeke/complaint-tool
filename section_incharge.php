<?php 

session_start();
include 'connection.php';
?>

<?php include 'header.php';?>

<body>

    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="page-container">
       
           <?php include 'sidebar.php';?>
            
            <!-- main content area start -->
            <div class="main-content">
            <?php include 'body.php';?>

              <div class="main-content-inner">
                <div class="row">
                    <div class="col-lg-6 col-ml-12">
                        <div class="row">
                            <!-- Textual inputs start -->
                            <div class="col-12 mt-5">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title"> Section Incharge Details</h4>
                                      <form action="incharge_add.php" method="post">

                                        <div class="form-group">
                                            <label class="col-form-label">Select Employee Section Type</label>

                                            <?php
                                            $sql = "SELECT section_code, section_name FROM section_tbl";
                                        //echo $query;die(); 
                                        $result = mysqli_query($db,$sql);
                                        if($result){
                                        

                                 echo '<select class="form-control" style="padding:7px !important;font-size:13px;" name="section_code" required>';
                                 echo '<option value="">Select</option>';
                                while ($row = mysqli_fetch_assoc($result)) {
                                        $section_code = $row['section_code'];
                                        $section_name = $row['section_name'];

                                        echo '<option value="' . $section_code . '">' . $section_name . '</option>';
                                    }
                                    echo '</select>';

                                  }

                                  ?>  

                                        </div>

                                        <div class="form-group">
                                            <label for="example-text-input" class="col-form-label">Enter Employee Code</label>
                                            <input class="form-control" type="text" value="" name="employee_code" id="example-text-input" placeholder="Enter Employee Code" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="example-text-input" class="col-form-label">Enter Employee Name</label>
                                            <input class="form-control" type="text" value="" name="employee_name" id="example-text-input" placeholder="Enter Employee Name" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="example-text-input" class="col-form-label">Enter Employee Designation</label>
                                            <input class="form-control" type="text" value="" name="employee_designation" id="example-text-input" placeholder="Enter Employee Designation" required>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-primary mt-4 pr-4 pl-4">Submit</button>
                                    </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Textual inputs end -->
                            
                            
                            
                        </div>
                    </div>

                    <div class="col-lg-6 col-ml-12">

                        <div class="row">
                            <!-- Textual inputs start -->
                            <div class="col-12 mt-5">
                                <div class="card">
                                    <div class="card-body">
                                     <h4 class="header-title text-danger">Instructions</h4>
                                    <p class="text-primary">Please Select Employee Section Type.</p>
                                        
                                    <p class="text-primary">Please Enter Employee Section Code.</p>
                                        
                                    <p class="text-primary">Please Enter Employee Name.</p>
                                       
                                        
                                     <p class="text-primary">Please Enter Employee Designation.</p>
                                      <p class="text-primary">Please Review form before submission.</p>              
                                    </div>
                                </div>
                             </div>
                         </div>        

                    </div>
                </div>
            </div> 
         </div>
            <!-- main content area end -->
     </div>
    <!-- page container area end -->
   
  <?php include 'body_head.php';?> 

<?php include 'footer_bar.php';?>
<?php include 'footer.php';?>

</body>

</html>
