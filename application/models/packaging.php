<?php
class Packaging extends CI_Model 
{	
	function insert($uom_data)
	{
            
            return $this->db->insert('package',$uom_data);
	}
        
        function update($name,$uom_data)
	{
            //$this->db->insert('invoice',$invoice_data);
            $this->db->where('package_name',$name);
            return $this->db->update('package',$uom_data);
	}
        
        //refactor
        
        function updateById($id,$uom_data)
	{
            //$this->db->insert('invoice',$invoice_data);
            $this->db->where('package_id',$id);
            return $this->db->update('package',$uom_data);
	}
	
	
        function exists($name)
	{
		$this->db->from('package');
		$this->db->where('package_name',$name);
		$query = $this->db->get();

		$ifExists = $query->num_rows()==1;

		return ($ifExists);
	}
        
        function getAll($csv = false,$clause=array(),$fields_array=null){
            $orderBy = 'package_id';
            $orderDir= 'desc';
            $startLimit = 0;
            $limit = 1000;
            
            if (!empty($clause['orderBy'])){
                $orderBy = $clause['orderBy'];
            }
            if (!empty($clause['orderDir'])){
                $orderDir = $clause['orderDir'];
            }
            if (!empty($clause['startLimit'])){
                $startLimit = $clause['startLimit'];
            }
            if (!empty($clause['limit'])){
                $limit = $clause['limit'];
            }
        
            $this->load->dbutil();  
            $this->db->select('*');
            if (!empty($fields_array)){
                $this->db->like($fields_array);
            }
            $this->db->order_by($orderBy,$orderDir);
            $this->db->limit($limit,$startLimit);
            $query = $this->db->get('package');
            
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
//        
        function totalNoOfRows () {
            $query = $this->db->query("select count(*) as total from ".$this->db->dbprefix."package");
            if ($query->num_rows() > 0)
            {
                $row = $query->first_row(); 
                $total = $row->total;
                if ($total == null){
                    $total = 0;
                }

                return  $total;
            
            } 
            
        }
	
        function getAllPackageDetails(){
            $this->db->select('package_id,package_name,package_type,applicable_uom_json');
            //$this->db->distinct();
            $query = $this->db->get('package');
            return $query->result_array();
        }
        
        function getAllPackageTypes(){
            $this->db->select('package_type');
            $this->db->distinct();
            $query = $this->db->get('package');
            return $query->result_array();
        }
        
        function getAllPackages(){
            $this->db->select('package_name');
            $this->db->distinct();
            $query = $this->db->get('package');
            return $query->result_array();
        }
        
        //todo: merge following two functions
	
        function getJson($name){
            $this->db->select('applicable_uom_json');
            $this->db->where('package_name',$name);
            $query = $this->db->get('package');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 
                return  $row['applicable_uom_json'];
            
            } 
            return 0;
        }
        
        function getJsonById($id){
            $this->db->select('applicable_uom_json');
            $this->db->where('package_id',$id);
            $query = $this->db->get('package');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 
                return  $row['applicable_uom_json'];
            
            } 
            return 0;
        }
            
        function getIdByPkgName($name){
            $this->db->select('package_id');
            $this->db->where('package_name',$name);
            $query = $this->db->get('package');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 
                return  $row['package_id'];
            
            } 
            return 0;
        }
        
        function getAllUoms($id){
            $uomjson = $this->getJsonById($id);
            //$result = array();
            $uomArray = json_decode($uomjson,true);
            return $uomArray ;
        }
}

?>