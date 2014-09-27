<?php
class Delivery_vehicle extends CI_Model 
{	
	function insert($delivery_vehicle_data)
	{
            //$this->db->insert('invoice',$invoice_data);
            return $this->db->insert('delivery_vehicle',$delivery_vehicle_data);
	}
        
        
	
        function getAll(){
            $this->db->select('id,reg_number');
            $query = $this->db->get('delivery_vehicle');
            return $query->result_array();
        }
        
        function getId($reg_number){
            $this->db->select('id');
            $this->db->where('reg_number',$reg_number);
            $query = $this->db->get('delivery_vehicle');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['id'];
            
            } 
            return 0;
        }
	
}

?>