<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class admin_layout extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('admin_adminusers_auth/model_adminusers_auth', 'adminusers_auth');
		$this->load->model('admin_menu_auth/model_menu_auth', 'menu_auth');
		date_default_timezone_set('Asia/Jakarta');
	}
	
	
	function index(){}
	
	
	function setheader_login()
	{
		$data = array(
				  'base_url' => base_url()
				  );
		$this->parser->parse('admin_layout/header_login.html', $data);
	}
	
	
	function setheader()
	{
		$uri2 = $this->uri->segment(2);
		$q = $this->adminusers_auth->getAccount("adminusers_auth",$this->session->userdata("adminID"));
		$admin_name =  "Unknown";
		$adminusers_auth_id = "#";
		$adminusers_auth_level_id = "";
		if($q->num_rows() > 0)
		{
			foreach($q->result() as $row)
			{
				$admin_name = $row->title;
				$adminusers_auth_level_id = $row->adminusers_level_id;
				$adminusers_auth_id = $row->id;
			}
		}
		
		
		$q =  $this->menu_auth->getMenu("menu_auth",$adminusers_auth_level_id);
		$list_menu = array();
		if($q->num_rows() > 0)
		{
			foreach($q->result() as $r_menu)
			{
				$q_menu1 =  $this->menu_auth->getMenu("menu_auth",$adminusers_auth_level_id,$r_menu->menu_id);
				$list_menu1 = array();
				$num_menu1 = $q_menu1->num_rows();
				if($num_menu1 > 0)
				{
					foreach($q_menu1->result() as $r_menu1)
					{
							$q_menu2 =  $this->menu_auth->getMenu("menu_auth",$adminusers_auth_level_id,$r_menu1->menu_id);
							$list_menu2 = array();
							$num_menu2 = $q_menu2->num_rows();
							if($num_menu2 > 0)
							{
								foreach($q_menu2->result() as $r_menu2)
								{
									$divider_menu2 = $r_menu1->menu_divider == "Yes" ? "<li class=\"divider\"></li>" : "";
									$uri_menu2 = $r_menu2->menu_uri == "#" ? $r_menu2->menu_uri : site_url('admincontrol')."/".$r_menu2->menu_uri;
									$actived_menu2 = $uri2 == $r_menu2->menu_uri ? "active" : "";
									$dropdown_toogle_menu2 = "";
									$caret_menu2 = "";
									$list_menu2[] = array(
												 "title"=>ucwords($r_menu2->menu_title),
												 "uri"=>$uri_menu2,
												 "actived"=>$actived_menu2,
												 "dropdown_toogle"=>$dropdown_toogle_menu2,
												 "caret"=>$caret_menu2,
												 "dropdown_menu"=>$dropdown_menu,
												 "divider"=>$divider_menu2
												 );
								}
							}
					
						$divider_menu1 = $r_menu1->menu_divider == "Yes" ? "<li class=\"divider\"></li>" : "";
						$uri_menu1 = $r_menu1->menu_uri == "#" ? $r_menu1->menu_uri : site_url('admincontrol')."/".$r_menu1->menu_uri;
						$actived_menu1 = $uri2 == $r_menu1->menu_uri ? "active" : "";
						$dropdown_toogle1 = "";
						$dropdown_menu1 = $num_menu2 > 0 ? "class=\"dropdown-menu sub-menu\"" : "";
						$caret_menu1 = $num_menu2 > 0 ? "<i class=\"icon-arrow-right\"></i>" : "";
						$list_menu1[] = array(
									 "list_menu2"=>$list_menu2,
									 "title"=>ucwords($r_menu1->menu_title),
									 "uri"=>$uri_menu1,
									 "actived"=>$actived_menu1,
									 "dropdown_toogle"=>$dropdown_toogle_menu1,
									 "caret"=>$caret_menu1,
									 "dropdown_menu"=>$dropdown_menu1,
									 "divider"=>$divider_menu1
									 );
					}
				}
				
				$q_selectParent = $this->menu_auth->getSelectParentMenu("menu",$uri2,$r_menu->menu_id);
				$divider = $r_menu->menu_divider == "Yes" ? "<li class=\"divider\"></li>" : "";
				$uri_menu = $r_menu->menu_uri == "#" ? $r_menu->menu_uri : site_url('admincontrol')."/".$r_menu->menu_uri;
				$actived_menu = $uri2 == $r_menu->menu_uri || $q_selectParent->num_rows() > 0 ? "active" : "";
				$dropdown_toogle = $num_menu1 > 0 ? "data-toggle=\"dropdown\" class=\"dropdown-toggle\"" : "";
				$dropdown_menu = $num_menu1 > 0 ? "class=\"dropdown-menu\" id=\"menu\"" : "";
				$caret = $num_menu1 > 0 ? "<b class=\"caret\"></b>" : "";
				
				
				
				$list_menu[] = array(
									 "list_menu1"=>$list_menu1,
									 "title"=>ucwords($r_menu->menu_title),
									 "uri"=>$uri_menu,
									 "actived"=>$actived_menu,
									 "dropdown_toogle"=>$dropdown_toogle,
									 "caret"=>$caret,
									 "dropdown_menu"=>$dropdown_menu,
									 "divider"=>$divider
									 );
			}
		}
		
		$data = array(
				  'base_url' => base_url(),
				  'admin_url'=>site_url('admincontrol')."/",
				  'list_menu' => $list_menu,
				  'admin_name'=>$admin_name,
				  'adminusers_auth_id'=>$adminusers_auth_id
				  );
		$this->parser->parse('admin_layout/header.html', $data);
	}
	
	
	function setfooter()
	{
		$year = gmdate("Y");
		$data = array(
				  'base_url' => base_url(),
				  'year'=>$year
				  );
		$this->parser->parse('admin_layout/footer.html', $data);
	}
	
	
	function pre($array)
	{
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
	

}

?>
