<?php
class Pricelist_master extends Base_model
{	
	function insert($insert_data,$set_array)
	{
            return parent::insertInternal('pricelist',$insert_data, $set_array);
	}
        
         function update($where_clause_array,$update_data_array,$set_array)
	{
            return parent::updateInternal('pricelist',$where_clause_array, $update_data_array, $set_array);
	}
        
       
        function getById($id,$column_array,$isarray){
            
            return parent::getUniqueRow('supplier_rate_contract_master',array('id'=>$id),$column_array,$isarray);
        }
	
        
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('supplier_rate_contract_master', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
           
        }
        
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null,$in_where_clause_array=null,$not_in_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('supplier_rate_contract_master',$where_clause_array,$like_fields_array,$or_where_clause_array,$in_where_clause_array,$not_in_where_clause_array);
       }
        
       function getByHighestVersion($root_pricelist_id){
            $this->db->select_max('version', 'highest_version');
            $this->db->where('parent',$root_pricelist_id);
            $query = $this->db->get('supplier_rate_contract_master');
            if ($query->num_rows() > 0)
            {
                    return $query->row()->highest_version;       
            } 
            return 0;
        }
        function applicableVersionList($date,$supplier_id){
            $this->db->select('*');
            $this->db->where('supplier_id',$supplier_id);
            $this->db->from('supplier_rate_contract_master');
            $sub = $this->subquery->start_subquery('where');
            $sub->select_max('version');
            $sub->from('supplier_rate_contract_master');
            $sub->where('\''.$date.'\'>=','valid_from',FALSE);
            $sub->where('\''.$date.'\'<=','valid_to',FALSE);
            $this->subquery->end_subquery('version');
            $query = $this->db->get();
            log_message('debug',$this->db->last_query());
            return $query->row();
        }
       
        
//	SELECT * FROM `ospos_pricelist` where version=(select max(`version`) from ospos_pricelist  WHERE '2012-11-23'>=`valid_from` and  '2012-11-23'<=`valid_to`)
        
	
}

?>