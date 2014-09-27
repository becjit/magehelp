<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<base href="<?php echo base_url();?>" />
<title><?php echo $this->config->item('company').' -- '.$this->lang->line('common_powered_by').' Magehelp' ?></title>
<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/ospos.css" />
<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/ospos_print.css"  media="print"/>
<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/superfish/css/superfish.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/redmond/jquery-ui-1.9.1.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/ui.jqgrid.css" />
<!-- This should always be last as it has all the customization -->
<link rel="stylesheet" rev="stylesheet" href="<?php echo base_url();?>css/shopifine.css" />


<script>BASE_URL = '<?php echo site_url(); ?>';</script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.7.2.min.js"></script>
<script type='text/javascript' src="<?php echo base_url();?>js/jquery-ui-1.9.1.custom.min.js"></script>
<script src="<?php echo base_url();?>js/superfish.js"></script>
<script src="<?php echo base_url();?>js/hoverIntent.js"></script>
<script type='text/javascript' src="<?php echo base_url();?>js/jquery.validate.min.js"></script>
<script type='text/javascript' src="<?php echo base_url();?>js/date.js"></script>
<script src="<?php echo base_url();?>js/shopifine.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="<?php echo base_url();?>js/common.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="<?php echo base_url();?>js/json2.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="<?php echo base_url(); ?>js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>js/jquery.jqGrid.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="<?php echo base_url(); ?>js/form2js.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="<?php echo base_url();?>js/jquery.toObject.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="<?php echo base_url();?>js/grid.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="<?php echo base_url();?>js/dialogs.js" type="text/javascript" language="javascript" charset="UTF-8"></script>



<style>
    .scannedRow { background-color: #F5A9A9;}
    #jMenu {position: relative;
z-index: 1000;}

</style>
<script>
    var user_id = <?php echo $this->session->userdata('person_id'); ?>;
$(document).ready(function(){ 
    $("ul.sf-menu").superfish();
    
});

</script>