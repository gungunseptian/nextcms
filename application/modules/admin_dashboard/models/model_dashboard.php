<?php
class model_dashboard extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper('date');
	}
	
	function get_total_customers($new=NULL)
	{
		$this->db->select("COUNT(id) AS total");
		if($new){
			$this->db->where("new","Yes");
		}
		$q = $this->db->get("users_customers");
		$r = $q->row();
		return $r->total;
	}
	
	function get_new_customers()
	{

		$this->db->where("new","Yes");
		$this->db->order_by("create_date","desc");
		$this->db->limit(5);
		$query = $this->db->get("users_customers");
		return $query;
	}
	
	function get_total_purchase()
	{
		$date = date("Y-m-d",now());
		$this->db->select("COUNT(id) AS total");
		$this->db->where("DATE(create_date)",$date);
		$q = $this->db->get("customers_order");
		$r = $q->row();
		return $r->total;
	}
	
	function get_total_amount($today=NULL)
	{
		$date = date("Y-m-d",now());
		$this->db->select("SUM(total_amount) AS total");
		if($today){
			$this->db->where("DATE(create_date)",$date);
		}
		$q = $this->db->get("customers_order");
		$r = $q->row();
		return $r->total;
	}
	
	
	function get_monthly_order($month)
	{
		$year = date("Y",now());
		$this->db->select("SUM(total_amount) AS total_pro");
		$this->db->where("YEAR(create_date) = '".$year."'");
		$this->db->where("MONTH(create_date) = '".$month."'");
		$q = $this->db->get("customers_order");
		$r = $q->row();
		$total = !empty($r->total_pro) ? $r->total_pro : 0;	
		$total = round($total);
		return $total;
	}
	
	
	function get_latest_order()
	{
		$this->db->select("customers_order.*,
						   users_sales.sales_code,
						   users_sales.name AS sales_name,
						   users_customers.customer_code,
						   users_sales.create_date AS users_sales_crt_date,
						   users_customers.name AS customer_name,
						   users_customers.id AS customer_id,
						   users_customers.publish AS pub,
						   users_customers.create_date AS users_customers_crt_date
						   ");
		$this->db->join("users_sales","users_sales.id=customers_order.sales_id");
		$this->db->join("users_customers","users_customers.id=customers_order.customer_id");
		$this->db->order_by("customers_order.create_date","desc");
		$this->db->limit(5);
		$query = $this->db->get("customers_order");
		return $query;
	}
	
	
	function get_jobs_done()
	{
		$this->db->select("jobs.*,
						   users_sales.sales_code,
						   users_sales.name AS sales_name,
						   users_sales.create_date AS users_sales_crt_date
						   ");
		$this->db->join("users_sales","users_sales.id=jobs.sales_id");
		$this->db->order_by("jobs.checkout_time","desc");
		$this->db->limit(5);
		$query = $this->db->get("jobs");
		return $query;
	}
	
	function get_best_seller()
	{
		$this->db->select("products.*,
						   COUNT(product_id) AS total
						   ");
		$this->db->join("products","products.id=customers_order_detail.product_id");
		$this->db->group_by("customers_order_detail.product_id");
		$this->db->order_by("total","desc");
		$this->db->limit(5);
		$query = $this->db->get("customers_order_detail");
		return $query;
	}
	
}
?>