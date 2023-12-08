<?php

/**
 * The core plugin class.
 *
 * This is used to define admin-specific hooks and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    new-custom-order-columns
 * @subpackage new-custom-order-columns/includes
 * @author     Yakacj
 */
 
namespace YCPlugins\YcOrderColumns;

defined( 'ABSPATH' ) or exit;

class Ycocw_Init {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
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
	    
	    define( 'YCOCW_VERSION', $this->get_yc_plugin_version() );
	    
		$this->plugin_name = 'ycocw';
		$this->load_dependencies();
		$this->define_admin_hooks();

	}
    
   /**
    * Currently plugin version.
    * Start at version 1.0.0 and use SemVer - https://semver.org
    *
    * @since    1.0.0
	* @access   private
	* @return   string    $plugin_version    Getting from plugin data
    */
    private function get_yc_plugin_version(){
        
	    if( ! function_exists( 'get_plugin_data' ) ){
		    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	    }
	        $plugin_data = get_plugin_data( YCOCW_FILE );
	        $plugin_version = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '1.0.0';
	        
        return $plugin_version;
    }
    
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - includes/loader class. Orchestrates the hooks of the plugin.
	 * - admin/admin class. Defines all hooks for the admin area.
	 * - public/public class. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ycocw-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ycocw-admin.php';

		$this->loader = new \YCPlugins\YcOrderColumns\Ycocw_Loader();

	}
	

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new \YCPlugins\YcOrderColumns\Ycocw_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_menu', $plugin_admin, 'ycocw_submenu_page', 99 );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'ycocw_init' );
        $this->loader->add_filter( 'plugin_action_links_'.plugin_basename( YCOCW_FILE ), $plugin_admin, 'ycocw_plugin_settings_link' );
        $this->loader->add_filter( 'manage_edit-shop_order_columns', $plugin_admin, 'ycocw_shop_orders_admin_columns', 20 );
        $this->loader->add_action( 'manage_shop_order_posts_custom_column', $plugin_admin, 'ycocw_custom_orders_list_column_content', 20, 2 );

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
	 * @return    includes/loader class    Orchestrates the hooks of the plugin.
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
