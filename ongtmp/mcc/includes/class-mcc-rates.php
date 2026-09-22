<?php
/**
 * Rate-book loader.
 *
 * @package ModularConstructionCalculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MCC_Rates {
	public static function mcc_rate_file() {
		return MCC_PLUGIN_DIR . 'rates/2026.08.json';
	}

	public static function mcc_load() {
		$path = self::mcc_rate_file();
		if ( ! is_readable( $path ) ) {
			return array();
		}
		$raw  = file_get_contents( $path );
		$data = json_decode( $raw, true );
		return is_array( $data ) ? $data : array();
	}

	public static function mcc_version() {
		$rates = self::mcc_load();
		return isset( $rates['version'] ) ? (string) $rates['version'] : 'unknown';
	}
}
