<?php
class Admin_auth extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('admin_adminusers_auth/model_adminusers_auth', 'adminusers_auth');
		$this->load->model('admin_menu_auth/model_menu_auth', 'menu_auth');
	}
	
	function index(){}
	
	function publicAuth()
	{
		
		if($this->session->userdata("adminID")){
			redirect("/admincontrol/index/",'refresh');
		}
	
	}
	
	function displayAuth()
	{
		$display = "display:none;";
		if($this->session->userdata("adminID")){
			$display = "";
		}
		return $display;
	}
	
	function privateAuth()
	{
		if(!$this->session->userdata("adminID")){
			redirect("/admincontrol/login/",'refresh');
		}
	
	}
	
	
	function forbiddenAuth()
	{
		$uri2 = $this->uri->segment(2);
		$q = $this->adminusers_auth->getAccount("adminusers_auth",$this->session->userdata("adminID"));
		$adminusers_auth_level_id = "";
		if($q->num_rows() > 0)
		{
			$row = $q->row(); 
			$adminusers_auth_level_id = $row->adminusers_level_id;
		}
		$q = $this->menu_auth->getMenuFromUri("menu",$uri2);
		if($q->num_rows() > 0){
			$row = $q->row(); 
			$menu_id = $row->id;
			$q =  $this->menu_auth->getMenuPermission("menu_auth",$adminusers_auth_level_id,$menu_id);
			if($q->num_rows() == 0){
				redirect("/admincontrol/index/",'refresh');
			}
		}else{
			redirect("/admincontrol/index/",'refresh');
		}
	}
	
}

?>
