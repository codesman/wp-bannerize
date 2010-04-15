<?php
/**
 * Main class for sub.classing backend and frontend class
 * 
 * @package         wp-bannerize
 * @subpackage      wp-bannerize_class
 * @author          =undo= <g.fazioli@saidmade.com>
 * @copyright       Copyright (C) 2010 Saidmade Srl
 *
 */

/**
 * Global define: nome della tabella senza il prefisso
 */
define('WP_BANNERIZE_TABLE', 'bannerize');

class WPBANNERIZE_CLASS {

	/**
	 * Plugin version (see above)
	 *
	 * @since 2.4.7
	 * @var string
	 */
	var $version 						= "2.4.7";

    /**
     * WP-BANNERIZE release.minor.revision
     * 
     * @since 2.3.0
     * @var integer
     */
    var $release                        = "";
    var $minor                          = "";
    var $revision                       = "";
    
    /**
     * Plugin name
     *
     * @since 1.0.0
     * @var string
     */
    var $plugin_name 					= "WP Bannerize";

    /**
     * Key for database options
     *
     * @since 1.0.0
     * @var string
     */
    var $options_key 					= "wp-bannerize";

    /**
     * Options array containing all options for this plugin
     * 
     * @since 1.0.0 
     * @var array
     */
    var $options						= array();
    
    /**
     * Backend title
     *
     * @since 1.0.0
     * @var string
     */
    var $options_title					= "WP Bannerize";

    /**
     * Property for table name
     *
     * @since 1.4.0
     * @var string
     */
    var $table_bannerize				= WP_BANNERIZE_TABLE;

    /**
     * Old table name for previous compatibility
     *
     * @since 1.5.0
     * @var string
     */
    var $_old_table_bannerize			= WP_BANNERIZE_TABLE;

    /**
     * Flag for database upgrade
     *
     * @since 1.5.0
     * @var boolean
     */
    var $update							= false;


    var $content_url					= "";
    var $plugin_url						= "";
    var $ajax_url						= "";

    var $path 							= "";
    var $file 							= "";
    var $directory						= "";
    var $uri 							= "";
    var $siteurl 						= "";
    var $wpadminurl 					= "";

    /**
     * Standard PHP 4 constructor
     *
     * @since 1.0.0
     * @global object $wpdb
     */
    function WPBANNERIZE_CLASS() {
        global $wpdb;

		/**
         * Split version for more detail
         */
        $split_version  = explode(".", $this->version);
        $this->release  = $split_version[0];
        $this->minor    = $split_version[1];
        $this->revision = $split_version[2];

        /**
         * Add $wpdb->prefix to table name define in WP_BANNERIZE_TABLE. This
         * featured makes wp-bannerize compatible with Wordpress MU and Wordpress
         * with different database prefix
         *
         * @since 2.2.1
         */
        $this->table_bannerize              = $wpdb->prefix . WP_BANNERIZE_TABLE;

        /**
         * Build internal usefull paths
         */
        $this->path 						= dirname(__FILE__);
        $this->file 						= basename(__FILE__);
        $this->directory 					= basename($this->path);
        $this->uri                          = plugins_url("", __FILE__);
        $this->siteurl						= get_bloginfo('url');
        $this->wpadminurl					= admin_url();

        $this->content_url 					= get_option('siteurl') . '/wp-content';
        $this->plugin_url 					= $this->content_url . '/plugins/' . plugin_basename( dirname(__FILE__) ) . '/';
        $this->ajax_url						= $this->plugin_url . "ajax.php";
    }

    /**
     * Get option from database
     */
    function getOptions() {
        $this->options 						= get_option( $this->options_key );
    }

    /**
     * Check the Wordpress relase for more setting
     *
     * @deprecated
     */
    function checkWordpressRelease() {
        global $wp_version;
        if ( strpos($wp_version, '2.7') !== false || strpos($wp_version, '2.8') !== false  ) {}
    }
} // end of class

/**
 * Avoid widget support
 *
 * @since 2.3.5
 */
if(class_exists("WP_Widget")) {
    require_once('wp-bannerize_widget.php');
    add_action('widgets_init', create_function('', 'return register_widget("WP_BANNERIZE_WIDGET");'));
}
?>