<?php
class model_widget extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper('date');
	}
	
	
	function getList($table,$id)
	{
		$this->db->where($table.".id",$id);
		$query = $this->db->get($table);
		return $query;
	}

	function setUpdate($table,$id,$publish,$user_id,$type)
	{
		
		$data = array(
			      $type=>$publish,
			      'modify_user_id'=>$user_id
			      );
		$this->db->where($table.'.id',$id);
		$this->db->update($table,$data);
	}
	
}
?>