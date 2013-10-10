<?php
class model_member extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper('date');
	}
	
	function getTotal($table,$search1_parm,$search2_parm,$search3_parm,$search4_parm)
	{
		if($search1_parm != 'null' && !empty($search1_parm) )
		{
			$this->db->like($table.'.rfid_number',$search1_parm);
		}
		
		if($search2_parm != 'null' && !empty($search2_parm) )
		{
			$this->db->where($table.'.publish',$search2_parm);
		}
		
		if($search3_parm != 'null' && !empty($search3_parm) )
		{
			$this->db->where($table.'.variant',$search3_parm);
		}
		
		if($search4_parm != 'null' && !empty($search4_parm) )
		{
			$this->db->where($table.'.status',$search2_parm);
		}
		
		$this->db->select("COUNT(id) AS total");
		$query = $this->db->get($table);
		$r = $query->row();
		return $r->total;
	}
	
	function getList($table,$per_page,$lmt,$search1_parm,$search2_parm,$search3_parm,$search4_parm)
	{
		if($search1_parm != 'null' && !empty($search1_parm) )
		{
			$this->db->like($table.'.rfid_number',$search1_parm);
		}
	
		if($search2_parm != 'null' && !empty($search2_parm) )
		{
			$this->db->where($table.'.publish',$search2_parm);
		}
		
		if($search3_parm != 'null' && !empty($search3_parm) )
		{
			$this->db->where($table.'.variant',$search3_parm);
		}
		
		if($search4_parm != 'null' && !empty($search4_parm) )
		{
			$this->db->where($table.'.status',$search2_parm);
		}
		
		$this->db->order_by($table.".create_date","desc");
		$query = $this->db->get($table,$per_page,$lmt);

		return $query;
	}
	
	function getDetail($table,$id)
	{
		$this->db->where($table.'.id',$id);
		$query = $this->db->get($table);
		return $query;
	}
	
	function setUpdate($table,$id,$title,$ref1,$ref2,$ref3,$user_id,$file_image_tmp,$file_image,$file_image_old)
	{
		
		if(!empty($file_image)){
			if(!empty($file_image_old)){
				$this->deleteFileUpload($file_image_old);
			}
			$file_image = $this->setFileUpload($file_image,$file_image_tmp,$file_image_old);
		}else{
			$file_image = $file_image_old;
		}

		$data = array(
			      "title"=>$title,
			      "variant"=>$ref2,
				  "status"=>$ref3,
			      "publish"=>$ref1,
			      "modify_user_id"=>$user_id,
			      "file_image"=>$file_image,
			      );
		$this->db->where('id',$id);
		$this->db->update($table,$data);

	}
	
	function setInsert($table,$id,$title,$ref1,$ref2,$ref3,$user_id,$file_image_tmp,$file_image,$file_image_old)
	{
		
		if(!empty($file_image)){
			$file_image = $this->setFileUpload($file_image,$file_image_tmp,$file_image_old);
		}
		
		$data = array(
			      "title"=>$title,
			      "variant"=>$ref2,
				  "status"=>$ref3,
			      "publish"=>$ref1,
			      "user_id"=>$user_id,
			      "file_image"=>$file_image,
			      "create_date"=>date("Y-m-d :H:i:s",now())
			      );
		$this->db->insert($table,$data);
		
		$last_id = $this->db->insert_id();

		$this->session->set_flashdata('last_id',$last_id);

		return $last_id;

	}


	function setDelete($table,$id)
	{
		$status = 0;
		#select first
		$this->db->where('id',$id);
		$this->db->where('publish','Publish');
		$query = $this->db->get($table);
		if($query->num_rows() == 0){
			$this->db->where('id',$id);
			$this->db->delete($table);

			$status = 1;
		}
		return $status;
	}
	
	function setFileUpload($file_image,$file_image_tmp,$file_image_old)
	{
		$d = date("Ymdhis");
		$file_image = $d.$file_image;
		$this->load->library('image_moo');
		
 		$this->image_moo->load($file_image_tmp)->resize_crop(350,200)->save("./uploads/".$file_image);
		$this->image_moo->load($file_image_tmp)->resize_crop(150,150)->save("./uploads/thumbs/".$file_image);
   		
   		if(!$file_image)
		{
			 	$file_image = $file_image_old;
		}

		return $file_image;
	}
	
	
	function deleteFileUpload($file_image)
	{
		unlink("uploads/".$file_image);
		unlink("uploads/thumbs/".$file_image);
	}
}
?>