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
                    <div class="col-lg-6 col-ml-12">
                        <div class="row">
                            <!-- Textual inputs start -->
                            <div class="col-12 mt-5">
                                <div class="card">
                                    <div class="card-body">
									<h4 class="header-title">Raise Complaint</h4>
									<?php
        								if(isset($_SESSION['message']))
										{ 
											echo $_SESSION['message']; 
											unset($_SESSION['message']);
										}?>
                                    <form class="needs-validation" novalidate="" action="complaint_submit.php" method="POST" >
										  <div class="form-group">
                                            <label for="validationCustom02" class="col-form-label">Section <span style="color:red">*</span></label>
                                            <select class="form-control" name="section_code" id="validationCustom02" style="font-size: 14px;padding:3.72px 15.8px;" required="">
                                                <option>Select Section</option>
												<option value="1">Electrical</option>
                                                <option value="2">Civil</option>
                                                <option value="3">Housekeeping</option>
												
                                            </select>
											<div class="invalid-feedback">
                                                Please select section
                                            </div>
                                        </div>
										 <div class="form-group">
                                            <label for="validationCustom01" class="col-form-label">Floor <span style="color:red">*</span></label>
                                            <select class="form-control" name="floor_no" id="validationCustom01" style="font-size: 14px;padding:3.72px 15.8px;" required="">
                                                <option>Select Floor</option>
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
                                            <label for="validationCustom03" class="col-form-label">Floor Coordinator Name <span style="color:red">*</span></label>
                                            <select class="form-control" name="floor_coordinator_empno" id="validationCustom03" style="font-size: 14px;padding:3.72px 15.8px;" required="">
											 <option>Select Floor Coordinator</option>
											<?php
											$floor_no=$_SESSION['floor_no'];		
											$sql = "select * from floor_tbl where floor_no='$floor_no'";
										$floor_co_data = mysqli_query($db, $sql);  
    
										if ($floor_co_data->num_rows > 0) {
										  // output data of each row
										while($row = mysqli_fetch_assoc($floor_co_data)) {
											
											Print "<option value='".$row["floor_coordinator_empno"]."'>".$row["floor_coordinator_name"]."</option>";
											
										}
										} else {
										  Print "<option value=''> No Records Found</option>";
										}
											
											?>
										</select>	
											<div class="invalid-feedback">
                                                Please Select Floor Coordinator Emp Name
                                            </div>
                                        </div>
										<div class="form-group">
                                            <label for="validationCustom04" class="col-form-label">Complaint Location In Floor <span style="color:red">*</span></label>
                                            <input class="form-control" type="text" value="" name="location_in_floor" id="validationCustom04" placeholder="Enter Floor Location" required="">
											<div class="invalid-feedback">
                                                Please Enter Floor Location
                                            </div>
										</div>
										<div class="form-group">
                                            <label for="validationCustom05" class="col-form-label">Complaint Description <span style="color:red">*</span></label>
                                            <textarea class="form-control" type="text" value="" name="complaint_description" id="validationCustom05" placeholder="Enter Complaint details" required=""></textarea>
											<div class="invalid-feedback">
                                                Please Enter complaint description
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
										1. Select complaint Section.<br/> 
										2. Select Floor No..<br/>
										3. Select floor coordinator Emp Name.<br/> 
										4. Enter Complaint Location In Floor.<br/>
										5. Enter Complaint Details.<br/>	
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
