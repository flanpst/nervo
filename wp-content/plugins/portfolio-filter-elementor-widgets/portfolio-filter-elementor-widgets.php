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

            $tags = get_the_tags();
            $tags_classes = $tags ? array_map(function($tag) {
                return 'elementor-filter-' . $tag->term_id;
            }, $tags) : [];

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


add_action( 'elementor/query/portfolio', function( $query ) {
    if ( function_exists( 'elementor' ) ) {
        $filters = $_POST['filters']; // Certifique-se de validar e limpar os dados

        if ( isset( $filters['client'] ) && ! empty( $filters['custom_field_name'] ) ) {
            $query->set( 'meta_key', 'client' );
            $query->set( 'meta_value', $filters['Moche'] );
        }
    }

    return $query;
} );

function my_portfolio_query_filter_function() {
    $client = isset($_POST['client']) ? sanitize_text_field($_POST['client']) : '';
    $year = isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';

    $meta_query = array('relation' => 'AND');

    if (!empty($client) && $client !== 'All') {
        $meta_query[] = array(
            'key' => 'client',
            'value' => $client,
            'compare' => '='
        );
    }
    if (!empty($year) && $year !== 'All') {
        $meta_query[] = array(
            'key' => 'year',
            'value' => $year,
            'compare' => '='
        );
    }
    if (!empty($type) && $type !== 'All') {
        $meta_query[] = array(
            'key' => 'type',
            'value' => $type,
            'compare' => '='
        );
    }

    $args = array(
        'post_type' => 'post',
        'meta_query' => $meta_query
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $posts = array();
        $unique_clients = array();
        $unique_years = array();
        $unique_types = array();

        ob_start();

        while ($query->have_posts()) {
            $query->the_post();
            $posts[] = get_the_ID();

            $client = get_post_meta(get_the_ID(), 'client', true);
            $year = get_post_meta(get_the_ID(), 'year', true);
            $type = get_post_meta(get_the_ID(), 'type', true);

            if ($client && !in_array($client, $unique_clients)) {
                $unique_clients[] = $client;
            }
            if ($year && !in_array($year, $unique_years)) {
                $unique_years[] = $year;
            }
            if ($type && !in_array($type, $unique_types)) {
                $unique_types[] = $type;
            }
            ?>
            <div class="portfolio-item">
                <h3><?php the_title(); ?></h3>
                <div class="portfolio-meta">
                    <span class="client"><?php echo get_post_meta(get_the_ID(), 'client', true); ?></span>
                    <span class="year"><?php echo get_post_meta(get_the_ID(), 'year', true); ?></span>
                    <span class="type"><?php echo get_post_meta(get_the_ID(), 'type', true); ?></span>
                </div>
                <div class="portfolio-content">
                    <?php the_content(); ?>
                </div>
            </div>
            <?php
        }

        $html_output = ob_get_clean();

        wp_send_json_success(array(
            'html' => $html_output,
            'clients' => $unique_clients,
            'years' => $unique_years,
            'types' => $unique_types,
        ));
    } else {
        wp_send_json_error('Nenhum post encontrado', 404);
    }
    wp_die();
}

add_action('wp_ajax_my_portfolio_query_filter', 'my_portfolio_query_filter_function');
add_action('wp_ajax_nopriv_my_portfolio_query_filter', 'my_portfolio_query_filter_function');
