<?php
class Invoice_master extends Base_model 
{	
	function insert($insert_data,$set_array)
	{
            return parent::insertInternal('invoice',$insert_data, $set_array);
	}
        function update($where_clause_array,$update_data_array,$set_array)
	{
            return parent::updateInternal('invoice',$where_clause_array, $update_data_array, $set_array);
	}
        
        function update_batch($values,$key)
	{
            $this->db->update_batch('invoice',$values,$key);
            log_message('debug',$this->db->last_query());   
	}
        
	
        function exists($magento_invoice_id)
	{
		$this->db->from('invoice');
		$this->db->where('magento_invoice_increment_id',$magento_invoice_id);
		$query = $this->db->get();

		$ifExists = $query->num_rows()==1;

		return ($ifExists);
	}
        
//        function getAll($csv = false,$whereClause=null,$owner_null=false,$or_where_clause_array=null,$clause=array(),$fields_array=null){
//            $orderBy = 'id';
//            $orderDir= 'desc';
//            $startLimit = 0;
//            $limit = 1000;
//            
//            if (!empty($clause['orderBy'])){
//                $orderBy = $clause['orderBy'];
//            }
//            if (!empty($clause['orderDir'])){
//                $orderDir = $clause['orderDir'];
//            }
//            if (!empty($clause['startLimit'])){
//                $startLimit = $clause['startLimit'];
//            }
//            if (!empty($clause['limit'])){
//                $limit = $clause['limit'];
//            }
//        
//            $this->load->dbutil();  
//            $this->db->select('*');
//            if (!empty($whereClause)){
//                $this->db->where($whereClause);
//            }
//            if ($owner_null){
//                $this->db->or_where('OWNER_ID IS NULL',null,false);
//            }
//            if (!empty($or_where_clause_array)){
//                $this->db->or_where($or_where_clause_array);
//            }
//            
//            if (!empty($fields_array)){
//                $this->db->like($fields_array);
//            }
//            $this->db->order_by($orderBy,$orderDir);
//            $this->db->limit($limit,$startLimit);
//            $query = $this->db->get('invoice');
//            
//            if ($csv){
//                return $this->dbutil->csv_from_result($query);
//            }
//            return $query->result_array();
//        }
        
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('invoice_grid', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        function getUnprocessedInvoicesFromMagento()
	{
		$entityId = $this->lastEntityIdFetched();
                $select = "select invoice.entity_id,invoice.increment_id as invoice_increment_id,invoice.order_id, ordermagento.increment_id as order_increment_id from sales_flat_invoice invoice left join sales_flat_order ordermagento on invoice.order_id=ordermagento.entity_id";
                //$query = $this->db->query("select MAX(id) as lastproductid from sales_flat_invoice");
                //$this->magento->select('entity_id,increment_id');
                //$array = array('entity_id >'=> $entityId);
                $where = " where invoice.entity_id > ". $entityId;
                $sql = $select . $where;
		//$this->magento->where($array);
		$query = $this->magento->query($sql);

		return $query->result_array();
	}
        
        function lastEntityIdFetched () {
            $query = $this->db->query("select MAX(magento_invoice_entity_id) as lastentityid from ".$this->db->dbprefix."invoice");
            if ($query->num_rows() > 0)
            {
                $row = $query->first_row(); 
                $lastentityid = $row->lastentityid;
                if ($lastentityid == null){
                    $lastentityid = 0;
                }

                return  $lastentityid;
            
            } 
            
        }
        
//        function totalNoOfRows ($where_clause_array=null,$owner_null=false,$or_where_clause_array=null) {
//            
//            if (!empty($where_clause_array)){
//                $this->db->where($where_clause_array);
//            }
//            if ($owner_null){
//                $this->db->or_where('OWNER_ID IS NULL',null,false);
//            }
//            if (!empty($or_where_clause_array)){
//                $this->db->or_where($or_where_clause_array);
//            }
//            
//            $this->db->from('invoice');
//            return $this->db->count_all_results() ;
//            
//            
//        }
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('invoice_grid', $where_clause_array, $like_fields_array, $or_where_clause_array);
        }
        
	
}

?>