<?php
class Delivery_point extends CI_Model 
{	
	function insert($delivery_point_data)
	{
            //$this->db->insert('invoice',$invoice_data);
            return $this->db->insert('delivery_point',$delivery_point_data);
	}
        
//        function getmagentotest (){
////            $CI =& get_instance();
////$CI->magento = $this->load->database('magento', TRUE);
////$this->magento =$CI->magento; 
//            $query = $this->magento->query("select count(*) as total from sales_flat_invoice");
//            if ($query->num_rows() > 0)
//            {
//                $row = $query->first_row(); 
//                $total = $row->total;
//                if ($total == null){
//                    $total = 0;
//                }
//
//                return  $total;
//            
//            } 
//                    
//                    
//                    
//                    
//                    
//        }
        
        function update($id,$delivery_point_data)
	{
            //$this->db->insert('invoice',$invoice_data);
             $this->db->where('id',$id);
            return $this->db->update('delivery_point',$delivery_point_data);
	}
        
	
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
            $query = $this->db->get('delivery_point');
            
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
        function totalNoOfRows () {
            $query = $this->db->query("select count(*) as total from ".$this->db->dbprefix."delivery_point");
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

	
        function getId($name){
            $this->db->select('id');
            $this->db->where('name',$name);
            $query = $this->db->get('delivery_point');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 

                return  $row['id'];
            
            } 
            return 0;
        }
}

?>