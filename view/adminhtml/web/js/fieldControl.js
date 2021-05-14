require(["jquery"], function (jQuery) {

	var toggleDriveFields = function(){
		jQuery("#orderexport_output_use_separate_directory").parents("div:eq(2)").toggle();
	}
	var toggleFtpFields = function(){
		jQuery("#orderexport_output_ftp_host").parents("div:eq(1)").toggle();
		jQuery("#orderexport_output_ftp_port").parents("div:eq(1)").toggle();
		jQuery("#orderexport_output_ftp_login").parents("div:eq(1)").toggle();
		jQuery("#orderexport_output_ftp_password").parents("div:eq(2)").toggle();
		jQuery("#orderexport_output_ftp_directory").parents("div:eq(1)").toggle();
		jQuery("#orderexport_output_use_sftp").parents("div:eq(1)").toggle();
		jQuery("#orderexport_output_delete_local_file").parents("div:eq(1)").toggle();
	}
	var toggleEmailFields = function(){
		jQuery("#orderexport_output_email_recipient").parents("div:eq(1)").toggle();
		jQuery("#orderexport_output_email_subject").parents("div:eq(1)").toggle();
	}
	var toggleCustomerGroup = function(){
		jQuery("#orderexport_filters_customer_group").parents("div:eq(1)").toggle();
	}
	var showCustomCron = function(){
		jQuery("#orderexport_cron_custom_period").parents("div:eq(2)").show();
	}

	var hideDriveFields = function(){
		jQuery("#orderexport_output_use_separate_directory").parents("div:eq(2)").hide();
	}
	var hideFtpFields = function(){
		jQuery("#orderexport_output_ftp_host").parents("div:eq(1)").hide();
		jQuery("#orderexport_output_ftp_port").parents("div:eq(1)").hide();
		jQuery("#orderexport_output_ftp_login").parents("div:eq(1)").hide();
		jQuery("#orderexport_output_ftp_password").parents("div:eq(2)").hide();
		jQuery("#orderexport_output_ftp_directory").parents("div:eq(1)").hide();
		jQuery("#orderexport_output_use_sftp").parents("div:eq(1)").hide();
		jQuery("#orderexport_output_delete_local_file").parents("div:eq(1)").hide();
	}
	var hideEmailFields = function(){
		jQuery("#orderexport_output_email_recipient").parents("div:eq(1)").hide();
		jQuery("#orderexport_output_email_subject").parents("div:eq(1)").hide();
	}
	var hideCustomerGroup = function(){
		jQuery("#orderexport_filters_customer_group").parents("div:eq(1)").hide();
	}
	var hideCustomCron = function(){
		jQuery("#orderexport_cron_custom_period").parents("div:eq(2)").hide();
	}

	// Check Fields on Page Load

	if(jQuery("#orderexport_output_use_google_drive").val() == 0){
		hideDriveFields();
	}
	if(jQuery("#orderexport_output_use_ftp").val() == 0){
		hideFtpFields();
	}
	if(jQuery("#orderexport_output_email_file").val() == 0){
		hideEmailFields();
	}
	if(jQuery("#orderexport_filters_filter_by_customer_group").val() == 0){
		hideCustomerGroup();
	}
	if(jQuery("#orderexport_cron_cron_period").val() != "custom"){
		hideCustomCron();
	}

	// Process Select Change

	jQuery(document).on("change", "#orderexport_output_use_google_drive", function(){
		toggleDriveFields();
	});
	jQuery(document).on("change", "#orderexport_output_use_ftp", function(){
		toggleFtpFields();
	});

	jQuery(document).on("change", "#orderexport_output_email_file", function(){
		toggleEmailFields();
	});

	jQuery(document).on("change", "#orderexport_filters_filter_by_customer_group", function(){
		toggleCustomerGroup();
	});

	// is auto execute enabled?
	if(jQuery("#orderexport_cron_auto_cron").val() == 0){
		jQuery("#orderexport_cron_cron_period").parents("div:eq(1)").hide();
		hideCustomCron();
	}
	jQuery(document).on("change", "#orderexport_cron_auto_cron", function(){
		if(jQuery(this).val() == 0){
			jQuery("#orderexport_cron_cron_period").parents("div:eq(1)").hide();
			hideCustomCron();
		}
		else{
			jQuery("#orderexport_cron_cron_period").parents("div:eq(1)").show();
			if(jQuery("#orderexport_cron_cron_period").val() == 'custom'){
				showCustomCron();
			} else{
				hideCustomCron();
			}
		}
	});

	jQuery(document).on("change", "#orderexport_cron_cron_period", function(){
		if(jQuery(this).val() == 'custom'){
			showCustomCron();
		}
		else{
			hideCustomCron();
		}
	});

	// FTP Password Hide/Show
	var hidden = true;
	jQuery(".field-ftp_password.with-addon label[for='orderexport_output_ftp_password']").click(function(){
		jQuery(".ftp-password-toggle").toggle();
		if(hidden){
			jQuery("#orderexport_output_ftp_password").attr('type', 'text');
			hidden = false;
		} else{
			jQuery("#orderexport_output_ftp_password").attr('type', 'password');
			hidden = true;
		}
	});

	// Template Tab Select All Button
	jQuery('#selectAll').click(function(){
		jQuery("#orderexport_template_order_fields option").prop('selected', true);
		return false;
	});

	// Template Tab Deselect All Button
	jQuery('#deselectAll').click(function(){
		jQuery("#orderexport_template_order_fields option").prop('selected', false);
		return false;
	});

});