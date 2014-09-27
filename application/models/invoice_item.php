<?php
class Invoice_item extends CI_Model 
{	
	function insert($invoice_data)
	{
            //$this->db->insert('invoice',$invoice_data);
            return $this->db->insert('invoice_item',$invoice_data);
	}
        
        function exists($magento_invoice_id,$sku)
	{
		$this->db->from('invoice_item');
		$this->db->where('invoice_id',$magento_invoice_id);
                $this->db->where('sku',$sku);
		$query = $this->db->get();
                $ifExists = $query->num_rows()==1;

		return ($ifExists);
	}
	function updateMultiple($skuItems,$invoiceId){
            foreach ($skuItems as $skuItem) {
                $sku_no = $skuItem['sku'];
                $itemsPacked = array('packed_number'=>$skuItem['items']);
               
                $this->db->where('invoice_id',$invoiceId);
                $this->db->where('sku',$sku_no);
                $this->db->update('invoice_item',$itemsPacked);
            }
        }
        
        function updateMultipleByEntityId($items){
            foreach ($items as $item) {
                $magento_entity_id = $item['id'];
                $itemsPacked = array('packed_number'=>$item['value']);
               
                $this->db->where('magento_entity_id',$magento_entity_id);
                
                $this->db->update('invoice_item',$itemsPacked);
            }
        }
        
//       
        
        function lastEntityIdFetched () {
            $query = $this->db->query("select MAX(magento_entity_id) as lastentityid from ".$this->db->dbprefix."invoice_item");
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
        
        function getUnprocessedInvoiceItemsFromMagento()
	{
		$entityId = $this->lastEntityIdFetched();
                $select = "SELECT invoiceitem.entity_id, invoiceitem.parent_id, invoice.increment_id, invoiceitem.product_id, product.type_id, product.sku, invoiceitem.name, invoiceitem.qty
                    FROM sales_flat_invoice_item invoiceitem
                    LEFT JOIN sales_flat_invoice invoice ON invoiceitem.parent_id = invoice.entity_id
                    LEFT JOIN catalog_product_entity product ON invoiceitem.product_id = product.entity_id ";
                //$query = $this->db->query("select MAX(id) as lastproductid from sales_flat_invoice");
                //$this->magento->select('entity_id,increment_id');
                //$array = array('entity_id >'=> $entityId);
                $where = " where invoiceitem.entity_id > ". $entityId;
                $sql = $select . $where;
		//$this->magento->where($array);
		$query = $this->magento->query($sql);

		return $query->result_array();
	}
        
//        
        
        function totalNoOfRows ($where_clause_array=null) {
           
            if (!empty($where_clause_array)){
                $this->db->where($where_clause_array);
            }
            $this->db->from('invoice_item');
            return $this->db->count_all_results() ;
            
            
        }
        
        function getAll($csv = false,$whereClause=null,$clause=array(),$fields_array=null){
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
            if (!empty($whereClause)){
                $this->db->where($whereClause);
            }
            if (!empty($fields_array)){
                $this->db->like($fields_array);
            }
            $this->db->order_by($orderBy,$orderDir);
            $this->db->limit($limit,$startLimit);
            $query = $this->db->get('invoice_item');
            
            if ($csv){
                return $this->dbutil->csv_from_result($query);
            }
            return $query->result_array();
        }
        
	
}

?>