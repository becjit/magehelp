<?php

class Shipment_master extends CI_Model 
{	
	function insert($shipment_data)
	{
            //$this->db->insert('invoice',$invoice_data);
            return $this->db->insert('shipment',$shipment_data);
	}
        
//        function update($invoiceId,$invoice_data)
//	{
//            //$this->db->insert('invoice',$invoice_data);
//            $this->db->where('magento_invoice_increment_id',$invoiceId);
//            return $this->db->update('shipment',$invoice_data);
//	}
	
	function last_insert_id(){
            $query = $this->db->query("select  MAX(id) from ".$this->db->dbprefix."shipment");
            if ($query->num_rows() > 0)
            {
            $row = $query->row_array(); 
            $id = $row['MAX(id)'];

            return $id ;
            
            } 
            return 0;
        }
        
}
?>
