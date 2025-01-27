<?php
/**
 * Plugin Name: YITH Maintenance Mode
 * Plugin URI: https://yithemes.com/themes/plugins/yith-maintenance-mode/
 * Description: YITH Maintenance Mode allows you to add a maintenance page and customize it.
 * Version: 1.10.1
 * Author: YITH <plugins@yithemes.com>
 * Author URI: http://yithemes.com/
 * Text Domain: yith-maintenance-mode
 * Domain Path: /languages/
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH Maintenance Mode
 * @version 1.10.1
 */

/**  Copyright 2013-2024  YITH  (email : plugins@yithemes.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/* Include common functions */
if ( ! defined( 'YITH_FUNCTIONS' ) ) {
	require_once 'yit-common/yit-functions.php';
}

load_plugin_textdomain( 'yith-maintenance-mode', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

! defined( 'YITH_MAINTENANCE' ) && define( 'YITH_MAINTENANCE', true );
define( 'YITH_MAINTENANCE_URL', plugin_dir_url( __FILE__ ) );
define( 'YITH_MAINTENANCE_DIR', plugin_dir_path( __FILE__ ) );

// Load required classes and functions.
require_once 'functions.yith-mm.php';
require_once 'yith-mm-options.php';
require_once 'class.yith-mm-admin.php';
require_once 'class.yith-mm-frontend.php';
require_once 'class.yith-mm.php';

// Let's start the game!
global $yith_maintenance;
$yith_maintenance = new YITH_Maintenance();
