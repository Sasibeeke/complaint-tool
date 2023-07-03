<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employee extends MX_Controller {
	function __construct()
	{	
		parent::__construct();
		$CI =& get_instance();
		parent::user_session_check(); //new added on 13-oct-2020		
   	 	$this->load->model('employee_model');
		$this->fromemailid='noreply.attendance@gov.in';
		$this->load->library('session');
		$this->data = array();
		$this->u_id	= $this->nehbr_auth->get_user_id();
		$this->u_name= $this->nehbr_auth->get_username();
		$this->load->library('pagination');
		$this->nb_lib->no_cache();
		$this->DB1=$this->load->database('vi',TRUE);
		$this->load->model('register/organization_model');
		
		$this->load->model('manager/empdesig_model');
		$this->load->model('manager/orgdept_model');
		$this->load->model('manager/orgbuilding_model');
		$this->load->library('form_validation');
		$this->form_validation->CI=& $this;
		$this->load->library('email');
		$this->org_key	= $this->session->userdata('org_key');
		$this->u_group_id= $this->nehbr_auth->get_group_id();
		$this->load->helper("random");
		$this->load->helper('cookie');	
		$this->grp_id = $this->nehbr_auth->get_group_id();
		$status1=$this->nehbr_auth->get_reset_status();
	// new added for loc user 30-04-19
		$this->loc_id = $this->nehbr_auth->get_loc_id();
	// new added for loc user 30-04-19	
		date_default_timezone_set('Asia/Kolkata');
		
		if(isset($status1) == true && $status1=='r')
		{
			 $this->grp_id = $this->nehbr_auth->get_group_id();
					
			if($this->grp_id==1 || $this->grp_id==2)	
					redirect('/auth/change_password');
			if($this->grp_id==3 || $this->grp_id==4)
					redirect('/auth/auth_nodal/change_password');
		}
	}
	
	//Added by sandeep on 1-12-2020
    private function aec_decrypt2($password){ 
		$ps = NULL;
		if($password){
			$fs = explode('&',$password);
			$encrypted = base64_decode($fs[1]); // data_base64 from JS
			$iv        = base64_decode($fs[2]);   // iv_base64 from JS
			$key       = base64_decode($fs[3]);
	
			$opass1 = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_128, $key, $encrypted, MCRYPT_MODE_CBC, $iv ), "\t\0 " );
			$ps = $this->security->xss_clean($opass1);
		}
		return $ps;
	}	
	// end here
	
	function check_empId($empId){
		$emp=$this->employee_model->get_employeelistTransfer($empId);
		if(count($emp)>0){
			return TRUE;
		}else{
			$this->form_validation->set_message('check_empId', 'Employee Is Not Marked For Transfer');
			return FALSE;
		}
	}	
	
	public function check_orgid($str)
	{
		if ($str != $this->org_key)
		{
			$this->form_validation->set_message('check_orgid', 'Select Your Organization');
			return FALSE;
		}else{
			return TRUE;	
		}
		
	}	
	
	function transferActiveEmployee($emp_id=NULL,$active_status=NULL){
		$this->form_validation->set_rules("reason", "Transfer Reason", "required|trim|xss_clean");				
		$this->form_validation->set_error_delimiters('<div style="color:#FF0000;">', '</div>');
		if($emp_id==NULL && $active_status==NULL)
		{
			$emp_id=$this->input->post('modal_emp_id');
			$active_status=$this->input->post('modal_active_status');
		}
		$transferLog=$this->employee_model->insert_transferlog($emp_id);
		if (isset($_POST) && $this->form_validation->run() == false) {					
			redirect('/manager/orgdept/index');			
		}
		else
		{
			$checkActiveStatus=$this->employee_model->checkActiveEmpStatus($emp_id,$active_status);		
			if($checkActiveStatus>0 && $this->employee_model->transferActiveEmp($emp_id,$active_status)){
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
											<i class="fa fa-check"></i>
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
											<b>Success!</b> Employee Transfer Out Successful.
										</div>');
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Active Employee',
								'action_taken'=>'Transfer',
								'status'=>'success'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail						
				if($active_status=='N')
					redirect('employee/not_marking_from_01Aug2018');
				else
					redirect('employee/index');
				
			}else if($checkActiveStatus>0){
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
											<i class="fa fa-check"></i>
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
											<b>Success!</b> Employee Transfer Out Not successful.
										</div>');
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Active Employee',
								'action_taken'=>'Transfer',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail						
				if($active_status=='N')
					redirect('employee/not_marking_from_01Aug2018');
				else
					redirect('employee/index');

			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
											<i class="fa fa-check"></i>
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
											<b>Sorry!</b> Unauthorized User.
										</div>');
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Active Employee',
								'action_taken'=>'Transfer',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail						
				if($active_status=='N')
					redirect('employee/not_marking_from_01Aug2018');
				else
					redirect('employee/index');
			}
		}		
	}
	
		
	function auto_division_org_select_call()
    {
		if (isset($_GET['term']) && isset($_GET['org_id'])){
		  $q = strtolower($_GET['term']);
		  $org_id = strtolower($_GET['org_id']);
		  $this->employee_model->get_department($q,$org_id);
		}else if (isset($_GET['term'])){
		  $q = strtolower($_GET['term']);
		  $this->employee_model->get_department($q);
		}
	}
	
	
	function transferEmpProcess($empId=NULL){		
		$data['org_names']=$this->organization_model->get_org_master();
		$data['org_id'] = $this->org_key;
		$data['org_division']= $this->employee_model->orgdivision();
		$this->form_validation->set_rules('org_dept_id', 'Department', 'required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('desig_id', 'Designation', 'required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('office_loc', 'Office Location', 'required|xss_clean|callback_char_check');
		
		if ($this->form_validation->run() == false) {	
				//new added for transfer IN for location user 30-04-2019
				if($this->loc_id!= NULL)
				{
					$data['loc_id'] = $this->loc_id;
					$data['loc_name']=$this->employee_model->loc_name_by_loc_id($this->loc_id);
				}
			   //end new added for transfer IN for location user 30-04-2019

				$data['query'] = $this->employee_model->view_employee_transfer($empId);											
				$this->load->view('../../__inc/usr_header',$data);	
				$this->load->view('edit_transfer_employee');			
		}else{			
			if($this->employee_model->update_employee_transfer()){
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee Transfer In Successful.
                                    </div>');

				$this->load->view('../../__inc/usr_header');								
				redirect('/employee/transferList');	
			}else {
				$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-close"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										 <b>Sorry!</b> Please try again. </div>');
				$this->load->view('../../__inc/usr_header');
				redirect('/employee/transferList');			
			}
		}
	}

	function transferList()
	{		
		$this->form_validation->set_rules('empId', 'Employee Id', 'required|xss_clean|callback_check_empId|callback_char_check');
		if ($this->form_validation->run() == false) 
		{
			$this->load->view('../../__inc/usr_header');	
			$this->load->view('transfer_search_employee');
		}else{		
	 		$data['geo_designation']=$this->employee_model->geo_designation();
			$data['officelocation']=$this->employee_model->get_officelocation();	
			$empId=$this->db->escape_str($this->input->post('empId'));		
			$data['query'] = $this->employee_model->get_employeelistTransfer($empId);			
			$this->load->view('../../__inc/usr_header',$data);			
			$this->load->view('employeeTransfer');
		}
	}
	
	function transferOutList()
	{
		$data['geo_designation']=$this->employee_model->geo_designation();
		$data['officelocation']=$this->employee_model->get_officelocation();	
		$data['results'] = $this->employee_model->get_employeeTransferOutlist();
		$this->load->view('../../__inc/usr_header',$data);			
		$this->load->view('transferOutEmployee');
	}
	
	function transferEmployee($emp_id=NULL)
	{
		$transferLog=$this->employee_model->insert_transferlog($emp_id);
		$checkInActiveStatus=$this->employee_model->checkInActiveStatus($emp_id);		
		if($checkInActiveStatus>0 && $this->employee_model->transfer($emp_id)){
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee Transfer Out Successful.
                                    </div>');
			//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Inactive Employee',
								'action_taken'=>'Transfer',
								'status'=>'success'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail						
			redirect('employee/inactiveList');
			
		}else if($checkInActiveStatus>0){
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee Transfer Out Not Successful.
                                    </div>');
			//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Inactive Employee',
								'action_taken'=>'Transfer',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail						
			redirect('employee/inactiveList');
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Sorry!</b> Unauthorized User.
                                    </div>');
									//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Inactive Employee',
								'action_taken'=>'Transfer',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail
			redirect('employee/inactiveList');
		}	
	}
	
	function inActiveDelete($emp_id=NULL)
	{		
		$checkInActiveStatus=$this->employee_model->checkInActiveStatus($emp_id);
		if($checkInActiveStatus>0 && $this->employee_model->inAactivateDel($emp_id))
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee Deleted successfully.
                                    </div>');
			redirect('employee/inactiveList');
			
		}else if($checkInActiveStatus>0){
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee not Deleted successfully.
                                    </div>');
			redirect('employee/inactiveList');
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Sorry!</b> Unauthorized User.
                                    </div>');
			redirect('employee/inactiveList');
		}	
	}
	
	function activate($emp_id=NULL)
	{
		$checkInActiveStatus=$this->employee_model->checkInActiveStatus($emp_id);
		if($checkInActiveStatus>0 && $this->employee_model->activate($emp_id))
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee Activated successfully.
                                    </div>');
			redirect('employee/inactiveList');
			
		}else if($checkInActiveStatus>0){
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee not Activated successfully.
                                    </div>');
			redirect('employee/inactiveList');
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Sorry!</b> Unauthorized User.
                                    </div>');
			redirect('employee/inactiveList');
		}	
	}
	
	function detailInactive($emp_id=NULL)
	{
		$this->data = array();
		$this->u_id	= $this->nehbr_auth->get_user_id();
		$this->u_name= $this->nehbr_auth->get_username();
		//$data['organization_types']=$this->employee_model->get_org_types();
		$data['officelocation']=$this->employee_model->get_officelocation();		
		$checkInActiveStatus=$this->employee_model->checkInActiveStatus($emp_id);	
			
		if($emp_id!=NULL && $checkInActiveStatus>0)
		{
			$data['organization_data']=$this->employee_model->get_detail_inactivate($emp_id);
			$this->load->view('../../__inc/usr_header',$data);	
			$this->load->view('employee/detailInactive');
			
		}
		else
		{
			redirect('employee/index');			
		}
	}
	
	function inactiveList($offset = 0)
	{
		//For location user only---start
		$this->grp_id = $this->nehbr_auth->get_group_id(); 
		$this->loc_id = $this->nehbr_auth->get_loc_id();
		$loc_id= $this->loc_id;
		//--------end

		$searchValue1	=	$this->input->post('searchValue');
		if($searchValue1)
		{
			$this->session->set_userdata('searchValue1', $searchValue1);
		}
		$searchValue	=	$this->session->userdata('searchValue1');
		
		if (preg_match("/([%\$'?;{}#@<>\*]+)/",$searchValue1))
		{	
			$this->session->set_flashdata('message', 'Invalid Characters in Search string');
			redirect("/employee/listVerified");
		}
		else{
			$data['geo_designation']=$this->employee_model->geo_designation();
			$data['officelocation']=$this->employee_model->get_officelocation();	
			$config= array();
			$config['base_url']    = base_url() . "employee/inactiveList";
			$config['total_rows']  = $this->employee_model->inActiveRecord_count($searchValue1,$loc_id);
			$config['per_page']    = 10;
			$config['uri_segment'] =3;
			$choice = $config['total_rows'] / $config["per_page"];
			$this->pagination->initialize($config);        
			$page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
			$data['off_set'] = $page;        
			$data['query'] = $this->employee_model->get_employeelistInActivate($config['per_page'], $page,$searchValue1,$loc_id);
			$data['links']   = $this->pagination->create_links();
			$this->load->view('../../__inc/usr_header',$data);			
			$this->load->view('employeeInActive');
		}	
	}
	
	function clearActivate()
	{
		$this->session->unset_userdata('searchValue');
			redirect('employee/index');
	}
	
	function clearSearchReject()
	{
		$this->session->unset_userdata('listRejectSearchValue');
			redirect('employee/listRejected');
	}
	
	function clearSearchVerify()
	{
		$this->session->unset_userdata('search_emp_name');
		redirect('employee/listVerified');	
	}
	
	function clearInActivate()
	{
		$this->session->unset_userdata('searchValue1');
		redirect('employee/inactiveList');	
	}
	
	public function clearNew()
	{
		$this->session->unset_userdata('listnewSearchValue');
		$this->session->unset_userdata('search_emp');
		redirect('employee/listNew');	
	}
	
	function clearNewAdmin()
	{
		$this->session->unset_userdata('search_org_id');
		$this->session->unset_userdata('listnewSearchValueAdmin');
		redirect('employee/adminlistNew');	
	}
	
	public function send_sms_msg($to=NULL,$msg=NULL,$template_id=NULL)
	{
		/* //commented for automatic audit 10-may-2023
		if($to!=NULL)
		{
			$this->load->helper('url');
			$msg=$msg;			
			// $user = 'aebas.auth';
			// $pass = 'Mkt*24nK';
			// $sendID = 'NICSMS';
			$user = $this->config->item(sms_user);
			$pass = $this->config->item(sms_password);
			$sendID = $this->config->item(sms_sendID);
			
			$mobile = $to;
			$text= urlencode($msg);
			$text = $text." NICSI";
			
			$url=$this->config->item(sms_url);
			$dlt_id=$this->config->item(dlt_id);
			$dlt_template_id = $template_id;
			$data = "username=$user&pin=$pass&message=$text&mnumber=91$mobile&signature=$sendID&dlt_entity_id=$dlt_id&dlt_template_id=$dlt_template_id";
			
			$ch = curl_init();
			// set URL and other appropriate options
			//curl_setopt($ch, CURLOPT_URL, "http://smsgw.sms.gov.in/failsafe/HttpLink?username=$user&pin=$pass&message=$text&mnumber=91$mobile&signature=$sendID");
			// Changes for SMS Content Template
			
			
			//curl_setopt($ch, CURLOPT_URL, "http://smsgw.sms.gov.in/failsafe/HttpLink?username=$user&pin=$pass&message=$text&mnumber=91$mobile&signature=$sendID&dlt_entity_id=$dlt_id&dlt_template_id=$dlt_template_id");
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,1);
			curl_setopt($ch, CURLOPT_CAINFO,'/etc/pki/tls/certs/smsgw.sms.gov.in.crt');
			if (curl_errno($ch)) echo 'Curl error: ' . curl_error($ch);
			else $curl_output =curl_exec($ch);
			curl_close($ch); 

			//$result = curl_exec($ch);
			//curl_close($ch);			
		}
		else
		{
			redirect('/');
		}
	*/	//end commented for automatic audit 10-may-2023		
	}
	
	public function check_email($str)
	{
		$emp_id=$this->input->post("emp_id");
		$status= $this->employee_model->check_email($str,$emp_id);
		
		if($status==1)
		{
		  $this->form_validation->set_message('check_email', 'Email Already exist');
		  return false;
		}
		else
		{
			return true;		
		}
	}	
		
	public function check_mobile($str)
	{
		$emp_id=$this->input->post("emp_id");
		$status= $this->employee_model->check_mobile($str,$emp_id);
		if($status==1)
		{
		  $this->form_validation->set_message('check_mobile', 'Mobile No Already exist');
		  return false;
		}
		else
		{
			return true;		
		}
	}
	
	public function check_DOB($str)
	{
		$date = date('d-m-Y');
		$str=date($str);
		if(strtotime($str)>=strtotime($date))
		{
			$this->form_validation->set_message('check_DOB', 'Birth Date can not be greater than current date');
			return false;
		}
		else
		{
			return true;	
		}
	}	
	
	function editNewAdmin($empId=NULL)
	{
		//new changes added for audit chnages 07-june-2023
		if(!preg_match("/(^[0-9]{8})$/",$empId)){
			$this->nehbr_auth->logout();
			redirect("/app/");
		}//new changes added for audit chnages 07-june-2023
		if($this->input->post('emp_cat')=='GOV')
		{
			$this->form_validation->set_rules('desig_id', 'Designation', 'required|xss_clean|numeric|min_length[6]|max_length[6]|callback_char_check');
		}		
		$this->form_validation->set_rules("emp_id", "Employee", "trim|required|xss_clean|numeric|min_length[8]|max_length[8]|callback_char_check");
		$this->form_validation->set_rules("org_id", "Organization Name", "trim|required|xss_clean|numeric|min_length[6]|max_length[6]|callback_char_check");
		$this->form_validation->set_rules("aadhaar", "Aadhaar", "trim|required|xss_clean|min_length[12]|max_length[12]|callback_char_check");
		$this->form_validation->set_rules('emp_name', 'Employee Name', 'trim|required|xss_clean|callback_char_check1');
		$this->form_validation->set_rules("org_emp_code", "org emp code", "trim|xss_clean|callback_char_check");
		$this->form_validation->set_rules('org_dept_id', 'Department', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('office_loc', 'Office Location', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('emp_dob', 'DOB', 'trim|xss_clean|callback_check_DOB');
		$this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean|alpha|callback_char_check');
		$this->form_validation->set_rules('emp_mobile', 'Mobile', 'trim|required|xss_clean|numeric|min_length[10]|max_length[10]|callback_char_check|callback_check_mobile');
		$this->form_validation->set_rules('emp_mobile1', 'Mobile', 'trim|required|xss_clean|numeric|min_length[10]|max_length[10]|callback_char_check|callback_check_mobile');
		$this->form_validation->set_rules('emp_mail', 'Email', 'trim|required|xss_clean|callback_check_email');			
		$this->form_validation->set_rules('emp_cat', 'Emp Type', 'trim|required|xss_clean|alpha|callback_char_check');
		/* Validation for third tab 28-05-2015 */
		$this->form_validation->set_rules("grade_pay", "Grade Pay", "trim|xss_clean|numeric|callback_char_check|callback_positive_check");
		$this->form_validation->set_rules("basic_pay", "Basic Pay", "trim|xss_clean|numeric|callback_char_check|callback_positive_check");
		$this->form_validation->set_rules("reporting_id", "Reporting ID", "trim|xss_clean|numeric|callback_char_check|callback_is_numeric_check|callback_Check_reporting_id");
		$this->form_validation->set_rules("reporting_name", "Reporting Name", "trim|xss_clean|callback_char_check1");
		/* End of validations of third tab*/
		
		$data['org_names']=$this->organization_model->get_org_master();			
		$data['org_name']=$this->input->post('org_id');		
		$data['org_division']= $this->employee_model->orgdivision_admin();	
		
	//new change on 13-oct-2020 audit  
		if (!empty($_FILES['image']['name']))
		{
   
			$filename_lenght= count(explode('.',$_FILES['image']['name'])); 
			if($filename_lenght > 2)
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name contains multiple extensions');
			}					
			elseif (preg_match("/([%\$'?;{}#@<>\*]+)/", $_FILES['image']['name']))
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name should be alphanumeric only');
			} 
			else{ 	
				if($this->validate_upload('image',$IsResize=1))
				{
					$image['content'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
					$image['type']=$_FILES['image']['type'];
				}
				else
				{
					if($this->upload_result!='')
					{
						$this->form_validation->set_rules('image', ' Input File', 'required');
						$this->form_validation->set_message('required', $this->upload_result);
					}
				}
			} 
		}else{
			$this->form_validation->set_rules('image', ' Input File', 'required');
		} 
	//end new change on 13-oct-2020 audit				
		
		$data['aadhar_status']=$this->employee_model->get_aadharStatus(); // get aadhaar Remarks from emp qc mac
		$data['demographStatus']=$this->employee_model->checkDemographStatus($empId);		
			
		if ($this->form_validation->run() == false) 
		{	
			$data['query'] = $this->employee_model->view_employee_new_admin($empId);
			$this->load->view('../../__inc/usr_header',$data);	
			$this->load->view('edit_new_employee_admin');			
		}else{
			if($this->input->post('emp_mobile')!=$this->input->post('emp_mobile1'))
			{
				$mobile=$this->input->post('emp_mobile1');
				$mobile_new=$this->input->post('emp_mobile');
				$msg='Your Mobile number no has been changed to'.$mobile_new;
				$sub='Mobile Changed';
				$this->send_sms_msg($mobile,$msg, $template_id="1107160818645972954"); //commented for automatic audit 25-sept-2020
			}
			if($this->input->post('emp_mail')!=$this->input->post('emp_mail1'))
			{
				$email=$this->input->post('emp_mail1');
				$email_new=$this->input->post('emp_mail');
				$msg='Your Email has been changed to'.$email_new;
				$sub='Email Changed';	  
				$this->send_email($email,$sub,$msg); //commented for automatic audit 25-sept-2020
			}
			if($this->employee_model->update_employeeNewAdmin())
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
											<i class="fa fa-check"></i>
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
											<b>Success!</b> Employee data updated successfully.
										</div>');
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Edit Employee',
								'action_taken'=>'Edit',
								'status'=>'success'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail	

				$this->load->view('../../__inc/usr_header');								
				redirect('/employee/adminlistNew');	
			}else {
					$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
											<i class="fa fa-close"></i>
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
											 <b>Error!</b> Please try again. </div>');
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Edit Employee',
								'action_taken'=>'Edit',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail
					$this->load->view('../../__inc/usr_header');
					redirect('/employee/adminlistNew');			
			}		
		}
	}
	
	public function ajax_activeStatus($id)
	{
		$id= $this->input->post('status');
		if ($id) 
		{
			$result= $this->employee_model->get_setStatus($id); 
			if($result['0']['active_status']=='Y'){
				echo 'On';	
			}else{
				echo 'Off';	
			}			
		}	
	} 
  //new changes added for csrf ajax requst 21-10-2020
	function org_id_select_call()
	{	      
		$arrFinal=array();
		//Checking so that people cannot go to the page directly.
		if (isset($_POST) && isset($_POST['org_name'])) 
		{
			$data = array();
			$org_name = $_POST['org_name'];
			$arrorg = $this->employee_model->_getAllWhere_order('ms_emp_desig','org_id',$org_name,'designation');
			$data['result'][] = '<option value="">- Select -</option>';
				
			if($arrorg)
			{
				foreach ($arrorg as $cities) 
				{
					//echo '<option value="'.$cities->geo_desig_id.'">'.$cities->designation.'</option>';
					$data['result'][] = '<option value="'.$cities->geo_desig_id.'">'.$cities->designation.'</option>';
				}
			}
		}
			$data['token_name']= $this->security->get_csrf_token_name();
			$data['token_value']= $this->security->get_csrf_hash();
			echo json_encode($data); 		
	}
	
	function org_id_select_call1()
	{	      
		$arrFinal=array();
		//Checking so that people cannot go to the page directly.
		if (isset($_POST) && isset($_POST['org_name'])) 
		{
			$data = array();
			$org_name = $_POST['org_name'];
			$arrorg = $this->employee_model->_getAllWhere_order1('geo_designation','designation');
			$data['result'][] = '<option value="">- Select -</option>';
				
			if($arrorg)
			{
				foreach ($arrorg as $cities) 
				{
					//echo '<option value="'.$cities->desig_id.'">'.$cities->designation.'</option>';
					$data['result'][] = '<option value="'.$cities->desig_id.'">'.$cities->designation.'</option>';
				}
			}
		}
			$data['token_name']= $this->security->get_csrf_token_name();
			$data['token_value']= $this->security->get_csrf_hash();
			echo json_encode($data); 
	}
		
	function office_loc_select_call()
	{
		$arrFinal=array();
		//Checking so that people cannot go to the page directly.
		if (isset($_POST) && isset($_POST['org_name'])) 
		{
			$data = array();
			$org_name = $_POST['org_name'];
			$arrorg = $this->employee_model->_getAllWhere_order('ms_org_building','org_id',$org_name,'build_name');
			if($arrorg)
			{
				$data['result'][] = '<option value="">- Select -</option>';
				foreach ($arrorg as $cities) 
				{
					$data['result'][] = '<option value="'.$cities->loc_id.'">'.$cities->build_name.'</option>';
				}
			}
		}
			$data['token_name']= $this->security->get_csrf_token_name();
			$data['token_value']= $this->security->get_csrf_hash();
			echo json_encode($data); 		
	}
