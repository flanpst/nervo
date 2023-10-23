<?php

/**
 * Plugin Name: Portfolio Filter Nervo Widget
 * Description: Portfolio display with filtering system for results
 * Plugin URI: https://slab.pt/
 * Author: Slab
 * Version: 1.0.0
 * Elementor tested up to: 3.15.0
 * Author URI: https://slab.pt/
 *
 * Text Domain: portfolio-elementor-filter-widgets
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
function register_portfolio_filter_widget( $widgets_manager ) {

    require_once( __DIR__ . '/widgets/portfolio-filter-widget.php' );

    $widgets_manager->register( new \Elementor_Custom_Portfolio_Filter_Widget() );

}
add_action( 'elementor/widgets/register', 'register_portfolio_filter_widget' );

function register_widget_filter_styles() {
    wp_register_style( 'style-portfolio-filter-nervo', plugin_dir_url( __FILE__ ) . 'assets/css/style.css');
}

add_action( 'elementor/frontend/before_enqueue_styles', 'register_widget_filter_styles' );
add_action( 'elementor/frontend/after_enqueue_styles', 'register_widget_filter_styles' );


function load_portfolio_posts() {
    $client = isset($_POST['client']) ? sanitize_text_field($_POST['client']) : 'All';
    $year = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : 'All';
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'All';

    $query_args = [
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'AND',
            ['key' => 'client', 'value' => $client, 'compare' => ($client === 'All' ? '!=' : '=')],
            ['key' => 'year', 'value' => $year, 'compare' => ($year === 'All' ? '!=' : '=')],
            ['key' => 'type', 'value' => $type, 'compare' => ($type === 'All' ? '!=' : '=')],
        ],
    ];

    $portfolio_query = new WP_Query($query_args);

    $output = [];

    if ($portfolio_query->have_posts()) {
        while ($portfolio_query->have_posts()) {
            $portfolio_query->the_post();

            $tags_classes = array_map(function($tag) {
                return 'elementor-filter-' . $tag->term_id;
            }, get_the_tags());

            $classes = [
                'elementor-portfolio-item',
                'elementor-post',
                implode(' ', $tags_classes),
            ];

            $output[] = [
                'post_title' => get_the_title(),
                'post_content' => get_the_content(),
                'post_classes' => $classes,
                'post_permalink' => get_permalink(),
            ];
        }
        wp_reset_postdata();
    }

    echo json_encode($output);

    wp_die();
}


add_action('wp_ajax_load_portfolio_posts', 'load_portfolio_posts');
add_action('wp_ajax_nopriv_load_portfolio_posts', 'load_portfolio_posts'); 

function enqueue_custom_script() {
    wp_enqueue_script('custom-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', ['jquery'], null, true);
    wp_localize_script('custom-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'enqueue_custom_script');


// Adiciona um hook para filtrar o widget de portfólio do Elementor
add_action( 'elementor/query/portfolio', function( $query ) {
    // Verifique se a consulta Elementor está ativa
    if ( function_exists( 'elementor' ) ) {
        // Adicione seus filtros à consulta
        // Por exemplo, se você quer filtrar por um campo personalizado chamado 'custom_field_name'
        $filters = $_POST['filters']; // Certifique-se de validar e limpar os dados

        if ( isset( $filters['client'] ) && ! empty( $filters['custom_field_name'] ) ) {
            $query->set( 'meta_key', 'client' );
            $query->set( 'meta_value', $filters['Moche'] );
        }
    }

    return $query;
} );