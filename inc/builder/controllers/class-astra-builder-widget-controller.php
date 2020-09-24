<?php
/**
 * Astra Builder Widget Controller.
 *
 * @package astra-builder
 */

if ( ! class_exists( 'Astra_Builder_Widget_Controller' ) ) {

	/**
	 * Class Astra_Builder_Widget_Controller.
	 */
	final class Astra_Builder_Widget_Controller {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance = null;


		/**
		 *  Initiator
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'widgets_init', array( $this, 'widget_init' ) );
			add_filter( 'customize_section_active', array( $this, 'display_sidebar' ), 99, 2 );

		}

		/**
		 * Display sidebar as section.
		 *
		 * @param bool   $active ios active.
		 * @param object $section section.
		 * @return bool
		 */
		public function display_sidebar( $active, $section ) {

			if ( ! Astra_Constants::$is_new_hfb_activated ) {
				return $active;
			}

			if ( strpos( $section->id, 'widgets-footer-widget-' ) || strpos( $section->id, 'widgets-header-widget-' ) ) {
				$active = true;
			}

			return $active;
		}

		/**
		 * Initiate Astra Widgets.
		 */
		public function widget_init() {

			if ( ! Astra_Constants::$is_new_hfb_activated ) {
				return;
			}

			// Register Footer Widgets.
			for ( $index = 1; $index <= Astra_Constants::$num_of_footer_widgets; $index++ ) {
				$this->register_sidebar( 'footer', $index );
			}

			// Register Header Widgets.
			$header_widgets_count = Astra_Constants::$num_of_header_widgets;

			if ( $header_widgets_count ) {
				for ( $index = 1; $index <= Astra_Constants::$num_of_header_widgets; $index++ ) {
					$this->register_sidebar( 'header', $index );
				}
			}
		}


		/**
		 * Register widget for the builder.
		 *
		 * @param string  $builder_type builder type.
		 * @param integer $index index of widget.
		 */
		public function register_sidebar( $builder_type = 'header', $index ) {
			register_sidebar(
				apply_filters(
					'astra_' . $builder_type . '_widget_' . $index . 'args',
					array(
						'name'          => ucfirst( $builder_type ) . ' Builder Widget ' . $index,
						'id'            => $builder_type . '-widget-' . $index,
						'description'   => esc_html__( 'Add widgets here:', 'astra' ),
						'before_widget' => '<section id="%1$s" class="widget %2$s">',
						'after_widget'  => '</section>',
						'before_title'  => '<h2 class="widget-title">',
						'after_title'   => '</h2>',
					)
				)
			);
		}
	}

	/**
	 *  Prepare if class 'Astra_Builder_Widget_Controller' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	Astra_Builder_Widget_Controller::get_instance();
}