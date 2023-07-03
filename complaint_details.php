<?php 
session_start();
include 'connection.php';
$id=$_GET['id'];
$sql = "select c.complaint_no,s.section_name,s.section_code,c.floor_no,f.floor_coordinator_name,f.floor_coordinator_empno,f.floor_coordinator_designation,c.location_in_floor,c.nature_of_complaint,c.complaint_date,c.flag,c.description from complaint_tbl c,floor_tbl f,section_tbl s where c.section_code=s.section_code and c.floor_coordinator_empno=f.floor_coordinator_empno  and c.flag='1' and c.complaint_no='$id'";
  $result = mysqli_query($db, $sql);
  $row = mysqli_fetch_assoc($result);
  $complaint_no = $row['complaint_no'];
  $complaint_date = $row['complaint_date'];
  $section_code = $row['section_code'];
  $section_name = $row['section_name'];
  $floor_no = $row['floor_no'];
  $floor_coordinator_empno = $row['floor_coordinator_empno'];
  $floor_coordinator_name = $row['floor_coordinator_name'];
  $location_in_floor = $row['location_in_floor'];
  $nature_of_complaint = $row['nature_of_complaint'];
  $flag = $row['flag'];
  $remarks = $row['description']; 


?>
<?php include 'header.php';?>

<body>
 
    <div class="page-container">
       <?php include 'sidebar_incharge.php';?>
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
                               <div class="invoice-area">
                                    <div class="invoice-head">
                                        <div class="row">
                                            <div class="iv-left col-6">
                                              <span >Complaint No: <?php echo $complaint_no;?></span>
                                            </div>
                                            <div class="iv-right col-6 text-md-right">
                                               <span>Complaint Date:<?php echo $complaint_date;?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="invoice-address">
                                            <h5>Section Name </h5>
                                            <h5>Floor No</h5>
                                            <h5>Floor Coordinator Employee No</h5>
                                            <h5>Floor Coordinator Employee Name</h5>
                                            <h5>Location In Floor</h5>
                                            <h5>Nature Of Complaint</h5>
                                            <h5>Status</h5>
                                            </div>
                                        </div>
                                    <div class="col-md-6 ">
                                    <div class="invoice-address">
                                      <h5><?php echo $section_name;?></h5>
                                       <h5><?php echo $floor_no;?></h5>
                                  <h5><?php echo $floor_coordinator_empno;?></h5>
                                  <h5><?php echo $floor_coordinator_name;?></h5>
                                  <h5><?php echo $location_in_floor;?></h5>
                                   <h5><?php echo $nature_of_complaint;?></h5>
                               <h5><?php if($flag=='1'){
                                  echo 'Pending';
                               };?></h5>
                                     </div>
                                        </div>
                                    </div>
                                      <hr>
                                    <div class="row">
                                  <div class="col-md-12">
                                    <strong><h5>Complaint Description:</h5></strong><br>
                                    <p><?php echo $remarks;?></p>
                                  </div>
                                </div>
                               
                                </div>



                                <div class="text-md-center" style="margin-right: 325px;">
                                  <?php if($flag=='1'){?>
                                    <a href="#" class="invoice-btn"> <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#form">
                                    Action
                                  </button> </a>
                                <?php }?>
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <button type="button" class="invoice-btn btn btn-info" onclick="goBack()">Back</button>
                                </div>
                                 </div>
                        </div>
                    </div>
                    <!-- data table end -->
                    
                    
                </div>
            </div>
        </div>


       <!---modal form code ---->
       <div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header border-bottom-0">
            <h5 class="modal-title" id="exampleModalLabel">Action </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
      <form action="update_complaint.php" method="post">
        <div class="modal-body">
          <div class="form-group">
            <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
        <label class="col-form-label">Action Taken</label>
            <select class="form-control" style="padding:7px !important;font-size:13px;" name="action_taken" required>
              <option value="">Select</option>
              <option value="C">Completed</option>
              <option value="R">Rejected</option>
                </select>
            </div>
          <div class="form-group">
            <label class="col-form-label">Remarks</label>
            <textarea class="form-control form-control-lg mb-3" name="remarks" rows="3" placeholder="Large textarea" required></textarea>
          </div>
          
        </div>
        <div class="modal-footer border-top-0 d-flex justify-content-center">
          <button type="submit" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
       <!----ends here ---------> 

        <!-- main content area end -->
        <?php include 'footer_bar.php'; ?>
    </div>
    <?php include 'footer.php';?>
<script>
function goBack() {
    window.location.href = 'pending_complaint_view.php';
}
</script>

</body>

</html>
