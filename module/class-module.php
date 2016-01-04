<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Koshinski_vc_addon_Module {
	public $shortcode_category_name;
	public $shortcode_prefix;
	public $shortcode_textdomain;
	
	public function __construct(){
		$this->shortcode_category_name = Koshinski_vc_addon::getShortcodeCategoryName();
		$this->shortcode_prefix = Koshinski_vc_addon::getShortcodePrefix();
		$this->shortcode_textdomain = Koshinski_vc_addon::getShortcodeTextDomain();
		
	}
	
	
}

$module = new Koshinski_vc_addon_Module();
