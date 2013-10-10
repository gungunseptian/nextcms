<?php
class model_card extends CI_Model {
	
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
	
	function setUpdate($table,$id,$rfid_number,$ref1,$ref2,$ref3,$user_id)
	{

		$data = array(
			      "rfid_number"=>$rfid_number,
			      "variant"=>$ref2,
				  "status"=>$ref3,
			      "publish"=>$ref1,
			      "modify_user_id"=>$user_id
			      );
		$this->db->where('id',$id);
		$this->db->update($table,$data);

	}
	
	function setInsert($table,$id,$rfid_number,$ref1,$ref2,$ref3,$user_id)
	{

		$data = array(
			      "rfid_number"=>$rfid_number,
			      "variant"=>$ref2,
				  "status"=>$ref3,
			      "publish"=>$ref1,
			      "user_id"=>$user_id,
			      "create_date"=>date("Y-m-d :H:i:s",now())
			      );
		$this->db->insert($table,$data);
		$last_id = $this->db->insert_id();
		$this->session->set_flashdata('last_id',$last_id);

		return $last_id;

	}
	
	function checkRfidNumber($table,$code,$id)
	{
		$this->db->where('id !=',$id);
		$this->db->where('rfid_number',$code);
		$query = $this->db->get($table);
		return $query->num_rows();
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
}
?>