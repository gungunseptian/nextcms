<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class admin_menu_auth extends MX_Controller  {
	
	var $table = "menu_auth";
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
		$refDropdown = Modules::run('admin_adminusers_level/getRefDropdown',$search2_parm,1,null,null,'publish');

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
		$jum_record = $this->menu_auth->getTotal($this->table,$search1_parm,$search2_parm);
		$paging = Modules::run("admin_widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		$query = $this->menu_auth->getList($this->table,$per_page,$lmt,$search1_parm,$search2_parm);
		$list = array();
		if($query->num_rows() > 0){
			foreach($query->result() as $row)
			{
				$no++;
				$zebra = $no % 2 == 0 ? "zebra" : "";
				$create_date = date("d/m/Y H:i:s",strtotime($row->create_date));
				
				$title = ucwords($row->menu_title);
				$title = highlight_phrase($title, $search1_parm, '<span style="color:#990000">', '</span>');
			
				$list[] = array(
								 "no"=>$no,
								 "id"=>$row->id,
								 "title" =>$title,
								 "level" =>$row->title,
								 "create_date"=>$create_date
								);
			}
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
				  'title_head'=>ucwords(str_replace("_"," ",$this->table)),
				  'title_link'=>$this->table,
				  'notification'=>$notification,
				  );
		return $this->parser->parse("admin_".$this->table.".html", $data, TRUE);
	}
	
	function search()
	{
		$search1 = rawurlencode($this->input->post('search1'));
		$search2 = rawurlencode($this->input->post('ref1'));
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
			
			$q = $this->menu_auth->getDetail($this->table,$id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
				foreach($q->result() as $r){
					
					/* ref1 */
					$menu_level_id = $r->menu_level_id;
					/* ref1 */
					
					/* ref2 */
					$menu_id = $r->menu_id;
					/* ref2 */

					$list[] = array(
									"id"=>$r->id,
									"create_date"=>$r->create_date
									);
				}
			}else{
				
				/* ref1 ref2 */
				$menu_level_id = "";
				$menu_id = 1;
				/* ref1 ref2 */


				$list[] = array(
									"id"=>0,
									"create_date"=>""
									);
			}
			
			
			$refDropdown = Modules::run('admin_menu/getRefDropdown',$menu_id,1,"_multiple");
			$refDropdown2 = Modules::run('admin_adminusers_level/getRefDropdown',$menu_level_id,2,null,null,'publish');
			
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
					  'list'=>$list,
					  'title_head'=>ucwords(str_replace('_',' ',$this->table)),
				 	  'title_link'=>$this->table,
					  "refDropdown"=>$refDropdown,
					  "refDropdown2"=>$refDropdown2,
					  'notification'=>$notification
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
		$ref2 = $this->input->post("ref2");
		$user_id = $this->session->userdata('adminID');
		$id = strip_tags($this->input->post("id"));
		
	
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ref1', 'select menu', 'required');
		$this->form_validation->set_rules('ref2', 'select admin level', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			redirect("admincontrol/".$this->table."/edit/".$id);
		}else{
				foreach($ref1 as $ref1_val){
					if($this->menu_auth->cekInsert($this->table,$ref1_val,$ref2) == 0){
						$this->menu_auth->setInsert($this->table,$id,$ref1_val,$ref2,$user_id);
					}
				}
				
				$this->session->set_flashdata("success","Data inserted successful");
				redirect("admincontrol/".$this->table."/edit/0");
		}
	}
	
	function delete($id=0)
	{
		$del_status = $this->menu_auth->setDelete($this->table,$id);
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
	
	function ajaxRequest1($id)
	{


		$name = 1;
		$type = "multiple";
		$refDropdown = $selected = "";
		
		# select menu id #
		$query = $this->menu_auth->getList($this->table,null,null,null,$id);
		foreach($query->result() as $row)
		{
			$this->db->where_not_in('id',$row->menu_id);
		}	
		$query_menu = $this->db->get('menu');
		

		$list=array();
		foreach($query_menu->result() as $row_menu)
		{

			$selected = $row_menu->id == 1 ? "selected='selected'" : "";

			$list[]= array(
									'id' =>$row_menu->id,
									'title'=>ucwords($row_menu->title),
									"selected"=>$selected
								 );
		}
		
		$data = array(
					"list"=>$list,
					"name"=>"ref".$name
					);
		$refDropdown = $this->parser->parse("admin_layout/ref_dropdown_".$type.".html", $data,TRUE);

		$data = array(
					  'base_url' => base_url(),
					  'ref'.$name=>$refDropdown
					  );
		echo $this->parser->parse("admin_".$this->table."_ajax.html", $data, TRUE);

		// if($q->num_rows() > 0){
		// 		foreach($q->result() as $r){
		// 			$q_sales = $this->jobs_customers->getDetail('users_sales',$r->sales_id);
		// 			$r_sales = $q_sales->row();
		// 			$sales = $r_sales->sales_code." ".$r_sales->name;
					
		// 			$q_cust = $this->jobs_customers->getCustomersNotIn('assigned_customers',$r->sales_id,$id);
					
		// 			$list = array();
		// 			foreach ($q_cust->result() as $val) {
		// 				$selected = "";	
		// 				$list[]= array(
		// 							'id' => $val->id,
		// 							'title'=>" ".$val->customer_code." ".$val->name,
		// 							"selected"=>$selected
		// 						 );
		// 			}
		// 			$data = array(
		// 			"list"=>$list,
		// 			"name"=>"ref".$name
		// 			);
		// 			$refDropdown = $this->parser->parse("admin_layout/ref_dropdown_".$type.".html", $data, TRUE);
					
		// 		}
				
		// 		$data = array(
		// 			  'base_url' => base_url(),
		// 			  'sales_name'=>$sales,
		// 			  'ref'.$name=>$refDropdown
		// 			  );
				
		// 		echo $this->parser->parse("admin_".$this->table."_ajax.html", $data, TRUE);
		// }else{
		// 	echo "";
		// }
	}




	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */