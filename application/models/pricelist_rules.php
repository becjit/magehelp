<?php
class Pricelist_rules extends Base_model
{	
	function insert($insert_data,$set_array)
	{
            return parent::insertInternal('pricelist_rules',$insert_data, $set_array);
	}
        
         function update($where_clause_array,$update_data_array,$set_array)
	{
            return parent::updateInternal('pricelist_rules',$where_clause_array, $update_data_array, $set_array);
	}
        
       
        function getById($id,$column_array,$isarray){
            return parent::getUniqueRow('product_price_rules',array('id'=>$id),$column_array,$isarray);
        }
	
        
        function getAll($csv =false,$whereClause=null,$columns_array = null,$order_limit_clause=array(),$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
            return parent::getResults('product_price_rules', $csv, $whereClause, $columns_array, $order_limit_clause, $like_fields_array, $in_where_clause_array, $or_where_clause_array,$not_in_where_clause_array);
        }
        
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('product_price_rules', $where_clause_array, $like_fields_array, $or_where_clause_array);
       }
        
       function getHighestPrecedence($pricelist_id,$product_id){
            $this->db->select_max('precedence', 'highest_precedence');
            $this->db->where('pricelist_id',$pricelist_id);
            $this->db->where('product_id',$product_id);
            $query = $this->db->get('product_price_rules');
            if ($query->num_rows() > 0)
            {
                    return $query->row()->highest_precedence;       
            } 
            return 0;
        }
	
         
