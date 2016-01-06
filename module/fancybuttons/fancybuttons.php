<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Koshinski_vc_addon_FancyButtons extends Koshinski_vc_addon_Module {
	public $shortcode_prefix;
	public $shortcode_category_name;
	public $shortcode_textdomain;
	public $plugin_name;
	public $version;
	public $variants;
	public $add_script;
	
	public function __construct(){
		parent::__construct();
		
		$this->plugin_name = 'fancybuttons';
		$this->version = '1.0.0';
		
		$this->init_variants();
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
			*	shortcode registrieren
			*/
			add_shortcode( $this->shortcode_prefix . $this->plugin_name, array( $this, $this->plugin_name . '_shortcode' ) );

			
			/**
			*	public script und style prüfen
			*/
			add_action( 'wp_footer', array( $this, 'conditional_scripts' ) );
			
		}		
	}
	
	public function init_variants(){
		
		$this->variants[] = array(
			'svg' => '<div style="height: 0; width: 0; position: absolute; visibility: hidden;" aria-hidden="true"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" focusable="false"><symbol id="ripply-scott-%1$s" viewBox="0 0 100 100"><circle id="ripple-shape-%1$s" cx="1" cy="1" r="1" /></symbol></svg></div>',
			'size' => 100,
			'class' => 'theme-1',
			'name' => __('Circle', $this->shortcode_textdomain),
		);
		$this->variants[] = array(
			'svg' => '<div style="height: 0; width: 0; position: absolute; visibility: hidden;" aria-hidden="true"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" focusable="false"><!-- Because of Firefox. --><defs><radialGradient id="gradient-%1$s"><stop offset="0" stop-color="#0868BB" /><stop offset="0.25" stop-color="#0075D8" /><stop offset="0.35" stop-color="#0868BB" /><stop offset="0.50" stop-color="#0075D8" /><stop offset="0.60" stop-color="#0868BB" /><stop offset="0.85" stop-color="#0075D8" /><stop offset="1" stop-color="#0868BB" /></radialGradient></defs><symbol id="ripply-scott-%1$s" viewBox="0 0 100 100"><circle id="ripple-shape-%1$s" fill="url(#gradient-%1$s)" cx="1" cy="1" r="1" /></symbol></svg></div>',
			'size' => 100,
			'class' => 'theme-2',
			'name' => __('Circle & RadialGradient', $this->shortcode_textdomain),
		);
		$this->variants[] = array(
			'svg' => '<div style="height: 0; width: 0; position: absolute; visibility: hidden;" aria-hidden="true"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" focusable="false"><symbol id="ripply-scott-%1$s" viewBox="0 0 100 100"><g><polygon points="5.6,77.4 0,29 39.1,0 83.8,19.3 89.4,67.7 50.3,96.7" /><polygon fill="rgba(255,255,255,0.35)" transform="scale(0.5), translate(50, 50)" points="5.6,77.4 0,29 39.1,0 83.8,19.3 89.4,67.7 50.3,96.7" /><polygon fill="rgba(255,255,255,0.25)" transform="scale(0.25), translate(145, 145)" points="5.6,77.4 0,29 39.1,0 83.8,19.3 89.4,67.7 50.3,96.7" /></g></symbol></svg></div>',
			'size' => 4,
			'class' => 'theme-3',
			'name' => __('Polygon', $this->shortcode_textdomain),
		);
		$this->variants[] = array(
			'svg' => '<div style="height: 0; width: 0; position: absolute; visibility: hidden;" aria-hidden="true"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" focusable="false"><defs><linearGradient id="gradient-%1$s" gradientTransform="rotate(0)"><stop offset="0" stop-color="#0868BB" /><stop offset="0.15" stop-color="#0075D8" /><stop offset="0.2" stop-color="#0868BB" /><stop offset="0.25" stop-color="#0075D8" /><stop offset="0.3" stop-color="#0868BB" /><stop offset="0.35" stop-color="#0075D8" /><stop offset="0.4" stop-color="#0868BB" /><stop offset="0.45" stop-color="#0075D8" /><stop offset="0.5" stop-color="#0868BB" /><stop offset="0.6" stop-color="#0075D8" /><stop offset="0.65" stop-color="#0868BB" /><stop offset="0.7" stop-color="#0075D8" /><stop offset="0.75" stop-color="#0868BB" /><stop offset="0.8" stop-color="#0075D8" /><stop offset="0.85" stop-color="#0868BB" /><stop offset="0.9" stop-color="#0075D8" /><stop offset="0.95" stop-color="#0868BB" /><stop offset="1" stop-color="#0075D8" /></linearGradient></defs><symbol id="ripply-scott-%1$s" viewBox="0 0 120 120"><rect width="5" height="5" fill="url(#gradient-%1$s)" /></symbol></svg></div>',
			'size' => 100,
			'class' => 'theme-4',
			'name' => __('Rect & LinearGradient', $this->shortcode_textdomain),
		);

	}
	
	public function fancybuttons_register_script_and_styles(){
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . $this->plugin_name . '-public.css', array(), $this->version, 'all' );
		
		wp_register_script( 'tweenmax', '//cdnjs.cloudflare.com/ajax/libs/gsap/1.17.0/TweenMax.min.js', array( 'jquery' ), $this->version, false );
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . $this->plugin_name . '-public.js', array( 'jquery' ), $this->version, false );
		
	}
	
	public function conditional_scripts(){
		/**
		*	public script und style nur laden wenn der shortcode im content vorhanden ist
		*/
		if( ! $this->add_script )
			return;
		
		wp_enqueue_style( $this->plugin_name );
		
		wp_enqueue_script( 'tweenmax' );
		wp_enqueue_script( $this->plugin_name );
	}

	public function fancybuttons_shortcode( $atts ) {
		$this->add_script = true;
		
		$output 	= '';
		$auswahl 	= array();

		// Attributes
		extract( shortcode_atts(
			array(
				'htmlid' 			=> 'koshinski-fancybuttons-',
				'label' 			=> '',
				'variants' 			=> '',
				'timing' 			=> '',
				'css' 				=> '',
				'link_type'			=> 'href',
				'href_link'			=> '',
				'js_callback_link'	=> '',
				'display_icon'		=> 'no',
				'icon_fontawesome'	=> '',
				'icon_alignment'	=> 'left',
				
				
				'additional_css' 	=> ''
			), $atts )
		);

		/* erzeugt unique id, für den fall das mehrere dieses shortcode auf einer page sind */
		$randomID = str_replace( '.', '', uniqid(true) );
		
		$rawhtmlid = $htmlid . $randomID;
		
		foreach( $this->variants as $key => $value ){
			if( $value['class'] === $variants ){
				$auswahl = array(
					'svg' 	=> $this->variants[$key]['svg'],
					'size'	=> $this->variants[$key]['size'],
					'class' => $this->variants[$key]['class'],
					'name' 	=> $this->variants[$key]['name'],
				);
				break;
			}
		}

		$htmlid 	= ( !empty($htmlid) ) 		? ' id="'.$rawhtmlid.'"' : '';
		$timing 	= ( !empty($timing) ) 		? (float)$timing : '0.75';
		$href_link 	= ( !empty($href_link) )	? vc_build_link( $href_link ) : '';
		$js_callback_link = ( !empty($js_callback_link) ) ? rawurldecode( base64_decode( $js_callback_link ) ) : '';
		$display_icon = ( !empty( $display_icon ) ) ? $display_icon : 'no';
		
		
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $additional_css, ' ' ), '', $atts );
		
		$svg_id 	= 'js-ripple-' . $randomID;
		$button_id 	= 'js-ripple-btn-' . $randomID;
		
		if( $display_icon == 'yes' && !empty($icon_fontawesome) && !empty($icon_alignment) ){
			vc_icon_element_fonts_enqueue( 'fontawesome' );
			$icon_fontawesome = '<i class="icon-'.$icon_alignment.' ' . esc_attr( $icon_fontawesome ) . '"></i>';
		}

		if( !empty($css) ){
			$css = rawurldecode( base64_decode( $css ) );
			$css = <<<DATA

			<style type="text/css">
			#{$button_id} {
				{$css}
			}
			</style>

