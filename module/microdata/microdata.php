<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



if ( defined( 'WPB_VC_VERSION' ) && class_exists('WPBakeryShortCodesContainer') ) {
	
	class WPBakeryShortCode_koshinski_vc_addon_microdata_container extends WPBakeryShortCodesContainer {}
	class WPBakeryShortCode_koshinski_vc_addon_microdata_innercontainer extends WPBakeryShortCodesContainer {}
	
	/*	
	nur 2 nesting level
	erlaubte szenarien:
	

	container {
		item
		container {
			item
		}
		item
	}

	*/
    
}

if ( defined( 'WPB_VC_VERSION' ) && class_exists('WPBakeryShortCodesContainer') ) {
	class WPBakeryShortCode_koshinski_vc_addon_microdata_item extends WPBakeryShortCode {
	}
}



class Koshinski_vc_addon_MircoData extends Koshinski_vc_addon_Module {
	public $shortcode_prefix;
	public $shortcode_category_name;
	public $shortcode_textdomain;
	public $plugin_name;
	public $version;
	public $add_script;
	public $container_types;
	public $item_types;

	public function __construct(){
		parent::__construct();
		
		$this->plugin_name = 'microdata';
		$this->version = '1.0.0';
		
		$this->container_types = array(
			'div' => 'div',
			'section' => 'section',
			'article' => 'article',
			'h1' => 'h1',
			'h2' => 'h2',
			'h3' => 'h3',
			'h4' => 'h4',
			'h5' => 'h5',
			'h6' => 'h6',
			'span' => 'span',
		);
		$this->item_types = array(
			'span' => 'span',
			'div' => 'div',
		);
		
	}
	
	public function run(){

		if( function_exists('vc_map') ){
			
			/**
			*	visual composer element mapping
			*/
			add_action( 'init', array( $this, $this->plugin_name . '_shortcode_mapper' ) );
			
			/**
			*	script und style registrieren
			*/
			add_action( 'init', array( $this, $this->plugin_name . '_register_script_and_styles' ) );

			/**
			*	shortcode(s) registrieren
			*/
			add_shortcode( $this->shortcode_prefix . $this->plugin_name . '_container', array( $this, $this->plugin_name . '_container_shortcode' ) );
			add_shortcode( $this->shortcode_prefix . $this->plugin_name . '_innercontainer', array( $this, $this->plugin_name . '_innercontainer_shortcode' ) );
			add_shortcode( $this->shortcode_prefix . $this->plugin_name . '_item', array( $this, $this->plugin_name . '_item_shortcode' ) );

			
			/**
			*	public script und style prüfen
			*/
			add_action( 'wp_footer', array( $this, 'conditional_scripts' ) );
		}		
		
	}

