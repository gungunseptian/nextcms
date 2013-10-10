<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class admin_configs extends MX_Controller  {
	
	var $table = "configs";
	var $uri_page = 5;
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
		$this->edit();
	}
	
	function edit()
	{
		$this->setheader();		
		$id = 1;
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
			
			$q = $this->configs->getDetail($this->table,$id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
				foreach($q->result() as $r){
					
				
					
					/* publish */
					$publish = $notpublish = '';
					if($r->publish_auth == 'Yes')
					{
						$publish = 'selected=selected';
					}
					else
					{
						$notpublish = 'selected=selected';
					}

					$list_publish[]= array(
									"publish"=>$publish,
									"notpublish"=>$notpublish);
					/* end publish */


				
		

					$list[] = array(
									"id"=>$r->id,
									"meta_title"=>$r->meta_title,
									"meta_keyword"=>$r->meta_keyword,
									"meta_description"=>$r->meta_description,
									"meta_author"=>$r->meta_author,
									"create_date"=>$r->create_date,
									"list_publish"=>$list_publish,
									);
				}
			}else{
				
				
				/* publish */
				$list_publish[]= array(
									"publish"=>"",
									"notpublish"=>"");
				/* end publish */

	


				$list[] = array(
									"id"=>0,
									"meta_title"=>"",
									"meta_keyword"=>"",
									"meta_description"=>"",
									"meta_author"=>"",
									"create_date"=>"",
									"list_publish"=>$list_publish,
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
			return $this->parser->parse("admin_".$this->table."_edit.html", $data, TRUE);
		}else{
			redirect("admincontrol/".$this->table);
		}
	}
	
	
	function submit()
	{
		$err = "";
		$meta_title = strip_tags($this->input->post("meta_title"));
		$meta_keyword = strip_tags($this->input->post("meta_keyword"));
		$meta_description = strip_tags($this->input->post("meta_description"));
		$meta_author = strip_tags($this->input->post("meta_author"));
		$publish = "";#$this->input->post("publish");
		$user_id = $this->session->userdata('adminID');
		$id = strip_tags($this->input->post("id"));

	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('meta_title', 'meta title', 'required');
		$this->form_validation->set_rules('meta_keyword', 'meta keyword', 'required');
		$this->form_validation->set_rules('meta_description', 'meta description', 'required');
		$this->form_validation->set_rules('meta_author', 'meta author', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			redirect("admincontrol/".$this->table);
		}else{
			if($id > 0)
			{
				$this->configs->setUpdate($this->table,$id,$meta_title,$meta_keyword,$meta_description,$meta_author,$publish,$user_id);
				$this->session->set_flashdata("success","Data saved successful");
				redirect("admincontrol/".$this->table);
			}else{
				$id_term = $this->configs->setInsert($this->table,$id,$title,$publish,$link,$user_id);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				redirect("admincontrol/".$this->table);
			}
		}
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */