<?php
/**
 * Plugin Name: Modular Construction Cost Calculator
 * Description: Live parametric factory-to-set modular construction feasibility calculator. Estimates the included modular stack (2026)—never labeled as total project, turnkey, or all-in cost.
 * Version: 1.1.2
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Modular Construction Cost Intelligence
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: modular-construction-calculator
 *
 * @package ModularConstructionCalculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MCC_VERSION', '1.1.2' );
define( 'MCC_PLUGIN_FILE', __FILE__ );
define( 'MCC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MCC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MCC_PLUGIN_SLUG', 'modular-construction-calculator' );

require_once MCC_PLUGIN_DIR . 'includes/class-mcc-rates.php';
require_once MCC_PLUGIN_DIR . 'includes/class-mcc-assets.php';
require_once MCC_PLUGIN_DIR . 'includes/class-mcc-shortcode.php';
require_once MCC_PLUGIN_DIR . 'includes/class-mcc-block.php';
require_once MCC_PLUGIN_DIR . 'includes/class-mcc-settings.php';
require_once MCC_PLUGIN_DIR . 'includes/class-mcc-leads.php';
require_once MCC_PLUGIN_DIR . 'includes/class-mcc-cron.php';
require_once MCC_PLUGIN_DIR . 'includes/class-mcc-plugin.php';

function mcc_activate() {
	MCC_Leads::mcc_install_table();
	MCC_Cron::mcc_ensure_scheduled();
}

function mcc_deactivate() {
	MCC_Cron::mcc_unschedule();
}

register_activation_hook( MCC_PLUGIN_FILE, 'mcc_activate' );
register_deactivation_hook( MCC_PLUGIN_FILE, 'mcc_deactivate' );

function mcc_plugin() {
	return MCC_Plugin::instance();
}

mcc_plugin();
