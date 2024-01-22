<?php

/**
 * Plugin admin class
 *
 * @since       1.0.0
 *
 * @package     ycocw
 * @subpackage  ycocw/admin
 * @author      yakacj
 * @link        https://profiles.wordpress.org/yakacj/
 */

namespace YCPlugins\YcOrderColumns;

defined( 'ABSPATH' ) or exit;

class Ycocw_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
    
    /**
	 * The settings link of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return   string    setting url
	 */
	private $configure;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since       1.0.0
	 * @param      string    $plugin_name  The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->configure   = admin_url( 'admin.php/?page=ycocw-columns' );

	}
    
    /**
	 * Register the stylesheets for the admin area.
	 * 
	 * @access  public
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Ycocw_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ycocw_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        wp_enqueue_style( $this->plugin_name . '_admin_style', plugin_dir_url( __FILE__ ) . 'css/ycocw-admin.css' );

        
	}
	
	/**
	 * Register the JavaScript for the admin area.
	 * 
	 * @access  public
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Ycocw_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ycocw_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        wp_enqueue_script( $this->plugin_name . '_admin_js', plugin_dir_url( __FILE__ ) . 'js/ycocw-admin.js', array( 'jquery' ) );

	}
	
    /**
     * Plugin admin menu
     * 
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function ycocw_submenu_page() {
        add_submenu_page( 'woocommerce', 'Order columns', 'Order columns', 'manage_woocommerce', 'ycocw-columns', [ $this,'ycocw_menu_page_contents' ], 20 ); 
    }
    
    /**
	 * Show quick settings link on plugin page
	 * 
	 * @access  public
	 * @since   1.0.0
	 * @return  Array
	 */
    public function ycocw_plugin_settings_link( $links ) {
	    
	    $links[] = sprintf('<a href="%1$s">%2$s</a>', esc_url( $this->configure ), __( 'Columns', 'new-custom-order-columns' ) );
	    
	    return $links;
    }
    
    /**
     * Get columns settings
     * 
     * @access  private static
     * @since   1.0.0
     * @return  array
     */
    private static function ycocw_get_settings(){
        return get_option( 'ycocw_columns', array() );
    }
    
    /**
	 * Admin settings page contents
	 * 
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
    public function ycocw_menu_page_contents(){
        if ( ! current_user_can( 'manage_woocommerce' ) ) wp_die();
        
        $cols = self::ycocw_get_settings();

        include_once 'view/ycocw-page-contents.php';
    }
    
    /**
	 * Save new columns
	 * 
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
    public function ycocw_init(){
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
        
        $cols = array();
        
        if( isset( $_POST['ycocw_save'] ) ) {
        	if ( ! ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'ycocw-nonce' ) ) ) {
			die( 'Security check' );
		}
			
            $titles =  isset( $_POST['col_title'] ) ? array_map( 'esc_attr', array_map( 'sanitize_text_field', (array) $_POST['col_title'] ) ) : array();
            $metas  =  isset( $_POST['col_meta'] ) ? array_map( 'esc_attr', array_map( 'sanitize_text_field', (array) $_POST['col_meta'] ) ) : array();
            
            for ( $i = 0; $i < count( $titles ); $i++ ) {
            
                if( ! empty( $titles[ $i ] ) ){
                    $cols[] = array( 
                        'col_title' => $titles[ $i ], 
                        'col_slug'  => sanitize_title( $titles[ $i ] ), 
                        'col_meta'  => $metas[ $i ] 
                        );
                }
            }
            
            if ( is_admin() && current_user_can( 'manage_options' ) ) { 
                update_option( 'ycocw_columns', $cols );   
            }
        }
    }
    
    /**
	 * Show new columns to shop order list
	 * 
	 * @access  public
	 * @since   1.0.0
	 * @return  Array
	 */    
    public function ycocw_shop_orders_admin_columns( $columns ) {
            
        $cols = self::ycocw_get_settings();
            
        if( empty( $cols ) ) return $columns;
            
        $custom_columns = array();
    
        foreach ( $columns as $column_name => $column_info ) {

            $custom_columns[ $column_name ] = $column_info;
            
            foreach( $cols as $col ){
                
                if ( 'order_total' === $column_name ) {
                    $custom_columns[ $col['col_slug'] ] = $col['col_title'];
                }
            }
        }

           return $custom_columns;
    }
    
    /**
	 * Show column contents
	 * 
	 * @access  public
	 * @since   1.0.0
	 * @return  void echo string
	 */
    public function ycocw_custom_orders_list_column_content( $column, $order_id ){

            $order  = wc_get_order( $order_id );
            $cols   = self::ycocw_get_settings();
            
            if( empty( $cols ) ) return;
            
            foreach( $cols as $k => $col ){
                if( $col['col_slug'] == $column ){
                    
                    $meta       = $col['col_meta'];
                    $get_meta   = "get_{$meta}";
                    $value      = '';
                    
                    if ( is_callable( array( $order , "get_{$meta}" ) ) ) {
                        $value = $order->$get_meta();
                    } else {
                        $value = get_post_meta( $order_id, $col['col_meta'], true );
                    }
                    
                    $value          = $value ? $value : '--';
                    $value_filter   = apply_filters( 'ycocw_column_value', $value, $column, $order_id );
                    // Sanitized value
                    echo esc_attr( $value_filter );
                }
            }

    }
    
    /**
     * Settings update message
     * 
     * @access  private
     * @since   1.0.0
     */
    private function ycocw_save_message(){
        if( isset( $_POST['ycocw_save'] ) ) :
        	if ( ! ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'ycocw-nonce' ) ) ) {
				return;
			}
        ?>
        <div id="message_success" class="updated inline"><p><strong><?php esc_html_e( 'New columns settings have been saved.', 'new-custom-order-columns' );?></strong></p></div>
        <?php
        endif;
    }
        
}

