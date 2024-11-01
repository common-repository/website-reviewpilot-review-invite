<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();
delete_option('reviewpilot_fields_submitted');
delete_option('website_reviewpilot_status');
delete_option('website_reviewpilot_use_style');
delete_option('website_reviewpilot_license_key');
delete_option('website_reviewpilot_sender_email');
