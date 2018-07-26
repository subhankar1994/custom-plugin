<?php
/**
 * @package Custom Plugin
 * @version 1.0
 */
/*
Plugin Name: Custom Plugin
Plugin URI: http://wordpress.org/plugins/custom-plugin/
Author: Subhankar Dutta
Version: 1.0
Author URI: http://ma.tt/
*/

define('PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ));
define('PLUGIN_URL', plugins_url());
function add_my_custom_menu(){
	add_menu_page( 
		'customplugin', 
		'Custom Plugin', 
		'manage_options', 
		'custom-plugin', 
		'custom_admin_view', 
		'dashicons-dashboard', 
		11 );

	add_submenu_page( 
		'custom-plugin', 
		'Add New', 
		'Add New', 
		'manage_options', 
		'custom-plugin', 
		'custom_admin_view' );

	add_submenu_page( 
		'custom-plugin', 
		'All Pages', 
		'All Pages', 
		'manage_options', 
		'all-pages', 
		'all_pages_function' );

}
add_action( 'admin_menu', 'add_my_custom_menu');

function custom_admin_view(){
	require_once PLUGIN_DIR_PATH.'/view/add-new.php';
}

function all_pages_function(){
	require_once PLUGIN_DIR_PATH.'/view/all-page.php';
}

function custom_plugin_assets(){
	wp_enqueue_style( 'cpt-style', PLUGIN_URL.'/assets/css/style.css');
	wp_enqueue_script( 'cpt-script', PLUGIN_URL.'/assets/js/script.js', array(), '', true);
	$object_data = array(
		'Name' => 'test object data',
		'Author' => 'test author');
	wp_localize_script( 'cpt-script', 'test_object_data', $object_data );


}
add_action( 'init', 'custom_plugin_assets');

function custom_plugin_table(){
	global $wpdb;

	require_once(ABSPATH.'wp-admin/includes/upgrade.php');

	if (count($wpdb->get_var('SHOW TABLES LIKE "wp_custom_plugin"')) == 0) {

		$sql = 'CREATE TABLE `wp_custom_plugin` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` varchar(150) DEFAULT NULL,
		`email` varchar(150) DEFAULT NULL,
		`phone` varchar(150) DEFAULT NULL,
		`create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1';

	dbDelta($sql);

}
}

register_activation_hook(__FILE__, 'custom_plugin_table');

function deactive_table(){
	global $wpdb;
	$wpdb->query('DROP table IF Exists wp_custom_plugin');

	$the_post_id = get_option( 'custom_page_id');
	if (!empty($the_post_id)) {
		wp_delete_post($the_post_id);
	}
}
register_deactivation_hook( __FILE__, 'deactive_table' );

function create_page(){
	$page = array();
	$page['post_title'] = 'Dynamic Test Page';
	$page['post_content'] = 'This Is The dynamic page content generate from code.';
	$page['post_status'] =  'publish';
	$page['post_slug'] =  'dynamic-test-page';
	$page['post_type'] = 'page';  

	$page_id = wp_insert_post($page);
	add_option( 'custom_page_id', $page_id);
}

register_activation_hook( __FILE__, 'create_page' );










?>
