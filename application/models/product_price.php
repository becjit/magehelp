<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Product_Price
 *
 * @author abhijit
 */
class Product_price extends Base_model  {
    //put your code here
//    function update ($where,$stock_data){
//        if (!empty($where)){
//            $this->db->where($where['column'],$where['value']);
//        }
//        
//        return $this->db->update('product_price',$stock_data);
//    }
//    
//    function updateByBarcode($barcode,$stock_data)
//    {
//        //$this->db->insert('invoice',$invoice_data);
//        $this->db->where('barcode',$barcode);
//        return $this->db->update('product_price',$stock_data);
//
//    }
//    
//    function insert($stock_data)
//    {
//        //$this->db->insert('invoice',$invoice_data);
//        //$this->db->where('barcode',$barcode);
//        return $this->db->insert('product_price',$stock_data);
//
//    }
//        
//    function getAll($csv = false,$clause=array(),$isactive=null,$like_fields_array=null){
//        $orderBy = 'id';
//        $orderDir= 'desc';
//        $startLimit = 0;
//        $limit = 1000;
//
//        if (!empty($clause['orderBy'])){
//            $orderBy = $clause['orderBy'];
//        }
//        if (!empty($clause['orderDir'])){
//            $orderDir = $clause['orderDir'];
//        }
//        if (!empty($clause['startLimit'])){
//            $startLimit = $clause['startLimit'];
//        }
//        if (!empty($clause['limit'])){
//            $limit = $clause['limit'];
//        }
//
//        $this->load->dbutil();
//
//        $this->db->select('*');
//        if (!empty($isactive)){
//            //only add for boolean ;ignore any other value;
//            if ($isactive == 1 || $isactive == 0){
//                $this->db->where('isactive',$isactive);
//            }
//
//        }
//
//        $this->db->like($like_fields_array);
//
//        $this->db->order_by($orderBy,$orderDir);
//        $this->db->limit($limit,$startLimit);
//        $query = $this->db->get('product_price');
//        if ($csv){
//            return $this->dbutil->csv_from_result($query);
//        }
//        return $query->result_array();
//    }
//        
//    function totalNoOfRows () {
//        $query = $this->db->query("select count(*) as total from ".$this->db->dbprefix."product_price");
//        if ($query->num_rows() > 0)
//        {
//            $row = $query->first_row(); 
//            $lastproductid = $row->total;
//            if ($lastproductid == null){
//                $lastproductid = 0;
//            }
//
//            return  $lastproductid;
//
//        } 
//
//    }
//
//    function getAllBarcodes(){
//        $this->db->select('barcode');
//        $query = $this->db->get('product_price');
//
//        return $query->result_array();
//    }
//    
//    function getByProductId($id){
//            
//            $this->db->select('*');
//            $this->db->where('product_id',$id);
//            
//            $query = $this->db->get('product_price');
//            if ($query->num_rows() > 0)
//            {
//                return $query->row(); 
//                
//            } 
//        }
    
        function insert($insert_data,$set_array)
	{
            return parent::insertInternal('product_price_grid',$insert_data, $set_array);
	}
        function update($where_clause_array,$update_data_array,$set_array)
	{
            return parent::updateInternal('product_price_grid',$where_clause_array, $update_data_array, $set_array,FALSE);
	}
        
      
        function getByProductId($id,$column_array,$isarray=false){
            
            return parent::getUniqueRow('product_price_grid',array('product_id'=>$id),$column_array,$isarray);
        }
        
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
             if (empty($order_limit_clause['orderBy'])){
                $order_limit_clause['orderBy']='product_id';
            }
            return parent::getResults('product_price_grid', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('product_price_grid', $where_clause_array, $like_fields_array, $or_where_clause_array);
        }
//        function getByProductId($id){
////            
//            $this->db->select('*');
//            $this->db->where('product_id',$id);
//            
//            $query = $this->db->get('product_price');
//            if ($query->num_rows() > 0)
//            {
//                return $query->row(); 
//                
//            } 
//        }
}

?>
