<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class admin_menu extends MX_Controller  {
	
	var $table = "menu";
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
		$refDropdown = Modules::run('admin_menu/getRefDropdown',$search3_parm,1);

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
		$jum_record = $this->menu->getTotal($this->table,$search1_parm,$search2_parm,$search3_parm);
		$paging = Modules::run("admin_widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		$query = $this->menu->getList($this->table,$per_page,$lmt,$search1_parm,$search2_parm,$search3_parm);
		$list = array();
		if($query->num_rows() > 0){
			foreach($query->result() as $row)
			{
				$no++;
				$zebra = $no % 2 == 0 ? "zebra" : "";
				$create_date = date("d/m/Y H:i:s",strtotime($row->create_date));
				
				$title = ucwords($row->title);
				$parent_title = ucwords($this->menu->getParentList($this->table,$row->parent_id));
				$title = highlight_phrase($title, $search1_parm, '<span style="color:#990000">', '</span>');
				$publish  = $row->publish;
			
				$list[] = array(
								 "no"=>$no,
								 "id"=>$row->id,
								 "title" =>$title,
								 "parent" =>$parent_title,
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
				  'refDropdown2'=>$refDropdown2,
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
			
			$q = $this->menu->getDetail($this->table,$id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
				foreach($q->result() as $r){
					
					
					$ref2_arr = array("No"=>"No","Yes"=>"Yes");
					$refDropdown2 = Modules::run('admin_widget/getStaticDropdown',$ref2_arr,$r->divider,2);
					
					$ref3_arr = array("Not Publish"=>"Not Publish","Publish"=>"Publish");
					$refDropdown3 = Modules::run('admin_widget/getStaticDropdown',$ref3_arr,$r->publish,3);


				
					$parent_id = $r->parent_id;
					$id = $r->id;

					$list[] = array(
									"id"=>$r->id,
									"title"=>$r->title,
									"ordered"=>$r->ordered,
									"uri"=>$r->uri,
									"create_date"=>$r->create_date,
									"refDropdown2"=>$refDropdown2,
									"refDropdown3"=>$refDropdown3
									);
				}
			}else{
				
				$parent_id = $id = "";
				
				$ref2_arr = array("No"=>"No","Yes"=>"Yes");
				$refDropdown2 = Modules::run('admin_widget/getStaticDropdown',$ref2_arr,null,2);
					
				$ref3_arr = array("Not Publish"=>"Not Publish","Publish"=>"Publish");
				$refDropdown3 = Modules::run('admin_widget/getStaticDropdown',$ref3_arr,null,3);
				
				$list[] = array(
									"id"=>0,
									"title"=>"",
									"uri"=>"",
									"ordered"=>"",
									"create_date"=>"",
									"refDropdown2"=>$refDropdown2,
									"refDropdown3"=>$refDropdown3
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
			
			
			$refDropdown = $this->getRefDropdownParent($id,$parent_id,1);
			
			$data = array(
					  'base_url' => base_url(),
					  'notification'=>$notification,
					  'list'=>$list,
					  'title_head'=>ucwords(str_replace('_',' ',$this->table)),
				 	  'title_link'=>$this->table,
					  'refDropdown'=>$refDropdown
					  );
			return $this->parser->parse("admin_".$this->table."_edit.html", $data, TRUE);
		}else{
			redirect("admincontrol/".$this->table);
		}
	}
	
	
	function submit()
	{
		$err = "";
		$ref1 = $this->input->post("ref1");
		$title = strip_tags($this->input->post("title"));
		$uri = $this->input->post("uri");
		$ref2 = $this->input->post("ref2");
		$ref3 = $this->input->post("ref3");
		$ordered = $this->input->post("ordered");
		$user_id = $this->session->userdata('adminID');
		$id = strip_tags($this->input->post("id"));
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'title', 'required');
		$this->form_validation->set_rules('uri', 'uri', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			redirect("admincontrol/".$this->table."/edit/".$id);
		}else{
			if($id > 0)
			{
				$this->menu->setUpdate($this->table,$id,$ref1,$title,$ref2,$ordered,$ref3,$uri,$user_id);
				$this->session->set_flashdata("success","Data saved successful");
				redirect("admincontrol/".$this->table."/edit/".$id);
			}else{
				
				$q = $this->menu->getMax($this->table);
				$r = $q->row();
				$ordered = $r->max_ordered+1;
				
				$id_term = $this->menu->setInsert($this->table,$id,$ref1,$title,$ref2,$ordered,$ref3,$uri,$user_id);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				redirect("admincontrol/".$this->table."/edit/".$last_id);
			}
		}
	}
	
	
	function ajaxsort()
	{
		$post = $this->input->post('data');
		$order =  $this->input->post('index_order');
		foreach($post as $val)
		{
			$order++;
			$this->menu->ajaxsort($this->table,$val,$order);
		}
	}
	
	
	function delete($id=0)
	{
		$del_status = $this->menu->setDelete($this->table,$id);
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
	
	function getRefDropdownParent($id,$parent_id,$name,$type=NULL)
	{
		$q = $this->menu->getMenuList($this->table,$id);
		$list = array();
		foreach ($q->result() as $val) {
			$selected = $val->id == $parent_id ? $selected = "selected='selected'" : "tidak";	
			$list[]= array(
						'id' => $val->id,
						'title'=>ucwords($val->title),
						"selected"=>$selected
					 );
		}
		$data = array(
				"list"=>$list,
				"name"=>"ref".$name
				);
		return $this->parser->parse("admin_layout/ref_dropdown".$type.".html", $data, TRUE);
	}
	
	function getRefDropdown($id,$name,$type=NULL)
	{
		$q = $this->menu->getList($this->table,null,null,null);
		$list = array();
		foreach ($q->result() as $val) {

			$selected = $val->id == $id ? $selected = "selected='selected'" : "";	
			
			$list[]= array(
						'id' => $val->id,
						'title'=>$val->title,
						"selected"=>$selected
					 );
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