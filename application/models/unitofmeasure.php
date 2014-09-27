<?php
class Unitofmeasure extends Base_Model 
{	
	function insert($insert_data,$set_array)
	{
            return parent::insertInternal('unitofmeasure',$insert_data, $set_array);
	}
        
        function update($name,$uom_data)
	{
            //$this->db->insert('invoice',$invoice_data);
            $this->db->where('unit_of_measure',$name);
            return $this->db->updateInternal('unitofmeasure',$uom_data);
	}
	
	
        function exists($name)
	{
		$this->db->from('unitofmeasure');
		$this->db->where('unit_of_measure',$name);
		$query = $this->db->get();

		$ifExists = $query->num_rows()==1;

		return ($ifExists);
	}
        
        
        function totalNoOfRows ($where_clause_array=null,$like_fields_array=null,$or_where_clause_array=null) {
            return parent::totalNoOfRowsForResults('unitofmeasure', $where_clause_array, $like_fields_array, $or_where_clause_array);
        }
             
	
        function getAll(){
            $this->db->select('id,unit_of_measure,denom_json');
            $query = $this->db->get('unitofmeasure');
            return $query->result_array();
        }
	
        function getJson($name){
            $this->db->select('denom_json');
            $this->db->where('unit_of_measure',$name);
            $query = $this->db->get('unitofmeasure');
            if ($query->num_rows() > 0)
            {
                $row = $query->row_array(); 
                return  $row['denom_json'];
            
            } 
            return 0;
        }
        
        function getByName($name,$column_array,$isarray){
            
            return parent::getUniqueRow('unitofmeasure',array('unit_of_measure'=>$name),$column_array,$isarray);
        }
}

?>