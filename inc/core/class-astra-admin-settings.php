<?php
/**
 * Admin settings helper
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2018, Astra
 * @link        http://wpastra.com/
 * @since       Astra 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Admin_Settings' ) ) {

	/**
	 * Astra Admin Settings
	 */
	class Astra_Admin_Settings {

		/**
		 * View all actions
		 *
		 * @since 1.0
		 * @var array $view_actions
		 */
		static public $view_actions = array();

		/**
		 * Menu page title
		 *
		 * @since 1.0
		 * @var array $menu_page_title
		 */
		static public $menu_page_title = 'Astra';

		/**
		 * Plugin slug
		 *
		 * @since 1.0
		 * @var array $plugin_slug
		 */
		static public $plugin_slug = 'astra';

		/**
		 * Default Menu position
		 *
		 * @since 1.0
		 * @var array $default_menu_position
		 */
		static public $default_menu_position = 'themes.php';

		/**
		 * Parent Page Slug
		 *
		 * @since 1.0
		 * @var array $parent_page_slug
		 */
		static public $parent_page_slug = 'general';

		/**
		 * Current Slug
		 *
		 * @since 1.0
		 * @var array $current_slug
		 */
		static public $current_slug = 'general';

		/**
		 * Constructor
		 */
		function __construct() {

			if ( ! is_admin() ) {
				return;
			}

			add_action( 'after_setup_theme', __CLASS__ . '::init_admin_settings', 99 );
		}

		/**
		 * Admin settings init
		 */
		static public function init_admin_settings() {

			self::$menu_page_title = apply_filters( 'astra_menu_page_title', __( 'Astra', 'astra' ) );

			if ( isset( $_REQUEST['page'] ) && strpos( $_REQUEST['page'], self::$plugin_slug ) !== false ) {

				add_action( 'admin_enqueue_scripts', __CLASS__ . '::styles_scripts' );

				// Let extensions hook into saving.
				do_action( 'astra_admin_settings_scripts' );

				self::save_settings();
			}

			add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_scripts' );

			add_action( 'customize_controls_enqueue_scripts', __CLASS__ . '::customizer_scripts' );

			add_action( 'admin_menu', __CLASS__ . '::add_admin_menu', 99 );

			add_action( 'astra_menu_general_action', __CLASS__ . '::general_page' );

			add_filter( 'admin_title', __CLASS__ . '::astra_admin_title', 10, 2 );

			add_action( 'astra_welcome_page_right_sidebar_content', __CLASS__ . '::astra_welcome_page_right_sidebar_content' );

			add_action( 'astra_welcome_page_content', __CLASS__ . '::astra_welcome_page_content' );
		}

		/**
		 * View actions
		 */
		static public function get_view_actions() {

			if ( empty( self::$view_actions ) ) {

				$actions            = array(
					'general' => array(
						'label' => __( 'Welcome', 'astra' ),
						'show'  => ! is_network_admin(),
					),
				);
				self::$view_actions = apply_filters( 'astra_menu_options', $actions );
			}

			return self::$view_actions;
		}

		/**
		 * Save All admin settings here
		 */
		static public function save_settings() {

			// Only admins can save settings.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Let extensions hook into saving.
			do_action( 'astra_admin_settings_save' );
		}

		/**
		 * Load the scripts and styles in the customizer controls.
		 *
		 * @since 1.2.1
		 */
		static public function customizer_scripts() {
			$color_palettes = json_encode( astra_color_palette() );
			wp_add_inline_script( 'wp-color-picker', 'jQuery.wp.wpColorPicker.prototype.options.palettes = ' . $color_palettes . ';' );
		}

		/**
		 * Enqueues the needed CSS/JS for Backend.
		 *
		 * @since 1.0
		 */
		static public function admin_scripts() {

			// Styles.
			wp_enqueue_style( 'astra-admin', ASTRA_THEME_URI . 'inc/assets/css/astra-admin.css', array(), ASTRA_THEME_VERSION );

			/* Directory and Extension */
			$file_prefix = ( SCRIPT_DEBUG ) ? '' : '.min';
			$dir_name    = ( SCRIPT_DEBUG ) ? 'unminified' : 'minified';

			$assets_js_uri = ASTRA_THEME_URI . 'assets/js/' . $dir_name . '/';

			wp_enqueue_script( 'astra-color-alpha', $assets_js_uri . 'wp-color-picker-alpha' . $file_prefix . '.js', array( 'jquery', 'customize-base', 'wp-color-picker' ), ASTRA_THEME_VERSION, true );
		}

		/**
		 * Enqueues the needed CSS/JS for the builder's admin settings page.
		 *
		 * @since 1.0
		 */
		static public function styles_scripts() {

			// Styles.
			wp_enqueue_style( 'astra-admin-settings', ASTRA_THEME_URI . 'inc/assets/css/astra-admin-menu-settings.css', array(), ASTRA_THEME_VERSION );
		}

		/**
		 * Init Nav Menu
		 *
		 * @param mixed $action Action name.
		 * @since 1.0
		 */
		static public function init_nav_menu( $action = '' ) {

			if ( '' !== $action ) {
				self::render_tab_menu( $action );
			}
		}

		/**
		 * Update Admin Title.
		 *
		 * @since 1.0.19
		 *
		 * @param string $admin_title Admin Title.
		 * @param string $title Title.
		 * @return string
		 */
		static public function astra_admin_title( $admin_title, $title ) {

			$screen = get_current_screen();
			if ( 'appearance_page_astra' == $screen->id ) {

				$view_actions = self::get_view_actions();

				$current_slug = isset( $_GET['action'] ) ? esc_attr( $_GET['action'] ) : self::$current_slug;
				$active_tab   = str_replace( '_', '-', $current_slug );

				if ( 'general' != $active_tab && isset( $view_actions[ $active_tab ]['label'] ) ) {
					$admin_title = str_replace( $title, $view_actions[ $active_tab ]['label'], $admin_title );
				}
			}

			return $admin_title;
		}

		/**
		 * Render tab menu
		 *
		 * @param mixed $action Action name.
		 * @since 1.0
		 */
		static public function render_tab_menu( $action = '' ) {
			?>
			<div id="ast-menu-page">
				<?php self::render( $action ); ?>
			</div>
			<?php
		}

		/**
		 * Prints HTML content for tabs
		 *
		 * @param mixed $action Action name.
		 * @since 1.0
		 */
		static public function render( $action ) {

			?>
			<div class="nav-tab-wrapper">
				<h1 class='ast-title'> <?php echo esc_html( self::$menu_page_title ); ?> </h1>
				<?php
				$view_actions = self::get_view_actions();

				foreach ( $view_actions as $slug => $data ) {

					if ( ! $data['show'] ) {
						continue;
					}

					$url = self::get_page_url( $slug );

					if ( $slug == self::$parent_page_slug ) {
						update_option( 'astra_parent_page_url', $url );
					}

					$active = ( $slug == $action ) ? 'nav-tab-active' : '';
					?>
						<a class='nav-tab <?php echo esc_attr( $active ); ?>' href='<?php echo esc_url( $url ); ?>'> <?php echo esc_html( $data['label'] ); ?> </a>
				<?php } ?>
			</div><!-- .nav-tab-wrapper -->

			<?php
			// Settings update message.
			if ( isset( $_REQUEST['message'] ) && ( 'saved' == $_REQUEST['message'] || 'saved_ext' == $_REQUEST['message'] ) ) {
				?>
					<span id="message" class="notice notice-success is-dismissive astra-notice"><p> <?php esc_html_e( 'Settings saved successfully.', 'astra' ); ?> </p></span>
				<?php
			}

		}

		/**
		 * Get and return page URL
		 *
		 * @param string $menu_slug Menu name.
		 * @since 1.0
		 * @return  string page url
		 */
		static public function get_page_url( $menu_slug ) {

			$parent_page = self::$default_menu_position;

			if ( strpos( $parent_page, '?' ) !== false ) {
				$query_var = '&page=' . self::$plugin_slug;
			} else {
				$query_var = '?page=' . self::$plugin_slug;
			}

			$parent_page_url = admin_url( $parent_page . $query_var );

			$url = $parent_page_url . '&action=' . $menu_slug;

			return esc_url( $url );
		}

		/**
		 * Add main menu
		 *
		 * @since 1.0
		 */
		static public function add_admin_menu() {

			$parent_page    = self::$default_menu_position;
			$page_title     = self::$menu_page_title;
			$capability     = 'manage_options';
			$page_menu_slug = self::$plugin_slug;
			$page_menu_func = __CLASS__ . '::menu_callback';

			if ( apply_filters( 'astra_dashboard_admin_menu', true ) ) {
				add_theme_page( $page_title, $page_title, $capability, $page_menu_slug, $page_menu_func );
			} else {
				do_action( 'asta_register_admin_menu', $parent_page, $page_title, $capability, $page_menu_slug, $page_menu_func );
			}
		}

		/**
		 * Menu callback
		 *
		 * @since 1.0
		 */
		static public function menu_callback() {

			$current_slug = isset( $_GET['action'] ) ? esc_attr( $_GET['action'] ) : self::$current_slug;

			$active_tab   = str_replace( '_', '-', $current_slug );
			$current_slug = str_replace( '-', '_', $current_slug );

			?>
			<div class="ast-menu-page-wrapper">
				<?php do_action( 'astra_menu_' . esc_attr( $current_slug ) . '_action' ); ?>
			</div>
			<?php
		}

		/**
		 * Include general page
		 *
		 * @since 1.0
		 */
		static public function general_page() {
			$astra_theme_name = apply_filters( 'astra_theme_name', __( 'Astra', 'astra' ) );
			$top_links        = apply_filters(
				'astra_page_top_links', array(
					array(
						'title' => 'Visit Website',
						'href'  => esc_url( 'https://wpastra.com/' ),
					),
				)
			);

			require_once ASTRA_THEME_DIR . 'inc/core/view-general.php';
		}

		/**
		 * Include Welcome page right sidebar content
		 *
		 * @since 1.2.4
		 */
		static public function astra_welcome_page_right_sidebar_content() {
			$astra_theme_name = apply_filters( 'astra_theme_name', __( 'Astra', 'astra' ) );
			?>
			<div class="postbox">
				<h2 class="hndle">
					<span>
					<?php
					printf(
						'%1$s %2$s',
						esc_html_e( 'Welcome To', 'astra-addon' ),
						esc_html( $astra_theme_name )
					);
					?>
					</span>
				</h2>
				<div class="inside">
					Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
				</div>
			</div>

			<div class="postbox">
				<h2 class="hndle"><span><?php esc_html_e( 'Getting Started', 'astra-addon' ); ?></span></h2>
				<div class="inside">
					Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
				</div>
			</div>

			<div class="postbox">
				<h2 class="hndle"><span><?php esc_html_e( 'Astra Starter Sites', 'astra-addon' ); ?></span></h2>
				<div class="inside">
					Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
				</div>
			</div>

			<div class="postbox">
				<h2 class="hndle"><span><?php esc_html_e( 'User Community', 'astra-addon' ); ?></span></h2>
				<div class="inside">
					Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
				</div>
			</div>

			<div class="postbox">
				<h2 class="hndle"><span><?php esc_html_e( 'Five Star Support', 'astra-addon' ); ?></span></h2>
				<div class="inside">
					Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
				</div>
			</div>

			<div class="postbox">
				<h2 class="hndle"><span><?php esc_html_e( 'Recommended Resources', 'astra-addon' ); ?></span></h2>
				<div class="inside">
					Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
				</div>
			</div>

			<a class="submit ast-customizer-btn button-primary button button-hero" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Go To Customizer', 'astra-addon' ); ?></a>
		<?php
		}


		/**
		 * Include Welcome page content
		 *
		 * @since 1.2.4
		 */
		static public function astra_welcome_page_content() {
			$quick_settings = apply_filters(
				'astra_quick_settings', array(
					array(
						'title' => 'Upload Logo & Favicon',
						'href'  => esc_url( admin_url( 'customize.php?autofocus[control]=custom_logo' ) ),
					),
					array(
						'title' => 'Set Colors',
						'href'  => esc_url( admin_url( 'customize.php?autofocus[panel]=panel-colors-background' ) ),
					),
					array(
						'title' => 'Choose Typography',
						'href'  => esc_url( admin_url( 'customize.php?autofocus[panel]=panel-typography' ) ),
					),
					array(
						'title' => 'Check Layout Options',
						'href'  => esc_url( admin_url( 'customize.php?autofocus[panel]=panel-layout' ) ),
					),
					array(
						'title' => 'Header Options',
						'href'  => esc_url( admin_url( 'customize.php?autofocus[section]=section-header-group' ) ),
					),
					array(
						'title' => 'BLog Layouts',
						'href'  => esc_url( admin_url( 'customize.php?autofocus[section]=section-blog-group' ) ),
					),
					array(
						'title' => 'Footer Settings',
						'href'  => esc_url( admin_url( 'customize.php?autofocus[section]=section-footer-group' ) ),
					),
				)
			);
			?>
			<div class="postbox">
				<h2 class="hndle"><span><?php esc_html_e( 'Quick Settings', 'astra-addon' ); ?></span></h2>
					<div class="ast-quick-setting-section">
						<?php
						if ( ! empty( $quick_settings ) ) :
							?>
							<div class="ast-quick-links">
								<ul>
									<?php
									foreach ( (array) $quick_settings as $key => $link ) {
										echo '<li class="ast-flex"><span class="ast-quick-setting-title">' . esc_html( $link['title'] ) . '</span><a class="ast-quick-setting-link" href="' . esc_url( $link['href'] ) . '" target="_blank" rel="noopener">' . esc_html__( 'Visit Option', 'astra-addon' ) . '</a></li>';
									}
									?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
			</div>

			<div class="postbox">
				<h2 class="hndle"><span><?php esc_html_e( 'Astra Pro Addon', 'astra-addon' ); ?></span></h2>
				<div class="inside">
					Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
				</div>
			</div>

		<?php
		}

	}

	new Astra_Admin_Settings;

}// End if().