//        function getAllUnion(){
//            $pricelist_id = 23;
//            $product_id = 17;
//            $old = $this->db->dbprefix;
//            //$this->db->select()
//            
//            $sub1 = $this->subquery->start_union();
//            //$this->db->set_dbprefix($old);
//            $sub1->select('*');
//            $sub1->from('product_price_rules');
//            $sub1->where('pricelist_id',$pricelist_id);
//            $sub1->where('product_id',$product_id);
//            $sub2 = $this->subquery->start_union();
//            //to workaround Bug #54382 . Codeigniter fix #1257 . Release 3.0
//            $sub2->select("* FROM ".$this->db->dbprefix."product_price_rules");
//            //$sub2->from('product_price_rules');
//            $sub = $this->subquery->start_subquery('where');
//            $sub->select('parent');
//            $sub->from('pricelist');
//            $sub->where('id', $pricelist_id);
//            $this->subquery->end_subquery('pricelist_id');
//            $sub2->where('product_id',$product_id);
//            $this->subquery->end_union();
//            $query = $this->db->get_compiled_select();
//            //echo $query;
//            $this->db->set_dbprefix('');
//            //$this->db->select('*');
//            $result = $this->db->get($this->db->escape($query));
////            $subfinal = $this->subquery->start_subquery('select');
////            $this->db->set_dbprefix('');
////            $subfinal->from($query, false);
////           
////            $this->db->from($query);
////            
////            $subfinal = $this->subquery->start_subquery('from');
////            $subfinal->from($query, false);
//            //$this->subquery->end_subquery();
//            //$result = $this->db->get();
//            echo $this->db->last_query();
//            return $result->result_array();
//            
//        }
        function getAllUnion($whereClause,$iscount=false,$limit_clause=array(),$columns_array = null,$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
//            $pricelist_id = 23;
//            $product_id = 17;
            $pricelist_id = $whereClause['pricelist_id'];
            $product_id = $whereClause['product_id'];
            $active=$whereClause['active'];
            $startLimit = 0;
            $limit = 1000;
            $dir = 'desc';
            
            if (!empty($limit_clause['orderDir'])){
                $dir = $limit_clause['orderDir'];
            }
            if (!empty($limit_clause['startLimit'])){
                $startLimit = $limit_clause['startLimit'];
            }
            if (!empty($limit_clause['limit'])){
                $limit = $limit_clause['limit'];
            }
            //$this->db->select()
            //$subtest = $this->subquery->start_subquery('from');
            $sub1 = $this->subquery->start_union();
            $sub1->select('*');
            $sub1->from('product_price_rules');
            $sub1->where('pricelist_id',$pricelist_id);
            $sub1->where('product_id',$product_id);
            if (!empty($active)){
                $sub1->where('active',$active);
            }
            $sub2 = $this->subquery->start_union();
            //to workaround Bug #54382 . Codeigniter fix #1257 . Release 3.0
            $sub2->select("* FROM ".$this->db->dbprefix."product_price_rules");
            //$sub2->from('product_price_rules');
            $sub = $this->subquery->start_subquery('where');
            $sub->select('parent');
            $sub->from('pricelist');
            $sub->where('id', $pricelist_id);
            $this->subquery->end_subquery('pricelist_id');
            $sub2->where('product_id',$product_id);
            if (!empty($active)){
                $sub2->where('active',$active);
            }
            $this->subquery->end_union();
            //$this->subquery->end_subquery();
            $query = $this->db->get_compiled_select();
            $final_sql = "SELECT ";
            if ($iscount){
                $final_sql.=" count(*) as total ";
            }
            else {
                if (!empty($columns_array)){
                    $len = count($columns_array);
                    $cnt = 1;
                    foreach ($columns_array as $column) {
                       $final_sql.= " $column  ";

                       if ($cnt!=$len){
                          $final_sql.= " , " ;
                       }
                        $cnt++ ;
                    }
                }
                else {
                    $final_sql.=" * ";
                }
                $limitclause = "LIMIT ".$startLimit." , ".$limit;
            }
           
            
             $sql = " $final_sql from ( $query ) as complete_rules order by `precedence` asc,`pricelist_id` $dir ".$limitclause;
            //$query = $this->db->get();
            //to workaround Bug #54382 . Codeigniter fix #1257 . Release 3.0
            $result = $this->db->query($sql);
             //$result = $this->db->get();
            //$this->subquery->end_subquery('testing');
            log_message('debug', $this->db->last_query());
            //$query = $this->db->get();
            //echo $this->db->last_query();
            if ($iscount){
                return $result->row()->total;
            }
            return $result->result_array();
            
        }
        
	 function getSomeUnion($whereClause,$iscount=false,$limit_clause=array(),$columns_array = null,$like_fields_array=null,$in_where_clause_array=null,$or_where_clause_array=null,$not_in_where_clause_array=null){
//            $pricelist_id = 23;
//            $product_id = 17;
//             $whereClause=array('pricelist_id'=>23,'product_id'=>17,'active'=>1);
            $pricelist_id = $whereClause['pricelist_id'];
            $product_id = $whereClause['product_id'];
            $active=$whereClause['active'];
            $startLimit = 0;
            $limit = 1000;
            $dir = 'desc';
            
            if (!empty($limit_clause['orderDir'])){
                $dir = $limit_clause['orderDir'];
            }
            //$iscount = true;
            //$columns_array = array('pricelist_id','rule_type','precedence');
            if (!empty($limit_clause['startLimit'])){
                $startLimit = $limit_clause['startLimit'];
            }
            if (!empty($limit_clause['limit'])){
                $limit = $limit_clause['limit'];
            }
            //$this->db->select()
            //$subtest = $this->subquery->start_subquery('from');
            $sub1 = $this->subquery->start_union();
            $sub1->select('*');
            $sub1->from('product_price_rules');
            $sub1->where('pricelist_id',$pricelist_id);
            $sub1->where('product_id',$product_id);
            if (!empty($active)){
                $sub1->where('active',$active);
            }
            $sub2 = $this->subquery->start_union();
            //to workaround Bug #54382 . Codeigniter fix #1257 . Release 3.0
            $sub2->select("* FROM ".$this->db->dbprefix."product_price_rules");
            //$sub2->from('product_price_rules');
            $sub = $this->subquery->start_subquery('where_in');
            $sub->select('rule_id');
            $sub->from('pricelist_rule_inheritance_exclude_mapping');
            
            $sub->where('pricelist_id', $pricelist_id);
            $this->subquery->end_subquery('id',FALSE);
            $sub2->where('product_id',$product_id);
            $sub2->where('pricelist_id !=',$pricelist_id);
            if (!empty($active)){
                $sub2->where('active',$active);
            }
            $this->subquery->end_union();
            //$this->subquery->end_subquery();
            $query = $this->db->get_compiled_select();
            $final_sql = "SELECT ";
            if ($iscount){
                $final_sql.=" count(*) as total ";
            }
            else {
                if (!empty($columns_array)){
                    $len = count($columns_array);
                    $cnt = 1;
                    foreach ($columns_array as $column) {
                       $final_sql.= " $column  ";

                       if ($cnt!=$len){
                          $final_sql.= " , " ;
                       }
                        $cnt++ ;
                    }
                }
                else {
                    $final_sql.=" * ";
                }
                $limitclause = "LIMIT ".$startLimit." , ".$limit;
            }
           
            
             $sql = " $final_sql from ( $query ) as complete_rules order by `precedence` asc,`pricelist_id` $dir ".$limitclause;
            //$query = $this->db->get();
            //to workaround Bug #54382 . Codeigniter fix #1257 . Release 3.0
            $result = $this->db->query($sql);
             //$result = $this->db->get();
            //$this->subquery->end_subquery('testing');
            log_message('debug', $this->db->last_query());
            //$query = $this->db->get();
            //echo $this->db->last_query();
            if ($iscount){
                return $result->row()->total;
            }
            return $result->result_array();
            
        }
}

?>