<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Koshinski_vc_addon_GoogleMaps extends Koshinski_vc_addon_Module {
	public $shortcode_prefix;
	public $shortcode_category_name;
	public $shortcode_textdomain;
	public $plugin_name;
	public $version;
	public $languages;
	public $add_script;

	public function __construct(){
		parent::__construct();
		
		$this->plugin_name = 'googlemaps';
		$this->version = '1.0.0';
		
		$this->languages = array(
			'de' => __('DE', $this->shortcode_textdomain),
			'en' => __('EN', $this->shortcode_textdomain),
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
			*	shortcode registrieren
			*/
			add_shortcode( $this->shortcode_prefix . $this->plugin_name, array( $this, $this->plugin_name . '_shortcode' ) );

			
			/**
			*	public script und style prüfen
			*/
			add_action( 'wp_footer', array( $this, 'conditional_scripts' ) );
		}		
		
	}

	public function googlemaps_register_script_and_styles(){
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . $this->plugin_name . '-public.css', array(), $this->version, 'all' );
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . $this->plugin_name . '-public.js', array( 'jquery' ), $this->version, false );
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
	

	public function googlemaps_shortcode( $atts, $content = null ) {
		$this->add_script = true;

		$output = $mapoverlay = $mapcontainer = '';

		// Attributes
		extract( shortcode_atts(
			array(
				'htmlid' => 'map-canvas-',
				'apikey' => '',
				'lat' => '15',
				'lng' => '15',
				'zoom' => '14',
				'json_styles' => '',
				'display_overlay' => '',
				'overlay_background_color' => '',
				'overlay_text_color' => '',
				'mode' => 'fullheight',
				'min_height' => '',
				'additional_css' => '',
				'language' => 'de',
				'map_marker' => ''
			), $atts )
		);

		/* erzeugt unique id, für den fall das mehrere dieses shortcode auf einer page sind */
		$randomID = str_replace( '.', '', uniqid(true) );
		
		$rawhtmlid = $htmlid . $randomID;
		

		$apikey = ( !empty($apikey) ) ? 'key='.$apikey.'&amp;' : '';
		$htmlid = ( !empty($htmlid) ) ? ' id="'.$htmlid . $randomID.'"' : '';
		$jsonstyles = ( !empty($json_styles) ) ? urldecode(base64_decode($json_styles)) : "''";
		$display_overlay = ( !empty($display_overlay) && $display_overlay == 'true' ) ? true : false;
		$mode = ( !empty($mode) ) ? $mode : 'minheight';
		$min_height = ( !empty($min_height) ) ? (int)$min_height : 350;
		$language = ( !empty($language) ) ? $language : 'de';
		$map_marker = ( !empty($map_marker) ) ? wp_get_attachment_image_src( $map_marker, 'thumbnail' )[0] : '';
		
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $additional_css, ' ' ), '', $atts );
		
		$content = ( !empty($content) ) ? wpb_js_remove_wpautop($content,true) : '';
		
		$output_min_height = ' data-minheight="'.$min_height.'"';
		$output_mode = ' data-mode="'.$mode.'"';
		
		if( !empty($rawhtmlid) ){
			
			$mapoverlay .= '
				
					<div class="gmap-wrap">

			';
			$mapoverlay .= "\n\t\t\t\t\t\t" . '<div'.$htmlid.' class="gmap"'.$output_min_height.$output_mode.'></div>' . "\n";
						
			if( $display_overlay && !empty($content) ){
				$mapoverlay .= '
						<!-- .googlemap-overlay-'.$randomID.' -->
						<div class="gmap-overlay '.esc_attr( $css_class ).'">
							%1$s
						</div><!-- /.gmap-overlay -->
						<!-- /.googlemap-overlay-'.$randomID.' -->
				';
			}
			$mapoverlay .= '

						%2$s
							
					</div><!-- /.gmap-wrap -->

			';
			$output = sprintf( $mapoverlay, do_shortcode($content), $output );
			
			$output .= '

			<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?'.$apikey.'v=3&amp;language='.$language.'"></script>
			<script type="text/javascript">
				function googlemaps_init_'.$randomID.'(){
					var styles = '.$jsonstyles.';
					var styledMap = new google.maps.StyledMapType(styles, { name: "Styled Map" });
					var myPosition = new google.maps.LatLng('.$lat.', '.$lng.');
					var map = new google.maps.Map(document.getElementById("'.$rawhtmlid.'"), {
						zoom: '.$zoom.',
						center: myPosition,
						draggable: false,
						scrollwheel: false,
						zoomControl: false,
						panControl: false,
						rotateControl: false,
						mapTypeControl: false,
						streetViewControl: false,
						scaleControl: false,
						overviewMapControl: false,
						mapTypeIds: [ google.maps.MapTypeId.ROADMAP, "map_style" ]
					});
					var marker = new google.maps.Marker({
						position: myPosition,
						map: map,
						icon: "'.$map_marker.'",
						draggable: false
					});
					marker.setMap(map);
					map.mapTypes.set("map_style", styledMap);
					map.setMapTypeId("map_style");

				}
				google.maps.event.addDomListener(window, "load", googlemaps_init_'.$randomID.');
				google.maps.event.addDomListener(window, "resize", googlemaps_init_'.$randomID.');
			</script>

			' . "\n";
		}
		return $output;
	}

	public function googlemaps_shortcode_mapper(){
		
		$shortcodes = array(
			$this->shortcode_prefix . $this->plugin_name =>  array(
				"name" 			=> __('Google Maps', $this->shortcode_textdomain),
				"description" 	=> __('Customize your Google Maps', $this->shortcode_textdomain),
				"base" 			=> $this->shortcode_prefix . $this->plugin_name,
				"class" 		=> "",
				"icon"			=> plugin_dir_url(__FILE__) . "../../admin/css/koshinski-icon.png",
				"controls" 		=> "full",
				"category" 		=> $this->shortcode_category_name,
				"params" 		=> array(
					array(
						'type' => 'textfield',
						'heading' => __('API KEY', $this->shortcode_textdomain),
						'param_name' => 'apikey',
						'description' => __('required', $this->shortcode_textdomain),
						'value' => '',
						'group' => __('Map Settings', $this->shortcode_textdomain)
					),
					array(
						'type' => 'textfield',
						'heading' => __('Latitude', $this->shortcode_textdomain),
						'param_name' => 'lat',
						'description' => __('required', $this->shortcode_textdomain),
						'value' => '',
						'group' => __('Map Settings', $this->shortcode_textdomain)
					),
					array(
						'type' => 'textfield',
						'heading' => __('Longitude', $this->shortcode_textdomain),
						'param_name' => 'lng',
						'description' => __('required', $this->shortcode_textdomain),
						'value' => '',
						'group' => __('Map Settings', $this->shortcode_textdomain)
					),
					array(
						'type' => 'textfield',
						'heading' => __('Zoom Level', $this->shortcode_textdomain),
						'param_name' => 'zoom',
						'description' => '',
						'value' => '14',
						'group' => __('Map Settings', $this->shortcode_textdomain)
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Language', $this->shortcode_textdomain),
						'param_name' => 'language',
						'description' => '',
						'value' => $this->languages,
						'group' => __('Map Settings', $this->shortcode_textdomain)
					),
					array(
						'type' => 'attach_image',
						'heading' => __('Marker Image', $this->shortcode_textdomain),
						'param_name' => 'map_marker',
						'description' => '',
						'value' => '',
						'group' => __('Map Settings', $this->shortcode_textdomain)
					),
					
					
					
					
					
					array(
						'type' => 'dropdown',
						'heading' => __('Full Height', $this->shortcode_textdomain),
						'param_name' => 'mode',
						'admin_label' => true,
						'description' => '',
						'value' => array(
							__('Full Height', $this->shortcode_textdomain) => 'fullheight',
							__('Min Height', $this->shortcode_textdomain) => 'minheight'
						),
						'group' => __('Visual Settings', $this->shortcode_textdomain)
					),
					array(
						'type' => 'textfield',
						'heading' => __('Min Height', $this->shortcode_textdomain),
						'param_name' => 'min_height',
						'description' => __('in px', $this->shortcode_textdomain),
						'value' => '350',
						'dependency' => array(
							'element' => 'mode',
							'not_empty' => false,
							'value' => array( 'minheight' )
						),
						'group' => __('Visual Settings', $this->shortcode_textdomain)
					),					
					array(
						'type' => 'textarea_raw_html',
						'edit_field_class' => 'vc_col-xs-12 vc_column textarea_small',
						'heading' => __('JSON Style Data', $this->shortcode_textdomain),
						'param_name' => 'json_styles',
						'description' => __('<a target="_blank" href="https://snazzymaps.com/">Snazzy Maps</a> copy &amp; paste JSON Data.', $this->shortcode_textdomain),
						'value' => '',
						'group' => __('Visual Settings', $this->shortcode_textdomain)
					),
					
					
					
					
					
					array(
						'type' => 'checkbox',
						'heading' => __('Display Overlay', $this->shortcode_textdomain),
						'param_name' => 'display_overlay',
						'description' => '',
						'value' => '',
						'group' => __('Overlay Settings', $this->shortcode_textdomain)
					),
/* 					array(
						'type' => 'colorpicker',
						'heading' => __('Overlay Background Color', $this->shortcode_textdomain),
						'param_name' => 'overlay_background_color',
						'description' => '',
						'value' => '',
						'dependency' => array(
							'element' => 'display_overlay',
							'not_empty' => true,
							'value' => array('true')
						),
						'group' => __('Overlay Settings', $this->shortcode_textdomain)
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Overlay Text Color', $this->shortcode_textdomain),
						'param_name' => 'overlay_text_color',
						'description' => '',
						'value' => '',
						'dependency' => array(
							'element' => 'display_overlay',
							'not_empty' => true,
							'value' => array('true')
						),
						'group' => __('Overlay Settings', $this->shortcode_textdomain)
					),
 */					array(
						'type' => 'textarea_html',
						'heading' => __('Overlay Content', $this->shortcode_textdomain),
						'param_name' => 'content',
						'description' => '',
						'value' => '',
						'dependency' => array(
							'element' => 'display_overlay',
							'not_empty' => true,
							'value' => array('true')
						),
						'group' => __('Overlay Settings', $this->shortcode_textdomain)
					),
					
					
					
					
					
					array(
						'type' => 'css_editor',
						'heading' => __('Additional CSS', $this->shortcode_textdomain),
						'param_name' => 'additional_css',
						'description' => '',
						'value' => '',
						'dependency' => array(
							'element' => 'display_overlay',
							'not_empty' => true,
							'value' => array('true')
						),
						'group' => __('Overlay Design Settings', $this->shortcode_textdomain)
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
$googlemaps = new Koshinski_vc_addon_GoogleMaps();
$googlemaps->run();



