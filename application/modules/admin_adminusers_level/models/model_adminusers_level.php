<?php
class model_adminusers_level extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper('date');
		$this->groupID = $this->session->userdata('adminGroupsID');
	}
	
	
	function getTotal($table,$search1_parm,$search2_parm)
	{
		if($search1_parm != 'null' && !empty($search1_parm) )
		{
			$this->db->like($table.'.title',$search1_parm);
		}
		if($search2_parm != 'null' && !empty($search2_parm) )
		{
			$this->db->where($table.'.publish',$search2_parm);
		}
		
		if($this->groupID <> 1){
			$this->db->where($table.".id !=",1);
		}
		$this->db->select("COUNT(id) AS total");
		$query = $this->db->get($table);
		$r = $query->row();
		return $r->total;
	}
	
	function getList($table,$per_page,$lmt,$search1_parm,$search2_parm,$refDropdown=NULL)
	{
		if($search1_parm != 'null' && !empty($search1_parm))
		{
			$this->db->like($table.'.title',$search1_parm);
		}
		
		if($search2_parm != 'null' && !empty($search2_parm))
		{
			$this->db->where($table.'.publish',$search2_parm);
		}
		if($this->groupID <> 1){
			$this->db->where($table.".id !=",1);
		}
		
		if($refDropdown){
			$this->db->order_by($table.'.create_date','asc');
		}else{
			$this->db->order_by($table.'.create_date','desc');
		}
		
		$query = $this->db->get($table,$per_page,$lmt);
		return $query;
	}
	
	function getChild($table,$id)
	{
		$this->db->where("parent_id",$id);
		$query = $this->db->get($table);
		return $query;
	}
	
	function getParent($table,$id)
	{
		$this->db->where("id",$id);
		$query = $this->db->get($table);
		return $query;
	}

	function getDetail($table,$id)
	{
		$this->db->where($table.'.id',$id);
		$query = $this->db->get($table);
		return $query;
	}
	
	function setUpdate($table,$id,$title,$publish,$user_id)
	{
		$data = array(
			      'title'=>$title,
			      'publish'=>$publish,
			      'modify_user_id'=>$user_id
			      );
		$this->db->where('id',$id);
		$this->db->update($table,$data);
	}
	
	
	function setInsert($table,$id,$title,$publish,$user_id)
	{
		
		
		$data = array(
			      'title'=>$title,
			      'publish'=>$publish,
			      'user_id'=>$user_id,
			      'create_date'=>date("Y-m-d :H:i:s",now())
			      );
		$this->db->insert($table,$data);
	}
	
	
	function setDelete($table,$id)
	{
		$status = $num1 = $num2 = 0;
		#select first
		$this->db->where('id',$id);
		$this->db->where('publish','Publish');
		$query1 = $this->db->get($table);
		$num1 = $query1->num_rows();
	
		
		if($num1 == 0){
			#check admin level in admins
			$this->db->where('adminusers_level_id',$id);
			$q1 = $this->db->get('adminusers_auth');
			if($q1->num_rows() == 0){
				$status = 1;
			}else{
				$status = 2;
			}
			
			if($status == 1){
				$this->db->where('id',$id);
				$this->db->delete($table);
			}
		}
		
		return $status;
	}
	
	function setFileUpload($file_image,$file_image_tmp,$file_image_old)
	{
		$this->load->library('image_moo');

 		$this->image_moo->load($file_image_tmp)->resize_crop(150,150)->save("./uploads/".$file_image);
   		
   			if(!$file_image)
			{
			 	$file_image = $file_image_old;
			}

			return $file_image;
	}
}
?>