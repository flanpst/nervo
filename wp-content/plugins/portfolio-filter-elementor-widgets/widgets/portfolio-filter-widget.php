<?php

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use ElementorPro\Base\Base_Widget;
use ElementorPro\Modules\QueryControl\Module as Module_Query;
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Related;
use Elementor\Controls_Manager;


if (!defined('ABSPATH')) {
	exit;
}

/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Elementor_Custom_Portfolio_Filter_Widget extends Base_Widget
{
	private $client_filter = 'Cliente';
    private $year_filter = 'Ano';
    private $type_filter = 'Tipo de Projecto';

	public function set_client($client)
	{
		$this->client = $client;
	}

	public function set_year($year)
	{
		$this->year = $year;
	}

	public function set_type($type)
	{
		$this->type = $type;
	}

	/**
	 * @var \WP_Query
	 */
	private $_query = null;

	protected $_has_template_content = false;

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'portfolio-filter-nervo';
	}

	public function get_style_depends()
	{
		return ['style-portfolio-filter-nervo'];
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return esc_html__('Portfolio Filter Nervo', 'elementor-portfolio-filter-widget');
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'eicon-gallery-grid';
	}

	public function get_keywords()
	{
		return ['posts', 'cpt', 'item', 'loop', 'query', 'portfolio', 'custom post type'];
	}

	public function on_import($element)
	{
		if (isset($element['settings']['posts_post_type']) && !get_post_type_object($element['settings']['posts_post_type'])) {
			$element['settings']['posts_post_type'] = 'post';
		}

		return $element;
	}

	public function get_query()
	{
		return $this->_query;
	}



	function get_clients_list() {
		global $wpdb;
	
		$query = "
			SELECT DISTINCT meta_value 
			FROM $wpdb->postmeta 
			WHERE meta_key = 'client' 
			AND meta_value != '' 
			ORDER BY meta_value ASC
		";
	
		$results = $wpdb->get_col($query);
	
		array_unshift($results, 'Cliente');
	
		return $results;
	}


	function get_years_list()
	{
		global $wpdb;
	
		$query = "
			SELECT DISTINCT meta_value 
			FROM $wpdb->postmeta 
			WHERE meta_key = 'year' 
			AND meta_value != '' 
			ORDER BY meta_value ASC
		";
	
		$results = $wpdb->get_col($query);
	
		array_unshift($results, 'Ano');
	
		return $results;
	}

	function get_types_list()
	{
		global $wpdb;
	
		$query = "
			SELECT DISTINCT meta_value 
			FROM $wpdb->postmeta 
			WHERE meta_key = 'type' 
			AND meta_value != '' 
			ORDER BY meta_value ASC
		";
	
		$results = $wpdb->get_col($query);
	
		array_unshift($results, 'Tipo de Projecto');
	
		return $results;
	}

	protected function register_controls()
	{
		$this->register_query_section_controls();
	}


	private function register_query_section_controls()
	{
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__('Layout', 'elementor-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__('Columns', 'elementor-pro'),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'prefix_class' => 'elementor-grid%s-',
				'frontend_available' => true,
				'selectors' => [
					'.elementor-msie {{WRAPPER}} .elementor-portfolio-item' => 'width: calc( 100% / {{SIZE}} )',
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__('Posts Per Page', 'elementor-pro'),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail_size',
				'exclude' => ['custom'],
				'default' => 'medium',
				'prefix_class' => 'elementor-portfolio--thumbnail-size-',
			]
		);


		$this->add_control(
			'item_ratio',
			[
				'label' => esc_html__('Item Ratio', 'elementor-pro'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.66,
				],
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__thumbnail__link' => 'padding-bottom: calc( {{SIZE}} * 100% )',
					'{{WRAPPER}}:after' => 'content: "{{SIZE}}"; position: absolute; color: transparent;',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => esc_html__('Show Title', 'elementor-pro'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => esc_html__('Off', 'elementor-pro'),
				'label_on' => esc_html__('On', 'elementor-pro'),
			]
		);

		$this->add_control(
			'show_client',
			[
				'label' => esc_html__('Show Client', 'elementor-pro'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => esc_html__('Off', 'elementor-pro'),
				'label_on' => esc_html__('On', 'elementor-pro'),
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => esc_html__('Title HTML Tag', 'elementor-pro'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__('Query', 'elementor-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_group_control(
			Group_Control_Related::get_type(),
			[
				'name' => 'posts',
				'presets' => ['full'],
				'exclude' => [
					'posts_per_page', //use the one from Layout section
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'filter_bar',
			[
				'label' => esc_html__('Filter Bar', 'elementor-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_filter_bar',
			[
				'label' => esc_html__('Show', 'elementor-pro'),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__('Off', 'elementor-pro'),
				'label_on' => esc_html__('On', 'elementor-pro'),
			]
		);

		$this->add_control(
			'taxonomy',
			[
				'label' => esc_html__('Taxonomy', 'elementor-pro'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => [],
				'options' => $this->get_taxonomies(),
				'condition' => [
					'show_filter_bar' => 'yes',
					'posts_post_type!' => 'by_id',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_layout',
			[
				'label' => esc_html__('Items', 'elementor-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		/*
		 * The `item_gap` control is replaced by `column_gap` and `row_gap` controls since v 2.1.6
		 * It is left (hidden) in the widget, to provide compatibility with older installs
		 */

		$this->add_control(
			'item_gap',
			[
				'label' => esc_html__('Item Gap', 'elementor-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem', 'custom'],
				'selectors' => [
					'{{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}; --grid-column-gap: {{SIZE}}{{UNIT}};',
				],
				'frontend_available' => true,
				'classes' => 'elementor-hidden',
			]
		);

		$this->add_control(
			'column_gap',
			[
				'label' => esc_html__('Columns Gap', 'elementor-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem', 'custom'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => ' --grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'row_gap',
			[
				'label' => esc_html__('Rows Gap', 'elementor-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem', 'custom'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__('Border Radius', 'elementor-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em', 'rem', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .elementor-portfolio-item__img, {{WRAPPER}} .elementor-portfolio-item__overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_overlay',
			[
				'label' => esc_html__('Item Overlay', 'elementor-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color_background',
			[
				'label' => esc_html__('Background Color', 'elementor-pro'),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} a .elementor-portfolio-item__overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'color_title',
			[
				'label' => esc_html__('Color', 'elementor-pro'),
				'separator' => 'before',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a .elementor-portfolio-item__title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'color_client',
			[
				'label' => esc_html__('Color Client', 'elementor-pro'),
				'separator' => 'before',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a .elementor-portfolio-item__client' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_client' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-portfolio-item__title',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => esc_html__('Typography Client', 'elementor-pro'),
				'name' => 'typography_client',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-portfolio-item__client',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->end_controls_section();



		$this->start_controls_section(
			'section_design_filter',
			[
				'label' => esc_html__('Filter Bar', 'elementor-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_filter_bar' => 'yes',
				],
			]
		);

		$this->add_control(
			'color_filter',
			[
				'label' => esc_html__('Color', 'elementor-pro'),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-portfolio__filter' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'color_filter_active',
			[
				'label' => esc_html__('Active Color', 'elementor-pro'),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-portfolio__filter.elementor-active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_filter',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-portfolio__filter',
			]
		);

		$this->add_control(
			'filter_item_spacing',
			[
				'label' => esc_html__('Space Between', 'elementor-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem', 'custom'],
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-portfolio__filter:not(:last-child)' => 'margin-right: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-portfolio__filter:not(:first-child)' => 'margin-left: calc({{SIZE}}{{UNIT}}/2)',
				],
			]
		);

		$this->add_control(
			'filter_spacing',
			[
				'label' => esc_html__('Spacing', 'elementor-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem', 'custom'],
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-portfolio__filters' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function get_taxonomies()
	{
		$taxonomies = get_taxonomies(['show_in_nav_menus' => true], 'objects');

		$options = ['' => ''];

		foreach ($taxonomies as $taxonomy) {
			$options[$taxonomy->name] = $taxonomy->label;
		}

		return $options;
	}

	protected function get_posts_tags()
	{
		$taxonomy = $this->get_settings('taxonomy');

		foreach ($this->_query->posts as $post) {
			if (!$taxonomy) {
				$post->tags = [];

				continue;
			}

			$tags = wp_get_post_terms($post->ID, $taxonomy);

			$tags_slugs = [];

			foreach ($tags as $tag) {
				$tags_slugs[$tag->term_id] = $tag;
			}

			$post->tags = $tags_slugs;
		}
	}

	public function query_posts($filters)
	{
		$meta_query_args = $this->prepare_meta_query_args($filters);

		$query_args = [
			'posts_per_page' => $this->get_settings('posts_per_page'),
			'meta_query' => $meta_query_args
		];

		/** @var Module_Query $elementor_query */
		$elementor_query = Module_Query::instance();
		$this->_query = $elementor_query->get_query($this, 'posts', $query_args, []);

	}

	public function prepare_meta_query_args($filters) {
		$meta_query_args = [];
	
		if ($filters['client'] !== 'Cliente') {
			$meta_query_args[] = [
				'key' => 'client',
				'value' => $filters['client'],
				'compare' => '='
			];
		}
	
		if ($filters['year'] !== 'Ano') {
			$meta_query_args[] = [
				'key' => 'year',
				'value' => $filters['year'],
				'compare' => '='
			];
		}
	
		if ($filters['type'] !== 'Tipo de Projecto') {
			$meta_query_args[] = [
				'key' => 'type',
				'value' => $filters['type'],
				'compare' => '='
			];
		}
	
		return $meta_query_args;
	}


	public function set_parameters($client, $year, $type)
	{
		$this->client = $client;
		$this->year = $year;
		$this->type = $type;
	}

	function result_filter($query_args) {
		$query = new WP_Query($query_args);
	
		$posts = $query->get_posts();
	
		return $posts;
	}

	// Renderização do widget
	public function render()
	{
		$filters = [
			'client' => $this->client_filter,
			'year' => $this->year_filter,
			'type' => $this->type_filter,
		];
	
		$this->query_posts($filters);
	
		$wp_query = $this->get_query();
		error_log(print_r($wp_query->request, true));
	
		if (!$wp_query->found_posts) {
			return;
		}
	
		$this->get_posts_tags();
	
		$this->render_loop_header();
	
		while ($wp_query->have_posts()) {
			$wp_query->the_post();
			$this->render_post();
		}
	
		$this->render_loop_footer();
	
		wp_reset_postdata();
	}

	public function set_filters($client, $year, $type) {
		$this->client_filter = $client;
		$this->year_filter = $year;
		$this->type_filter = $type;

		$this->render();
	}



	public function render_thumbnail()
	{
		$settings = $this->get_settings();

		$settings['thumbnail_size'] = [
			'id' => get_post_thumbnail_id(),
		];
?>
		<div class="elementor-portfolio-item__img elementor-post__thumbnail">
			<?php Group_Control_Image_Size::print_attachment_image_html($settings, 'thumbnail_size'); ?>
		</div>
	<?php
	}

	protected function render_filter_menu()
	{
		$taxonomy = $this->get_settings('taxonomy');

		if (!$taxonomy) {
			return;
		}

		$terms = [];

		foreach ($this->_query->posts as $post) {
			$terms += $post->tags;
		}

		if (empty($terms)) {
			return;
		}

		usort($terms, function ($a, $b) {
			return strcmp($a->name, $b->name);
		});

	?>
		<ul class="elementor-portfolio__filters">
			<li class="elementor-portfolio__filter elementor-active" data-filter="__all"><?php echo esc_html__('All', 'elementor-pro'); ?></li>
			<?php foreach ($terms as $term) { ?>
				<li class="elementor-portfolio__filter" data-filter="<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></li>
			<?php } ?>
		</ul>
	<?php
	}

	public function render_title()
	{
		if (!$this->get_settings('show_title')) {
			return;
		}

		$tag = $this->get_settings('title_tag');
	?>
		<<?php Utils::print_validated_html_tag($tag); ?> class="elementor-portfolio-item__title">
			<?php the_title(); ?>
		</<?php Utils::print_validated_html_tag($tag); ?>>
	<?php
	}
	

	public function render_client()
	{

		if (!$this->get_settings('show_client')) {
			return;
		}

		$post_id = get_the_ID();
		$client = get_post_meta($post_id, 'client', true);
	?>
		<?php if ($client) : ?>
			<span class="elementor-portfolio-item__client"><?php echo esc_html($client); ?></span>
		<?php endif; ?>
	<?php
	}

	protected function render_categories_names()
	{
		global $post;

		if (!$post->tags) {
			return;
		}

		$separator = '<span class="elementor-portfolio-item__tags__separator"></span>';

		$tags_array = [];

		foreach ($post->tags as $tag) {
			$tags_array[] = '<span class="elementor-portfolio-item__tags__tag">' . esc_html($tag->name) . '</span>';
		}

	?>
		<div class="elementor-portfolio-item__tags">
			<?php // PHPCS - `$separator`, `$separator` is safe. 
			?>
			<?php echo implode($separator, $tags_array); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
			?>
		</div>
	<?php
	}

	protected function render_post_header()
	{
		global $post;

		$tags_classes = array_map(function ($tag) {
			return 'elementor-filter-' . $tag->term_id;
		}, $post->tags);

		$classes = [
			'elementor-portfolio-item',
			'elementor-post',
			implode(' ', $tags_classes),
		];

		// PHPCS - `get_permalink` is safe.
	?>
		<article <?php post_class($classes); ?>>
			<a class="elementor-post__thumbnail__link" href="<?php echo get_permalink(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
																?>">
			<?php
		}

		protected function render_post_footer()
		{
			?>
			</a>
		</article>
	<?php
		}

		protected function render_overlay_header()
		{
	?>
		<div class="elementor-portfolio-item__overlay">
		<?php
		}

		protected function render_overlay_footer()
		{
		?>
		</div>
	<?php
		}

		protected function render_loop_header() {
			if ($this->get_settings('show_filter_bar')) {
				$this->render_filter_menu();
			}
			$clients = $this->get_clients_list();
			?>
			<section class="elementor-section elementor-top-section elementor-element elementor-element-8dc766a elementor-section-boxed elementor-section-height-default elementor-section-height-default">
				<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-9b1251b">
						<div class="elementor-widget-wrap elementor-element-populated">
							<div class="elementor-element elementor-element-96a8779 elementor-button-align-stretch elementor-widget elementor-widget-form">
								<div class="elementor-widget-container">
									<form class="elementor-form" method="post" name="New Form">
										<div class="elementor-form-fields-wrapper elementor-labels-above">
											<div class="elementor-field-type-select elementor-field-group elementor-column elementor-col-25">
												<div class="elementor-field elementor-select-wrapper remove-before ">
													<div class="select-caret-down-wrapper">
														<i aria-hidden="true" class="eicon-caret-down"></i>
													</div>
													<select name="form_fields[name]" id="client" class="elementor-field-textual elementor-size-sm" onchange="handleChange()">
														<?php
														foreach ($clients as $client) {
															echo "<option value='$client'>$client</option>";
														}
														?>
													</select>
												</div>
											</div>
											<div class="elementor-field-type-select elementor-field-group elementor-column elementor-col-25">
												<div class="elementor-field elementor-select-wrapper remove-before ">
													<div class="select-caret-down-wrapper">
														<i aria-hidden="true" class="eicon-caret-down"></i>
													</div>
													<select name="form_fields[type]" id="type" class="elementor-field-textual elementor-size-sm" onchange="handleChange()">
														<?php
														$types = $this->get_types_list();
														foreach ($types as $type) {
															echo "<option value='$type'>$type</option>";
														}
														?>
													</select>
												</div>
											</div>
											<div class="elementor-field-type-select elementor-field-group elementor-column elementor-col-25">
												<div class="elementor-field elementor-select-wrapper remove-before ">
													<div class="select-caret-down-wrapper">
														<i aria-hidden="true" class="eicon-caret-down"></i>
													</div>
													<select name="form_fields[year]" id="year" class="elementor-field-textual elementor-size-sm" onchange="handleChange()">
														<?php
														$years = $this->get_years_list();
														foreach ($years as $year) {
															echo "<option value='$year'>$year</option>";
														}
														?>
													</select>
												</div>
											</div>
											<div class="elementor-field-group elementor-column elementor-field-type-submit elementor-col-25 e-form__buttons">
												<button id="clearFilters" class="elementor-button elementor-size-sm">
													<span>
														<span class="elementor-button-text">Limpar</span>
													</span>
												</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<div id="portfolio-container" class="elementor-portfolio elementor-grid elementor-posts-container">
			<?php
		}		

		protected function render_loop_footer()
		{
		?>
		</div>
<?php
		}

		public function render_post()
		{
			$this->render_post_header();
			$this->render_thumbnail();
			$this->render_overlay_header();
			$this->render_title();
			$this->render_client();
			// $this->render_categories_names();
			$this->render_overlay_footer();
			$this->render_post_footer();
		}

		public function render_plain_content()
		{
		}

		public function get_group_name()
		{
			return 'posts';
		}
	}

	function render_custom_widget($widget) 
	{
		$widget_obj = $widget['widgetObj'];
		echo $widget_obj->render_content();
	}
