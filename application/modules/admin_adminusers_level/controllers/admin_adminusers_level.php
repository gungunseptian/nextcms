<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class admin_adminusers_level extends MX_Controller  {
	
	var $table = "adminusers_level";
	var $uri_page = 7;
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
		$refDropdown = Modules::run('admin_widget/getStaticDropdown',$search2_arr,$search2_parm,2);

		$get_page = $this->uri->segment(6);

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
		$path = base_url()."admincontrol/".$this->table."/pages/".$search1_parm."/".$search2_parm."/".$per_page;
		$jum_record = $this->adminusers_level->getTotal($this->table,$search1_parm,$search2_parm);
		$paging = Modules::run("admin_widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		$query = $this->adminusers_level->getList($this->table,$per_page,$lmt,$search1_parm,$search2_parm);
		$list = array();
		if($query->num_rows() > 0){
			foreach($query->result() as $row)
			{
				$no++;
				$zebra = $no % 2 == 0 ? "zebra" : "";
				$create_date = date("d/m/Y H:i:s",strtotime($row->create_date));
				
				$title = $row->title;
				$title = highlight_phrase($title, $search1_parm, '<span style="color:#990000">', '</span>');
				$publish  = $row->publish;
			
				$list[] = array(
								 "no"=>$no,
								 "id"=>$row->id,
								 "title" =>$title,
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
				  'search1_val'=>$search1_val,
				  'refDropdown'=>$refDropdown,
				  'per_page'=>$per_page,
				  'pg'=>$go_pg,
				  'title_head'=>ucwords(str_replace('_',' ',$this->table)),
				  'title_link'=>$this->table
				  );
		return $this->parser->parse("admin_".$this->table.".html", $data, TRUE);
	}
	
	function search()
	{
		$search1 = rawurlencode($this->input->post('search1'));
		$search2 = rawurlencode($this->input->post('ref2'));
		$per_page = rawurlencode($this->input->post('per_page'));

		if(empty($search1))
		{
			$search1 = 'null';
		}
		if(empty($search2))
		{
			$search2 = 'null';
		}

		redirect("admincontrol/".$this->table."/pages/".$search1."/".$search2."/".$per_page);
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
			
			#record
			$q = $this->adminusers_level->getDetail($this->table,$id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
				foreach($q->result() as $r){

					$ref_arr = array("Not Publish"=>"Not Publish","Publish"=>"Publish");
					$refDropdown = Modules::run('admin_widget/getStaticDropdown',$ref_arr,$r->publish,2);

					$list[] = array(
									"id"=>$r->id,
									"title"=>$r->title,
									"create_date"=>$r->create_date,
									"refDropdown"=>$refDropdown
									);
				}
			}else{
				
				
				$ref_arr = array("Not Publish"=>"Not Publish","Publish"=>"Publish");
				$refDropdown = Modules::run('admin_widget/getStaticDropdown',$ref_arr,null,3);

	
				$list[] = array(
									"id"=>0,
									"title"=>"",
									"create_date"=>"",
									"refDropdown"=>$refDropdown
									);
			}
			#end record

	
			#notification
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
			#end notification
		
			$data = array(
					  'base_url' => base_url(),
					  'notification'=>$notification,
					  'list'=>$list,
					  'title_head'=>ucwords(str_replace('_',' ',$this->table)),
				 	  'title_link'=>$this->table
					  );
			return $this->parser->parse("admin_".$this->table."_edit.html", $data, TRUE);
		}else{
			redirect("admincontrol/".$this->table);
		}
	}
	
	
	function submit()
	{
		$err = "";
		$title = strip_tags($this->input->post("title"));
		$publish = $this->input->post("publish");
		$ordered = $this->input->post("ordered");
		$link = $this->input->post("link");
		$user_id = $this->session->userdata('adminID');
		$id = strip_tags($this->input->post("id"));

	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'title', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			redirect("admincontrol/".$this->table."/edit/".$id);
		}else{
			if($id > 0)
			{
				$this->adminusers_level->setUpdate($this->table,$id,$title,$publish,$link,$user_id);
				$this->session->set_flashdata("success","Data saved successful");
				redirect("admincontrol/".$this->table."/edit/".$id);
			}else{
				$id_term = $this->adminusers_level->setInsert($this->table,$id,$title,$publish,$link,$user_id);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				redirect("admincontrol/".$this->table."/edit/".$last_id);
			}
		}
	}
	
	function delete($id=0)
	{
		$del_status = $this->adminusers_level->setDelete($this->table,$id);
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
	
	function getRefDropdown($id,$name,$type=NULL,$search1=null,$search2=null)
	{
		$q = $this->adminusers_level->getList($this->table,null,null,$search1,$search2,'dropdown');
		$list = array();
		
		foreach ($q->result() as $val) {
			$selected = $val->id == $id ? $selected = "selected='selected'" : "";	
			$qchild = $this->adminusers_level->getChild($this->table,$val->id);
			$title_parent = "";
			if($qchild->num_rows() == 0){
				if($val->parent_id > 0){
					$q_parent = $this->adminusers_level->getParent($this->table,$val->parent_id);
					$r_parent = $q_parent->row();
					$title_parent = $r_parent->title." - ";
				}
				$title = $title_parent.$val->title;
			}
			if($qchild->num_rows() == 0){
					$list[]= array(
						'id' => $val->id,
						'title'=>$title,
						"selected"=>$selected
					 );
			}
		}
		
		$data = array(
				"list"=>$list,
				"name"=>"ref".$name
				);
		return $this->parser->parse("admin_layout/ref_dropdown".$type.".html", $data, TRUE);
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */