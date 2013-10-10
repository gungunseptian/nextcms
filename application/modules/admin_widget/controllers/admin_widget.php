<?php
class Admin_widget extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		$this->db = $this->load->database("default",TRUE);
		$this->pagination = new CI_Pagination();
		$this->session = new CI_Session();
		$this->parser = new CI_Parser();
		$this->load->model("admin_widget/model_widget", "widget");
	}
	
	function index(){
	}
	
	
	function page($jum_record,$per_page,$path,$uri_segment)
	{
		$link = "";
		
		$config['base_url'] = $path;
		$config['total_rows'] = $jum_record;
		$config['per_page'] = $per_page;
		$config['num_links'] = 5;
		$config['uri_segment'] = $uri_segment;
		$config['next_link'] = '&raquo;';
		$config['prev_link'] = '&laquo;';
		$config['next_tag_open'] = "<span class='next'>";
		$config['next_tag_close'] = "</span>";
		$config['prev_tag_open'] = "<span class='prev'>";
		$config['prev_tag_close'] = "</span>";
		$this->pagination->initialize($config);
		$link = $this->pagination->create_links();
		return $link;
	}
	
	function setalias($uri_title)
	{
		$groups_alias = ereg_replace("/[^A-Za-z0-9\s*]/","",$uri_title);
		$groups_alias = strtolower(url_title($groups_alias));
		return $groups_alias;
	}
	
	function cek_uri($uri,$count)
	{
		if(!is_numeric($uri) || ereg("[^A-Za-z0-9]",$uri)){
			redirect(base_url());
		}
	}
	
	
	function getUrlDate($date)
	{
		$date = str_replace("-","/",date("Y-m-d",strtotime($date)));
  		return $date;
	}
	
	
	function getTitleMenu($path=NULL)
	{
		$this->db->where("menu_file",$path);
		$query = $this->db->get("ddi_ref_menu");
		return $query;
	}
	
	
	function publish()
	{
		$table = $this->uri->segment(4);
		$id = $this->uri->segment(5);
		$setval = 'Publish'; 
		
		$q = $this->widget->getList($table,$id);

		if($q->num_rows() > 0)
		{
			$row = $q->row(); 
			$val = $row->publish;
		}
		
		if($val == 'Publish'){ 
			$setval = 'Not Publish'; 
		}
		
		$this->widget->setUpdate($table,$id,$setval,$this->session->userdata('adminID'),'publish');
		
		$status = 0;
		$title = "";
		$q = $this->widget->getList($table,$id);
		if($q->num_rows() > 0)
		{
			$status = 1; 
			$row = $q->row(); 
			$title = $row->publish;
		}
		
		$response['id'] = $id;
		$response['val'] = $title;
		$response['status'] = $status;
		echo $result = json_encode($response);
		
	}
	
	
	function publish_parent()
	{
		$table = $this->uri->segment(4);
		$table_child = $this->uri->segment(5);
		$id = $this->uri->segment(6);
		$setval = 'Publish'; 
		
		#get parent id
		$this->db->where("id",$id);
		$qc = $this->db->get($table_child);
		$rc = $qc->row();
		$id = $rc->user_auth_id;
		
		$q = $this->widget->getList($table,$id);

		if($q->num_rows() > 0)
		{
			$row = $q->row(); 
			$val = $row->publish;
		}
		
		if($val == 'Publish'){ 
			$setval = 'Not Publish'; 
		}
		
		$this->widget->setUpdate($table,$id,$setval,$this->session->userdata('adminID'),'publish');
		
		$status = 0;
		$title = "";
		$q = $this->widget->getList($table,$id);
		if($q->num_rows() > 0)
		{
			$status = 1; 
			$row = $q->row(); 
			$title = $row->publish;
		}
		
		$response['id'] = $this->uri->segment(6);
		$response['val'] = $title;
		$response['status'] = $status;
		echo $result = json_encode($response);
		
	}
	
	
	function featured()
	{
		$table = $this->uri->segment(4);
		$id = $this->uri->segment(5);
		$setval = 'Yes'; 
		
		$q = $this->widget->getList($table,$id);

		if($q->num_rows() > 0)
		{
			$row = $q->row(); 
			$val = $row->featured;
		}
		
		if($val == 'Yes'){ 
			$setval = 'No'; 
		}
		
		$this->widget->setUpdate($table,$id,$setval,$this->session->userdata('adminID'),'featured');
		
		$status = 0;
		$title = "";
		$q = $this->widget->getList($table,$id);
		if($q->num_rows() > 0)
		{
			$status = 1; 
			$row = $q->row(); 
			$title = $row->featured;
		}
		
		$response['id'] = $id;
		$response['val'] = $title;
		$response['status'] = $status;
		echo $result = json_encode($response);
		
	}

	function new_product()
	{
		$table = $this->uri->segment(4);
		$id = $this->uri->segment(5);
		$setval = 'Yes'; 
		
		$q = $this->widget->getList($table,$id);

		if($q->num_rows() > 0)
		{
			$row = $q->row(); 
			$val = $row->new;
		}
		
		if($val == 'Yes'){ 
			$setval = 'No'; 
		}
		
		$this->widget->setUpdate($table,$id,$setval,$this->session->userdata('adminID'),'new');
		
		$status = 0;
		$title = "";
		$q = $this->widget->getList($table,$id);
		if($q->num_rows() > 0)
		{
			$status = 1; 
			$row = $q->row(); 
			$title = $row->new;
		}
		
		$response['id'] = $id;
		$response['val'] = $title;
		$response['status'] = $status;
		echo $result = json_encode($response);
		
	}
	
	function getStaticDropdown($array,$id,$name,$disabled=Null){
		
		if(!empty($disabled) && !is_null($disabled)){
			$disabled = "disabled='disabled'";
		}
		
		foreach($array as $key=>$val){
			$selected = $key == $id ? $selected = "selected='selected'" : "";	
			$list[]= array(
						'id' => $key,
						'title'=>$val,
						"selected"=>$selected
					 );
		}
		$data = array(
				"list"=>$list,
				"name"=>"ref".$name,
				"disabled"=>$disabled
				);
		return $this->parser->parse("admin_layout/ref_dropdown.html", $data, TRUE);
	}
	
}

?>
