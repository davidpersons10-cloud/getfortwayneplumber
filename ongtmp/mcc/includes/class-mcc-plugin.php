<?php
/**
 * Plugin bootstrap.
 *
 * @package ModularConstructionCalculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MCC_Plugin {
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'mcc_init' ) );
	}

	public function mcc_init() {
		load_plugin_textdomain( 'modular-construction-calculator', false, dirname( plugin_basename( MCC_PLUGIN_FILE ) ) . '/languages' );
		MCC_Assets::register();
		MCC_Shortcode::register();
		MCC_Block::register();
		MCC_Settings::register();
		MCC_Leads::register();
		MCC_Cron::register();
	}
}