//new changes added for csrf ajax requst 21-10-2020	
	function _create_captcha()
	{
		return site_url().$this->config->item('cool_captcha_folder', 'nehbr_auth').'/captcha.php';
	}
	
	function _check_captcha($code)
	{
		session_start();
		if($_SESSION['captcha'] != $_POST['captcha']){
			$this->form_validation->set_message('_check_captcha', 'The Confirmation Code is wrong.');
			return FALSE;
		}	
		return TRUE;
	}
	
	function get_departments_employee()
	{
		if (isset($_GET['term'])){
			$q = strtolower($_GET['term']);
			print_r($this->employee_model->get_departments_employee($q));
		}
	}
	
	public function validate_upload($field,$IsResize=0)
	{		
        $config['upload_path'] = 'xyz';
        $config['allowed_types'] = 'jpg|jpeg';
        $config['max_size']    = '150'; //new change on 13-oct-2020
        $this->load->library('my_upload', $config);
        if ( ! $this->my_upload->do_upload($field))
        {
            $this->upload_result = $this->my_upload->display_errors();
            return false;
        }
        else
        {
            $data = array('upload_data' => $this->my_upload->data());			
			//print_r($data);
            $this->upload_result = '';
       
			//	image resize start
			if($IsResize)
			{
				$config_img['image_library'] = 'gd2';
				$config_img['source_image']	= $data['upload_data']['image_tmp_name'];
				$config_img['create_thumb'] = FALSE;
				$config_img['maintain_ratio'] = TRUE;
				$config_img['width']	= 120;
				$config_img['height']	= 150;

				$this->load->library('image_lib', $config_img); 

				if ( ! $this->image_lib->resize())
				{
					$this->upload_result = $this->image_lib->display_errors();	
					return false;
				}
			}
			// image resize end
			 return true;
        }
	}
	
	public function index($offset = 0)
	{	if($this->nehbr_auth->is_logged_in()){
		//For location user only---start
		$this->grp_id = $this->nehbr_auth->get_group_id(); 
		$this->loc_id = $this->nehbr_auth->get_loc_id();
		$loc_id= $this->loc_id;
		//--------end

		$random_no=hash_random();
		$cookie = array(
		'name'   => 'random',
		'value'  => $random_no,
		'expire' => time()+86500,
		'path'   => '/',
		'HttpOnly'=>TRUE
		);
		set_cookie($cookie);		 
		$data['random']=$random_no;	
		$searchValue	=	$this->input->post('searchValue');
		if($searchValue)
		{
			$this->session->set_userdata('searchValue', $searchValue);
		}
		$searchValue	=	$this->session->userdata('searchValue');		
		if (preg_match("/([%\$'?;{}#@<>\*]+)/",$searchValue))
		{	
			$this->session->set_flashdata('message', 'Invalid Characters in Search string');
			redirect("/employee");
		}
		else{
			$data['geo_designation']=$this->employee_model->geo_designation();
			$data['officelocation']=$this->employee_model->get_officelocation();	
			$config= array();
			$config['base_url']    = base_url() . "employee/index";
			//change for loc user
			$config['total_rows']  = $this->employee_model->record_count($searchValue,$loc_id);
			$config['per_page']    = 10;
			$config['uri_segment'] =3;
			$choice = $config['total_rows'] / $config["per_page"];
			$this->pagination->initialize($config);        
			$page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
			$data['off_set'] = $page;
			//change for loc user
			$data['query'] = $this->employee_model->get_employeelist($config['per_page'], $page,$searchValue,$loc_id);
			$data['links']   = $this->pagination->create_links();
			$this->load->view('../../__inc/usr_header',$data);			
			$this->load->view('employeeActive');	
		}
		}else{
				redirect("/auth/login1");
		}
	}

	public function username_check($str)
	{
		$rest = substr($str, 0, 1); // returns "d"
		if ($rest == 0 )
		{
			$this->form_validation->set_message('username_check', 'Aadhaar number must not start with zero');
			return FALSE;
		}
		else if($rest == 1)
		{
			$this->form_validation->set_message('username_check', 'Aadhaar number must not start with One');
			return FALSE;;
			
		}else{
			return TRUE;	
		}
	}
			
	function ajax_multi_select_call() 
	{  
		$arrFinal=array();
        //Checking so that people cannot go to the page directly.
		if (isset($_POST) && isset($_POST['state'])) 
		{
            $state = $_POST['state'];
            $arrCities = $this->employee_model->_getAllWhere('geo_district','scode',$state);
			if($arrCities)
			{				
				foreach ($arrCities as $cities) 
				{				
					echo '<option value="'.$cities->dcode.'">'.$cities->district.'</option>';
				}
            //Using the form_dropdown helper function to get the new dropdown.
			}else{ print('No District Found');}
        } else {
           // redirect('site'); //Else redire to the site home page.
        }
    }
