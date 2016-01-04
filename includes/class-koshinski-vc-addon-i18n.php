<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Koshinski_vc_addon
 * @subpackage Koshinski_vc_addon/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Koshinski_vc_addon
 * @subpackage Koshinski_vc_addon/includes
 * @author     Your Name <email@example.com>
 */
class Koshinski_vc_addon_i18n {

	

	function __construct( $textdomain ){
		$this->textdomain = $textdomain;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			$this->textdomain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