DATA;
		}

		if( !empty($rawhtmlid) ){
			$css_class 			= esc_attr( $css_class );
			$label 				= esc_attr( $label );
			$output 	   	   .= sprintf( $auswahl['svg'], $randomID );
			$run_after_click	= '';
			
			if( $link_type == 'js_callback' && !empty($js_callback_link) ){
				$run_after_click = $js_callback_link;
			}elseif( $link_type == 'href' && isset($href_link) && is_array($href_link) ){
				$link_target = 'self';
				if( isset($href_link['target']) && trim($href_link['target']) == '_blank' ){
					$link_target = 'window';
				}
				$run_after_click = <<<DATA
				
							{$link_target}.location.href = '{$href_link['url']}';
				
DATA;
			}
			
			if( $icon_alignment == 'right' ){
				$label = sprintf( '%1$s%2$s', $label, $icon_fontawesome );
			}else{
				$label = sprintf( '%2$s%1$s', $label, $icon_fontawesome );
			}
			
			$output .= <<<DATA


			<div{$htmlid} class="koshinski-fancybuttons-wrap {$auswahl['class']}">
				<button id="{$button_id}" type="button" class="button styl-material {$css_class}">
					{$label}

					<svg class="ripple-obj" id="{$svg_id}"><use height="{$auswahl['size']}" width="{$auswahl['size']}" xlink:href="#ripply-scott-{$randomID}" class="js-ripple"></use></svg>
				</button>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					ripplyScott.init('{$button_id}', {$timing});
					$(document).on('click', '#{$button_id}', function(e){
						{$run_after_click}
					});
				});
			</script>
			{$css}

