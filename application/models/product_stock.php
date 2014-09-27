<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Product_Stock
 *
 * @author abhijit
 */
class Product_stock extends Base_model  {
    //put your code here
    //refactor
    function update ($id,$stock_data){
        $this->db->where('id',$id);
        return $this->db->update('product_stock',$stock_data);
    }
    //refactor
    function updateByBarcode($barcode,$stock_data)
    {
        //$this->db->insert('invoice',$invoice_data);
        $this->db->where('barcode',$barcode);
        return $this->db->update('product_stock',$stock_data);

    }
    function updateGeneral($where_clause_array,$update_data_array,$set_array)
    {
        return parent::updateInternal('product_stock',$where_clause_array, $update_data_array, $set_array,false);
    }
    function insert($stock_data)
    {
        //$this->db->insert('invoice',$invoice_data);
        //$this->db->where('barcode',$barcode);
        return $this->db->insert('product_stock',$stock_data);

    }
    
    function getStockLevel ($column ,$id){
        $this->db->select('stock');
        $this->db->where($column,$id);
        $query = $this->db->get('product_stock');
            if ($query->num_rows() > 0)
            {
                $row = $query->first_row(); 
                $stock = $row->stock;
                

                return  $stock;
            
            } 
            
        return 0;
    }
    
    function getAll($csv = false,$clause=array(),$isactive=null,$where=array(),$columns_array = null,$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            if (!empty($isactive)){
                //only add for boolean ;ignore any other value;
                if ($isactive == 1 || $isactive == 0){
                    //$this->db->where('isactive',$isactive);
                    $where['isactive'] = $isactive;
                }
                 
            } 
            return parent::getResults('product_stock', $csv, $where, $columns_array, $clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
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
//            
//            $this->db->select('*');
//            if (!empty($isactive)){
//                //only add for boolean ;ignore any other value;
//                if ($isactive == 1 || $isactive == 0){
//                    $this->db->where('isactive',$isactive);
//                }
//                 
//            }
//            $this->db->order_by($orderBy,$orderDir);
//            $this->db->limit($limit,$startLimit);
//            $query = $this->db->get('product_stock');
//            if ($csv){
//                return $this->dbutil->csv_from_result($query);
//            }
//            return $query->result_array();
        }
        
        function getAllByLike($fields_array,$csv = false,$clause=array(),$isactive=null){
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
            if (!empty($isactive)){
                //only add for boolean ;ignore any other value;
                if ($isactive == 1 || $isactive == 0){
                    $this->db->where('isactive',$isactive);
                }
                 
            }
            $this->db->like($fields_array);
            
            $this->db->order_by($orderBy,$orderDir);
            $this->db->limit($limit,$startLimit);
            $query = $this->db->get('product_stock');
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null,$in_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('product_stock', $where_clause_array, $like_fields_array, $or_where_clause_array,$in_where_clause_array);
        }
        
        function getAllBarcodes(){
            $this->db->select('barcode');
            $query = $this->db->get('product_stock');
            
            return $query->result_array();
        }
        
        function insertStockUpdateHistory($insert_data,$set_array)
	{
            return parent::insertInternal('stock_update_history`',$insert_data, $set_array);
	}
        function updateStockUpdateHistory($where_clause_array,$update_data_array,$set_array)
	{
            return parent::updateInternal('stock_update_history`',$where_clause_array, $update_data_array, $set_array,false);
	}
        
        function insertStockUpdateHistoryBatch($insert_batch_data)
	{
            if (!empty($insert_batch_data)){
                $this->db->insert_batch('stock_update_history', $insert_batch_data); 
            }
            log_message('debug',$this->db->last_query());
	}
        function getHistoryData($where_clause=array(),$column_array=array(),$isarray=false){
            
            return parent::getUniqueRow('stock_update_history', $where_clause,$column_array,$isarray);
        }
        function totalNoOfRowsInHistory ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null,$in_where_clause_array=null,$not_in_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('stock_update_history', $where_clause_array, $like_fields_array, $or_where_clause_array,$in_where_clause_array,$not_in_where_clause_array);
        }
}

?>
