<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class admin_adminusers_auth extends MX_Controller  {
	
	var $table = "adminusers_auth";
	var $uri_page = 8;
	var $per_page = 10;
	 
	function __construct()
	{
		parent::__construct();
		$this->load->model("admin_".$this->table."/model_".$this->table, $this->table);
		$this->lang->load('elemen_layout', 'indonesia');
	}
	
	public function setheader()
	{
		return Modules::run('admin_layout/setheader');
	}

	public function setfooter()
	{
		return Modules::run('admin_layout/setfooter');
	}
	 
	public function auth()
	{
		return Modules::run('admin_auth/privateAuth');
	}

	public function forbiddenAuth()
	{
		return Modules::run('admin_auth/forbiddenAuth');
	}

	function index()
	{
		$this->auth();
		$this->forbiddenAuth();
		$this->grid();
	}


	function grid()
	{
		$this->setheader();		
		$contents = $this->grid_content();
	
		$data = array(
				  'base_url' => base_url(),
				  'contents' => $contents,
				  );
		$this->parser->parse('admin_layout/contents.html', $data);
		
		$this->setfooter();
	}
	
	
	
	function grid_content()
	{	
		$this->load->helper('text');
		$search1_parm = rawurldecode($this->uri->segment(4));
		$search1_parm = $search1_parm != 'null' && !empty($search1_parm) ? $search1_parm : 'null';
		$search1_val = $search1_parm != 'null' ? $search1_parm : '';
		
		$search2_parm = rawurldecode($this->uri->segment(5));
		$search2_parm = $search2_parm != 'null' && !empty($search2_parm) ? $search2_parm : 'null';
		$search2_arr = array("Not Publish"=>"Not Publish","Publish"=>"Publish");
		$refDropdown2 = Modules::run('admin_widget/getStaticDropdown',$search2_arr,$search2_parm,2);

		$search3_parm = rawurldecode($this->uri->segment(6));
		$search3_parm = $search3_parm != 'null' && !empty($search3_parm) ? $search3_parm : 'null';
		$refDropdown = Modules::run('admin_adminusers_level/getRefDropdown',$search3_parm,1,null,null,'publish');


		$get_page = $this->uri->segment(7);

		#paging
		$uri_segment = $this->uri_page;
		$pg = $this->uri->segment($uri_segment);
		$per_page = !empty($get_page) ? $get_page : $this->per_page;
		$no = $go_pg = !$pg ? 0 : $pg;
		if(!$pg)
		{
			$lmt = 0;
			$pg = 1;
		}else{
			$lmt = $pg;
		}
		$path = base_url()."admincontrol/".$this->table."/pages/".$search1_parm."/".$search2_parm."/".$search3_parm."/".$per_page;
		$jum_record = $this->adminusers_auth->getTotal($this->table,$search1_parm,$search2_parm,$search3_parm);
		$paging = Modules::run("admin_widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		
		$query = $this->adminusers_auth->getList($this->table,$per_page,$lmt,$search1_parm,$search2_parm,$search3_parm);
		$list = array();
		if($query->num_rows() > 0){
			foreach($query->result() as $row)
			{
				$no++;
				$zebra = $no % 2 == 0 ? "zebra" : "";
				$create_date = date("d/m/Y H:i:s",strtotime($row->create_date));
				
				$username = $row->username;
				$username = highlight_phrase($username, $search1_parm, '<span style="color:#990000">', '</span>');
				$publish  = $row->publish;
			
				$list[] = array(
								 "no"=>$no,
								 "id"=>$row->id,
								 "title" =>$username,
								 "level" =>$row->title,
								 "publish"=>$publish,
								 "create_date"=>$create_date
								);
			}
		}	
	
		$data = array(
				  'base_url' => base_url(),
				  'paging'=>$paging,
				  'list'=>$list,
				  'jum_record'=>$jum_record,
				  'display_record'=>$display_record,
				  'search1_parm'=>$search1_parm,
				  'search2_parm'=>$search2_parm,
				  'search3_parm'=>$search3_parm,
				  'search1_val'=>$search1_val,
				  'refDropdown'=>$refDropdown,
				  'refDropdown2'=>$refDropdown2,
				  'per_page'=>$per_page,
				  'pg'=>$go_pg,
				  'title_head'=>ucwords(str_replace("_"," ",$this->table)),
				  'title_link'=>$this->table
				  );
		return $this->parser->parse("admin_".$this->table.".html", $data, TRUE);
	}
	
	function search()
	{
		$search1 = rawurlencode($this->input->post('search1'));
		$search2 = rawurlencode($this->input->post('ref2'));
		$search3 = rawurlencode($this->input->post('ref1'));
		$per_page = rawurlencode($this->input->post('per_page'));
		
		if(empty($search1))
		{
			$search1 = 'null';
		}
		if(empty($search2))
		{
			$search2 = 'null';
		}
		if(empty($search3))
		{
			$search3 = 'null';
		}
		redirect("admincontrol/".$this->table."/pages/".$search1."/".$search2."/".$search3."/".$per_page);
	}
	
	
	function edit()
	{
		$this->setheader();		
		$id = $this->uri->segment(4);
		$contents = $this->edit_content($id);
	
		$data = array(
				  'base_url' => base_url(),
				  'contents' => $contents,
				  );
		$this->parser->parse('admin_layout/contents.html', $data);
		
		$this->setfooter();
	}
	
	
	
	function edit_content($id)
	{
		$number = 0;
		$file_image = "";
		
		if(is_numeric($id)){
			
			$q = $this->adminusers_auth->getDetail($this->table,$id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
				foreach($q->result() as $r){
					
					$ref_arr = array("Not Publish"=>"Not Publish","Publish"=>"Publish");
					$refDropdown2 = Modules::run('admin_widget/getStaticDropdown',$r->publish,null,2);
					
					
					$adminusers_level_id = $r->adminusers_level_id;

					$list[] = array(
									"id"=>$r->id,
									"title"=>$r->username,
									"password"=>$r->password,
									"create_date"=>$r->create_date,
									"refDropdown2"=>$refDropdown2
									);
				}
			}else{
				
				
				$ref_arr = array("Not Publish"=>"Not Publish","Publish"=>"Publish");
				$refDropdown2 = Modules::run('admin_widget/getStaticDropdown',$ref_arr,null,2);

				$adminusers_level_id = "";


				$list[] = array(
									"id"=>0,
									"title"=>"",
									"password"=>"",
									"create_date"=>"",
									"refDropdown2"=>$refDropdown2
									);
			}
			
			
			$refDropdown = Modules::run('admin_adminusers_level/getRefDropdown',$adminusers_level_id,1,null,null,'publish');

	
			//notification
			$err = $this->session->flashdata("err") ? $this->session->flashdata("err") : "";
			$success = $this->session->flashdata("success") ? $this->session->flashdata("success") : "";
			$notification = array();
			if(!empty($success)){
				$notification[] = array(
									"msg_title"=>$success,
									"msg_class"=>"warning fade in"
									);
			}else if(!empty($err)){
				$notification[] = array(
									"msg_title"=>$err,
									"msg_class"=>"alert-message error fade in"
									);
			}
		
			$data = array(
					  'base_url' => base_url(),
					  'notification'=>$notification,
					  'list'=>$list,
					  'title_head'=>ucwords(str_replace('_',' ',$this->table)),
				 	  'title_link'=>$this->table,
					  "refDropdown"=>$refDropdown
					  );
			return $this->parser->parse("admin_".$this->table."_edit.html", $data, TRUE);
		}else{
			redirect("admincontrol/".$this->table);
		}
	}
	
	
	function submit()
	{
		$err = "";
		$username = strip_tags($this->input->post("username"));
		$password = $this->input->post("password");
		$password_old = $this->input->post("password_old");
		$publish = $this->input->post("publish");
		$adminusers_level_id = $this->input->post("ref1");
		$user_id = $this->session->userdata('adminID');
		$id = strip_tags($this->input->post("id"));
		
	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'username', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			redirect("admincontrol/".$this->table."/edit/".$id);
		}else{
			
			if(!empty($password)){
				$password = md5($password);
				$key = "adminkey";
				$password = md5($key.$password);
			}else{
				$password = $password_old;
			}
			
			if($id > 0)
			{
				$this->adminusers_auth->setUpdate($this->table,$id,$username,$password,$publish,$adminusers_level_id,$user_id);
				$this->session->set_flashdata("success","Data saved successful");
				redirect("admincontrol/".$this->table."/edit/".$id);
			}else{
				$this->adminusers_auth->setInsert($this->table,$id,$username,$password,$publish,$adminusers_level_id,$user_id);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				redirect("admincontrol/".$this->table."/edit/".$last_id);
			}
		}
	}
	
	
	function edit_account()
	{
		$this->setheader();		
		$id = $this->uri->segment(4);
		$contents = $this->edit_account_content($id);
	
		$data = array(
				  'base_url' => base_url(),
				  'contents' => $contents,
				  );
		$this->parser->parse('admin_layout/contents.html', $data);
		
		$this->setfooter();
	}
	
	
	
	function edit_account_content($id)
	{
		$number = 0;
		$file_image = "";
		
		if(is_numeric($id)){
			
			$q = $this->adminusers_auth->getDetail($this->table,$id);
			$list =  array();
			if($q->num_rows() > 0){
				foreach($q->result() as $r){

					$list[] = array(
									"id"=>$r->id,
									"title"=>$r->username,
									"level_title"=>$r->title,
									"password"=>$r->password,
									"publish"=>$r->publish,
									"ref1"=>$r->adminusers_level_id,
									"create_date"=>$r->create_date
									);
				}
			}else{
				

				$list[] = array(
									"id"=>0,
									"title"=>"",
									"level_title"=>"",
									"password"=>"",
									"publish"=>"",
									"ref1"=>"",
									"create_date"=>""
									);
			}

	
			//notification
			$err = $this->session->flashdata("err") ? $this->session->flashdata("err") : "";
			$success = $this->session->flashdata("success") ? $this->session->flashdata("success") : "";
			$notification = array();
			if(!empty($success)){
				$notification[] = array(
									"msg_title"=>$success,
									"msg_class"=>"warning fade in"
									);
			}else if(!empty($err)){
				$notification[] = array(
									"msg_title"=>$err,
									"msg_class"=>"alert-message error fade in"
									);
			}
		
			$data = array(
					  'base_url' => base_url(),
					  'notification'=>$notification,
					  'list'=>$list,
					  'title_head'=>ucwords(str_replace('_',' ',$this->table)),
				 	  'title_link'=>$this->table
					  );
			return $this->parser->parse("admin_".$this->table."_account_edit.html", $data, TRUE);
		}else{
			redirect("admincontrol/".$this->table);
		}
	}
	
	
	function submit_account()
	{
		$err = "";
		$username = strip_tags($this->input->post("username"));
		$password = $this->input->post("password");
		$password_old = $this->input->post("password_old");
		$publish = $this->input->post("publish");
		$adminusers_level_id = $this->input->post("ref1");
		$user_id = $this->session->userdata('adminID');
		$id = strip_tags($this->input->post("id"));
		
	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'username', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			redirect("admincontrol/".$this->table."/edit_account/".$id);
		}else{
			
			if(!empty($password)){
				$password = md5($password);
				$key = "adminkey";
				$password = md5($key.$password);
			}else{
				$password = $password_old;
			}
			
			if($id > 0)
			{
				$this->adminusers_auth->setUpdate($this->table,$id,$username,$password,$publish,$adminusers_level_id,$user_id);
				$this->session->set_flashdata("success","Data saved successful");
				redirect("admincontrol/".$this->table."/edit_account/".$id);
			}else{
				$this->adminusers_auth->setInsert($this->table,$id,$username,$password,$publish,$adminusers_level_id,$user_id);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				redirect("admincontrol/".$this->table."/edit_account/".$last_id);
			}
		}
	}
	
	
	function delete($id=0)
	{
		$del_status = $this->adminusers_auth->setDelete($this->table,$id);
		$response['id'] = $id;
		$response['status'] = $del_status;
		echo $result = json_encode($response);
		exit();
	}
	
	function unlink($id,$file_image)
	{
		$this->db->where("id",$id);
		$this->db->update($this->table,array("file_image"=>""));
		unlink("uploads/".$file_image);
		redirect("admincontrol/".$this->table."/edit/".$id);
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */