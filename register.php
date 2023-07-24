
<?php include 'header.php';
include 'connection.php';
?>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
   
    <!-- login area start -->
    <div class="login-area login-bg">
        <div class="container">
            <div class="login-box ptb--100">
                <form method="post" action="signup.php">
                    <div class="login-form-head">
                        <h4>Sign up</h4>
                        <p>Hello there, Sign up and Join with Us</p>
                        <br>
                         <?php
        
                    if(isset($_SESSION['message']))
                        { 
                            echo $_SESSION['message']; 
                            //unset($_SESSION['message']);
                        }?>
                    </div>
                    <div class="login-form-body">
                        <div class="form-gp">
                            <label for="exampleInputName1">Full Name</label>
                            <input type="text" name="name" id="exampleInputName1" required>
                            <i class="ti-user"></i>
                            <div class="text-danger"></div>
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" name="email" id="exampleInputEmail1" required>
                            <i class="ti-email"></i>
                            <div class="text-danger"></div>
                        </div>
						<div class="form-gp">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" name="password" id="exampleInputPassword1" required>
                            <i class="ti-lock"></i>
                            <div class="text-danger"></div>
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword2">Confirm Password</label>
                            <input type="password" name="confirmpassword" id="exampleInputPassword2" required>
                            <i class="ti-lock"></i>
                            <div class="text-danger"></div>
                        </div>
						<div class="form-gp">
						 
                           <!-- <select name="role" id="role" class="form-control" style="font-size: 14px;padding:3.72px 15.8px;" onchange="loaddiv(this.value)">-->
							<select name="role" id="role" class="form-control" style="font-size: 14px;padding:3.72px 15.8px;" onchange="loaddiv(this.value)" required>
								<option value=''> Select User Type </option>
								<option value="1">Floor Coordinator</option>
								<option value="2">Section Incharge</option>
								<!--<option value="3">Employee</option>-->
									
							</select>
                        </div>
						<div class="form-gp" id="floor" style="display:none">
						 
                            <select name="floor_no" id="floor_no" class="form-control" style="font-size: 14px;padding:3.72px 15.8px;">
								<option value="">Select Floor</option>
								<option value="0">Ground</option>
								<option value="1">First</option>
								<option value="2">Second</option>
								<option value="3">Third</option>
								<option value="4">Fourth</option>
									
							</select>
                        </div>
						<div class="form-gp" id="incharge" style="display:none">
						 
						 <?php
							$sql = "SELECT section_code, section_name FROM section_tbl";
							//echo $query;die(); 
							$result = mysqli_query($db,$sql);
							if($result){
                                        

						echo '<select class="form-control" style="padding:7px !important;font-size:13px;" name="section_code" id="section_code">';
						echo '<option value="">Select Section</option>';
						while ($row = mysqli_fetch_assoc($result)) {
							$section_code = $row['section_code'];
							$section_name = $row['section_name'];

							echo '<option value="' . $section_code . '">' . $section_name . '</option>';
						}
						echo '</select>';

						}

						  ?>
						</div>
						
                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit" name="submit">Submit <i class="ti-arrow-right"></i></button>
                           
                        </div>
                        <div class="form-footer text-center mt-5">
                            <p class="text-muted">If you have an account? <a href="login.php">Sign in</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- login area end -->

    <?php include 'footer.php';?>
<script>
function loaddiv(id)
 {
	 if(id=='1')
	 {
		
		$("#floor").show();
		$("#incharge").hide(); 
		document.getElementById("section_code").value= '';
	 }
	 else if(id=='2')
	 {
		 $("#incharge").show(); 
		  $("#floor").hide();
		  document.getElementById("floor_no").value='';
	 }
	
 }
</script>	
	
</body>

</html>