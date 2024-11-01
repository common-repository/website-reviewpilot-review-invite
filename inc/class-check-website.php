<?php
/**
* WC Dependency Checker
*
* Checks if website is enabled
*/
class Reviewpilot_WOO_Check {
	private static $active_plugins;
	public static function init() {
		self::$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() )
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}
	public static function website_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'website/website.php', self::$active_plugins ) || array_key_exists( 'website/website.php', self::$active_plugins );
	}
}