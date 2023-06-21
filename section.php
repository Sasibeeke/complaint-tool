

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
                                        <h4 class="header-title"> Section Details</h4>
                                      <form action="section_add.php" method="post">  
                                        <div class="form-group">
                                            <label for="example-text-input" class="col-form-label">Enter Section Name</label>
                                            <input class="form-control" type="text" value="" name="section_name" id="example-text-input" placeholder="Enter your section name" required>
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
                                    <p class="text-primary">Please Select Employee Section Name.</p>
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
