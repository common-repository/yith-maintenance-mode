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

if ( ! class_exists( 'YITH_Maintenance' ) ) {
	/**
	 * YITH Maintenance Mode
	 *
	 * @since 1.0.0
	 */
	class YITH_Maintenance {
		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = '1.4.0';

		/**
		 * Plugin object
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $obj = null;

		/**
		 * Constructor
		 *
		 * @return YITH_Maintenance_Admin|YITH_Maintenance_Frontend
		 * @since 1.0.0
		 */
		public function __construct() {
			if ( is_admin() ) {
				$this->obj = new YITH_Maintenance_Admin( $this->version );
			} else {
				$this->obj = new YITH_Maintenance_Frontend( $this->version );
			}

			return $this->obj;
		}
	}
}
