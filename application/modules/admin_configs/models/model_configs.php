<?php
class model_configs extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper('date');
	}
	
	
	function getTotal($table,$search1_parm)
	{
		if($search1_parm != 'null' && !empty($search1_parm) )
		{
			$this->db->like($table.'.title',$search1_parm);
			$this->db->or_like($table.'.content',$search1_parm);
		}
		
		$this->db->select("COUNT(id) AS total");
		$query = $this->db->get($table);
		$r = $query->row();
		return $r->total;
	}
	

	function getDetail($table,$id)
	{
		$this->db->where($table.'.id',$id);
		$query = $this->db->get($table);
		return $query;
	}
	
	
	function setUpdate($table,$id,$meta_title,$meta_keyword,$meta_description,$meta_author,$publish,$user_id)
	{
		$data = array(
			      'meta_title'=>$meta_title,
				  'meta_keyword'=>$meta_keyword,
				  'meta_description'=>$meta_description,
				  'meta_author'=>$meta_author,
			      'publish_auth'=>$publish,
			      'modify_user_id'=>$user_id,
			      'create_date'=>date("Y-m-d :H:i:s",now())
			      );
		$this->db->where('id',$id);
		$this->db->update($table,$data);
	}
	
}
?>