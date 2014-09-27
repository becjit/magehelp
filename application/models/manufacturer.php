<?php
class Manufacturer extends CI_Model 
{	
	function insert($mfr_data)
	{
            //$this->db->insert('invoice',$invoice_data);
           $this->db->insert('manufacturer',$mfr_data);
           
           return $this->db->insert_id();
	}
        
        function update($id,$mfr_data)
	{
            //$this->db->insert('invoice',$invoice_data);
             $this->db->where('id',$id);
            return $this->db->update('manufacturer',$mfr_data);
	}
        
	
//        function getAll(){
//            $this->db->select('id,manufacturer_name');
//            $query = $this->db->get('manufacturer');
//            return $query->result_array();
//        }
        
        
        function getAll($csv = false,$clause=array(),$fields_array=null){
            $orderBy = 'id';
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
            $query = $this->db->get('manufacturer');
            
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
        function totalNoOfRows () {
            $query = $this->db->query("select count(*) as total from ".$this->db->dbprefix."manufacturer");
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
        
        function getAllMfrs(){
            $this->db->select('manufacturer_name');
            $query = $this->db->get('manufacturer');
            return $query->result_array();
        }
	
        function getId($name){
            $this->db->select('id');
            $this->db->where('manufacturer_name',$name);
            $query = $this->db->get('manufacturer');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['id'];
            
            } 
            return 0;
        }
        
        function getName($id){
            $this->db->select('manufacturer_name');
            $this->db->where('id',$id);
            $query = $this->db->get('manufacturer');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['manufacturer_name'];
            
            } 
            
        }
}

?>