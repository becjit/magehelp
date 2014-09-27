<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of attribute_set
 *
 * @author abhijit
 */
class Attribute_set extends CI_Model{
    
    
    function getAttributeSetsFromMagento()
    {
            //$entityId = $this->lastEntityIdFetched();
            $select = "SELECT distinct( `attribute_set_name`) FROM `eav_attribute_set`";

            $sql = $select;

            $query = $this->magento->query($sql);

            return $query->result_array();
    }
    
    
    function importAttributeSets () {
           $success = false;
            
           $attributesets = $this->getAttributeSetsFromMagento();
           if (!empty($attributesets)){
                $this->db->trans_start();
                foreach ($attributesets as $attributeset){
                    $this->save(array('attribute_set_name'=>$attributeset['attribute_set_name']));
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    //echo $this->db->_error_message();
                    
                   log_message('Attribute  Import Failed '.$this->db->_error_message() );
                   throw new Exception('Attribute Import Failed' );
                }
                else {
                    $success = true;
                    log_message('debug','Attribute Import Successful');
                }
           }
            return $success;
            
            
        }
        
        function save($attribute_set_name){
            $success=false;
            //Run these queries as a transaction, we want to make sure we do all or nothing
            if (!empty($attribute_set_name)){
                if ( !$this->exists($attribute_set_name['attribute_set_name'])){
                    $success = $this->db->insert('attribute_set',$attribute_set_name);
                    if ($success){
                        log_message('debug','attribute_set Inserted');
                    }
                }
//                else{
//                    $this->db->where('magento_entity_id', $magento_entity_id);
//                    $success = $this->db->update('category',$attribute_set_name);
//                    if ($success){
//                        log_message('debug','Category Updated');
//                    }
//                }
            }
            
            if (!$success)
            {
                //echo $this->db->_error_message();
               log_message('attribute_set Creation/Updation Failed '.$this->db->_error_message() );
               throw new Exception('attribute_set Creation/Updation Failed' );
            }
           
            return $success;
        }
        
        function exists($attribute_set_name)
	{
		$this->db->from('attribute_set');	
		$this->db->where('attribute_set_name',$attribute_set_name);
		$query = $this->db->get();
                return ($query->num_rows()==1);
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
            $query = $this->db->get('attribute_set');
            
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
        function importProductAttributeSetMapping() {
           $success = false;
            
           $attributesets = $this->getAttributeSetsFromMagento();
           if (!empty($attributesets)){
                $this->db->trans_start();
                foreach ($attributesets as $attributeset){
                    $this->save(array('attribute_set_name'=>$attributeset['attribute_set_name']));
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    //echo $this->db->_error_message();
                    
                   log_message('Attribute  Import Failed '.$this->db->_error_message() );
                   throw new Exception('Attribute Import Failed' );
                }
                else {
                    $success = true;
                    log_message('debug','Attribute Import Successful');
                }
           }
            return $success;
            
            
        }
}

?>
