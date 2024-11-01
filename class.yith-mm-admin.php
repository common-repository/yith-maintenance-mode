<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Main admin class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH Maintenance Mode
 * @version 1.1.2
 */

if ( ! defined( 'YITH_MAINTENANCE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Maintenance_Admin' ) ) {
	/**
	 * YITH Custom Login Admin
	 *
	 * @since 1.0.0
	 */
	class YITH_Maintenance_Admin {
		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version;

		/**
		 * Parameters for add_submenu_page
		 *
		 * @since  1.0.0
		 * @var array
		 * @access public
		 */
		public $submenu = array();

		/**
		 * Initial Options definition:
		 *
		 * @since  1.0.0
		 * @var array
		 * @access public
		 */
		public $options = array();

		/**
		 * Panel instance
		 *
		 * @since 1.0.0
		 * @var YITH_Panel
		 */
		public $panel;

		/**
		 * Various links
		 *
		 * @since  1.0.0
		 * @var string
		 * @access public
		 */
		public $doc_url = 'https://docs.yithemes.com/yith-maintenance-mode/';

		/**
		 * Constructor
		 *
		 * @param string $version The version number.
		 *
		 * @return YITH_Maintenance_Admin
		 * @since 1.0.0
		 */
		public function __construct( $version ) {

			$this->version = $version;
			$this->submenu = apply_filters(
				'yith_maintenance_submenu',
				array(
					'themes.php',
					__( 'YITH Maintenance Mode', 'yith-maintenance-mode' ),
					__( 'Maintenance Mode', 'yith-maintenance-mode' ),
					'administrator',
					'yith-maintenance-mode',
				)
			);

			add_action( 'init', array( $this, 'load_default_options' ) );
			add_action( 'init', array( $this, 'init_panel' ) );
			add_action( 'init', array( $this, 'default_options' ) );
			add_action( 'update_option_yith_maintenance_skin', array( $this, 'update_option' ), 10, 2 );
			add_filter( 'plugin_action_links_' . plugin_basename( dirname( __FILE__ ) . '/init.php' ), array( $this, 'action_links' ) );

			return $this;
		}

		/**
		 * Load options
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function load_default_options() {
			global $yith_maintenance_options;
			$this->options = apply_filters( 'yith_maintenance_options', $yith_maintenance_options );
		}

		/**
		 * Default options
		 *
		 * Sets up the default options used on the settings page
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function default_options() {
			global $yith_maintenance_options;
			$this->options = apply_filters( 'yith_maintenance_options', $yith_maintenance_options );
			foreach ( $this->options as $tab ) {
				foreach ( $tab['sections'] as $section ) {
					foreach ( $section['fields'] as $id => $value ) {
						if ( isset( $value['std'] ) && isset( $id ) ) {
							add_option( $id, $value['std'] );
						}
					}
				}
			}
		}

		/**
		 * Init the panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function init_panel() {
			$this->panel = new YITH_Panel(
				$this->submenu,
				$this->options,
				array(
					'url' => '',
					'img' => '',
				),
				'yith-maintenance-mode-group',
				'yith-maintenance-mode'
			);
		}

		/**
		 * Action_links function.
		 *
		 * @access public
		 *
		 * @param mixed $links The action links.
		 *
		 * @return array
		 */
		public function action_links( $links ) {

			$plugin_links = array(
				'<a href="' . admin_url( $this->submenu[0] . '?page=' . $this->submenu[4] ) . '">' . __( 'Settings', 'yith-maintenance-mode' ) . '</a>',
				'<a href="' . $this->doc_url . '">' . __( 'Docs', 'yith-maintenance-mode' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Change the skin
		 *
		 * @param string $oldvalue The old skin value.
		 * @param string $newvalue The new skin value.
		 *
		 * @access public
		 * @return void
		 */
		public function update_option( $oldvalue, $newvalue ) {

			global $yith_maintenance_options;
			if ( $oldvalue !== $newvalue ) {

				$options = include YITH_MAINTENANCE_DIR . "assets/skins/$newvalue.php";

				array_walk_recursive( $options, array( $this, 'convert_url' ) );

				foreach ( $yith_maintenance_options as $tab => $tab_options ) {
					foreach ( $tab_options['sections'] as $section => $section_options ) {
						foreach ( $section_options['fields'] as $id => $args ) {
							if ( isset( $args['in_skin'] ) && ! $args['in_skin'] ) {
								unset( $options[ $id ] );
							}
						}
					}
				}

				foreach ( $options as $key => $value ) {
					if ( 'yith_maintenance_skin' === $key ) {
						update_option( $key, $newvalue );
					} else {
						update_option( $key, $value );
					}
				}
				$referer = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
				wp_safe_redirect( $referer );
				exit();
			}
		}

		/**
		 * Convert url
		 *
		 * @param string $item The array item.
		 *
		 * @access public
		 * @return void
		 */
		public function convert_url( &$item ) {
			if ( ! isset( $this->importer_uploads_url ) ) {
				$upload_dir                 = wp_upload_dir();
				$this->importer_uploads_url = $upload_dir['baseurl'];
			}

			if ( ! isset( $this->importer_site_url ) ) {
				$this->importer_site_url = site_url();
			}

			$item = str_replace( '%uploadsurl%', $this->importer_uploads_url, $item );
			$item = str_replace( '%siteurl%', $this->importer_site_url, $item );
		}
	}
}
