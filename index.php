<?php
/*
    Plugin Name: Instamojo
    Plugin URI: http://www.instamojo.com
    Description: Embed your Instamojo items directly into your wordpress site.
    Version: 0.0.1
    Author: instamojo.com
    Author URI: http://www.instamojo.com
*/
    if(!defined('ABSPATH'))
    	exit;

    define('INSTAMOJO_VERSION', '0.0.1');
    define('INSTAMOJO_DIR', plugin_dir_path(__FILE__));
    define('INSTAMOJO_URL', plugin_dir_url(__FILE__));
    define('INSTAMOJO_NAME', 'INSTAMOJO');

    register_activation_hook(__FILE__, 'instamojo_activation_hook');
    add_action('plugins_loaded', 'instamojo_plugin_loaded');

    function instamojo_activation_hook(){
    	add_option('instamojo_version', INSTAMOJO_VERSION);
    }

    function instamojo_plugin_loaded(){
    	add_action('widgets_init', 'instamojo_widgets_init');	
    }

    function instamojo_widgets_init(){
    	require_once(INSTAMOJO_DIR . 'widget.php');
    	register_widget('instamojo_widget');
    }
?>

