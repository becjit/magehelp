<!--<div id="menubar" class="ui-widget-header">-->
<div id="menubar">
    <div id="menubar_container">
		<div id="menubar_company_info">
		<span id="company_title"><?php echo $this->config->item('company'); ?></span><br />
<!--                    <h1 class="logo"><a accesskey="1" href="http://www.shopifine.com"></a></h1>-->
		
                </div>

		<div id="menubar_navigation">
			
                    <ul id="superfishMenu" class="sf-menu sf-js-enabled sf-shadow">
                            <?php echo $menu; ?>
                    </ul>

		</div>
        <div id="menubar_admin_navigation">
			
<ul id="superfishAdminMenu" class="sf-menu sf-js-enabled sf-shadow">
	<?php echo $adminmenu; ?>
</ul>

</div>
                
		<div id="menubar_footer">
		<?php echo $this->lang->line('common_welcome') ?>
                <?php echo anchor("home/loadProfile"," $user_info->first_name $user_info->last_name!"); ?>
                    &nbsp;&nbsp;&nbsp;
		<?php echo anchor("home/logout",$this->lang->line("common_logout")); ?>
		</div>

<!--		<div id="menubar_date">
		<?php echo date('F d, Y h:i a') ?>
		</div>-->

	</div>
</div>
<div id="content_area_wrapper">
<div id="content_area">
    <style>
        #menubar_footer > a {
    color: #819FF7;
    font-size: 110%;
}
    </style>