<div id="menubar">
	<div id="menubar_container">
		<div id="menubar_company_info">
		<span id="company_title"><?php echo $this->config->item('company'); ?></span><br />
		<span style='font-size:8pt;'><?php echo $this->lang->line('common_powered_by').' Open Source Point Of Sale'; ?></span>
	</div>

		<div id="menubar_navigation">
			<div class="dcjq-mega-menu">
<ul id="mega-menu-tut" class="menu">
	<?php echo $menu; ?>
</ul>
</div>
		</div>
                
		<div id="menubar_footer">
		<?php echo $this->lang->line('common_welcome')." $user_info->first_name $user_info->last_name! | "; ?>
		<?php echo anchor("home/logout",$this->lang->line("common_logout")); ?>
		</div>

		<div id="menubar_date">
		<?php echo date('F d, Y h:i a') ?>
		</div>

	</div>
</div>
<div id="content_area_wrapper">
<div id="content_area">
