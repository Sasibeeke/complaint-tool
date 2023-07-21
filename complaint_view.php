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
                    <!-- data table start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
							
                                <h4 class="header-title">View Complaints</h4>
								<?php
									if(isset($_SESSION['message']))
									{ 
										echo $_SESSION['message']; 
										unset($_SESSION['message']);
									}
								?>
								
                                <div class="data-tables">
										<?php 
										
										$sql = "select c.complaint_no,s.section_name,c.floor_no,f.floor_coordinator_name,f.floor_coordinator_designation,c.location_in_floor,c.nature_of_complaint,c.complaint_date,c.flag,c.description from complaint_tbl c,floor_tbl f,section_tbl s where c.section_code=s.section_code and c.floor_coordinator_empno=f.floor_coordinator_empno";
										$complaint_data = mysqli_query($db, $sql);  
    
										if ($complaint_data) {?>
									 <table id="dataTable" class="table table-striped table-bordered" style="width:100%">	
                                        <thead class="bg-light text-capitalize">
                                            <tr>
                                                <th>Complaint No</th>
												<th>Section</th>
												<th>Floor</th>
                                                <th>Floor Coordinator Name</th>
                                                <th>Floor Coordinator Designation</th>
												<th>Location in Floor</th>
											<!--	<th>Nature of Complaint</th> -->
												<th>Description</th>
												<th>Complaint Date</th>
												<th>Status</th>
												<th>Action</th>
												
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php
										  // output data of each row
										while($row = mysqli_fetch_assoc($complaint_data)) {
											
											Print "<tr><td>".$row["complaint_no"]."</td>";
											Print "<td>".$row["section_name"]."</td>";
											Print "<td>".$row["floor_no"]."</td>";
											Print "<td>".$row["floor_coordinator_name"]."</td>";
											Print "<td>".$row["floor_coordinator_designation"]."</td>";
											Print "<td>".$row["location_in_floor"]."</td>";
											//Print "<td>".$row["nature_of_complaint"]."</td>";
											Print "<td>".$row["description"]."</td>";
											Print "<td>".$row["complaint_date"]."</td>";
											
											if($row["flag"]==0)
												Print "<td><span class='status-p bg-primary'>Pending</span></td>";
											elseif($row["flag"]==1)
												Print "<td><span class='status-p bg-info'>Process</span></td>";
											elseif($row["flag"]==2)
												Print "<td><span class='status-p bg-success'>Completed</span></td>";
											else
												Print "<td><span class='status-p bg-danger'>Rejected</span></td>";
											
											if($row["flag"]==0){
											Print '<td>
                                                        <ul class="d-flex justify-content-center">
                                                            <li class="mr-3"><a href="edit_complaint_form.php?id='.$row['complaint_no'].'" class="text-secondary"><i class="fa fa-edit" title="Edit"></i></a></li>
															 <li class="mr-3"><a href="#" class="text-secondary" data-toggle="modal" data-target="#exampleModalLong" onClick="myFunction('.$row["complaint_no"].')"><i class="fa fa-tasks" aria-hidden="true" title="Process"></i></a></li>
                                                            <!--<li><a href="#" class="text-danger"><i class="ti-trash" title="Delete"></i></a></li>-->
															
                                                        </ul>
                                                    </td>';
											}else{
												Print '<td></td>';
											}		
											Print '</tr>';		
											
										}
										
																		
										?>
                                      
                                        </tbody>
                                    </table>
									<?php } else {
										  Print "No Records Found";
										}?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- data table end -->
                </div>
            </div>
		    <div class="modal fade" id="exampleModalLong">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Modal title</h5>
							<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
						</div>
						<div class="modal-body">
						<form action="complaint_action.php" method="POST">
							<div class="form-gp">
							
                            <select name="action_taken" id="action_taken" class="form-control" style="font-size: 14px;padding:3.72px 15.8px;">
								<option value=''> Select Action </option>
								<option value="F">Forward Complaint</option>";
								<option value="R">Reject Complaint</option>";
							</select>
							</div>
							
							<div class="form-group">
								<label for="validationCustom05" class="col-form-label">Remarks</label>
								<textarea class="form-control" type="text" value="" name="remark" id="validationCustom05" placeholder="Enter Remark" required=""></textarea>
								<div class="invalid-feedback">
									Please Enter Remark
								</div>
										</div>
						<input type="hidden" autocomplete="off" name="complaint_no" id="complaint_no" value="" class="form-control">
				
							
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
							<button type="submit" name="submit" class="btn btn-primary">Save changes</button>
						</div>
						</form>
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
    <!-- jquery latest version -->
     <?php include 'footer.php';?>
<script>
function myFunction(obj) {
	//alert(obj);
	//console.log("here in console");
    //var URl = window.location.origin; 
    //alert(URl+'/employee/unblockEmp/'+obj);
    document.getElementById("complaint_no").value = obj;
    //document.getElementById("form_id").action = URl+'/employee/unblockEmp/'+obj;
}
</script>
	 
</body>

</html>