	public function microdata_register_script_and_styles(){
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . $this->plugin_name . '-public.css', array(), $this->version, 'all' );
		/* wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . $this->plugin_name . '-public.js', array( 'jquery' ), $this->version, false ); */
	}
	
	public function conditional_scripts(){
		/**
		*	public script und style nur laden wenn der shortcode im content vorhanden ist
		*/
		if( ! $this->add_script )
			return;
		
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name );
	}
	

	public function microdata_container_shortcode( $atts, $content = null ) {
		$this->add_script = true;

		$output = '';

		// Attributes
		extract( shortcode_atts(
			array(
				'htmlid' => 'microdata-',
				'type' => 'div',
				'itemtype' => '',
				'itemprop' => '',
				'css_class' => '',
				'additional_css' => '',

			), $atts )
		);

		/* erzeugt unique id, für den fall das mehrere dieses shortcode auf einer page sind */
		$randomID = str_replace( '.', '', uniqid(true) );
		
		$rawhtmlid = $htmlid . $randomID;
		$htmlid = ( !empty($htmlid) ) ? 'id="'.$htmlid . $randomID.'"' : '';
		$css_class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $additional_css, ' ' ), '', $atts );
		$css_class .= ' microdata-wrap';
		
		
		$elementOpener = sprintf( '<%1$s %2$s class="%3$s" itemscope %4$s %5$s>', $type, $htmlid, esc_attr( $css_class ), ((!empty($itemtype)) ? ' itemtype="'.esc_url($itemtype).'"' : ''), ((!empty($itemprop)) ? ' itemprop="'.esc_attr($itemprop).'"' : '') );
		$elementCloser = sprintf( '</%1$s>', $type );
		
		
		
		$content = ( !empty($content) ) ? wpb_js_remove_wpautop($content,false) : '';
		
		if( !empty($rawhtmlid) ){
			$output .= $elementOpener;
			
			$output .= do_shortcode($content);
			
			$output .= $elementCloser;
		}
		return $output;
	}

	public function microdata_innercontainer_shortcode( $atts, $content = null ) {
		$this->add_script = true;

		$output = '';

		// Attributes
		extract( shortcode_atts(
			array(
				'htmlid' => 'microdata-',
				'type' => 'div',
				'itemtype' => '',
				'itemprop' => '',
				'css_class' => '',
				'additional_css' => '',

			), $atts )
		);

		/* erzeugt unique id, für den fall das mehrere dieses shortcode auf einer page sind */
		$randomID = str_replace( '.', '', uniqid(true) );
		
		$rawhtmlid = $htmlid . $randomID;
		$htmlid = ( !empty($htmlid) ) ? 'id="'.$htmlid . $randomID.'"' : '';
		$css_class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $additional_css, ' ' ), '', $atts );
		$css_class .= ' microdata-wrap';
		
		
		$elementOpener = sprintf( '<%1$s %2$s class="%3$s" itemscope %4$s %5$s>', $type, $htmlid, esc_attr( $css_class ), ((!empty($itemtype)) ? ' itemtype="'.esc_url($itemtype).'"' : ''), ((!empty($itemprop)) ? ' itemprop="'.esc_attr($itemprop).'"' : '') );
		$elementCloser = sprintf( '</%1$s>', $type );
		
		
		
		$content = ( !empty($content) ) ? wpb_js_remove_wpautop($content,false) : '';
		
		if( !empty($rawhtmlid) ){
			$output .= $elementOpener;
			
			$output .= do_shortcode($content);
			
			$output .= $elementCloser;
		}
		return $output;
	}	
	
	public function microdata_item_shortcode( $atts, $content = null ) {
		$this->add_script = true;

		$output = '';

		// Attributes
		extract( shortcode_atts(
			array(
				'htmlid' => 'microdataitem-',
				'type' => 'span',
				'label' => '',
				'content_field' => '',
				'itemprop' => '',
				'css_class' => '',
				'additional_css' => '',


			), $atts )
		);

		/* erzeugt unique id, für den fall das mehrere dieses shortcode auf einer page sind */
		$randomID = str_replace( '.', '', uniqid(true) );
		
		$rawhtmlid = $htmlid . $randomID;
		$htmlid = ( !empty($htmlid) ) ? 'id="'.$htmlid . $randomID.'"' : '';
		$css_class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $additional_css, ' ' ), '', $atts );
		$css_class .= ' microdata-item';
		
		
		$elementOpener = sprintf( '<%1$s %2$s class="%3$s" %4$s>', $type, $htmlid, esc_attr( $css_class ), ((!empty($itemprop)) ? ' itemprop="'.esc_attr($itemprop).'"' : '') );
		$elementCloser = sprintf( '</%1$s>', $type );
		
		
		
		$content_field = ( !empty($content_field) ) ? wpb_js_remove_wpautop($content_field,false) : '';
		
		if( !empty($rawhtmlid) ){
			if(!empty($label)){
				$output .= '<label class="microdata-label">'.$label.'</label>';
			}
			$output .= $elementOpener;
			$output .= do_shortcode($content_field);
			$output .= $elementCloser;
		}
		return $output;

	}
	
	
	
	public function microdata_shortcode_mapper(){
		
		$shortcodes = array(
			$this->shortcode_prefix . $this->plugin_name . '_container' =>  array(
				"name" 			=> __('Microdata Container', $this->shortcode_textdomain),
				"description" 	=> __('Customize your Google Microdata Container (only 2 Nesting Levels)', $this->shortcode_textdomain),
				"base" 			=> $this->shortcode_prefix . $this->plugin_name . '_container',
				"class" 		=> "",
				"icon"			=> plugin_dir_url(__FILE__) . "../../admin/css/koshinski-icon.png",
				"controls" 		=> "full",
				"category" 		=> $this->shortcode_category_name,
				"as_parent"		=> array('only' => $this->shortcode_prefix . $this->plugin_name . '_item,'.$this->shortcode_prefix . $this->plugin_name . '_innercontainer,vc_column_text'),
				"js_view"		=> 'VcColumnView',
				"is_container"	=> true,
				"content_element" => true,
				"show_settings_on_create" => true,
				"params" 		=> array(
				
					array(
						'type' => 'dropdown',
						'heading' => __('Container Type', $this->shortcode_textdomain),
						'param_name' => 'type',
						'admin_label' => true,
						'description' => __('Select Type of wrapping element', $this->shortcode_textdomain),
						'value' => $this->container_types,
						'group' => __('General', $this->shortcode_textdomain)
					),
					array(
						'type' => "textfield",
						'heading' => __('Itemtype', $this->shortcode_textdomain),
						'param_name' => 'itemtype',
						'admin_label' => true,
						'description' => __('Eg. http://schema.org/Person  ...  Please <a href="https://developers.google.com/schemas/formats/microdata" target="_blank">look at</a>', $this->shortcode_textdomain),
						'group' => __('General', $this->shortcode_textdomain)
					),					
					array(
						'type' => "textfield",
						'heading' => __('Itemprop', $this->shortcode_textdomain),
						'param_name' => 'itemprop',
						'admin_label' => true,
						'description' => __('Eg. jobTitle  ...  Can be empty. Please <a href="https://developers.google.com/schemas/formats/microdata" target="_blank">look at</a>', $this->shortcode_textdomain),
						'group' => __('General', $this->shortcode_textdomain)
					),				
					array(
						'type' => "textfield",
						'heading' => __('CSS Class', $this->shortcode_textdomain),
						'param_name' => 'css_class',
						'group' => __('General', $this->shortcode_textdomain)
					),				
					
					
					array(
						'type' => 'css_editor',
						'heading' => __('Additional CSS', $this->shortcode_textdomain),
						'param_name' => 'additional_css',
						'description' => '',
						'value' => '',
						'group' => __( 'Design Options', 'js_composer' ),
					),
					
				)
			),

			$this->shortcode_prefix . $this->plugin_name . '_innercontainer' =>  array(
				"name" 			=> __('Microdata Container', $this->shortcode_textdomain),
				"description" 	=> __('Customize your Google Microdata Container', $this->shortcode_textdomain),
				"base" 			=> $this->shortcode_prefix . $this->plugin_name . '_innercontainer',
				"class" 		=> "",
				"icon"			=> plugin_dir_url(__FILE__) . "../../admin/css/koshinski-icon.png",
				"controls" 		=> "full",
				"category" 		=> $this->shortcode_category_name,
				"as_parent"		=> array('only' => $this->shortcode_prefix . $this->plugin_name . '_item,vc_column_text'),
				"as_child"		=> array('only' => $this->shortcode_prefix . $this->plugin_name . '_container' ),
				"js_view"		=> 'VcColumnView',
				"is_container"	=> true,
				"content_element" => true,
				"show_settings_on_create" => true,
				"params" 		=> array(
				
					array(
						'type' => 'dropdown',
						'heading' => __('Container Type', $this->shortcode_textdomain),
						'param_name' => 'type',
						'admin_label' => true,
						'description' => __('Select Type of wrapping element', $this->shortcode_textdomain),
						'value' => $this->container_types,
						'group' => __('General', $this->shortcode_textdomain)
					),
					array(
						'type' => "textfield",
						'heading' => __('Itemtype', $this->shortcode_textdomain),
						'param_name' => 'itemtype',
						'admin_label' => true,
						'description' => __('Eg. http://schema.org/Person  ...  Please <a href="https://developers.google.com/schemas/formats/microdata" target="_blank">look at</a>', $this->shortcode_textdomain),
						'group' => __('General', $this->shortcode_textdomain)
					),					
					array(
						'type' => "textfield",
						'heading' => __('Itemprop', $this->shortcode_textdomain),
						'param_name' => 'itemprop',
						'admin_label' => true,
						'description' => __('Eg. jobTitle  ...  Can be empty. Please <a href="https://developers.google.com/schemas/formats/microdata" target="_blank">look at</a>', $this->shortcode_textdomain),
						'group' => __('General', $this->shortcode_textdomain)
					),				
					array(
						'type' => "textfield",
						'heading' => __('CSS Class', $this->shortcode_textdomain),
						'param_name' => 'css_class',
						'group' => __('General', $this->shortcode_textdomain)
					),				
					
					
					array(
						'type' => 'css_editor',
						'heading' => __('Additional CSS', $this->shortcode_textdomain),
						'param_name' => 'additional_css',
						'description' => '',
						'value' => '',
						'group' => __( 'Design Options', 'js_composer' ),
					),
					
				)
			),			
			
			

			$this->shortcode_prefix . $this->plugin_name . '_item' =>  array(
				"name" 			=> __('Microdata Item', $this->shortcode_textdomain),
				"description" 	=> __('Customize your Google Microdata Item', $this->shortcode_textdomain),
				"base" 			=> $this->shortcode_prefix . $this->plugin_name . '_item',
				"class" 		=> "",
				"icon"			=> plugin_dir_url(__FILE__) . "../../admin/css/koshinski-icon.png",
				"controls" 		=> "full",
				"category" 		=> $this->shortcode_category_name,
				"content_element" => true,
				"as_child"		=> array('only' => $this->shortcode_prefix . $this->plugin_name . '_container,' . $this->shortcode_prefix . $this->plugin_name . '_innercontainer' ),
				"params" 		=> array(

					array(
						'type' => 'dropdown',
						'heading' => __('Element Type', $this->shortcode_textdomain),
						'param_name' => 'type',
						'admin_label' => true,
						'description' => __('Select Type of element', $this->shortcode_textdomain),
						'value' => $this->item_types,
						'group' => __('General', $this->shortcode_textdomain)
					),
					array(
						'type' => "textfield",
						'heading' => __('Label', $this->shortcode_textdomain),
						'param_name' => 'label',
						'admin_label' => true,
						'group' => __('General', $this->shortcode_textdomain)
					),					
					array(
						'type' => "textfield",
						'heading' => __('Content', $this->shortcode_textdomain),
						'param_name' => 'content_field',
						'admin_label' => true,
						'group' => __('General', $this->shortcode_textdomain)
					),					
				
					array(
						'type' => "textfield",
						'heading' => __('Itemprop', $this->shortcode_textdomain),
						'param_name' => 'itemprop',
						'admin_label' => true,
						'description' => __('Eg. jobTitle  ...  Can be empty. Please <a href="https://developers.google.com/schemas/formats/microdata" target="_blank">look at</a>', $this->shortcode_textdomain),
						'group' => __('General', $this->shortcode_textdomain)
					),				
					array(
						'type' => "textfield",
						'heading' => __('CSS Class', $this->shortcode_textdomain),
						'param_name' => 'css_class',
						'group' => __('General', $this->shortcode_textdomain)
					),				

					
					array(
						'type' => 'css_editor',
						'heading' => __('Additional CSS', $this->shortcode_textdomain),
						'param_name' => 'additional_css',
						'description' => '',
						'value' => '',
						'group' => __( 'Design Options', 'js_composer' ),
					),			
			
				),
			),
			
			
		);
		if ( is_array($shortcodes) ){
			foreach ($shortcodes as $sc_name => $sc_array) {
				vc_map($sc_array);
			}
		}
	}

	
}
$microdata = new Koshinski_vc_addon_MircoData();
$microdata->run();



