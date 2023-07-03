<?php 
    session_start();
    include 'connection.php';
   //echo $_SESSION['error'];die();
?>


<?php include 'header.php';?>

<body>
  
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- login area start -->
    <div class="login-area login-bg">
        <div class="container">
            <div class="login-box ptb--100">

                <form action="signin.php" method="post">
                    <div class="login-form-head">
                        <h4>Sign In</h4>
                        <p>Hello there, Sign in and start managing  your Complaint monitoring tool</p>
                       <br>     
                        <?php
        
                    if(isset($_SESSION['error']))
                        { 
                            echo $_SESSION['error']; 
                            unset($_SESSION['error']);
                        }?>
                    </div>

                    <div class="login-form-body">
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
                        <div class="row mb-4 rmber-area">
                            <div class="col-6">
                               
                            </div>
                            <div class="col-6 text-right">
                                <a href="forgot-password.php">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit" name="submit">Submit <i class="ti-arrow-right"></i></button>
                        </div>
                        <div class="form-footer text-center mt-5">
                            <p class="text-muted">Don't have an account? <a href="register.php">Sign up</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- login area end -->

   <?php include 'footer.php';?>
</body>

</html>