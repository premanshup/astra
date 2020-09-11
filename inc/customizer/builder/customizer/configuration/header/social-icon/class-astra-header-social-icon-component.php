<?php
/**
 * Heading Colors for Astra theme.
 *
 * @package     astra-builder
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       x.x.x
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ASTRA_HEADER_SOCIAL_ICON_DIR', ASTRA_THEME_DIR . 'inc/customizer/builder/customizer/configuration/header/social-icon' );
define( 'ASTRA_HEADER_SOCIAL_ICON_URI', ASTRA_THEME_URI . 'inc/customizer/builder/customizer/configuration/header/social-icon' );

if ( ! class_exists( 'Astra_Header_Social_Icon_Component' ) ) {

	/**
	 * Heading Initial Setup
	 *
	 * @since x.x.x
	 */
	class Astra_Header_Social_Icon_Component {

		/**
		 * Constructor function that initializes required actions and hooks
		 */
		public function __construct() {

			require_once ASTRA_HEADER_SOCIAL_ICON_DIR . '/class-astra-header-social-icon-component-loader.php';

			// Include front end files.
			if ( ! is_admin() ) {
				require_once ASTRA_HEADER_SOCIAL_ICON_DIR . '/dynamic-css/dynamic.css.php';
			}
		}
	}

	/**
	 *  Kicking this off by creating an object.
	 */
	new Astra_Header_Social_Icon_Component();

}
