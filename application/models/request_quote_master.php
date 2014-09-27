<?php
class Request_quote_master extends Base_Model 
{	
        
        function insert($insert_data,$set_array)
	{
            return parent::insertInternal('request_quote',$insert_data, $set_array);
	}
        function update($where_clause_array,$update_data_array,$set_array)
	{
            return parent::updateInternal('request_quote',$where_clause_array, $update_data_array, $set_array);
	}
        
        function getById($id,$column_array,$isarray){
            
            return parent::getUniqueRow('request_quote',array('id'=>$id),$column_array,$isarray);
        }
        function exists($id)
	{
		return (parent::existInternal('request_quote', $id));
	}
        
        function getGridById($id,$isarray=true){
            return parent::getUniqueRow('request_quote_grid',array('id'=>$id),null,$isarray);
        }

        //business logic ..use this to create quote
        
        public function createRequest($request_quote_data,$set_data){
           // $this->db->trans_start();
            $this->insert($request_quote_data,$set_data);
            
            $id = $this->db->insert_id();
            
            $where_clause = array('id'=>$id);
            $this->update($where_clause, array('reference' => 10000000 + $id));
            
            //$this->db->trans_complete();
            return $id;
        }
        
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('request_quote_grid', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('request_quote_grid', $where_clause_array, $like_fields_array, $or_where_clause_array);
        }
}

?>