<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('to_excel'))
{
	function to_excel($query, $filename='xlsoutput')
	{
		  $headers = ''; // variable untuk menampung header
		  header("Content-type: application/x-msdownload");
		  header("Content-Disposition: attachment; filename=$filename.xls");
		  echo "$headers\n$query";
	}
}
?>