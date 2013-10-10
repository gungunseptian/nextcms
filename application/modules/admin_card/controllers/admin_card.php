<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class admin_card extends MX_Controller  {
	
	var $table = "card";
	var $uri_page = 9;
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
		$refDropdown = Modules::run('admin_widget/getStaticDropdown',$search2_arr,$search2_parm,1);
		
		$search3_parm = rawurldecode($this->uri->segment(6));
		$search3_parm = $search3_parm != 'null' && !empty($search3_parm) ? $search3_parm : 'null';
		$ref2_arr = array("Walkin"=>"Walkin","Member"=>"Member","Visitor"=>"Visitor");
		$refDropdown2 = Modules::run('admin_widget/getStaticDropdown',$ref2_arr,$search3_parm,2);
		
		$search4_parm = rawurldecode($this->uri->segment(7));
		$search4_parm = $search4_parm != 'null' && !empty($search4_parm) ? $search4_parm : 'null';
		$ref3_arr = array("Available"=>"Available","Registered"=>"Registered","Expired"=>"Expired","Used"=>"Used");
		$refDropdown3 = Modules::run('admin_widget/getStaticDropdown',$ref3_arr,$search4_parm,3);

		$get_page = $this->uri->segment(8);

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
		$path = base_url()."admincontrol/".$this->table."/pages/".$search1_parm."/".$search2_parm."/".$search3_parm."/".$search4_parm."/".$per_page;
		$jum_record = $this->card->getTotal($this->table,$search1_parm,$search2_parm,$search3_parm,$search4_parm);
		$paging = Modules::run("admin_widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		#record
		$query = $this->card->getList($this->table,$per_page,$lmt,$search1_parm,$search2_parm,$search3_parm,$search4_parm);
		$list = array();
		if($query->num_rows() > 0){
			foreach($query->result() as $row)
			{
				$no++;
				$zebra = $no % 2 == 0 ? "zebra" : "";
				$create_date = date("d/m/Y H:i",strtotime($row->create_date));
				
				$rfid_number = $row->rfid_number;
				$rfid_number = highlight_phrase($rfid_number, $search1_parm, '<span style="color:#990000">', '</span>');
				$publish  = $row->publish;

				$list[] = array(
								"no"=>$no,
								"id"=>$row->id,
								"rfid_number" =>$rfid_number,
								"variant" =>$row->variant,
								"status" =>$row->status,
								"publish"=>$publish,
								"create_date"=>$create_date
								);
			}
		}	
		#end record
	
		$data = array(
				  'base_url' => base_url(),
				  'paging'=>$paging,
				  'list'=>$list,
				  'jum_record'=>$jum_record,
				  'display_record'=>$display_record,
				  'search1_parm'=>$search1_parm,
				  'search2_parm'=>$search2_parm,
				  'search3_parm'=>$search3_parm,
				  'search4_parm'=>$search4_parm,
				  'search1_val'=>$search1_val,
				  'refDropdown'=>$refDropdown,
				  'refDropdown2'=>$refDropdown2,
				  'refDropdown3'=>$refDropdown3,
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
		$search2 = rawurlencode($this->input->post('ref1'));
		$search3 = rawurlencode($this->input->post('ref2'));
		$search4 = rawurlencode($this->input->post('ref3'));
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
		if(empty($search4))
		{
			$search4 = 'null';
		}
		redirect("admincontrol/".$this->table."/pages/".$search1."/".$search2."/".$search3."/".$search4."/".$per_page);
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
			$q = $this->card->getDetail($this->table,$id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
				foreach($q->result() as $r){
					
					$rfid_number = $this->session->flashdata("rfid_number") ? $this->session->flashdata("rfid_number") : $r->rfid_number;
					$variant = $this->session->flashdata("ref2") ? $this->session->flashdata("ref2") : $r->variant;
					
					$ref1_arr = array("Not Publish"=>"Not Publish","Publish"=>"Publish");
					$refDropdown = Modules::run('admin_widget/getStaticDropdown',$ref1_arr,$r->publish,1);

					$ref2_arr = array("Walkin"=>"Walkin","Member"=>"Member","Visitor"=>"Visitor");
					$refDropdown2 = Modules::run('admin_widget/getStaticDropdown',$ref2_arr,$variant,2);
					
					$ref3_arr = array("Available"=>"Available","Registered"=>"Registered","Expired"=>"Expired","Used"=>"Used");
					$refDropdown3 = Modules::run('admin_widget/getStaticDropdown',$ref3_arr,$r->status,3);

					$list[] = array(
									"id"=>$r->id,
									"rfid_number"=>$rfid_number,
									"refDropdown"=>$refDropdown,
									"refDropdown2"=>$refDropdown2,
									"refDropdown3"=>$refDropdown3,
									"create_date"=>$r->create_date
									);
				}
			}else{
				
				$rfid_number = $this->session->flashdata("rfid_number") ? $this->session->flashdata("rfid_number") : "";
				$variant = $this->session->flashdata("ref2") ? $this->session->flashdata("ref2") : null;
				
				$ref1_arr = array("Not Publish"=>"Not Publish","Publish"=>"Publish");
				$refDropdown = Modules::run('admin_widget/getStaticDropdown',$ref1_arr,'Publish',1);
				
				$ref2_arr = array("Walkin"=>"Walkin","Member"=>"Member","Visitor"=>"Visitor");
				$refDropdown2 = Modules::run('admin_widget/getStaticDropdown',$ref2_arr,$variant,2);
					
				$ref3_arr = array("Available"=>"Available","Registered"=>"Registered","Expired"=>"Expired","Used"=>"Used");
				$refDropdown3 = Modules::run('admin_widget/getStaticDropdown',$ref3_arr,'Available',3);

				$list[] = array(
									"id"=>0,
									"rfid_number"=>$rfid_number,
									"refDropdown"=>$refDropdown,
									"refDropdown2"=>$refDropdown2,
									"refDropdown3"=>$refDropdown3,
									"create_date"=>""

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
		$rfid_number = strip_tags($this->input->post("rfid_number"));
		$ref1 = strip_tags($this->input->post("ref1"));
		$ref2 = strip_tags($this->input->post("ref2"));
		$ref3 = strip_tags($this->input->post("ref3"));
		$ordered = $this->input->post("ordered");
		$user_id = $this->session->userdata('adminID');
		$id = strip_tags($this->input->post("id"));

		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('rfid_number', 'RFID Number', 'required|callback_rfid_number_check');
		$this->form_validation->set_rules('ref2', 'variant', 'required');
	
		if ($this->form_validation->run($this) == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			$this->session->set_flashdata("rfid_number",$rfid_number);
			$this->session->set_flashdata("ref2",$ref2);
			redirect("admincontrol/".$this->table."/edit/".$id);
		}else{
			if($id > 0)
			{
				$this->card->setUpdate($this->table,$id,$rfid_number,$ref1,$ref2,$ref3,$user_id);
				$this->session->set_flashdata("success","Data saved successful");
				redirect("admincontrol/".$this->table."/edit/".$id);
			}else{
				$id_term = $this->card->setInsert($this->table,$id,$rfid_number,$ref1,$ref2,$ref3,$user_id);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				redirect("admincontrol/".$this->table."/edit/".$id_term);
			}
		}
	}
	
	function rfid_number_check($code)
	{
		$count = $this->card->checkRfidNumber($this->table,$code,$this->input->post('id'));
		if ($count > 0)
		{
			$this->form_validation->set_message('rfid_number_check', 'RFID Number are used');
			return false;
		}else{
			return true;
		}
	}
	
	function delete($id=0)
	{
		$del_status = $this->card->setDelete($this->table,$id);
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
		unlink("uploads/thumbs/".$file_image);
		redirect("admincontrol/".$this->table."/edit/".$id);
	}

	function getTitle($id)
	{
		$title = $this->card->getDetail($this->table,$id)->row_array();

		return $title['title'];
	}
	
	function getRefDropdown($id,$name,$type=NULL)
	{
	
		$q = $this->card->getList($this->table,null,null,null);
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