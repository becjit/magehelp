

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<base href="<?php echo base_url();?>" />
	<title><?php echo $this->config->item('company').' -- '.$this->lang->line('common_powered_by').' Magehelp' ?></title>
	<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/ospos.css" />
<!--        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />-->
	<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/ospos_print.css"  media="print"/>
<!--        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/menu/dcmegamenu.css" />
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/menu/custommenu.css" />-->
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/superfish/css/superfish.css" />
        <!--<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/redmond/jquery-ui-1.8.24.custom.css" />-->
        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/redmond/jquery-ui.css">
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/ui.jqgrid.css" />
        <!-- This should always be last as it has all the customization -->
        <link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />
        
        
	<script>BASE_URL = '<?php echo site_url(); ?>';</script>
<!--	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
        <script src="http://users.tpg.com.au/j_birch/plugins/superfish/js/superfish.js"></script>
        <script src="http://cherne.net/brian/resources/jquery.hoverIntent.js"></script>-->
          
        
        
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js"></script>
        <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/datejs/1.0/date.min.js"></script>
         <!--<script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.8.24.custom.min.js"></script>-->
        <script src="<?php echo base_url();?>js/superfish.js"></script>
        <script src="<?php echo base_url();?>js/hoverIntent.js"></script>
<!--          <script type='text/javascript' src="<?php echo base_url();?>js/jquery.validate.min.js"></script>-->
        <script type='text/javascript' src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.js"></script>
        <script src="<?php echo base_url();?>js/shopifine.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
	<script src="<?php echo base_url();?>js/thickbox.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
	<script src="<?php echo base_url();?>js/common.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
	<script src="<?php echo base_url();?>js/manage_tables.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
	<script src="<?php echo base_url();?>js/swfobject.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
	<script src="<?php echo base_url();?>js/date.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
	<script src="<?php echo base_url();?>js/json2.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
        <script src="<?php echo base_url(); ?>js/i18n/grid.locale-en.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>js/jquery.jqGrid.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
        <script src="<?php echo base_url();?>js/grid.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<!--<script src="<?php echo base_url();?>js/dialogs.js" type="text/javascript" language="javascript" charset="UTF-8"></script>-->
        <script type="text/javascript" src="https://raw.github.com/maxatwork/form2js/master/src/jquery.toObject.js"></script>
        <script type="text/javascript" src="https://raw.github.com/maxatwork/form2js/master/src/form2js.js"></script>
<!--        <script src="<?php echo base_url();?>js/menu/jquery.dcmegamenu.1.3.3.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
        <script src="<?php echo base_url();?>js/menu/jquery.hoverIntent.minified.js" type="text/javascript" language="javascript" charset="UTF-8"></script>-->
<!--        <script src="<?php echo base_url();?>js/jMenu.jquery.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>-->
        
<!--        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/ui-lightness/jquery-ui.css">-->
<!--<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/redmond/jquery-ui.css">-->
<!--<link rel="stylesheet" type="text/css" href="http://users.tpg.com.au/j_birch/plugins/superfish/css/superfish.css">-->


        
        <style>
            .scannedRow { background-color: #F5A9A9;}
            #jMenu {position: relative;
    z-index: 1000;}
            
        </style>
	
<!--<script type="text/javascript">
$(document).ready(function($){
	$('#mega-menu-tut').dcMegaMenu({
		rowItems: '3',
		speed: 'fast'
	});
});
</script>-->
        
        <script>
            var user_id = <?php echo $this->session->userdata('person_id'); ?>;
    $(document).ready(function(){ 
        $("ul.sf-menu").superfish();
        
       // $("#jMenu").jMenu();
        // $(".jmenu").jMenu();// more complex jMenu plugin called $("#jMenu").jMenu({ ulWidth : 'auto', effects : { effectSpeedOpen : 300, effectTypeClose : 'slide' }, animatedText : false }); 
    });
    
</script>