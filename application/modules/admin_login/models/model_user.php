<?php
class model_user extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}

	
	function cekUserLogin($username,$password)
	{
		$password = md5($password);
		$key = "adminkey";
		$password = md5($key.$password);
		
		$this->db->select("id,adminusers_level_id");
		$this->db->where("username",$username);
		$this->db->where("password",$password);
		$this->db->where("publish","Publish");
		$query = $this->db->get("adminusers_auth");
		
		return $query;
	}

	
	function cekEmailExists($email)
	{
	
		$this->db->where("email",$email);
		$query = $this->db->get("adminusers_auth");
		return $query;
	}
	
	
	function getUserById($id)
	{
			$this->db->join("adminusers_level","adminusers_level.adminusers_level_id=adminusers_auth.adminusers_level_id");
			$this->db->where("adminusers_auth.id",$id);
			$this->db->where("adminusers_auth.publish","Publish");
			$query = $this->db->get("adminusers_auth");
			return $query;
	}

	
	function getUserTotal($keyword)
	{
		if($keyword != 'null' && !empty($keyword) )
		{
			$this->db->like('adminusers_auth.username',$keyword);
		}
		$this->db->join("adminusers_level","adminusers_level.adminusers_level_id=adminusers_auth.adminusers_level_id");
		$this->db->select("COUNT(id) AS total");
		$query = $this->db->get("adminusers_auth");
		$r = $query->row();
		return $r->total;
	}
	
	
	function getUser($per_page,$lmt,$keyword)
	{
		
		if($keyword != 'null' && !empty($keyword) )
		{
			$this->db->like('adminusers_auth.username',$keyword);
		}
		$this->db->join("adminusers_level","adminusers_level.adminusers_level_id=adminusers_auth.adminusers_level_id");
		$query = $this->db->get("adminusers_auth",$per_page,$lmt);
		return $query;
	}
	
	function insertUserAuth($username,$password,$adminusers_level_id)
	{
		$d = gmdate("Y-m-d H:i:s");
		$password = md5($password);
		$key = "nextwebkey";
		$password = md5($key.$password);
		$data = array(
					"username"=>$username,
					"password"=>$password,
					"publish"=>"Publish",
					'adminusers_level_id'=>$adminusers_level_id,
					"create_date"=>$d
					);
		$query = $this->db->insert("adminusers_auth",$data);
		$id = $this->db->insert_id();
		return $id;
	}
	
	
	function updateUserAuth($username,$password,$oldpassword,$id,$adminusers_level_id)
	{
		if(!empty($password)){
			$password = md5($password);
			$key = "nextwebkey";
			$setpassword = md5($key.$password);
		}else{
			$setpassword = $oldpassword;
		}
		
		$data = array(
					"username"=>$username,
					"password"=>$setpassword,
					"adminusers_level_id"=>$adminusers_level_id
					);
		$this->db->where("id",$id);
		$query = $this->db->update("adminusers_auth",$data);
		$id = $this->db->insert_id();
		return $id;
	}
	
	function getNewUser()
	{
			$this->db->join("user_profile","user_profile.id_user_auth=user_auth.id_user_auth","left");
			$this->db->where("tbl_user_auth.create_date BETWEEN DATE_SUB(NOW(),INTERVAL 1 WEEK) AND NOW()");
			$this->db->where("user_auth.publish","Publish");
			$this->db->order_by("user_auth.create_date","desc");
			$query = $this->db->get("user_auth");
			return $query;
	}
	
	function deleteuser($id)
	{
		$this->db->where('id',$id);
		$this->db->delete('adminusers_auth');
	}
	

	
}
?>