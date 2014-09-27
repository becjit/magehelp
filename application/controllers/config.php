<?php
require_once ("secure_area.php");
class Config extends Secure_area 
{
	function __construct()
	{
		parent::__construct('config','adminmenu');
	}
	
	function index()
	{
		$this->load->view("config");
	}
		
	function save()
	{
		$batch_save_data=array(
		'company'=>$this->input->post('company'),
		'address'=>$this->input->post('address'),
		'phone'=>$this->input->post('phone'),
		'email'=>$this->input->post('email'),
		'fax'=>$this->input->post('fax'),
		'website'=>$this->input->post('website'),
		'default_tax_1_rate'=>$this->input->post('default_tax_1_rate'),		
		'default_tax_1_name'=>$this->input->post('default_tax_1_name'),		
		'default_tax_2_rate'=>$this->input->post('default_tax_2_rate'),	
		'default_tax_2_name'=>$this->input->post('default_tax_2_name'),		
		'currency_symbol'=>$this->input->post('currency_symbol'),
		'return_policy'=>$this->input->post('return_policy'),
		'language'=>$this->input->post('language'),
		'timezone'=>$this->input->post('timezone'),
		'print_after_sale'=>$this->input->post('print_after_sale')	
		);
		
		if($_SERVER['HTTP_HOST'] !='ospos.pappastech.com' && $this->Appconfig->batch_save($batch_save_data))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('config_saved_successfully')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('config_saved_unsuccessfully')));
	
		}
	}
        
        function populateConfigs(){
           $searchOn = strip($_REQUEST['_search']);
           $page = $_REQUEST['page'];
           $limit = $_REQUEST['rows'];
           $sidx = $_REQUEST['sidx'];
           $sord = $_REQUEST['sord'];
            
            
           $configdata = array();
           $count = $this->Appconfig->totalNoOfRows();
           log_message('debug','Total '.$count);
           if( $count > 0 && $limit > 0) { 
                $total_pages = ceil($count/$limit); 
           } else { 
               $total_pages = 0; 
           } 
           if ($page > $total_pages) $page=$total_pages;

           $start = $limit*$page - $limit;
 
            // if for some reasons start position is negative set it to 0 
            // typical case is that the user type 0 for the requested page 
           if($start <0) $start = 0; 
           $clauses = array('orderBy'=>$sidx,'orderDir'=>$sord,'startLimit'=>$start,'limit'=>$limit);
           $data['total'] = $total_pages;
           $data['page'] = $page;
           $data['records'] = $count; 
          
           if($searchOn=='true') {
                
                $filters = json_decode($_REQUEST['filters'],true);
                $groupOp = $filters['groupOp'];
                $rules = $filters['rules'];
                $like_condition = array();
                foreach ($rules as $rule){
                    $field = $rule['field'];
                    $op= $rule['op'];
                    $input = $rule['data'];
                    $like_condition[$field] = $input;
                }
                $configs = $this->Appconfig->getAll(false,null,$clauses,$like_condition);
            }
            else {
                $configs = $this->Appconfig->getAll(false,null,$clauses);
            }
            
            //$dp['system_name']
            foreach ($configs as $dp){
                array_push($configdata, array('id'=> $dp['id'],'dprow' => array($dp['key'],$dp['value'])));
            }
            $data['configdata'] = $configdata;
            
            echo json_encode($data);
        }
        
        function updateConfigs(){
            $oper = $_REQUEST['oper'];
            $id= $_REQUEST['id'];
            $key = $_REQUEST['key'];
            $value = $_REQUEST['value'];
            
            if ($oper== 'add' or $oper=='edit'){
                $this->Appconfig->save($key,$value);
            }
            else if ($oper== 'del'){
                $this->Appconfig->delete($id);
            }
        }
}
?>