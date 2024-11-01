<?php
/*
Plugin Name: website Review pilot review invite
Description: This plugin adds the Review pilot invite review to customer after an order is completed.
Version: 0.3.7
Author: Review pilot
Author URI: reviewpilot.nl
*/
require_once('inc/reviewpilot-funct.php');
require_once('admin/reviewpilot-settings.php');
add_action('admin_menu', 'website_reviewpilot_admin_menu');
add_action('add_meta_boxes', 'website_reviewpilot_add_box');
add_action( 'save_post', 'reviewpilot_save_meta_settings' );
add_shortcode( 'all_reviewpilot_review', 'all_reviewpilot_review_shortcode' );
add_action('wp_footer','woo_reviewpilot_styles');
add_action( 'wp_enqueue_scripts', 'woo_reviewpilot_scripts' );
?>