<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Main class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH Maintenance Mode
 * @version 1.1.2
 */

if ( ! defined( 'YITH_MAINTENANCE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Maintenance_Frontend' ) ) {
	/**
	 * YITH Custom Login Frontend
	 *
	 * @since 1.0.0
	 */
	class YITH_Maintenance_Frontend {
		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version;

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $template_file = 'maintenance.php';

		/**
		 * Constructor
		 *
		 * @param string $version Version number.
		 *
		 * @return YITH_Maintenance_Frontend
		 * @since 1.0.0
		 */
		public function __construct( $version ) {
			$this->version = $version;

			if ( ! yith_maintenance_is_enabled() ) {
				return $this;
			}

			// start frontend.
			add_action( 'template_redirect', array( $this, 'activate_maintenance' ), 99 );
			add_action( 'admin_bar_menu', array( &$this, 'admin_bar_menu' ), 1000 );
			add_action( 'wp_head', array( &$this, 'custom_style' ) );
			add_action( 'admin_head', array( &$this, 'custom_style' ) );

			return $this;
		}

		/**
		 * Admin bar menu item
		 */
		public function admin_bar_menu() {
			global $wp_admin_bar;

			/* Add the main siteadmin menu item */
			$wp_admin_bar->add_menu(
				array(
					'id'     => 'maintenance-bar',
					'href'   => current_user_can( 'administrator' ) ? admin_url( 'themes.php?page=yith-maintenance-mode' ) : '#',
					'parent' => 'top-secondary',
					'title'  => apply_filters( 'yit_maintenance_admin_bar_title', __( 'Maintenance Mode Active', 'yith-maintenance-mode' ) ),
					'meta'   => array( 'class' => 'yit_maintenance' ),
				)
			);
		}

		/**
		 * Custom css for admin bar menu item
		 */
		public function custom_style() {
			if ( ! is_user_logged_in() ) {
				return;
			} ?>
			<style type="text/css">
				#wp-admin-bar-maintenance-bar a.ab-item {
					background: rgb(197, 132, 8) !important;
					color: #fff !important
				}
			</style>
			<?php
		}

		/**
		 * Render the maintenance page
		 */
		public function activate_maintenance() {
			if ( $this->user_is_allowed() || $this->is_login_page() ) {
				return;
			}

			list ( $http_status, $background, $color, $logo, $mascotte, $message, $title_font, $p_font, $newsletter, $custom, $title, $socials ) = yit_extract( $this->vars(), 'http_status', 'background', 'color', 'logo', 'mascotte', 'message', 'title_font', 'p_font', 'newsletter', 'custom', 'title', 'socials' );

			$skin           = get_option( 'yith_maintenance_skin', 'skin1' );
			$stylesheet_url = YITH_MAINTENANCE_URL . 'assets/css/style' . ( 'skin1' !== $skin ? '-' . $skin : '' ) . '.css';

			// set HTTP status.
			http_response_code( apply_filters( 'yith_maintenance_http_status', $http_status ) );

			include plugin_dir_path( __FILE__ ) . 'templates/maintenance' . ( 'skin1' !== $skin ? '-' . $skin : '' ) . '.php';
			exit();

		}

		/**
		 * Is the user allowed to access to frontend?
		 *
		 * @return bool
		 * @since  1.0.0
		 * @access protected
		 */
		protected function user_is_allowed() {

			// Super admin.
			if ( current_user_can( 'manage_network' ) || current_user_can( 'administrator' ) ) {
				return true;
			}

			$allowed    = get_option( 'yith_maintenance_roles' );
			$user_roles = yit_user_roles();

			$is_allowed = false;
			if ( ! empty( $user_roles ) ) {
				foreach ( $user_roles as $role ) {
					if ( in_array( $role, $allowed, true ) ) {
						$is_allowed = true;
						break;
					}
				}
			}

			return $is_allowed;
		}

		/**
		 * Is it a login page?
		 *
		 * @return bool
		 * @since  1.0.0
		 * @access protected
		 */
		protected function is_login_page() {
			$login_url    = site_url( 'wp-login.php', 'login' );
			$admin_index  = admin_url( 'index.php' );
			$pages        = array( str_replace( site_url(), '', $login_url ), str_replace( site_url(), '', $admin_index ) );
			$current_page = isset( $_SERVER['PHP_SELF'] ) ? sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ) : '';
			$found        = false;

			foreach ( $pages as $page ) {
				if ( strpos( $current_page, $page ) !== false ) {
					$found = true;
					break;
				}
			}

			return $found;
		}

		/**
		 * Generate template vars
		 *
		 * @return array
		 * @since  1.0.0
		 * @access protected
		 */
		protected function vars() {
			$vars = array(
				'http_status' => get_option( 'yith_maintenance_http_status', 200 ),
				'background'  => array(
					'color'      => get_option( 'yith_maintenance_background_color' ),
					'image'      => get_option( 'yith_maintenance_background_image' ),
					'repeat'     => get_option( 'yith_maintenance_background_repeat' ),
					'position'   => get_option( 'yith_maintenance_background_position' ),
					'attachment' => get_option( 'yith_maintenance_background_attachment' ),
				),
				'color'       => array(
					'border_top' => get_option( 'yith_maintenance_border_top' ),
				),
				'logo'        => array(
					'image'        => get_option( 'yith_maintenance_logo_image' ),
					'tagline'      => get_option( 'yith_maintenance_logo_tagline' ),
					'tagline_font' => yit_typo_option_to_css( get_option( 'yith_maintenance_logo_tagline_font' ) ),
				),
				'mascotte'    => get_option( 'yith_maintenance_mascotte' ),
				'message'     => get_option( 'yith_maintenance_message' ),
				'title_font'  => yit_typo_option_to_css( get_option( 'yith_maintenance_title_font' ) ),
				'p_font'      => yit_typo_option_to_css( get_option( 'yith_maintenance_paragraph_font' ) ),
				'newsletter'  => array(
					'enabled'       => (int) get_option( 'yith_maintenance_enable_newsletter_form' ) === 1,
					'submit'        => array(
						'color' => get_option( 'yith_maintenance_newsletter_submit_background' ),
						'hover' => get_option( 'yith_maintenance_newsletter_submit_background_hover' ),
						'label' => get_option( 'yith_maintenance_newsletter_submit_label' ),
						'font'  => yit_typo_option_to_css( get_option( 'yith_maintenance_newsletter_submit_font' ) ),
					),
					'form_action'   => get_option( 'yith_maintenance_newsletter_action' ),
					'form_method'   => get_option( 'yith_maintenance_newsletter_method' ),
					'email_label'   => get_option( 'yith_maintenance_newsletter_email_label' ),
					'email_name'    => get_option( 'yith_maintenance_newsletter_email_name' ),
					'email_font'    => yit_typo_option_to_css( get_option( 'yith_maintenance_newsletter_email_font' ) ),
					'hidden_fields' => wp_parse_args( get_option( 'yith_maintenance_newsletter_hidden_fields' ) ),
				),
				'custom'      => get_option( 'yith_maintenance_custom_style' ),
				'title'       => get_option( 'yith_maintenance_newsletter_title' ),
				'socials'     => array(
					'facebook'  => get_option( 'yith_maintenance_socials_facebook' ),
					'twitter'   => get_option( 'yith_maintenance_socials_twitter' ),
					'gplus'     => get_option( 'yith_maintenance_socials_gplus' ),
					'youtube'   => get_option( 'yith_maintenance_socials_youtube' ),
					'rss'       => get_option( 'yith_maintenance_socials_rss' ),
					'behance'   => get_option( 'yith_maintenance_socials_behance' ),
					'dribble'   => get_option( 'yith_maintenance_socials_dribble' ),
					'email'     => get_option( 'yith_maintenance_socials_email' ),
					'flickr'    => get_option( 'yith_maintenance_socials_flickr' ),
					'instagram' => get_option( 'yith_maintenance_socials_instagram' ),
					'linkedin'  => get_option( 'yith_maintenance_socials_linkedin' ),
					'pinterest' => get_option( 'yith_maintenance_socials_pinterest' ),
					'skype'     => get_option( 'yith_maintenance_socials_skype' ),
					'tumblr'    => get_option( 'yith_maintenance_socials_tumblr' ),
				),
			);

			return $vars;
		}

	}
}
