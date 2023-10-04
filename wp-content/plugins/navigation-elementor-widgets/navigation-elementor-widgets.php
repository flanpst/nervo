<?php
/**
 * Plugin Name: Navigation Nervo Project
 * Description: Navigation system with display of project title and client.
 * Plugin URI: https://slab.pt/
 * Author: Slab
 * Version: 1.0.0
 * Elementor tested up to: 3.15.0
 * Author URI: https://slab.pt/
 *
 * Text Domain: navigation-elementor-widgets
 */

if( ! defined( 'ABSPATH' ) ){
    exit;
}

/**
 * Register oEmbed Widget.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function register_navigation_widget( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/navigation-widget.php' );

	$widgets_manager->register( new \Elementor_Navigation_Widget() );

}
add_action( 'elementor/widgets/register', 'register_navigation_widget' );

function register_widget_navigation_styles() {
	wp_register_style( 'style-navigation-nervo', plugin_dir_url( __FILE__ ) . 'assets/css/style.css');
}

add_action( 'elementor/frontend/before_enqueue_styles', 'register_widget_navigation_styles' );
add_action( 'elementor/frontend/after_enqueue_styles', 'register_widget_navigation_styles' );

