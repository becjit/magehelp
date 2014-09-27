<?php
require_once ("secure_area.php");
class Suppliers extends Secure_area
{
	function __construct()
	{
		parent::__construct('suppliers');
	}
	
	function index()
	{
		
		$this->load->view('suppliers/suppliers_grid',$data);
	}
	
	
}
?>