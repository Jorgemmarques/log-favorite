<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              log.pt
 * @since             1.0.0
 * @package           Log_Favorite
 *
 * @wordpress-plugin
 * Plugin Name:       Log Favorite
 * Plugin URI:        log.pt
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Jorge
 * Author URI:        log.pt
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       log-favorite
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LOG_FAVORITE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-log-favorite-activator.php
 */
function activate_log_favorite() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-log-favorite-activator.php';
	Log_Favorite_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-log-favorite-deactivator.php
 */
function deactivate_log_favorite() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-log-favorite-deactivator.php';
	Log_Favorite_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_log_favorite' );
register_deactivation_hook( __FILE__, 'deactivate_log_favorite' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-log-favorite.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_log_favorite() {

	$plugin = new Log_Favorite();
	$plugin->run();

}
run_log_favorite();

// Enqueue Scripts
wp_enqueue_script( 'main-js', plugin_dir_url( __FILE__ ) . 'public/js/log-favorite-public.js' );
wp_localize_script( 'main-js', 'mainajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

// Filters and Actions
add_filter('the_content', 'add_button');

add_action('wp_ajax_add_favorite', 'add_favorite');
add_action('wp_ajax_nopriv_add_favorite', 'add_favorite');

add_action('wp_ajax_remove_favorite', 'remove_favorite');
add_action('wp_ajax_nopriv_remove_favorite', 'remove_favorite');


// Function to display button on content
function add_button($content) {

	if (!is_singular('post') || !is_user_logged_in()){
		return $content;
	}

	$favorites = get_user_meta(get_current_user_id(), 'user_favorites', false);

	$after_content = '<form id="add-favorite">';
	$after_content .= '<input type="hidden" name="user_id" value="'. get_current_user_id() .'">';
	$after_content .= '<input type="hidden" name="post_id" value="'. get_the_ID() .'">';

	if(in_array(get_the_ID(), $favorites)) {
		$after_content .= '<input type="hidden" name="action" value="remove_favorite">';
		$after_content .= '<input type="submit" value="Remover de Favoritos">';
	} else {
		$after_content .= '<input type="hidden" name="action" value="add_favorite">';
		$after_content .= '<input type="submit" value="Adicionar a Favoritos">';
	}
	
	$after_content .= '</form>';
	$content = $content . $after_content;
	

	return $content;

}


//Function to save post in user_meta
function add_favorite() {
	$user_id = $_POST['user_id'];
	$post_id = $_POST['post_id'];

	add_user_meta( $user_id, 'user_favorites', $post_id);

	print_r('Post Adicionado a Favoritos');

	die();
}


//Function to remove post from user_meta
function remove_favorite() {
	$user_id = $_POST['user_id'];
	$post_id = $_POST['post_id'];

	delete_user_meta( $user_id, 'user_favorites', $post_id);

	print_r('Post Removido de Favoritos');

	die();
}