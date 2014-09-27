<div id="menubar">
	<div id="menubar_container">
		<div id="menubar_company_info">
		<span id="company_title"><?php echo $this->config->item('company'); ?></span><br />
		<span style='font-size:8pt;'><?php echo $this->lang->line('common_powered_by').' Open Source Point Of Sale'; ?></span>
	</div>

		<div id="menubar_navigation">
			<?php
			foreach($allowed_modules->result() as $module)
			{
			?>
			<div class="menu_item">
				
				<a href="<?php echo site_url("$module->module_id");?>"><?php echo $this->lang->line("module_".$module->module_id) ?></a>
			</div>
			<?php
			}
			?>
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
