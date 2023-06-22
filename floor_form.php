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
                    <div class="col-lg-6 col-ml-12">
                        <div class="row">
                            <!-- Textual inputs start -->
                            <div class="col-12 mt-5">
                                <div class="card">
                                    <div class="card-body">
									<h4 class="header-title">Floor Form</h4>
									<?php
        								if(isset($_SESSION['message']))
										{ 
											echo $_SESSION['message']; 
											unset($_SESSION['message']);
										}?>
                                    <form class="needs-validation" novalidate="" action="floor_submit.php" method="POST" >
										 <div class="form-group">
                                            <label for="validationCustom01" class="col-form-label">Select Floor</label>
                                            <select class="form-control" name="floor_no" id="validationCustom01" style="font-size: 14px;padding:3.72px 15.8px;" required="">
                                                <option>Select</option>
												<option value="0">Ground</option>
                                                <option value="1">First</option>
                                                <option value="2">Second</option>
												<option value="3">Third</option>
												<option value="4">Fourth</option>
                                            </select>
											<div class="invalid-feedback">
                                                Please select floor
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="validationCustom02" class="col-form-label">Floor Coordinator Emp No</label>
                                            <input class="form-control" type="text" name="floor_coordinator_empno" value="" id="validationCustom02" placeholder="Enter Floor Coordinator Emp No" required="">
											<div class="invalid-feedback">
                                                Please Enter Floor Coordinator Emp No
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label for="validationCustom03" class="col-form-label">Floor Coordinator Name</label>
                                            <input class="form-control" type="text" value="" name="floor_coordinator_name" id="validationCustom03" placeholder="Enter Floor Coordinator Emp Name" required="">
											<div class="invalid-feedback">
                                                Please Enter Floor Coordinator Emp Name
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label for="validationCustom04" class="col-form-label">Floor Coordinator Designation</label>
                                            <input class="form-control" type="text" value="" name="floor_coordinator_designation" id="validationCustom04" placeholder="Enter Floor Coordinator Designation" required="">
											<div class="invalid-feedback">
                                                Please Enter Floor Coordinator Designation
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label for="validationCustom05" class="col-form-label">Location In Floor</label>
                                            <input class="form-control" type="text" value="" name="location_in_floor" id="validationCustom05" placeholder="Enter Floor Location" required="">
											<div class="invalid-feedback">
                                                Please Enter Floor Location
                                            </div>
										</div>
										
                                         <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                                    </form>    
                                    </div>
                                </div>
                            </div>
                            <!-- Textual inputs end -->
                        </div>
                    </div>
                    <div class="col-lg-6 col-ml-12">
                        <div class="row">
                             <!-- Server side start -->
                            <div class="col-12">
                                <div class="card mt-5">
                                    <div class="card-body">
                                        <h4 class="header-title">Instructions :- </h4>
										1. Select Floor.<br/>
										2. Enter floor coordinator Emp No.<br/> 
										3. Enter floor coordinator Emp Name.<br/> 
										4. Enter floor coordinator Designation.<br/> 
										5. Enter floor location.<br/> 
                                    </div>
                                </div>
                            </div>
                            <!-- Server side end -->
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
	<!-- page container area end -->
    <?php include 'footer.php';?>
</body>

</html>