DATA;
			
		}
		return $output;
	}

	public function fancybuttons_shortcode_mapper(){

		$shortcodes = array(
			$this->shortcode_prefix . $this->plugin_name =>  array(
				"name" 			=> __('Fancy Buttons', $this->shortcode_textdomain),
				"description" 	=> __('Some fresh buttons', $this->shortcode_textdomain),
				"base" 			=> $this->shortcode_prefix . $this->plugin_name,
				"class" 		=> "",
				"icon"			=> plugin_dir_url(__FILE__) . "../../admin/css/koshinski-icon.png",
				"controls" 		=> "full",
				"category" 		=> $this->shortcode_category_name,
				"params" 		=> array(
					array(
						'admin_label' => true,
						'type' => 'textfield',
						'heading' => __('Label', $this->shortcode_textdomain),
						'param_name' => 'label',
						'description' => '',
						'value' => '',
						'group' => __('Style Settings', $this->shortcode_textdomain)
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Display Icon', $this->shortcode_textdomain),
						'param_name' => 'display_icon',
						'description' => '',
						'value' => array(
							__( 'No', $this->shortcode_textdomain ) => 'no',
							__( 'Yes', $this->shortcode_textdomain ) => 'yes',
						),
						'group' => __('Style Settings', $this->shortcode_textdomain),
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Icon Alignment', $this->shortcode_textdomain),
						'param_name' => 'icon_alignment',
						'description' => '',
						'value' => array(
							__( 'Left', $this->shortcode_textdomain ) => 'left',
							__( 'Right', $this->shortcode_textdomain ) => 'right',
						),
						'group' => __('Style Settings', $this->shortcode_textdomain),
						'dependency' => array(
							'element' => 'display_icon',
							'not_empty' => false,
							'value' => array( 'yes' )
						),
					),
					array(
						'type' => 'iconpicker',
						'heading' => __( 'Icon', $this->shortcode_textdomain ),
						'param_name' => 'icon_fontawesome',
						'settings' => array(
							'emptyIcon' => false, // default true, display an "EMPTY" icon? - if false it will display first icon from set as default.
							'iconsPerPage' => 200, // default 100, how many icons per/page to display
						),
						'group' => __('Style Settings', $this->shortcode_textdomain),
						'dependency' => array(
							'element' => 'display_icon',
							'not_empty' => false,
							'value' => array( 'yes' )
						),
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Variants', $this->shortcode_textdomain),
						'param_name' => 'variants',
						'description' => '',
						'value' => $this->variants,
						'group' => __('Style Settings', $this->shortcode_textdomain)
					),
					array(
						'type' => 'textfield',
						'heading' => __('Timing', $this->shortcode_textdomain),
						'param_name' => 'timing',
						'description' => __('Timing in seconds; 0.75', $this->shortcode_textdomain),
						'value' => '0.75',
						'group' => __('Style Settings', $this->shortcode_textdomain)
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Link Type', $this->shortcode_textdomain),
						'param_name' => 'link_type',
						'description' => '',
						'value' => array(
							__('Href', $this->shortcode_textdomain) => 'href',
							__('JS Callback', $this->shortcode_textdomain) => 'js_callback'
						),
						'group' => __('Style Settings', $this->shortcode_textdomain)
					),
					array(
						'type' => 'vc_link',
						'heading' => __('Href Link', $this->shortcode_textdomain),
						'param_name' => 'href_link',
						'description' => '',
						'value' => '',
						'group' => __('Style Settings', $this->shortcode_textdomain),
						'dependency' => array(
							'element' => 'link_type',
							'not_empty' => false,
							'value' => array( 'href' )
						),
					),
					array(
						'type' => 'textarea_raw_html',
						'edit_field_class' => 'vc_col-xs-12 vc_column textarea_small',
						'heading' => __('JS Callback', $this->shortcode_textdomain),
						'param_name' => 'js_callback_link',
						'description' => __('Enter Functionname, incl. Parameters:   test(123);', $this->shortcode_textdomain),
						'value' => '',
						'group' => __('Style Settings', $this->shortcode_textdomain),
						'dependency' => array(
							'element' => 'link_type',
							'not_empty' => false,
							'value' => array( 'js_callback' )
						),
					),


					array(
						'type' => 'textarea_raw_html',
						'heading' => __('CSS', $this->shortcode_textdomain),
						'param_name' => 'css',
						'description' => __('Button specific CSS rules', $this->shortcode_textdomain),
						'value' => '',
						'group' => __('CSS Settings', $this->shortcode_textdomain)
					),
					
					
					
					
					array(
						'type' => 'css_editor',
						'heading' => __('Additional CSS', $this->shortcode_textdomain),
						'param_name' => 'additional_css',
						'description' => '',
						'value' => '',
						'group' => __('Design Settings', $this->shortcode_textdomain)
					),
				)
			),
		);
		if ( is_array($shortcodes) ){
			foreach ($shortcodes as $sc_name => $sc_array) {
				vc_map($sc_array);
			}
		}
	}

	
}
$fancybuttons = new Koshinski_vc_addon_FancyButtons();
$fancybuttons->run();



