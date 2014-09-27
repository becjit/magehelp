<?php
class Mfr_model extends CI_Model 
{	
	function insert($mfr_data)
	{
            //$this->db->insert('invoice',$invoice_data);
            return $this->db->insert('model',$mfr_data);
	}
        
        function update($id,$model_data)
	{
            $this->db->where('id',$id);
            return $this->db->update('model',$model_data);
	}
	
        function getAllByMfrId($id){
            $this->db->select('id,model_name');
            $this->db->where('manufacturer_id',$id);
            $query = $this->db->get('model');
            return $query->result_array();
        }
        
        
        function getMfrId($name){
            $this->db->select('manufacturer_id');
            $this->db->where('model_name',$name);
            $query = $this->db->get('model');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['manufacturer_id'];
            
            } 
            return 0;
        }
        
        function getName($id){
            $this->db->select('model_name');
            $this->db->where('id',$id);
            $query = $this->db->get('model');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['model_name'];
            
            } 
            
        }
        
        function getAll($csv = false,$clause=array(),$fields_array=null){
            $orderBy = 'model.id';
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
            $this->db->select('model.id as id,model.model_name as model_name,model.description as description
                ,model.manufacturer_id as manufacturer_id, manufacturer.manufacturer_name as manufacturer_name');
            $this->db->from('model');
            $this->db->join('manufacturer','manufacturer.id=model.manufacturer_id',left);
            if (!empty($fields_array)){
                $this->db->like($fields_array);
            }
            $this->db->order_by($orderBy,$orderDir);
            $this->db->limit($limit,$startLimit);
            $query = $this->db->get();
            
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
        function totalNoOfRows () {
            $query = $this->db->query("select count(*) as total from ".$this->db->dbprefix."model");
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
}

?>