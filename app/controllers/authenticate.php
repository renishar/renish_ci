<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Authenticate extends CI_Controller {

	public $s_id , $s_mail , $s_name , $s_image , $joined;
	function __construct() {  
	   	parent::__construct();
	   	nocache();
	   	$this->load->model('auth','',TRUE); 
	   	$this->load->model('user','',TRUE);
	   	$this->load->model('ind','',TRUE);
	   	$this->load->model('set','',TRUE);
	   	$this->load->library('facebook'); // Automatically picks appId and secret from config
		if(!empty($this->session->userdata['userLogged_in'])){ 
	   		$session_array=$this->session->userdata['userLogged_in'];    
	   		$this->s_id=$session_array['id'];
	   		$this->s_mail=$session_array['email'];
	   		$this->s_name=$session_array['first_name']." ".$session_array['last_name'];
	   		$this->s_image=$session_array['image']; 
	   		$this->joined=$session_array['joined'];
	    } 
	}
	 
	
	public function login()
	{
		if($_POST){   
			$this->form_validation->set_rules('email', 'Email ID', 'trim|required|valid_email|xss_clean');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
			if($this->form_validation->run() != FALSE){
				redirect($_SERVER['HTTP_REFERER']); 
				//redirect('user/authenticate', 'location');
			}  
		} 
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	
	public function fbLogin()
	{
	
		$user = $this->facebook->getUser();
		if ($user) {
			try {
				$user_profile = $this->facebook->api('/me'); 
				$data['user_profile']=$user_profile;
	
			} catch (FacebookApiException $e) {
				$user = null;
			}
		}else {
			$this->facebook->destroySession();
		}
	
		if ($user) {
	
			$data['logout_url'] = site_url('user/authenticate/logout'); // Logs off application
			if($user_profile!=""){
					
				 $userverified=$user_profile['verified'];
				 $usermail=$user_profile['email'];  
				 
				 if($userverified==1){
					 $result = $this->auth->fbLogin($usermail); 
					 if($result)
					 {
					 	$sess_array = array();
					 	foreach($result as $row)
					 	{
					 		$sess_array = array(
					 				'id' => $row->id,
					 				'email' => $row->email,
					 				'first_name' => $row->first_name,
					 				'last_name' => $row->last_name,
					 				'image' => $row->image ,
					 				'joined' => $row->created_date
					 		);
					 		$this->session->set_userdata('userLogged_in', $sess_array);
					 		redirect('home','');
					 	} 
					 }else{
					 	$fname=$user_profile['first_name'];
					 	$lname=$user_profile['last_name'];
					 	$dob=date('Y-m-d',strtotime($user_profile['birthday']));
					 	$input=array(
					 			'first_name' 				=> $fname,
					 			'last_name' 				=> $lname,
					 			'email' 					=> $usermail,
					 			'dob' 						=> $dob, 
					 			'security_question_id'		=> 1, 
					 			'role' 						=> 2 ,
					 			'status' 					=> 1
					 	); 
					 	$ins=$this->user->addUser($input);
					 	$result = $this->auth->fbLogin($usermail);
					 	if($result) {
					 	$sess_array = array();
					 	foreach($result as $row)
					 	{
					 		$sess_array = array(
					 				'id' => $row->id,
					 				'email' => $row->email,
					 				'first_name' => $row->first_name,
					 				'last_name' => $row->last_name,
					 				'image' => $row->image ,
					 				'joined' => $row->created_date
					 		);
					 		$this->session->set_userdata('userLogged_in', $sess_array);
					 		redirect('home','');
					 	}
					 }	
					}
				} 
			} 
		} 
		 
	}
	
	public function newLogin(){ 
		//$password=$this->olt->encPas($this->input->post('log_password'));echo $password; 
		if(($this->input->post('log_email')!="")&&($this->input->post('log_password')!="")){
			 $result=$this->newcheck_database($this->input->post('log_email'),$this->input->post('log_password'));
			 echo $result;
		} 
	}
	
	 
	function check_database($password)
	{
		//Field validation succeeded.  Validate against database
		$email = $this->input->post('email');
		$password=(string)$this->olt->encPas($password);
	
		//query the database
		$result = $this->auth->login($email, $password);
	
		if($result)
		{
			$sess_array = array();
			foreach($result as $row)
			{
				$sess_array = array(
						'id' => $row->id,
						'email' => $row->email,
						'first_name' => $row->first_name,
						'last_name' => $row->last_name,
						'image' => $row->image ,
						'joined' => $row->created_date
				);
				$this->session->set_userdata('userLogged_in', $sess_array);
			}
			return TRUE;
		}else{
			$this->form_validation->set_message('check_database', 'Invalid username or password');
			return false;
		}
	}

	function newcheck_database($email,$password)
	{
		//Field validation succeeded.  Validate against database
		//$email = $this->input->post('email'); 
		
		$password=$this->olt->encPas($password); 
	
		//query the database
		$result = $this->auth->login($email, $password);
	
		if($result)
		{
			$sess_array = array();
			foreach($result as $row)
			{
				$sess_array = array(
						'id' => $row->id,
						'email' => $row->email,
						'first_name' => $row->first_name,
						'last_name' => $row->last_name,
						'image' => $row->image ,
						'joined' => $row->created_date
				);
				$this->session->set_userdata('userLogged_in', $sess_array);
			}
			echo 1;
		}else{
			//$this->form_validation->set_message('check_database', 'Invalid username or password');
			echo 2;
		}
	}
	
	
	public function signup(){
		if($_POST){ 
			$this->form_validation->set_rules('first_name', 'First Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|xss_clean|is_unique[user.email]');
			$this->form_validation->set_rules('dob', 'DOB', 'trim|required|xss_clean');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|matches[confirm_password]');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('security_question', 'Security Question', 'trim|required|xss_clean');
			$this->form_validation->set_rules('security_answer', 'Security Question Answer', 'trim|required|xss_clean');
		 	if($this->form_validation->run() != FALSE){
		 		$password=$this->olt->encPas($this->input->post('confirm_password'));
		 		$input=array(
		 				'first_name' 				=> $this->input->post('first_name'),
		 				'last_name' 				=> $this->input->post('last_name'),
		 				'email' 					=> $this->input->post('email'),
		 				'dob' 						=> date('Y-m-d',strtotime($this->input->post('dob'))),
		 				'password' 					=> $password ,
		 				'security_question_id'		=> $this->input->post('security_question'),
		 				'security_question_answer'	=> $this->input->post('security_answer'),
		 				'role' 						=> 2 ,
		 				'status' 					=> 1
		 		);
		 		$ins=$this->user->addUser($input);
		 		
		 		//query the database
		 		$result = $this->auth->login($this->input->post('email'), $password);
		 		
		 		if($result)
		 		{
		 			$sess_array = array();
		 			foreach($result as $row)
		 			{
		 				$sess_array = array(
		 						'id' => $row->id,
		 						'email' => $row->email,
		 						'first_name' => $row->first_name,
		 						'last_name' => $row->last_name,
		 						'image' => $row->image ,
		 						'joined' => $row->created_date
		 				);
		 				$this->session->set_userdata('userLogged_in', $sess_array);
		 			} 
		 		}
				 
			}  
			//redirect('Home','');
		} 
	}
	
	public function newsignup(){
		  
		if(($this->input->post('first_name')!="")&&( $this->input->post('last_name')!="")&&($this->input->post('email')!="")&&($this->input->post('dob')!="")&&($this->input->post('security_question')!="")&&($this->input->post('security_answer')!="")&&($this->input->post('confirm_password')!=""))
		{
			$password=$this->olt->encPas($this->input->post('confirm_password'));
			$input=array(
					'first_name' 				=> $this->input->post('first_name'),
					'last_name' 				=> $this->input->post('last_name'),
					'email' 					=> $this->input->post('email'),
					'dob' 						=> date('Y-m-d',strtotime($this->input->post('dob'))),
					'password' 					=> $password ,
					'security_question_id'		=> $this->input->post('security_question'),
					'security_question_answer'	=> $this->input->post('security_answer'),
					'role' 						=> 2 ,
					'status' 					=> 1
			);
			$ins=$this->user->addUser($input);
			if($ins){ 
			//query the database
			$result = $this->auth->login($this->input->post('email'), $password);
			 
			if($result)
			{
				$sess_array = array();
				foreach($result as $row)
				{
					$sess_array = array(
							'id' => $row->id,
							'email' => $row->email,
							'first_name' => $row->first_name,
							'last_name' => $row->last_name,
							'image' => $row->image ,
							'joined' => $row->created_date
					);
					$this->session->set_userdata('userLogged_in', $sess_array);
				}
			}
			echo 1;
			}
		}	
	}
	
	public function forgotPassword(){
		$email=$this->input->post('email');
		$question=$this->input->post('security_question');
		$answer=$this->input->post('answer');
		$check=$this->auth->existingUser($email,$question,$answer);
		if(!empty($check)){
			foreach($check as $row){
				$receiver=$row->first_name." ".$row->last_name;
				$user=$row->id;
			}
			$newpass=genPas();
			$sitename="www.choozyee.com";
			$sender="Choozyee Admin";
			$mail_template=$this->auth->mailTemplate('forgot_password');
			if(!empty($mail_template)){
				foreach($mail_template as $temp){
					$content=$temp->content; 
				}
				$content = str_replace("{receiverName}",$receiver,$content);
				$content = str_replace("{siteName}",$sitename,$content);
				$content = str_replace("{new_password}",$newpass,$content);
				$content = str_replace("{adminName}",$sender,$content); 
			}else{
				$content="Your new password is - ".$newpass;
			}
			$ch_pwd=$this->olt->encPas($newpass);
			$data=array('password' => $ch_pwd);
			$upd=$this->auth->pwdChange($data,$user); 
			if($upd){  
				$this->load->library('email');
				$this->email->set_mailtype("html");
				$this->email->from('admin@choozyee.com', 'Choozyee Admin');
				$this->email->to($email);
				
				$this->email->subject('Password Recovery');
				$this->email->message($content);
				$this->email->send();
				
				echo 1;
			}
			
		}else {
			echo 2;
		}
		 
	}
	
	public function logout(){ 
		$this->session->unset_userdata('userLogged_in');
		$this->session->sess_destroy();
		$this->facebook->destroySession();
		redirect(base_url('user'), 'location');
	}
}
?>
