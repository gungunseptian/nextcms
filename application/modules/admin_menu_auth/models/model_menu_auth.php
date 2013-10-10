<?php
class model_menu_auth extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper('date');
		$this->groupID = $this->session->userdata('adminGroupsID');
	}
	
	
	function getMenu($table,$id,$parent_id=0)
	{
		$this->db->select("menu_auth.*,
						   adminusers_level.title,
						   adminusers_level.publish AS pub,
						   adminusers_level.create_date AS crt_date,
						   menu.title AS menu_title,
						   menu.id AS menu_id,
						   menu.id AS parent_id,
						   menu.uri AS menu_uri,
						   menu.divider AS menu_divider,
						   menu.publish AS pub,
						   menu.create_date AS menu_crt_date");
		$this->db->join("menu","menu.id=".$table.".menu_id");
		$this->db->join("adminusers_level","adminusers_level.id=".$table.".adminusers_level_id");
		$this->db->where($table.".adminusers_level_id",$id);
		$this->db->where("menu.parent_id",$parent_id);
		$this->db->where("menu.publish","Publish");
		$this->db->order_by("menu.ordered","asc");
		$query = $this->db->get($table);
		return $query;
	}
	
	function getSelectParentMenu($table,$uri,$id)
	{
		$this->db->where("menu.uri",$uri);
		$this->db->where("menu.parent_id",$id);
		$this->db->where("menu.publish","Publish");
		$this->db->order_by("menu.ordered","asc");
		$query = $this->db->get($table);
		return $query;
	}
	
	function getMenuFromUri($table,$uri)
	{
		$this->db->where("menu.uri",$uri);
		$this->db->where("menu.publish","Publish");
		$query = $this->db->get($table);
		return $query;
	}
	
	function getMenuPermission($table,$id,$menu_id)
	{
		$this->db->join("menu","menu.id=".$table.".menu_id");
		$this->db->where($table.".menu_id",$menu_id);
		$this->db->where($table.".adminusers_level_id",$id);
		$this->db->where("menu.publish","Publish");
		$query = $this->db->get($table);
		return $query;
	}

	function getTotal($table,$search1_parm,$search2_parm)
	{
		if($search1_parm != 'null' && !empty($search1_parm) )
		{
			$this->db->like('menu.title',$search1_parm);
		}
		if($search2_parm != 'null' && !empty($search2_parm) )
		{
			$this->db->like($table.'.adminusers_level_id',$search2_parm);
		}
		if($this->groupID <> 1){
			$this->db->where("adminusers_level.id !=",1);
		}
		$this->db->select("menu_auth.*,
						   COUNT(tbl_".$table.".id) AS total,
						   adminusers_level.title,
						   adminusers_level.publish AS pub,
						   adminusers_level.create_date AS crt_date,
						   menu.title AS menu_title,
						   menu.publish AS pub,
						   menu.create_date AS menu_crt_date");
		$this->db->join("menu","menu.id=".$table.".menu_id");
		$this->db->join("adminusers_level","adminusers_level.id=".$table.".adminusers_level_id");
		$query = $this->db->get($table);
		$r = $query->row();
		return $r->total;
	}
	
	
	function getList($table,$per_page,$lmt,$search1_parm,$search2_parm)
	{
		if($search1_parm != 'null' && !empty($search1_parm) )
		{
			$this->db->like('menu.title',$search1_parm);
		}
		if($search2_parm != 'null' && !empty($search2_parm) )
		{
			$this->db->like($table.'.adminusers_level_id',$search2_parm);
		}
		if($this->groupID <> 1){
			$this->db->where("adminusers_level.id !=",1);
		}
		$this->db->select("menu_auth.*,
						   adminusers_level.title,
						   adminusers_level.publish AS pub,
						   adminusers_level.create_date AS crt_date,
						   menu.title AS menu_title,
						   menu.publish AS pub,
						   menu.create_date AS menu_crt_date");
		$this->db->join("menu","menu.id=".$table.".menu_id");
		$this->db->join("adminusers_level","adminusers_level.id=".$table.".adminusers_level_id");
		$this->db->order_by("adminusers_level.id","desc");
		$this->db->order_by("menu_auth.id","asc");
		$query = $this->db->get($table,$per_page,$lmt);
		return $query;
	}
	

	function getDetail($table,$id)
	{
		$this->db->where($table.'.id',$id);
		$query = $this->db->get($table);
		return $query;
	}
	
	function setInsert($table,$id,$menu_id,$adminusers_level_id,$user_id)
	{
		$data = array(
			      'menu_id'=>$menu_id,
			      'adminusers_level_id'=>$adminusers_level_id,
			      'user_id'=>$user_id,
			      'create_date'=>date("Y-m-d :H:i:s",now())
			      );
		$this->db->insert($table,$data);
	}
	
	function cekInsert($table,$menu_id,$adminusers_level_id)
	{
		$this->db->where($table.'.menu_id',$menu_id);
		$this->db->where($table.'.adminusers_level_id',$adminusers_level_id);
		$query = $this->db->get($table);
		return $query->num_rows();
	}
	
	
	function setDelete($table,$id)
	{
		$status = 0;
		$this->db->where('id',$id);
		$this->db->delete($table);
		$status = 1;
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