<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Koshinski_vc_addon
 * @subpackage Koshinski_vc_addon/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Koshinski_vc_addon
 * @subpackage Koshinski_vc_addon/includes
 * @author     Your Name <email@example.com>
 */
class Koshinski_vc_addon {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Koshinski_vc_addon_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	
	public static $shortcode_category_name;
	public static $shortcode_prefix;
	public static $shortcode_textdomain;
	
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'koshinski-vc-addon';
		$this->version = '1.0.0';
		$this->vc_required_version = '4.4';
		$this->public_name = 'Visual Composer Addon';
		$this->textdomain = 'koshinski-vc-addon';
		
		self::$shortcode_category_name = 'koshinski addons';
		self::$shortcode_prefix = 'koshinski-vc-addon-';
		self::$shortcode_textdomain = $this->textdomain;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}
	
	public static function getShortcodeCategoryName() {
		return self::$shortcode_category_name;
	}
	public static function getShortcodePrefix() {
		return self::$shortcode_prefix;
	}
	public static function getShortcodeTextDomain(){
		return self::$shortcode_textdomain;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Koshinski_vc_addon_Loader. Orchestrates the hooks of the plugin.
	 * - Koshinski_vc_addon_i18n. Defines internationalization functionality.
	 * - Koshinski_vc_addon_Admin. Defines all hooks for the admin area.
	 * - Koshinski_vc_addon_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		
		/**
		 * Base Class
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'module/class-module.php';

		/**
		 * Modul: Google Maps
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'module/googlemaps/googlemaps.php';
		
		/**
		 * Modul: Fancy Buttons
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'module/fancybuttons/fancybuttons.php';
		
		
		

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-koshinski-vc-addon-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-koshinski-vc-addon-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-koshinski-vc-addon-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-koshinski-vc-addon-public.php';

		$this->loader = new Koshinski_vc_addon_Loader();

		
		/**
		 *	VC Version Check
		*/
		if ( !defined( 'WPB_VC_VERSION' ) ){
			$this->loader->add_action( 'admin_notices', $this, 'koshinski_vc_addon_notice' );
			return;
		}elseif( version_compare( WPB_VC_VERSION, $this->vc_required_version ) < 0 ){
			$this->loader->add_action( 'admin_notices', $this, 'koshinski_vc_addon_notice_version' );
			return;
		}
		
	}

	public function koshinski_vc_addon_notice(){
		echo '
			<div class="updated">
				<p>' . sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', $this->textdomain), $this->public_name) . '</p>
			</div>';		
	}
	public function koshinski_vc_addon_notice_version(){
		echo '
			<div class="updated">
				<p>' . sprintf(__('<strong>%s</strong> requires <strong>%s</strong> version of <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site. Current version is %s.', $this->textdomain), $this->public_name, $this->vc_required_version, WPB_VC_VERSION) . '</p>
			</div>';
	}
	
	
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Koshinski_vc_addon_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		
		$plugin_i18n = new Koshinski_vc_addon_i18n( $this->textdomain );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Koshinski_vc_addon_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Koshinski_vc_addon_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Koshinski_vc_addon_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
