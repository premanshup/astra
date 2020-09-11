<?php
/**
 * Above Footer component.
 *
 * @package     Astra Builder
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2020, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra x.x.x
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ASTRA_BUILDER_FOOTER_ABOVE_FOOTER_DIR', ASTRA_THEME_DIR . 'inc/customizer/builder/customizer/configuration/footer/above-footer' );
define( 'ASTRA_BUILDER_FOOTER_ABOVE_FOOTER_URI', ASTRA_THEME_URI . 'inc/customizer/builder/customizer/configuration/footer/above-footer' );

if ( ! class_exists( 'Astra_Above_Footer' ) ) {

	/**
	 * Above Footer Initial Setup
	 *
	 * @since x.x.x
	 */
	class Astra_Above_Footer {

		/**
		 * Constructor function that initializes required actions and hooks
		 */
		public function __construct() {

			require_once ASTRA_BUILDER_FOOTER_ABOVE_FOOTER_DIR . '/class-astra-above-footer-component-loader.php';

			// Include front end files.
			if ( ! is_admin() ) {
				require_once ASTRA_BUILDER_FOOTER_ABOVE_FOOTER_DIR . '/dynamic-css/dynamic.css.php';
			}
		}
	}

	/**
	 *  Kicking this off by creating an object.
	 */
	new Astra_Above_Footer();

}