//Multi Select END///////////////////////////////	

	public function listNew($offset=0)
	{
		$data['geo_designation']=$this->employee_model->geo_designation();
		$data['officelocation']=$this->employee_model->get_officelocation();		
		$this->session->unset_userdata('search_emp');
		$searchValue='';
		$data['regorg_search'] = $searchValue;
		$config= array();
        $config['base_url']    = base_url() . "employee/listNew";
        $config['total_rows']  = $this->employee_model->record_countnew($searchValue);
        $config['per_page']    = 10;
        $config['uri_segment'] =3;
        $choice = $config['total_rows'] / $config["per_page"];		
		$random_no=hash_random();
		$cookie = array(
		'name'   => 'random',
		'value'  => $random_no,
		'expire' => time()+86500,
		'path'   => '/',
		'HttpOnly'=>TRUE
		);
		set_cookie($cookie);		 
		$data['random']=$random_no;		
        $this->pagination->initialize($config);        
        $page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
        $data['off_set'] = $page;        
        $data['results'] = $this->employee_model->get_employeelistNew($config['per_page'], $page,$searchValue);
        $data['links']   = $this->pagination->create_links();	
        $this->load->view('../../__inc/usr_header', $data);	
		$this->load->view('employeeNewReg');			
	} 
	
	public function SearchlistNew($offset=0)
	{	
		$data['geo_designation']=$this->employee_model->geo_designation();
		$data['officelocation']=$this->employee_model->get_officelocation();
		if($this->input->post('searchValue')!='')
		{
			$searchValue=$this->input->post('searchValue');
			$this->session->set_userdata('search_emp',$this->input->post('searchValue'));
		} 
		else
		{				
			$searchValue=$this->session->userdata('search_emp');
					
		}
		$data['regorg_search'] = $searchValue;
		
		if (preg_match("/([%\$'?;{}#@<>\*]+)/",$searchValue))
		{	
			$this->session->set_flashdata('message', 'Invalid Characters in Search string');
			redirect("/employee/listNew");		
		}
		else
		{
			$config= array();
			$config['base_url']    = base_url() . "employee/SearchlistNew";
			$config['total_rows']  = $this->employee_model->record_countnew($searchValue);
			$config['per_page']    = 10;
			$config['uri_segment'] =3;
			$choice = $config['total_rows'] / $config["per_page"];
			
			$random_no=hash_random();
			$cookie = array(
			'name'   => 'random',
			'value'  => $random_no,
			'expire' => time()+86500,
			'path'   => '/',
			'HttpOnly'=>TRUE
			);
			set_cookie($cookie);
			 
			$data['random']=$random_no;		
			$this->pagination->initialize($config);        
			$page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
			$data['off_set'] = $page;        
			$data['results'] = $this->employee_model->get_employeelistNew($config['per_page'], $page,$searchValue);
			$data['links']   = $this->pagination->create_links();			
			$this->load->view('../../__inc/usr_header', $data);	
			$this->load->view('employeeNewReg');
		}
	} 
	
	function set_session()
	{
		$id=$this->input->post("id");
		$hex=$this->input->post("hex");
		$random=get_cookie("random");
		$status=hash_random_check($hex,$id,$random);

		if($status)
		{         
			$this->session->set_userdata('edit_id',$id);
		}
		else{
			echo "wrong";
		}
	}
	
	public function adminlistNew($offset=0)
	{		
		if($this->session->userdata('group_id')!=1){ redirect(base_url());}	
		$data['geo_designation']=$this->employee_model->geo_designation();
		$data['organizations']=$this->employee_model->get_organizations();
		$data['officelocation']=$this->employee_model->get_officelocation();
		$searchStr=$this->input->post('searchValue');
		$org_id=$this->input->post('org_id');		
		$this->session->unset_userdata('listnewSearchValueAdmin');
		$this->session->unset_userdata('search_org_id');
		
		
		$data['searchValue'] = $this->session->userdata('listnewSearchValueAdmin');
		$data['search_org_id'] = $this->session->userdata('search_org_id');
		$searchValue = $data['searchValue'];
		$config= array();
        $config['base_url']    = base_url()."employee/adminlistNew";   
        $config['total_rows']  = $this->employee_model->admin_record_countnew($searchValue,$data['search_org_id']);
      	$config['per_page']    = 10;
        $config['uri_segment'] =3;
        $choice = $config['total_rows'] / $config["per_page"];
        $this->pagination->initialize($config);        
        $page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
        $data['off_set'] = $page;      
        $data['results'] = $this->employee_model->admin_get_employeelistNew($config['per_page'], $page,$searchValue,$data['search_org_id']);
        $data['links']   = $this->pagination->create_links();	
        $this->load->view('../../__inc/usr_header', $data);	
		$this->load->view('employeeNewRegAdmin');
	} 
	
	public  function listVerified($offset=NULL)
	{	

		$data['geo_designation']=$this->employee_model->geo_designation();
		$data['officelocation']=$this->employee_model->get_officelocation();
		$search_str=$this->input->post('searchValue');
		//for location user only-----start
		$this->grp_id = $this->nehbr_auth->get_group_id(); 
		$this->loc_id = $this->nehbr_auth->get_loc_id();
		$loc_id= $this->loc_id;
		//---end
		
		$random_no=hash_random();
		$cookie = array(
		'name'   => 'random',
		'value'  => $random_no,
		'expire' => time()+86500,
		'path'   => '/',
		'HttpOnly'=>TRUE
		);
		set_cookie($cookie);
		$data['random']=$random_no;		
		
		if (preg_match("/([%\$'?;{}#@<>\*]+)/",$searchValue))
		{	
			$this->session->set_flashdata('message', 'Invalid Characters in Search string');
			redirect("/employee/listVerified");
		}
		else{
			$config= array();
			$data['name'] = $search_str;
			if(!empty($search_str))
			{
				$this->session->set_userdata('search_emp_name',$search_str);
			}
			$sess_search_str=$this->session->userdata('search_emp_name');
			$data['search_string']=$sess_search_str;
			$config["base_url"] = base_url() . "employee/listVerified";
			$config['total_rows']  = $this->employee_model->record_countVerified($sess_search_str,$loc_id);
			$config['per_page']    = 10;
			$config['uri_segment'] =3;
			$choice = $config['total_rows'] / $config["per_page"];
			$this->pagination->initialize($config);        
			$page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
			$data['off_set'] = $page;        
			$data['results'] = $this->employee_model->get_employeelistVerified($config['per_page'], $page,$sess_search_str,$loc_id);
			$data['links']   = $this->pagination->create_links();	
			$this->load->view('../../__inc/usr_header', $data);	
			$this->load->view('employeeVerified');
		}
	}

	public function listRejected($offset=0)
	{
		$data['aadhar_status']=$this->employee_model->get_aadharStatus();		
		$data['geo_designation']=$this->employee_model->geo_designation();
		$data['officelocation']=$this->employee_model->get_officelocation();
		$searchStr=$this->input->post('searchValue');
		$searchValue = $searchStr;
		if (preg_match("/([%\$'?;{}#@<>\*]+)/",$searchValue))
		{	
			$this->session->set_flashdata('message', 'Invalid Characters in Search string');
			redirect("/employee/listRejected");
		}
		else{
			$config= array();
			$config["base_url"] = base_url() . "employee/listRejected";
			$config['total_rows']  = $this->employee_model->record_countRejected($searchValue);
			$config['per_page']    = 10;
			$config['uri_segment'] =3;
			$choice = $config['total_rows'] / $config["per_page"];
			$this->pagination->initialize($config);        
			$page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
			$data['off_set'] = $page;        
			$data['results'] = $this->employee_model->get_employeelistRejected($config['per_page'], $page,$searchValue);
			$data['links']   = $this->pagination->create_links();	
			$this->load->view('../../__inc/usr_header', $data);	
			$this->load->view('employeeRejected');
		}
	}
	
	function thanks()
	{
		$this->load->view('thanksMessage');
	}
	function send_email($to,$subjecct,$otpmessage)
	{
	/* //commented for automatic audit 10-may-2023		
		$this->email->set_newline("\r\n");
		$this->email->from($this->fromemailid, 'UIDAI');
		$this->email->to($to);
		$this->email->subject($subjecct);
		$this->email->message($otpmessage);					
		if($this->email->send()){
			// echo 'Email sent.';
		}
		 else{
			 //echo $this->email->print_debugger();
		 }
	*/	//end commented for automatic audit 10-may-2023
	}
	
	public function process($emp_id=NULL)
	{
		//$this->data = array();
		$this->u_id	= $this->nehbr_auth->get_user_id();
		$this->u_name= $this->nehbr_auth->get_username();
		$msg="";	
		$checkVerified=$this->employee_model->checkVerified($emp_id);
		if($emp_id!=NULL && $checkVerified>0)
		{
			$data=array();
			$data['organization_types']=$this->employee_model->get_org_types();
			$data['officelocation']=$this->employee_model->get_officelocation();
			$this->load->helper('form');
			$this->load->library('form_validation');
			$this->form_validation->CI=& $this;
			$this->form_validation->set_rules('processRemarks', 'Remarks', 'trim|required|callback_char_check');		
			$this->form_validation->set_rules('verifyStatus', 'Action to be taken', 'trim|required|callback_char_check');
			$this->form_validation->set_error_delimiters('<div style="color:#FF0000;">', '</div>');
			$emptmpId=$this->input->post('emp_id');
			$org_id=$this->input->post('org_id');
			
			if ($this->form_validation->run() == FALSE)
		 	{
				$data['organization_data']=$this->employee_model->get_forms_detail($emp_id);						
				$this->load->view('../../__inc/usr_header',$data);	
				$this->load->view('employee/process');
		   	}
			else if($emptmpId==$emp_id)
			{
				$this->load->library('subdomain_lib');
				$this->org_details= $this->subdomain_lib->get_subdomain_detail();
				$this->org_full_domain = $this->org_details->full_domain;
				$this->org_subdomain = $this->org_details->subdomain;
				
				$status=$this->employee_model->verify_employee();	
				if($status['retutndata']=='Verified')
				{
						
					//$header = "From:Attendance@Helpdesk<helpdesk-attendance@attendance.gov.in> \r\n";
					//$header = "From:Noreply@Attendance<noreply.attendance@gov.in> \r\n";
					$msg.=' Employee Verification Process completed successfully ';
					$to=$status['emp_mail'];
					$subject='AEBAS';
					$message = 'Dear Sir/Madam, 
					Congratulations! Your Employee Registration is now Verified and Activated on '.$this->org_subdomain.'.'.$this->org_full_domain.' . Employee Name: '.$status['emp_name'].', Attendance ID: '.$status['emp_id'].'  
					Regards, AEBAS Team';
					$this->load->library('email');
				/* //commented for automatic audit 10-may-2023	
					$this->email->from('noreply.attendance@gov.in','noreply.attendance@gov.in');
					$this->email->to($to);
					$this->email->subject($subject);
					$this->email->message($message);
					$retval = $this->email->send();
        					
					//if(mail($to,$subject,$message,$header)){
					if($retval){
						$msg.='# Mail successfully sent to '.$status['emp_mail'].'.';	
					}else{						
						$msg.='# Mail not sent to '.$status['emp_mail'].'.';	
					}
				*/	//end commented for automatic audit 10-may-2023		
					$data['status_message']=$msg;					
					//$mobilemsg = 'Dear Sir/Madam, Your Employee no : '.$status['emp_id'].' is Active on '.$this->org_subdomain.'.'.$this->org_full_domain.' Regards,	AEBAS Team';							
					$mobilemsg = 'Dear Sir/Madam,Congratulations! Your Employee Registration is now Verified and Activated on '.$this->org_subdomain.'.'.$this->org_full_domain.' . Employee Name: '.$status['emp_name'].', Attendance ID: '.$status['emp_id'].' Regards, AEBAS Team';
					//echo "<br> Mobile Msg = ".$mobilemsg;exit;
					$this->send_sms_msg($status['emp_mobile'],$mobilemsg,$template_id="1107160818807909686"); // commented for automatic audit 25-sept-2020 
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Employee Process',
								'action_taken'=>'Activate',
								'status'=>'success'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail
					
				}else if($status['retutndata']=='Rejected')
				{
					//$header = "From:Attendance@Helpdesk<helpdesk-attendance@attendance.gov.in> \r\n";
					//$header = "From:Noreply@Attendance<noreply.attendance@gov.in> \r\n";
					$msg.=' Employee Verification Rejected ';
					$to=$status['emp_mail'];
					$subject='AEBAS';
					$message='Dear Sir/Madam, 
					Your Registration with id no : '.$status['emp_id'].'  on ‘'.$this->org_subdomain.'.'.$this->org_full_domain.'’ is rejected by the Nodal officer. Please contact your Nodal Officer. 
					Regards, AEBAS Team';
					$this->load->library('email');
			/* //commented for automatic audit 10-may-2023
			
					$this->email->from('noreply.attendance@gov.in','noreply.attendance@gov.in');
					$this->email->to($to);
					$this->email->subject($subject);
					$this->email->message($message);
					$retval = $this->email->send();
					
        					
					//if(mail($to,$subject,$message,$header)){
					if($retval){
						$msg.='# Mail successfully sent to '.$status['emp_mail'].'.';	
					}else{						
						$msg.='# Mail not sent to '.$status['emp_mail'].'.';	
					}
			*/	//end commented for automatic audit 10-may-2023
					$data['status_message']=$msg;
					//$mobilemsg = 'Dear Sir/Madam, Your Registration with id no : '.$status['emp_id'].'  on ‘'.$this->org_subdomain.'.'.$this->org_full_domain.'’ is rejected by the Nodal officer. Please contact your Nodal Officer. Regards, AEBAS Team';
					$mobilemsg = 'Dear Sir/Madam, Your Registration with id no : '.$status['emp_id'].'  on '.$this->org_subdomain.'.'.$this->org_full_domain.' is rejected by the Nodal officer. Please contact your Nodal Officer. Regards, AEBAS Team';
					//echo "<br> Mobile Msg = ".$mobilemsg;exit;
					$this->send_sms_msg($status['emp_mobile'],$mobilemsg, $template_id ="1107160879392693743"); //commented for automatic audit 25-sept-2020
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Employee Process',
								'action_taken'=>'Reject',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail
				}else if($status['retutndata']=='notprocess'){
					$data['status_message']='Your Application is not Processed';	
				}
				$this->db_status=$data;
				$this->load->view('../../__inc/usr_header',$data);	
				$this->load->view('employee/success');
			}else
			{
				redirect('employee/listVerified');	
			}
		}
		else
		{
			redirect('employee/listVerified');
		}
	}
	
	function view($id=NULL)
	{
		$data['org_names']=$this->organization_model->get_org_master();	
		$data['state']= $this->employee_model->get_state();
		$data['district']= $this->employee_model->get_district();
		$data['empdesig']= $this->empdesig_model->get_empdesig();
		$data['orgdept']= $this->orgdept_model->get_orgdept();
		$data['orgbuilding']= $this->orgbuilding_model->get_orgbuilding();
		$data['query']= $this->employee_model->view_employee($id);		
		$this->load->view('../../__inc/usr_header',$data);	
		$this->load->view('view_employee');		
	}

	public function success()
	{
		$this->data = array();
		$this->u_id	= $this->nehbr_auth->get_user_id();
		$this->u_name= $this->nehbr_auth->get_username();
		$this->load->view('../../__inc/usr_header');	
		$this->load->view('employee/success');		
	}
	
	public function edit($id=NULL,$page=NULL)
	{	
		if($this->input->post('emp_cat')=='GOV'){
			$this->form_validation->set_rules('desig_id', 'Designation', 'required|xss_clean|callback_char_check');
		}
			
		// $this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');    //commented for automatic audit 10-may-2023			
		$this->form_validation->set_rules('emp_name', 'Employee Name', 'trim|required|xss_clean|callback_char_check');	
		$this->form_validation->set_rules('org_id', 'org_id', 'trim|required|callback_char_check');
		$this->form_validation->set_rules('org_dept_id', 'Department', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('office_loc', 'Office Location', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('emp_dob', 'DOB', 'trim|xss_clean|callback_check_DOB');
		$this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('emp_mobile', 'Mobile', 'trim|required|xss_clean|callback_check_mobile|callback_char_check');
		$this->form_validation->set_rules('emp_mail', 'Email', 'trim|required|xss_clean|callback_check_email|callback_char_check');	
		$data['org_names']=$this->organization_model->get_org_master();	
		// $data['captcha_html'] = $this->_create_captcha();	  //commented for automatic audit 10-may-2023	
	//new change on 13-oct-2020 audit  
		if (!empty($_FILES['image']['name']))
		{
   
			$filename_lenght= count(explode('.',$_FILES['image']['name'])); 
			if($filename_lenght > 2)
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name contains multiple extensions');
			}					
			elseif (preg_match("/([%\$'?;{}#@<>\*]+)/", $_FILES['image']['name']))
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name should be alphanumeric only');
			} 
			else{ 	
				if($this->validate_upload('image',$IsResize=1))
				{
					$image['content'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
					$image['type']=$_FILES['image']['type'];
				}
				else
				{
					if($this->upload_result!='')
					{
						$this->form_validation->set_rules('image', ' Input File', 'required');
						$this->form_validation->set_message('required', $this->upload_result);
					}
				}
			} 
		} 
	//end new change on 13-oct-2020 audit				
 
		
		$data['aadhar_status']=$this->employee_model->get_aadharStatus();
		$checkStatuAndAadhar=$this->employee_model->checkStatuAndAadhar($id,$page);	
		if ($this->form_validation->run() == false && $checkStatuAndAadhar>0) 
		{
			// 1 new request
			// 2 rejected
			// 3 verified
			if($page==1 || $page==2 || $page==3)
			{
				$data['query'] = $this->employee_model->view_employee($id,$page);
				$data['pageComes'] = $page;								
				$this->load->view('../../__inc/usr_header',$data);	
				$this->load->view('edit_employee');
			}else{
				redirect('/employee/listNew');
			}
		}else
		{
			if($this->employee_model->get_employeeNameEdit() && $checkStatuAndAadhar>0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee data updated successfully.
                                    </div>');

				$this->load->view('../../__inc/usr_header');
				if($this->session->userdata('group_id')==1){
				redirect('/employee/adminlistNew');}
				else if($this->input->post('pageComes')==2){
					redirect('/employee/listRejected');	
				}else if($this->input->post('pageComes')==1){
					redirect('/employee/listNew');	
				}else if($this->input->post('pageComes')==3){
					redirect('/employee/listVerified');	
				}						
				else{redirect('/employee/listNew');}
			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-close"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										 <b>Error!</b> Please try again. </div>');
				$this->load->view('../../__inc/usr_header');
				if($this->session->userdata('group_id')==1){
					redirect('/employee/adminlistNew');}else{redirect('/employee/listNew');}				
			}
		}
	}
	
	//For special characters 19June2015
	public function char_check($str)
	{
		if(preg_match("/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:\<\>,\\?\\\]/",$str))
		{
		   $this->form_validation->set_message('char_check', 'Invalid characters in string');
		   return false;
		}// end audit chnages 07-june-2023
		elseif($str[0]=='-' or $str[0]==',' or $str[0]=='_'){
			$this->form_validation->set_message('char_check', 'Invalid characters in string');
			return false;
		}// end audit chnages 07-june-2023
		else
		{
		  return true;	
		}	
	}	
	
	public function char_check1($str)
	{
		if(preg_match("/[\\/~`\!@#\$%\^&\*\(\)_\\+=\{\}\[\]\|;:\<\>,\\?\\\]/",$str))
		{
		   $this->form_validation->set_message('char_check1', 'Invalid characters in string');
		   return false;
		   
		}
		else
		{
		  return true;	
		}	
	}	
	
	public function editNew(){
		{	
			if($this->input->post('emp_cat')=='GOV'){
				$this->form_validation->set_rules('desig_id', 'Designation', 'required|xss_clean');
			}		
				
			$id=$this->session->userdata("edit_id");
			$this->form_validation->set_rules('emp_name', 'Employee Name', 'trim|required|xss_clean|callback_char_check1');			
			$this->form_validation->set_rules('org_dept_id', 'Department', 'trim|required|xss_clean|callback_char_check');
			$this->form_validation->set_rules('office_loc', 'Office Location', 'trim|required|xss_clean|callback_char_check');
			$this->form_validation->set_rules('emp_dob', 'DOB', 'trim|xss_clean|callback_char_check|callback_check_DOB');
			$this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean|callback_char_check');
			$this->form_validation->set_rules('emp_mobile', 'Mobile', 'trim|required|xss_clean|callback_char_check|callback_check_mobile');
			$this->form_validation->set_rules('emp_mail', 'Email', 'trim|required|xss_clean|callback_check_email');	
			$this->form_validation->set_rules('emp_cat', 'Emp Type', 'trim|required|xss_clean|callback_char_check');	
			
			/* Validation for third tab 26-05-2015 */
			$this->form_validation->set_rules("grade_pay", "Grade Pay", "required|trim|xss_clean|callback_char_check|callback_positive_check");
			$this->form_validation->set_rules("basic_pay", "Basic Pay", "required|xss_clean|callback_char_check|callback_positive_check");
			$this->form_validation->set_rules("reporting_id", "Reporting ID", "required|xss_clean|callback_char_check|callback_is_numeric_check|callback_Check_reporting_id");
			$this->form_validation->set_rules("reporting_name", "Reporting Name", "required|xss_clean|callback_char_check1");
			/* End of validations of third tab*/
			
			$data['org_names']=$this->organization_model->get_org_master();	
			$data['org_division']= $this->employee_model->orgdivision();			
		//new change on 13-oct-2020 audit  
			if (!empty($_FILES['image']['name']))
			{
	   
				$filename_lenght= count(explode('.',$_FILES['image']['name'])); 
				if($filename_lenght > 2)
				{
					$this->form_validation->set_rules('image', ' Input File', 'required');
					$this->form_validation->set_message('required', 'File name contains multiple extensions');
				}					
				elseif (preg_match("/([%\$'?;{}#@<>\*]+)/", $_FILES['image']['name']))
				{
					$this->form_validation->set_rules('image', ' Input File', 'required');
					$this->form_validation->set_message('required', 'File name should be alphanumeric only');;
				} 
				else{ 	
					if($this->validate_upload('image',$IsResize=1))
					{
						$image['content'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
						$image['type']=$_FILES['image']['type'];
					}
					else
					{
						if($this->upload_result!='')
						{
							$this->form_validation->set_rules('image', ' Input File', 'required');
							$this->form_validation->set_message('required', $this->upload_result);
						}
					}
				} 
			} 
		//end new change on 13-oct-2020 audit				

			
			$data['aadhar_status']=$this->employee_model->get_aadharStatus();
			$checkStatuAndAadhar=$this->employee_model->checkStatuAndAadharNew($id);	
			if ($this->form_validation->run() == false && $checkStatuAndAadhar>0) 
			{
				// 1 new request
				// 2 rejected
				// 3 verified			
				$data['query'] = $this->employee_model->view_employee_new($id);
				$this->load->view('../../__inc/usr_header',$data);	
				$this->load->view('edit_new_employee');			
			}else
			{			
				if($this->employee_model->update_employeeNew() && $checkStatuAndAadhar>0)
				{
					$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee data updated successfully.
                                    </div>');

					$this->load->view('../../__inc/usr_header');								
					redirect('/employee/listNew');	
				}else if($checkStatuAndAadhar>0)
				{
					$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-close"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										 <b>Error!</b> Please try again. </div>');
					$this->load->view('../../__inc/usr_header');
					redirect('/employee/listNew');			
				}else{
					redirect(base_url());	
				}
			}
		}		
	}
	
	public function editNew1()
	{
		if($this->input->post('emp_cat')=='GOV'){
				$this->form_validation->set_rules('desig_id', 'Designation', 'required|xss_clean');
		}		
		$id=$this->session->userdata("edit_id");
		$this->form_validation->set_rules('emp_name', 'Employee Name', 'trim|required|xss_clean|callback_char_check1');			
		$this->form_validation->set_rules('org_dept_id', 'Department', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('office_loc', 'Office Location', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('emp_dob', 'DOB', 'trim|xss_clean|callback_check_DOB');
		$this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('emp_mobile', 'Mobile', 'trim|required|xss_clean|callback_char_check|callback_check_mobile');
		$this->form_validation->set_rules('emp_mail', 'Email', 'trim|required|xss_clean|callback_check_email');	
		$this->form_validation->set_rules('emp_cat', 'Emp Type', 'trim|required|xss_clean|callback_char_check');	
		
		/* Validation for third tab 26-05-2015 */
		$this->form_validation->set_rules("grade_pay", "Grade Pay", "required|trim|xss_clean|callback_char_check|callback_positive_check");
		$this->form_validation->set_rules("basic_pay", "Basic Pay", "required|xss_clean|callback_char_check|callback_positive_check");
		$this->form_validation->set_rules("reporting_id", "Reporting ID", "required|xss_clean|callback_char_check|callback_is_numeric_check|callback_Check_reporting_id");
		$this->form_validation->set_rules("reporting_name", "Reporting Name", "required|xss_clean|callback_char_check1");
		/* End validation of third tab */
			
		$data['org_names']=$this->organization_model->get_org_master();	
		$data['org_division']= $this->employee_model->orgdivision();			
        
	//new change on 13-oct-2020 audit  
		if (!empty($_FILES['image']['name']))
		{
   			$filename_lenght= count(explode('.',$_FILES['image']['name'])); 
			if($filename_lenght > 2)
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name contains multiple extensions');
			}					
			elseif (preg_match("/([%\$'?;{}#@<>\*]+)/", $_FILES['image']['name']))
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name should be alphanumeric only');
			} 
			else{ 	
				if($this->validate_upload('image',$IsResize=1))
				{
					$image['content'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
					$image['type']=$_FILES['image']['type'];
				}
				else
				{
					if($this->upload_result!='')
					{
						$this->form_validation->set_rules('image', ' Input File', 'required');
						$this->form_validation->set_message('required', $this->upload_result);
					}
				}
			} 
		} 
	//end new change on 13-oct-2020 audit				
 			
		$data['aadhar_status']=$this->employee_model->get_aadharStatus();
		$checkStatuAndAadhar=$this->employee_model->checkStatuAndAadharNew($id);	
		if ($this->form_validation->run() == false && $checkStatuAndAadhar>0) 
		{
			// 1 new request
			// 2 rejected
			// 3 verified
			
			$data['query'] = $this->employee_model->view_employee_new($id);
			$this->load->view('../../__inc/usr_header',$data);	
			$this->load->view('edit_new_employee');			
		}else
		{
			if($this->input->post('emp_mobile')!=$this->input->post('emp_mobile1'))
			{
				$mobile=$this->input->post('emp_mobile1');
				$mobile_new=$this->input->post('emp_mobile');
				$msg='Your Mobile number no has been changed to'.$mobile_new;
				$sub='Mobile Changed';
				$this->send_sms_msg($mobile,$msg, $template_id="1107160818645972954"); //commented for automatic audit 25-sept-2020
			}
			if($this->input->post('emp_mail')!=$this->input->post('emp_mail1'))
			{
				$email=$this->input->post('emp_mail1');
				$email_new=$this->input->post('emp_mail');
				$msg='Your Email has been changed to'.$mobile_new;
				$sub='Email Changed';
				$this->send_email($email,$sub,$msg);//commented for automatic audit 25-sept-2020
			}
			if($this->employee_model->update_employeeNew() && $checkStatuAndAadhar>0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee data updated successfully.
                                    </div>');

				$this->load->view('../../__inc/usr_header');								
				redirect('/employee/listNew');	
			}else if($checkStatuAndAadhar>0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-close"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										 <b>Error!</b> Please try again. </div>');
				$this->load->view('../../__inc/usr_header');
				redirect('/employee/listNew');			
			}else{
				redirect(base_url());	
			}
		}
	}

	function editRejected($id=NULL)
	{	
		if($this->input->post('emp_cat')=='GOV'){
			$this->form_validation->set_rules('desig_id', 'Designation', 'required|xss_clean');
		}				
		$this->form_validation->set_rules('emp_name', 'Employee Name', 'trim|required|xss_clean|callback_char_check');	
		$this->form_validation->set_rules('org_id', 'org_id', 'trim|required|callback_char_check');
		$this->form_validation->set_rules('org_dept_id', 'Department', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('office_loc', 'Office Location', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('emp_dob', 'DOB', 'trim|xss_clean|callback_check_DOB|callback_char_check');
		$this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean|callback_char_check');		
		$this->form_validation->set_rules('emp_mobile', 'Mobile', 'trim|required|xss_clean|callback_check_mobile|callback_char_check');
		$this->form_validation->set_rules('emp_mail', 'Email', 'trim|required|xss_clean|callback_check_email');	
		$data['org_names']=$this->organization_model->get_org_master();
		$data['org_division']= $this->employee_model->orgdivision();
	
	//new change on 13-oct-2020 audit  
		if (!empty($_FILES['image']['name']))
		{
   			$filename_lenght= count(explode('.',$_FILES['image']['name'])); 
			if($filename_lenght > 2)
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name contains multiple extensions');
			}					
			elseif (preg_match("/([%\$'?;{}#@<>\*]+)/", $_FILES['image']['name']))
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name should be alphanumeric only');
			} 
			else{ 	
				if($this->validate_upload('image',$IsResize=1))
				{
					$image['content'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
					$image['type']=$_FILES['image']['type'];
				}
				else
				{
					if($this->upload_result!='')
					{
						$this->form_validation->set_rules('image', ' Input File', 'required');
						$this->form_validation->set_message('required', $this->upload_result);
					}
				}
			} 
		} 
	//end new change on 13-oct-2020 audit				
 
			
		$data['aadhar_status']=$this->employee_model->get_aadharStatus();
		$checkStatuAndAadhar=$this->employee_model->checkStatusAndAadharRejeccted($id);	
				
		if ($this->form_validation->run() == false && $checkStatuAndAadhar>0) 
		{
			$data['query'] = $this->employee_model->view_employee_rejected($id);
			$this->load->view('../../__inc/usr_header',$data);	
			$this->load->view('edit_rejected_employee');
		}else
		{
			if($this->employee_model->update_employeeRejected() && $checkStatuAndAadhar>0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee data updated successfully.
                                    </div>');

				$this->load->view('../../__inc/usr_header');
				redirect('/employee/listRejected');
			}else if($checkStatuAndAadhar>0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-close"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										 <b>Error!</b> Please try again. </div>');
				$this->load->view('../../__inc/usr_header');
				redirect('/employee/listRejected');			
			}else{
				redirect(base_url());			
			}
		}
	}
	
	function edit_verified()
	{
		if($this->input->post('emp_cat')=='GOV'){
			$this->form_validation->set_rules('desig_id', 'Designation', 'required|xss_clean');				
		}
		$id=$this->session->userdata("edit_id");
		$this->form_validation->set_rules('emp_name', 'Employee Name', 'trim|required|xss_clean|callback_char_check1');	
		$this->form_validation->set_rules('org_dept_id', 'Department', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('office_loc', 'Office Location', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('emp_dob', 'DOB', 'trim|xss_clean|callback_check_DOB');
		$this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('emp_mobile', 'Mobile', 'trim|required|xss_clean|callback_char_check|callback_check_mobile');
		//$this->form_validation->set_rules('emp_mail', 'Email', 'trim|required|xss_clean|callback_check_email');	
		$this->form_validation->set_rules('emp_mail', 'Email', 'trim|required|xss_clean|valid_email');	
		$this->form_validation->set_rules('emp_cat', 'Emp Type', 'trim|required|xss_clean|callback_char_check');	
		$this->form_validation->set_rules("grade_pay", "Grade Pay", "required|trim|xss_clean|callback_char_check|callback_positive_check");
		$this->form_validation->set_rules("basic_pay", "Basic Pay", "required|xss_clean|callback_char_check|callback_positive_check");
		$this->form_validation->set_rules("reporting_id", "Reporting ID", "required|xss_clean|callback_char_check|callback_is_numeric_check|callback_Check_reporting_id");
		$this->form_validation->set_rules("reporting_name", "Reporting Name", "required|xss_clean|callback_char_check1");
		$data['org_names']=$this->organization_model->get_org_master();	
		$data['org_division']= $this->employee_model->orgdivision();			
		$data['aadhar_status']=$this->employee_model->get_aadharStatus();
		$checkStatuAndAadhar=$this->employee_model->checkStatusAndAadharVerified($id);
		$data['demographStatus']=$this->employee_model->checkDemographStatus($id);		
	
	//new added on 13-oct-2020 audit  
		if (!empty($_FILES['image']['name']))
		{
   			$filename_lenght= count(explode('.',$_FILES['image']['name'])); 
			if($filename_lenght > 2)
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name contains multiple extensions');
			}					
			elseif (preg_match("/([%\$'?;{}#@<>\*]+)/", $_FILES['image']['name']))
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name should be alphanumeric only');
			} 
			else{ 	
				if($this->validate_upload('image',$IsResize=1))
				{
					$image['content'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
					$image['type']=$_FILES['image']['type'];
				}
				else
				{
					if($this->upload_result!='')
					{
						$this->form_validation->set_rules('image', ' Input File', 'required');
						$this->form_validation->set_message('required', $this->upload_result);
					}
				}
			} 
		} 
	//end new added on 13-oct-2020 audit				
		
		if ($this->form_validation->run() == false && $checkStatuAndAadhar>0) 
		{	
			$data['status'] = $this->employee_model->orgid_in_siosclist();	
			$data['query'] = $this->employee_model->view_employee_verified($id);
			$this->load->view('../../__inc/usr_header',$data);	
			$this->load->view('edit_verified_employee');
		}else
		{
			if($this->employee_model->update_employeeNew() && $checkStatuAndAadhar>0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee data updated successfully.
                                    </div>');
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Newly Registered Employee',
								'action_taken'=>'Edit',
								'status'=>'success'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail					

				$this->load->view('../../__inc/usr_header');
				redirect('/employee/listVerified');	
			}else if($checkStatuAndAadhar>0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-close"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										 <b>Error!</b> Please try again. </div>');
			    //new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Newly Registered Employee',
								'action_taken'=>'Edit',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail							 
				$this->load->view('../../__inc/usr_header');
				redirect('/employee/listVerified');			
			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-close"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										 <b>Sorry!</b> Please try again. </div>');
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Newly Registered Employee',
								'action_taken'=>'Edit',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail						 
				$this->load->view('../../__inc/usr_header');
				redirect('/employee/listVerified');	
			}
		}		
	}
		
	function edit_active()
	{			
		if($this->input->post('emp_cat')=='GOV'){
			$this->form_validation->set_rules('desig_id', 'Designation', 'required|xss_clean');				
		}
		$id=$this->session->userdata("edit_id");
		
		$this->form_validation->set_rules('emp_name', 'Employee Name', 'trim|required|xss_clean|callback_char_check1');	
		$this->form_validation->set_rules('org_dept_id', 'Department', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('office_loc', 'Office Location', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('emp_dob', 'DOB', 'trim|xss_clean|callback_check_DOB');
		$this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('emp_mobile', 'Mobile', 'trim|required|xss_clean|callback_char_check|callback_check_mobile');
		$this->form_validation->set_rules('emp_mail', 'Email', 'trim|xss_clean|callback_check_email');	
		$this->form_validation->set_rules('emp_cat', 'Emp Type', 'trim|required|xss_clean|callback_char_check');	
		$this->form_validation->set_rules("grade_pay", "Grade Pay", "required|trim|xss_clean|callback_char_check|callback_positive_check");
		$this->form_validation->set_rules("basic_pay", "Basic Pay", "required|xss_clean|callback_char_check|callback_positive_check");
		$this->form_validation->set_rules("reporting_id", "Reporting ID", "required|xss_clean|callback_char_check|callback_is_numeric_check|callback_Check_reporting_id");
		$this->form_validation->set_rules("reporting_name", "Reporting Name", "required|xss_clean|callback_char_check1");
		$this->form_validation->set_rules('overnight_shift', 'Over Night Shift', 'trim|required|xss_clean');
		$data['org_names']=$this->organization_model->get_org_master();
		$data['org_division']= $this->employee_model->orgdivision();
		$data['aadhar_status']=$this->employee_model->get_aadharStatus();
		$checkStatusAndActiveStatus=$this->employee_model->checkStatusAndActive($id);
		$data['demographStatus']=$this->employee_model->checkDemographStatus($id);		//Edit of demograph_status failed
		
	//new added on 13-oct-2020 audit  
		if (!empty($_FILES['image']['name']))
		{
   
			$filename_lenght= count(explode('.',$_FILES['image']['name'])); 
			if($filename_lenght > 2)
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name contains multiple extensions');
			}					
			elseif (preg_match("/([%\$'?;{}#@<>\*]+)/", $_FILES['image']['name']))
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name should be alphanumeric only');
			} 
			else{ 	
				if($this->validate_upload('image',$IsResize=1))
				{
					$image['content'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
					$image['type']=$_FILES['image']['type'];
				}
				else
				{
					if($this->upload_result!='')
					{
						$this->form_validation->set_rules('image', ' Input File', 'required');
						$this->form_validation->set_message('required', $this->upload_result);
					}
				}
			} 
		} 
	//end new added on 13-oct-2020 audit				

		if ($this->form_validation->run() == false && $checkStatusAndActiveStatus>0) 
		{	
			$data['status'] = $this->employee_model->orgid_in_siosclist();
			$data['query'] = $this->employee_model->view_employee_active($id);
			$this->load->view('../../__inc/usr_header',$data);	
			$this->load->view('edit_active_employee');
		}else
		{
			if($this->employee_model->update_employeeNew() && $checkStatusAndActiveStatus>0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee data updated successfully.
                                    </div>');
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Active Employee',
								'action_taken'=>'Edit',
								'status'=>'success'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail
				$this->load->view('../../__inc/usr_header');
				redirect('/employee/index');	
			}else if($checkStatusAndActiveStatus>0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-close"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										 <b>Error!</b> Please try again. </div>');
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Active Employee',
								'action_taken'=>'Edit',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail
				$this->load->view('../../__inc/usr_header');
				redirect('/employee/index');			
			}else
			{
				$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-close"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										 <b>Sorry!</b> Please try again. </div>');
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Active Employee',
								'action_taken'=>'Edit',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail
				$this->load->view('../../__inc/usr_header');
				redirect('/employee/index');	
			}		
		}	
	}
	
	function delete($empId=NULL)
	{
		if($this->employee_model->deleteRecords($empId))
		{
			// deleted	
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee deleted successfully.
                                    </div>');
			redirect('employee/listRejected');
		}
		else{
			// not deleted	
			$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-close"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										 <b>Sorry!</b>Could not delete, Please try again. </div>');
			redirect('employee/listRejected');
		}
	}
	
	function detail($emp_id=NULL)
	{		
		// emp id is sno//
		$this->data = array();
		$this->u_id	= $this->nehbr_auth->get_user_id();
		$this->u_name= $this->nehbr_auth->get_username();
		$data['officelocation']=$this->employee_model->get_officelocation();		
		$checkActiveStatus=$this->employee_model->checkActiveStatus($emp_id);	
		if($emp_id!=NULL && $checkActiveStatus>0)
		{
			$data['organization_data']=$this->employee_model->get_detail_activate($emp_id);	
			$this->load->view('../../__inc/usr_header',$data);	
			$this->load->view('employee/detail');			
		}
		else
		{
				redirect('employee/index');
		}		
	}
	
	function detactive($emp_id=NULL)
	{
		$checkActiveStatus=$this->employee_model->checkActiveStatus($emp_id);
		if($checkActiveStatus>0 && $this->employee_model->deactivate($emp_id))
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee Deactivated successfully.
                                    </div>');
			redirect('employee/index');
			
		}else if($checkActiveStatus>0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee not Deactivated successfully.
                                    </div>');
			redirect('employee/index');
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Sorry!</b> Unauthorized User.
                                    </div>');
			redirect('employee/index');
		}	
	}
	
	function detactiveDelete($emp_id=NULL)
	{
		$checkActiveStatus=$this->employee_model->checkActiveStatus($emp_id);
		if($checkActiveStatus>0 && $this->employee_model->deactivateDel($emp_id))
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee Deleted successfully.
                                    </div>');
			redirect('employee/index');
			
		}else if($checkActiveStatus>0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee not Deleted successfully.
                                    </div>');
			redirect('employee/index');
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Sorry!</b> Unauthorized User.
                                    </div>');
			redirect('employee/index');
		}	
	}
//new changes added for csrf ajax requst 21-10-2020	
	//Get the name of reporting officer name
	function get_reporting_name_sql_qry()
	{
		$rep_name = $_POST['emp_id'];
		$query=$this->DB1->query("SELECT emp_name FROM emp WHERE emp_id=$rep_name");
		$a= $query->row();
		if($a){
			$data['emp_name'] = $a->emp_name;
		}
		else{
			$data['emp_name'] = "Reporting officer name not found. ";
		}
		$data['token_name']= $this->security->get_csrf_token_name();
		$data['token_value']= $this->security->get_csrf_hash();
		echo json_encode($data);	
	}
//new changes added for csrf ajax requst 21-10-2020	
	/* Inserted validation for third tab 26-05-2015 */
	public function positive_check($str)
	{
		if(!preg_match("/(^[0-9])$/",$str))
		{
		   $this->form_validation->set_message('positive_check', 'Enter valid number');
		   return false; 	
		}
		else if(strlen($str) > 6)
		{
		   $this->form_validation->set_message('positive_check', 'Enter valid number');
		   return false; 	
		}
		else
		{
		    return true;
		}	
	}	

	public function is_numeric_check($str)
	{
		if(strlen($str)>8)
		{
			$this->form_validation->set_message('is_numeric_check', 'Enter valid number');
			return false; 
		}
		if(!preg_match("/(^[0-9])$/",$str))
		{
			$this->form_validation->set_message('is_numeric_check', 'Enter Correct Reporting ID');
			return false;
		}
		else
		{
			return true;	
		}	
	}
	
	/*public function Check_reporting_id($str)
	{
		$query=$this->DB1->query("SELECT emp_id FROM emp WHERE emp_id=$str");
		$a= $query->row();
		$id = $this->session->userdata("edit_id");;
		if($a)
		{
			if($id == $str)
			{	
				$this->form_validation->set_message('Check_reporting_id', 'Reporting ID cannot be self employee ID');
				return false; 
			}
			else
			{
				return true; 
			}
		}
		else
		{
			if($str == 0)
			{
				return true; 
			}
			$this->form_validation->set_message('Check_reporting_id', 'Please enter correct reporting ID');
			return false;
		}
	}*/
	
	public function Check_reporting_id($str)
	{
		//new changes done by sasiram 10-08-2022 //
		if($str=='0'){
			return true;
		}
		else if($str==''){
			$this->form_validation->set_message('Check_reporting_id', 'Please enter correct reporting ID');
			return false;
		}
		else{
			$query=$this->DB1->query("SELECT emp_id FROM emp WHERE emp_id = $str");
			$a= $query->row();
			//echo $a->emp_id;die();
			$id = $this->session->userdata("edit_id");
			if($a)
			{
				if($id == $str)
				{	
					$this->form_validation->set_message('Check_reporting_id', 'Reporting ID cannot be self employee ID');
					return false; 
				}
				else
				{
					return true; 
				}
			}
		}
	}
	
	//Search for employee new request newly added
	public function searchadminlistNew($offset=0)
	{		
		if($this->session->userdata('group_id')!=1){ redirect(base_url());}
		$data['geo_designation']=$this->employee_model->geo_designation();
		$data['organizations']=$this->employee_model->get_organizations();
		$data['officelocation']=$this->employee_model->get_officelocation();
		$searchStr=$this->input->post('searchValue');
		$org_id=$this->input->post('org_id');
		//new changes added for audit chnages 07-june-2023
		if(!preg_match("/(^[0-9]{6})$/",$org_id)){
			$this->session->set_flashdata('message', 'Invalid Characters in Search string');
			redirect("/employee/adminlistNew");
		}//new changes added for audit chnages 07-june-2023
			
		if (preg_match("/([%\$'?;{}#@<>\*]+)/",$searchStr) )
		{	
			$this->session->set_flashdata('message', 'Invalid Characters in Search string');
			redirect("/employee/adminlistNew");
		}
		else
		{
			if($searchStr){
				$this->session->set_userdata('listnewSearchValueAdmin',$searchStr);
			}
			if($org_id != null){
				$this->session->set_userdata('search_org_id',$org_id);
			}		

			$data['searchValue'] = $this->session->userdata('listnewSearchValueAdmin');
			$data['search_org_id'] = $this->session->userdata('search_org_id');
			$searchValue = $data['searchValue'];
			$config= array();
			$config['base_url']    = base_url()."employee/searchadminlistNew";
			$config['total_rows']  = $this->employee_model->admin_record_countnew($searchValue,$data['search_org_id']);
			$config['per_page']    = 10;
			$config['uri_segment'] =3;
			$choice = $config['total_rows'] / $config["per_page"];
			$this->pagination->initialize($config);        
			$page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
			$data['off_set'] = $page;        
			$data['results'] = $this->employee_model->admin_get_employeelistNew($config['per_page'], $page,$searchValue,$data['search_org_id']);
			$data['links']   = $this->pagination->create_links();	
			$this->load->view('../../__inc/usr_header', $data);	
			$this->load->view('employeeNewRegAdmin');
		} 
	}

	/* End of Third tab changes */
	public function orgwise_division()
	{
		if (isset($_POST) && isset($_POST['org_name'])) 
		{
			$org_name = $_POST['org_name'];
			$org_dept_id = $_POST['org_dept_id'];
			
			$org_division = $this->employee_model->orgwise_division($org_name);
			if($org_division)
			{
				echo '<option value="">- Select -</option>';
				foreach ($org_division as $org_div) 
				{
					$selected="";
					if($org_div->org_dept_key == $org_dept_id)
					{
						$selected='selected="selected"';
					}	                                          
					echo '<option value="'. $org_div->org_dept_key . '"'. $selected.'>' .$org_div->department . '</option>';
				}				
			}
		} 
	}

	//For demographic added on August 23,2016 by Khabiruddin/Vaishali
	function demographFailedList($offset = 0)
	{
		$searchstr	=	$this->input->post('searchValue');
		if($searchstr)
		{
			$this->session->set_userdata('listdemographValue', $searchstr);
		}
		$searchValue	=	$this->session->userdata('listdemographValue');
		$data['searchValue']=$searchValue;	
	 	$data['geo_designation']=$this->employee_model->geo_designation();		
		$config= array();
		$config['base_url']    = base_url() . "employee/demographFailedList";
		$config['total_rows']  = $this->employee_model->demographFailedRecord_count($searchValue);
		$config['per_page']    = 10;
		$config['uri_segment'] =3;
		$choice = $config['total_rows'] / $config["per_page"];
		$this->pagination->initialize($config);        
		$page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
		$data['off_set'] = $page;        
		$data['query'] = $this->employee_model->get_employeelistdemographFailed($config['per_page'], $page,$searchValue);
		$data['links']   = $this->pagination->create_links();
		$this->load->view('../../__inc/usr_header',$data);			
		$this->load->view('demographFailed');
	}

	function cleardemographics()
	{
		$this->session->unset_userdata('listdemographValue');
		redirect('employee/demographFailedList');	
	}

	function demographFailedList_export($offset = 0)
	{
		$searchstr	=	$this->input->post('searchValue');
		if($searchstr)
		{
			$this->session->set_userdata('listdemographValue', $searchstr);
		}
		$searchValue	=	$this->session->userdata('listdemographValue');
		$this->load->library('Export');
		$this->load->helper('file');
		$filename = "csv_file_".time().".csv";
		$header = "List of Demographics Failed Employees :";
	    $report =$this->employee_model->get_employeelistdemographFailed_export($searchValue);
		$new_report = $this->export->to_excel($report,$filename,$header);
		$this->load->helper('download');
		force_download($filename, $new_report); 
	}	

	function edit_demofailed($id=NULL)
	{
	  	
		if($this->input->post('emp_cat')=='GOV'){
			$this->form_validation->set_rules('desig_id', 'Designation', 'required|xss_clean|callback_char_check');				
		}
		
		$this->form_validation->set_rules('emp_mobile', 'Mobile', 'trim|required|xss_clean|callback_check_mobile');
		$this->form_validation->set_rules('emp_mail', 'Email', 'trim|required|xss_clean|callback_check_email');			
		/*	commented for demoauth...27/06/2016
		$this->form_validation->set_rules('org_dept_id', 'Department', 'trim|required|xss_clean');						
		$this->form_validation->set_rules('emp_cat', 'Emp. Type', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('office_loc', 'Office Location', 'trim|required|xss_clean');*/
		$this->form_validation->set_rules('emp_dob', 'DOB', 'trim|xss_clean|callback_check_DOB');
		$data['org_names']=$this->organization_model->get_org_master();
		$data['org_division']= $this->employee_model->orgdivision();			
		$data['aadhar_status']=$this->employee_model->get_aadharStatus();
		$checkStatusDemoFailed=$this->employee_model->checkStatusDemoFalied($id);
		if ($this->form_validation->run() == false && $checkStatusDemoFailed>0) 
		{ 
			$data['query'] = $this->employee_model->view_employee_demofailed($id); 
			$this->load->view('../../__inc/usr_header',$data);	
			$this->load->view('edit_demofailed_employee');
		}else
		{
			if($this->input->post('aadhaar_demograph')>0)
			{
				if($this->employee_model->update_DemoFailedemployee()){
					$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
											<i class="fa fa-check"></i>
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
											<b>Success!</b> Employee data updated successfully.
										</div>');

					$this->load->view('../../__inc/usr_header');
					redirect('/employee/demographFailedList');	
				}else if($checkStatusDemoFailed>0){
					$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
											<i class="fa fa-close"></i>
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
											 <b>Error!</b> Please try again. </div>');
					$this->load->view('../../__inc/usr_header');
					redirect('/employee/demographFailedList');			
				}else{
					$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
											<i class="fa fa-close"></i>
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
											 <b>Sorry!</b> Please try again. </div>');
					$this->load->view('../../__inc/usr_header');
					redirect('/employee/demographFailedList');	
				}
			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
											<i class="fa fa-close"></i>
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
											 <b>Sorry!</b> Aadhaar Authentication Not Successful.Please try again. </div>');
				$this->load->view('../../__inc/usr_header');
				redirect('/employee/edit_demofailed/'.$id);	
			}
		}
	}
	
	//Detail Demographics Failed Employee
	function detailDemoFailed($emp_id=NULL)
	{		
		$this->data = array();
		$this->u_id	= $this->nehbr_auth->get_user_id();
		$this->u_name= $this->nehbr_auth->get_username();
		$data['officelocation']=$this->employee_model->get_officelocation();		
		$checkStatusDemoFalied=$this->employee_model->checkStatusDemoFalied($emp_id);	
			
		if($emp_id!=NULL && $checkStatusDemoFalied>0)
		{
			$data['organization_data']=$this->employee_model->get_detail_demoFailed($emp_id);
			$this->load->view('../../__inc/usr_header',$data);	
			$this->load->view('employee/detailDemoFailed');
		}
		else
		{
			redirect('employee/demographFailedList');			
		}		
	}
	
	//Demograph functions start
	function validate_aadhaar($aadhaar,$gender,$emp_name,$emp_dob)
    {
        $emp_dob = date('Y-m-d',strtotime($emp_dob));
		//$input_xml="<name>".$emp_name."</name><sa>1015DEITY</sa><lk>Atweb6UAgT5YRe7WFqc4</lk><project>AEBASCENTRAL</project><uid>".$aadhaar."</uid><gender>".$gender."</gender><dob>".$emp_dob."</dob>";
		$input_xml="<name>".$emp_name."</name><sa>1015DEITY</sa><lk>Atweb6UAgT5YRe7WFqc4</lk><project>AEBAS</project><ver>2.5</ver><uid>".$aadhaar."</uid><gender>".$gender."</gender><dob>".$emp_dob."</dob>";
		//$service_url='http://10.249.74.180:8080/PidInfoService/InfoServlet';
		$service_url='http://10.247.198.98:8080/PidInfoService/InfoServlet';		//new prod
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$service_url);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlRequest=" . $input_xml);
 
		$aauthXml = curl_exec($ch);
		curl_close($ch);
 
		//currently pointed AUA/ASA server aua url
		$url = $this->config->item('aua_host');
   
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $aauthXml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
    
        $xml                       = simplexml_load_string($result);
        $ret_val['ret']            = (string) $xml->attributes()->ret;
        $ret_val['error']          = (string) $xml->attributes()->err;
        $ret_val['transaction_id'] = (string) $xml->attributes()->txn;
        $ret_val['code']           = (string) $xml->attributes()->code;
        $ret_val['ts']             = (string) $xml->attributes()->ts;
   
        if (!empty($ret_val)) 
		{
            if ($ret_val['ret'] == 'y') {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
	
	
	function check_aadhaar()
    {
		if (!$this->input->get('aadhaar')) 
		{
            echo "empty";
        } else 
		{
            //$aadhaar = $this->input->get('aadhaar');
			
			$get_aadhaar = $this->input->get('aadhaar');
			$de_data = $this->aec_decrypt2($get_aadhaar);
			$de_data_aadhaar = explode("*_*",$de_data);
			$aadhaar = $de_data_aadhaar[1];
		
            $gender = $this->input->get('gender');
            $emp_name = $this->input->get('emp_name');
            $emp_dob = $this->input->get('emp_dob');
			if(!ctype_digit($aadhaar)|| (strlen($aadhaar) != 12)){
				echo "incorrect";
			}
			else
			{
				$a= $this->validate_aadhaar($aadhaar,$gender,$emp_name,$emp_dob);
				$a=1; //new added for automatic audit 10-may-2023
				if($a)
				echo "yes";
				else
				echo "autherr";
			}
        }
    }
	//Demograph functions end
	
	//Under Transfer Employee List
	function EmpUnderTransfer($offset = 0)
	{		
		//For location user only---start
		$this->grp_id = $this->nehbr_auth->get_group_id(); 
		$this->loc_id = $this->nehbr_auth->get_loc_id();
		$loc_id= $this->loc_id;
		//--------end

		$searchstr	=	$this->input->post('searchValue');
		$searchValue	=	$this->input->post('searchValue');
		
		if (preg_match("/([%\$'?;{}#@<>\*]+)/",$searchValue))
		{	
			$this->session->set_flashdata('message', 'Invalid Characters in Search string');
			redirect("/employee/EmpUnderTransfer");
		}
		else
		{
			$data['geo_designation']=$this->employee_model->geo_designation();
			$data['officelocation']=$this->employee_model->get_officelocation();	
			$config= array();
			$config['base_url']    = base_url() . "employee/EmpUnderTransfer";
			$config['total_rows']  = $this->employee_model->UnderTransferRecord_count($searchValue,$loc_id);
			$config['per_page']    = 10;
			$config['uri_segment'] =3;
			$choice = $config['total_rows'] / $config["per_page"];
			$this->pagination->initialize($config);        
			$page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
			$data['off_set'] = $page;        
			$data['query'] = $this->employee_model->get_UnderTransferList($config['per_page'], $page,$searchValue,$loc_id);
			$data['links']   = $this->pagination->create_links();
			$this->load->view('../../__inc/usr_header',$data);			
			$this->load->view('employeeUnderTransfer');
		}	
	}
	
	//added by sandeep for employee block //
	function blockempList($offset = 0){		
		$searchstr	=	$this->input->post('searchValue');
		if($searchstr)
		{
			$this->session->set_userdata('listBlockValue', $searchstr);
		}
		$searchValue	=	$this->session->userdata('listBlockValue');
		$data['searchValue']=$searchValue;	
	 	$data['geo_designation']=$this->employee_model->geo_designation();
		$data['officelocation']=$this->employee_model->get_officelocation();	
		$config= array();
        $config['base_url']    = base_url() . "employee/blockempList";
        $config['total_rows']  = $this->employee_model->blockRecord_count($searchValue);
        $config['per_page']    = 10;
        $config['uri_segment'] =3;
        $choice = $config['total_rows'] / $config["per_page"];
        $this->pagination->initialize($config);        
        $page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
        $data['off_set'] = $page;        
        $data['query'] = $this->employee_model->get_employeelistBlock($config['per_page'], $page,$searchValue);
        $data['links']   = $this->pagination->create_links();
		$this->load->view('../../__inc/usr_header',$data);			
		$this->load->view('employeeBlock');
	}

	function clearBlock()
	{
		$this->session->unset_userdata('listBlockValue');
		redirect('employee/blockempList');	
	}
	
	function blockEmp($emp_id=NULL,$active_status=NULL)
	{  
	    // new added 27Nov2018 for notMarkingAtt01Aug2018 emp block
		if($emp_id==NULL && $active_status==NULL)
		{
			$emp_id=$this->input->post('modal_emp_id');
			$active_status=$this->input->post('modal_active_status');
		}
		// end new added 27Nov2018 for notMarkingAtt01Aug2018 emp block
		$checkBlockStatus=$this->employee_model->checkBlockStatus($emp_id);
		if($checkBlockStatus>0 && $this->employee_model->block_emp($emp_id))
		{
			$transferLog=$this->employee_model->insert_banlog($emp_id);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee Blocked successfully.
                                    </div>');
			//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Block Employee',
								'action_taken'=>'Block',
								'status'=>'success'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail						
			if($active_status=='A')
				redirect('employee/index');
			elseif($active_status=='I')
				redirect('employee/inactiveList');
			else
				redirect('employee/not_marking_from_01Aug2018');
			
		}else if($checkBlockStatus>0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee not Blocked successfully.
                                    </div>');
			//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Block Employee',
								'action_taken'=>'Block',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail						
			if($active_status=='A')
				redirect('employee/index');
			elseif($active_status=='I')
				redirect('employee/inactiveList');
			else
				redirect('employee/not_marking_from_01Aug2018');
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Sorry!</b> Unauthorized User.
                                    </div>');
				//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Block Employee',
								'action_taken'=>'Block',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail
			if($active_status=='A')
				redirect('employee/index');
			elseif($active_status=='I')
				redirect('employee/inactiveList');
			else
				redirect('employee/not_marking_from_01Aug2018');
		}	
		
	}
	
	function unblockEmp($emp_id=NULL)
	{	
		$data = $this->input->post();//changes by kamini23/07/019
		//echo $emp_id;exit;
		$checkBlockStatus=$this->employee_model->checkBlockStatus($emp_id);
		//echo $checkBlockStatus;exit;
		if($checkBlockStatus>0 && $this->employee_model->unblock_emp($emp_id))
		{
			//$transferLog=$this->employee_model->insert_banlog($emp_id);
			$transferLog=$this->employee_model->insert_banlog_reason($data);//changes by kamini23/07/019
			
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee UnBlocked successfully.
									</div>');
			//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Unblock Employee',
								'action_taken'=>'Unblock',
								'status'=>'success'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail
			redirect('employee/blockempList');
			
		}else if($checkBlockStatus>0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee not UnBlocked successfully.
                                    </div>');
			//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Unblock Employee',
								'action_taken'=>'Unblock',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail						
			redirect('employee/blockempList');
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Sorry!</b> Unauthorized User.
                                    </div>');
			//new added on 9-nov-2020 for user audit trail							
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Unblock Employee',
								'action_taken'=>'Unblock',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				//end new added on 9-nov-2020 for user audit trail						
			redirect('employee/blockempList');
		}	
		
	}
	
//export of blocked employees
	function blockempList_export($offset = 0)
	{
		$searchstr	=	$this->input->post('searchValue');
		if($searchstr)
		{
			$this->session->set_userdata('listBlockValue', $searchstr);
		}
		$searchValue	=	$this->session->userdata('listBlockValue');
		$this->load->library('Export');
		$this->load->helper('file');
		$filename = "csv_file_".time().".csv";
		$header = "List of Blocked Employees :";
	    $report =$this->employee_model->get_employeelistBlock_export($searchValue);
		$new_report = $this->export->to_excel($report,$filename,$header);
		$this->load->helper('download');
		force_download($filename, $new_report); 
	}

	//added by sandeep for employee block //
	function activeempList_export($offset = 0)
	{	
		//For location user only---start
		$this->grp_id = $this->nehbr_auth->get_group_id(); 
		$this->loc_id = $this->nehbr_auth->get_loc_id();
		$loc_id= $this->loc_id;
		//--------end
		$searchValue	=	$this->session->userdata('searchValue');
		$this->load->library('Export');
		$this->load->helper('file');
		$filename = "csv_file_".time().".csv";
		$header = "List of Active Employees :";
	    $report =$this->employee_model->get_activeemployeelist_export($searchValue,$loc_id);
		$new_report = $this->export->to_excel($report,$filename,$header);
		$this->load->helper('download');
		force_download($filename, $new_report); 
	}

	function inactiveempList_export($offset = 0)
	{	
		//For location user only---start
		$this->grp_id = $this->nehbr_auth->get_group_id(); 
		$this->loc_id = $this->nehbr_auth->get_loc_id();
		$loc_id= $this->loc_id;
		//--------end
		
		$searchValue1	=	$this->input->post('searchValue');
		if($searchValue1)
		{
			$this->session->set_userdata('searchValue1', $searchValue1);
		}
		$searchValue1	=	$this->session->userdata('searchValue1');
		$this->load->library('Export');
		$this->load->helper('file');
		$filename = "csv_file_".time().".csv";
		$header = "List of InActive Employees :";
	    $report =$this->employee_model->get_inactive_employeelist_export($searchValue1,$loc_id);
		$new_report = $this->export->to_excel($report,$filename,$header);
		$this->load->helper('download');
		force_download($filename, $new_report); 
	}
	
  //Not Marking attendance from 01Aug2018 /// 16oct2018	
	public function not_marking_from_01Aug2018($offset = 0)
	{	
	    $random_no=hash_random();
		$cookie = array(
		'name'   => 'random',
		'value'  => $random_no,
		'expire' => time()+86500,
		'path'   => '/',
		'HttpOnly'=>TRUE
		);
		set_cookie($cookie);		 
		$data['random']=$random_no;	
		$searchValue	=	$this->input->post('searchValue');
		if($searchValue)
		{
			$this->session->set_userdata('searchValue', $searchValue);
		}
		$searchValue	=	$this->session->userdata('searchValue');		
		if (preg_match("/([%\$'?;{}#@<>\*]+)/",$searchValue))
		{	
			$this->session->set_flashdata('message', 'Invalid Characters in Search string');
			redirect("/employee/not_marking_from_01Aug2018");
		}
		else{
			$data['geo_designation']=$this->employee_model->geo_designation();
			$data['officelocation']=$this->employee_model->get_officelocation();	
			$config= array();
			$config['base_url']    = base_url() . "employee/not_marking_from_01Aug2018";
			$config['total_rows']  = $this->employee_model->not_marking_from_01Aug2018_record_count($searchValue);
			$config['per_page']    = 10;
			$config['uri_segment'] =3;
			$choice = $config['total_rows'] / $config["per_page"];
			$this->pagination->initialize($config);        
			$page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
			$data['off_set'] = $page;
			$data['query'] = $this->employee_model->not_marking_from_01Aug2018_get_employeelist($config['per_page'], $page,$searchValue);
			//Print_r($data['query']);exit;
			$data['links']   = $this->pagination->create_links();
			$this->load->view('../../__inc/public_head',$data);			
			$this->load->view('employeeNotMarking01Aug2018');	
		}
	}
	
	function clearNotMarking_from_01Aug2018()
	{
		$this->session->unset_userdata('searchValue');
		redirect('employee/not_marking_from_01Aug2018');
	}
	
	function not_marking_empList_export($offset = 0)
	{	
		$searchValue	=	$this->session->userdata('searchValue');
		$this->load->library('Export');
		$this->load->helper('file');
		$filename = "csv_file_".time().".csv";
		$header = "List of Not Marking Attendance Employees From 01 August 2018:";
	    $report =$this->employee_model->get_not_marking_employeelist_export($searchValue);
		$new_report = $this->export->to_excel($report,$filename,$header);
		$this->load->helper('download');
		force_download($filename, $new_report); 
	}

	/*function detailNotMarkingAtt01Aug2018($emp_id=NULL)
	{
		$this->data = array();
		$this->u_id	= $this->nehbr_auth->get_user_id();
		$this->u_name= $this->nehbr_auth->get_username();
		//$data['organization_types']=$this->employee_model->get_org_types();
		$data['officelocation']=$this->employee_model->get_officelocation();		
		$check_NotMarkingAtt01Aug2018_Status=$this->employee_model->check_NotMarkingAtt01Aug2018_Status($emp_id);	
			
		if($emp_id!=NULL && $check_NotMarkingAtt01Aug2018_Status>0)
		{
			$data['organization_data']=$this->employee_model->get_detail_NotMarkingAtt01Aug2018($emp_id);
			$this->load->view('../../__inc/usr_header',$data);	
			$this->load->view('employee/detail_NotMarkingAttFrom_01Aug2018');
			
		}
		else
		{
			redirect('employee/not_marking_from_01Aug2018');			
		}
	}
	
	function notarkingAtt01Aug2018Delete($emp_id=NULL)
	{		
		$check_NotMarkingAtt01Aug2018_Status=$this->employee_model->check_NotMarkingAtt01Aug2018_Status($emp_id);
		if($check_NotMarkingAtt01Aug2018_Status>0 && $this->employee_model->notMarkingAtt01Aug2018Del($emp_id))
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee Deleted successfully.
                                    </div>');
			redirect('employee/not_marking_from_01Aug2018');
			
		}else if($check_NotMarkingAtt01Aug2018_Status>0){
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee not Deleted successfully.
                                    </div>');
			redirect('employee/not_marking_from_01Aug2018');
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Sorry!</b> Unauthorized User.
                                    </div>');
			redirect('employee/not_marking_from_01Aug2018');
		}	
	}
	*/
 //Not Marking attendance from 01Aug2018 /// 16oct2018	

 //new added by sandeep on 1-oct-2021 emp transfer super login 
	public function transfer_emp_admin()
	{
		header("Cache-Control: no-cache, must-revalidate");
		if($this->u_group_id == 1 OR $this->u_group_id == 2){
			$query_org = $this->employee_model->get_organization_list();

			if($query_org)
			{
				$data['org_down'] = $query_org;
			}
		//new changes added for audit chnages 07-june-2023	
			$this->form_validation->set_rules('org_id', 'Organization Name', 'trim|required|xss_clean|min_length[6]|max_length[6]|numeric|callback_char_check');
			$this->form_validation->set_rules('emp_id', 'Employee Name', 'trim|required|xss_clean|min_length[8]|max_length[8]|numeric');
			$this->form_validation->set_rules("reason", "Transfer Reason", "required|trim|xss_clean|callback_char_check");	
			$this->form_validation->set_error_delimiters('<div style="color:#FF0000;">', '</div>');
			 
			if($this->form_validation->run()==FALSE)
			{
				$this->load->view('../../__inc/usr_header', $data);
				$this->load->view('transferout_emp_admin',$data);
			}
			else{
				
				if($this->employee_model->transferActiveEmp_admin())
				{
					// data save	
					$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
										<i class="fa fa-check"></i>
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										<b>Success!</b> Employee Transfer Out Successful.
									</div>');
				
					$this->load->view('../../__inc/usr_header',$data);
					redirect('/employee/transfer_emp_admin');
				}
				else
				{   // data not save
					$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">
										<i class="fa fa-check"></i>
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										<b>Sorry!</b> Employee Transfer Out Not successful.
									</div>');
								
					$this->load->view('../../__inc/usr_header');
					redirect('/employee/transfer_emp_admin');
				}
			}
		}else{
			redirect('/auth/login1');
		}

	}
	
	
function emp_id_select_call()
{	     
	$arrorg=array();
	$org_id = $this->input->post('org_name');
	if ($org_id) 
	{
		$arrorg = $this->employee_model->_getAllWhere_orderadmin('emp','org_id',$org_id,'emp_name');

		if($arrorg)
		{
			$data['result'][]= '<option value="">- Select Admin-</option>';
			foreach ($arrorg as $emp) {

			if($emp->active_status=='A')
			$status='Active';
			if($emp->active_status=='I')
			$status='Inactive';
			if($emp->active_status=='X')
			$status='Blocked';
			if($emp->active_status=='N')
			$status='Not Marking';
			if($emp->active_status=='F')
			$status='Old Registered';
			if($emp->active_status=='R')
			$status='Newly Registered';

			$data['result'][]= '<option value="'.$emp->emp_id.'">'.$emp->emp_name.'('.$emp->emp_id.' - '.$status.')</option>';
			}
		}
	}
	$data['token_name']= $this->security->get_csrf_token_name();
	$data['token_value']= $this->security->get_csrf_hash();
	echo json_encode($data);
}

	
//new added by sandeep on 18-jan-2022              newly registered employee 
	public function listNewlyRegistered($offset=NULL)
	{	

		$data['geo_designation']=$this->employee_model->geo_designation();
		$data['officelocation']=$this->employee_model->get_officelocation();
		$search_str=$this->input->post('searchValue');
		//for location user only-----start
		$this->grp_id = $this->nehbr_auth->get_group_id(); 
		$this->loc_id = $this->nehbr_auth->get_loc_id();
		$loc_id= $this->loc_id;
		//---end
		
		$random_no=hash_random();
		$cookie = array(
		'name'   => 'random',
		'value'  => $random_no,
		'expire' => time()+86500,
		'path'   => '/',
		'HttpOnly'=>TRUE
		);
		set_cookie($cookie);
		$data['random']=$random_no;		
		
		if (preg_match("/([%\$'?;{}#@<>\*]+)/",$searchValue))
		{	
			$this->session->set_flashdata('message', 'Invalid Characters in Search string');
			redirect("/employee/listNewlyRegistered");
		}
		else{
			$config= array();
			$data['name'] = $search_str;
			if(!empty($search_str))
			{
				$this->session->set_userdata('search_emp_name',$search_str);
			}
			$sess_search_str=$this->session->userdata('search_emp_name');
			$data['search_string']=$sess_search_str;
			$config["base_url"] = base_url() . "employee/listNewlyRegistered";
			$config['total_rows']  = $this->employee_model->record_countNewlyRegistered($sess_search_str,$loc_id);
			$config['per_page']    = 10;
			$config['uri_segment'] =3;
			$choice = $config['total_rows'] / $config["per_page"];
			$this->pagination->initialize($config);        
			$page= ($this->uri->segment($config['uri_segment'])) ? $this->uri->segment($config['uri_segment']) : 0;		
			$data['off_set'] = $page;        
			$data['results'] = $this->employee_model->get_employeelistNewlyRegistered($config['per_page'], $page,$sess_search_str,$loc_id);
			$data['links']   = $this->pagination->create_links();	
			$this->load->view('../../__inc/usr_header', $data);	
			$this->load->view('employeeNewlyRegistered');
		}
	}
	
	function clearSearchNewlyRegistered()
	{
		$this->session->unset_userdata('search_emp_name');
		redirect('employee/listNewlyRegistered');	
	}
	
	function edit_NewlyRegistered()
	{
		if($this->input->post('emp_cat')=='GOV'){
			$this->form_validation->set_rules('desig_id', 'Designation', 'required|xss_clean');				
		}
		$id=$this->session->userdata("edit_id");
		$this->form_validation->set_rules('emp_name', 'Employee Name', 'trim|required|xss_clean|callback_char_check1');	
		$this->form_validation->set_rules('org_dept_id', 'Department', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('office_loc', 'Office Location', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('emp_dob', 'DOB', 'trim|xss_clean|callback_check_DOB');
		$this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean|callback_char_check');
		$this->form_validation->set_rules('emp_mobile', 'Mobile', 'trim|required|xss_clean|callback_char_check|callback_check_mobile');
		//$this->form_validation->set_rules('emp_mail', 'Email', 'trim|required|xss_clean|callback_check_email');	
		$this->form_validation->set_rules('emp_mail', 'Email', 'trim|required|xss_clean|valid_email');	
		$this->form_validation->set_rules('emp_cat', 'Emp Type', 'trim|required|xss_clean|callback_char_check');	
		$this->form_validation->set_rules("grade_pay", "Grade Pay", "required|trim|xss_clean|callback_char_check|callback_positive_check");
		$this->form_validation->set_rules("basic_pay", "Basic Pay", "required|xss_clean|callback_char_check|callback_positive_check");
		$this->form_validation->set_rules("reporting_id", "Reporting ID", "required|xss_clean|callback_char_check|callback_is_numeric_check|callback_Check_reporting_id");
		$this->form_validation->set_rules("reporting_name", "Reporting Name", "required|xss_clean|callback_char_check1");
		$data['org_names']=$this->organization_model->get_org_master();	
		$data['org_division']= $this->employee_model->orgdivision();			
		$data['aadhar_status']=$this->employee_model->get_aadharStatus();
		$checkStatuAndAadhar=$this->employee_model->checkStatusAndAadharNewlyRegistered($id);
		$data['demographStatus']=$this->employee_model->checkDemographStatus($id);		
	
		if (!empty($_FILES['image']['name']))
		{
   			$filename_lenght= count(explode('.',$_FILES['image']['name'])); 
			if($filename_lenght > 2)
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name contains multiple extensions');
			}					
			elseif (preg_match("/([%\$'?;{}#@<>\*]+)/", $_FILES['image']['name']))
			{
				$this->form_validation->set_rules('image', ' Input File', 'required');
				$this->form_validation->set_message('required', 'File name should be alphanumeric only');
			} 
			else{ 	
				if($this->validate_upload('image',$IsResize=1))
				{
					$image['content'] = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
					$image['type']=$_FILES['image']['type'];
				}
				else
				{
					if($this->upload_result!='')
					{
						$this->form_validation->set_rules('image', ' Input File', 'required');
						$this->form_validation->set_message('required', $this->upload_result);
					}
				}
			} 
		} 
			
		if ($this->form_validation->run() == false && $checkStatuAndAadhar>0) 
		{	
			$data['status'] = $this->employee_model->orgid_in_siosclist();	
			$data['query'] = $this->employee_model->view_employee_NewlyRegistered($id);
			$this->load->view('../../__inc/usr_header',$data);	
			$this->load->view('edit_verified_employee');
		}else
		{
			if($this->employee_model->update_employeeNew() && $checkStatuAndAadhar>0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <b>Success!</b> Employee data updated successfully.
                                    </div>');
									
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Newly Registered Employee',
								'action_taken'=>'Edit',
								'status'=>'success'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				$this->load->view('../../__inc/usr_header');
				redirect('/employee/listNewlyRegistered');	
			}else if($checkStatuAndAadhar>0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-close"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										 <b>Error!</b> Please try again. </div>');
			   					
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Newly Registered Employee',
								'action_taken'=>'Edit',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
										 
				$this->load->view('../../__inc/usr_header');
				redirect('/employee/listNewlyRegistered');			
			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">
                                        <i class="fa fa-close"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										 <b>Sorry!</b> Please try again. </div>');
										
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Newly Registered Employee',
								'action_taken'=>'Edit',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
									 
				$this->load->view('../../__inc/usr_header');
				redirect('/employee/listNewlyRegistered');	
			}
		}		
	}
	
	public function process_NewlyRegistered($emp_id=NULL)
	{
		
		$this->u_id	= $this->nehbr_auth->get_user_id();
		$this->u_name= $this->nehbr_auth->get_username();
		$msg="";	
		$checkVerified=$this->employee_model->checkNewlyRegistered($emp_id);
		if($emp_id!=NULL && $checkVerified>0)
		{
			$data=array();
			$data['organization_types']=$this->employee_model->get_org_types();
			$data['officelocation']=$this->employee_model->get_officelocation();
			$this->load->helper('form');
			$this->load->library('form_validation');
			$this->form_validation->CI=& $this;
			$this->form_validation->set_rules('processRemarks', 'Remarks', 'trim|required|callback_char_check');		
			$this->form_validation->set_rules('verifyStatus', 'Action to be taken', 'trim|required|callback_char_check');
			$this->form_validation->set_error_delimiters('<div style="color:#FF0000;">', '</div>');
			$emptmpId=$this->input->post('emp_id');
			$org_id=$this->input->post('org_id');
			
			if ($this->form_validation->run() == FALSE)
		 	{
				$data['organization_data']=$this->employee_model->get_forms_detail_NewlyRegistered($emp_id);						
				$this->load->view('../../__inc/usr_header',$data);	
				$this->load->view('employee/process');
		   	}
			else if($emptmpId==$emp_id)
			{
				$this->load->library('subdomain_lib');
				$this->org_details= $this->subdomain_lib->get_subdomain_detail();
				$this->org_full_domain = $this->org_details->full_domain;
				$this->org_subdomain = $this->org_details->subdomain;
				
				$status=$this->employee_model->verify_employee();	
				if($status['retutndata']=='Verified')
				{
						
					$msg.=' Employee Verification Process completed successfully ';
					$to=$status['emp_mail'];
					$subject='AEBAS';
					$message = 'Dear Sir/Madam, 
					Congratulations! Your Employee Registration is now Verified and Activated on '.$this->org_subdomain.'.'.$this->org_full_domain.' . Employee Name: '.$status['emp_name'].', Attendance ID: '.$status['emp_id'].'  
					Regards, AEBAS Team';
					$this->load->library('email');
				/* //commented for automatic audit 10-may-2023
					$this->email->from('noreply.attendance@gov.in','noreply.attendance@gov.in');
					$this->email->to($to);
					$this->email->subject($subject);
					$this->email->message($message);
					$retval = $this->email->send();
        					
				
					if($retval){
						$msg.='# Mail successfully sent to '.$status['emp_mail'].'.';	
					}else{						
						$msg.='# Mail not sent to '.$status['emp_mail'].'.';	
					}
				*/	//end commented for automatic audit 10-may-2023		
					$data['status_message']=$msg;					
											
					$mobilemsg = 'Dear Sir/Madam,Congratulations! Your Employee Registration is now Verified and Activated on '.$this->org_subdomain.'.'.$this->org_full_domain.' . Employee Name: '.$status['emp_name'].', Attendance ID: '.$status['emp_id'].' Regards, AEBAS Team';
					$this->send_sms_msg($status['emp_mobile'],$mobilemsg,$template_id="1107160818807909686"); 
										
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Employee Process',
								'action_taken'=>'Activate',
								'status'=>'success'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				
					
				}else if($status['retutndata']=='Rejected')
				{
					$msg.=' Employee Verification Rejected ';
					$to=$status['emp_mail'];
					$subject='AEBAS';
					$message='Dear Sir/Madam, 
					Your Registration with id no : '.$status['emp_id'].'  on ‘'.$this->org_subdomain.'.'.$this->org_full_domain.'’ is rejected by the Nodal officer. Please contact your Nodal Officer. 
					Regards, AEBAS Team';
					$this->load->library('email');
				/* //commented for automatic audit 10-may-2023	
					$this->email->from('noreply.attendance@gov.in','noreply.attendance@gov.in');
					$this->email->to($to);
					$this->email->subject($subject);
					$this->email->message($message);
					$retval = $this->email->send();
					
        					
					if($retval){
						$msg.='# Mail successfully sent to '.$status['emp_mail'].'.';	
					}else{						
						$msg.='# Mail not sent to '.$status['emp_mail'].'.';	
					}
				*/	//end commented for automatic audit 10-may-2023	
					$data['status_message']=$msg;
					$mobilemsg = 'Dear Sir/Madam, Your Registration with id no : '.$status['emp_id'].'  on '.$this->org_subdomain.'.'.$this->org_full_domain.' is rejected by the Nodal officer. Please contact your Nodal Officer. Regards, AEBAS Team';
					$this->send_sms_msg($status['emp_mobile'],$mobilemsg, $template_id ="1107160879392693743"); //commented for automatic audit 25-sept-2020
									
					$user_transactions_audit_trail_data=array(
								'user_id'=>$this->u_id,
								'username'=>$this->session->userdata('username'),
								'action_details'=>'Employee Process',
								'action_taken'=>'Reject',
								'status'=>'unsuccess'
								);
					$this->employee_model->user_transactions_audit_trail_entry($user_transactions_audit_trail_data);
				
				}else if($status['retutndata']=='notprocess'){
					$data['status_message']='Your Application is not Processed';	
				}
				$this->db_status=$data;
				$this->load->view('../../__inc/usr_header',$data);	
				$this->load->view('employee/success');
			}else
			{
				redirect('employee/listNewlyRegistered');	
			}
		}
		else
		{
			redirect('employee/listNewlyRegistered');
		}
	}
	
 //end new added by sandeep on 18-jan-2022
}
