<?php
/**
 * Plugin Name: Portfolio Nervo Widget
 * Description: Enables the inclusion of client portfolios, displaying the project name and client.
 * Plugin URI: https://slab.pt/
 * Author: Slab
 * Version: 1.0.0
 * Elementor tested up to: 3.15.0
 * Author URI: https://slab.pt/
 *
 * Text Domain: portfolio-elementor-widgets
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
function register_portfolio_widget( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/portfolio-widget.php' );

	$widgets_manager->register( new \Elementor_Custom_Portfolio_Widget() );

}
add_action( 'elementor/widgets/register', 'register_portfolio_widget' );

function register_widget_styles() {
	wp_register_style( 'style-portfolio-nervo', plugin_dir_url( __FILE__ ) . 'assets/css/style.css');
}

add_action( 'elementor/frontend/before_enqueue_styles', 'register_widget_styles' );
add_action( 'elementor/frontend/after_enqueue_styles', 'register_widget_styles' );