<?php
/**
 * Script and style registration.
 *
 * @package ModularConstructionCalculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MCC_Assets {
	const HANDLE_JS    = 'mcc-app';
	const HANDLE_CSS   = 'mcc-app';
	const HANDLE_PRINT = 'mcc-print';

	public static function register() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'mcc_maybe_enqueue' ), 20 );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'mcc_editor_assets' ) );
	}

	public static function mcc_register_front() {
		$js      = MCC_PLUGIN_DIR . 'assets/dist/mcc-app.js';
		$css     = MCC_PLUGIN_DIR . 'assets/dist/mcc-app.css';
		$js_ver  = file_exists( $js ) ? (string) filemtime( $js ) : MCC_VERSION;
		$css_ver = file_exists( $css ) ? (string) filemtime( $css ) : MCC_VERSION;

		wp_register_style(
			self::HANDLE_CSS,
			MCC_PLUGIN_URL . 'assets/dist/mcc-app.css',
			array(),
			$css_ver
		);
		wp_register_style(
			self::HANDLE_PRINT,
			MCC_PLUGIN_URL . 'assets/css/mcc-print.css',
			array( self::HANDLE_CSS ),
			MCC_VERSION,
			'print'
		);
		wp_register_script(
			self::HANDLE_JS,
			MCC_PLUGIN_URL . 'assets/dist/mcc-app.js',
			array(),
			$js_ver,
			true
		);

		$rates    = MCC_Rates::mcc_load();
		$settings = array(
			'reviewerStatus' => get_option( 'mcc_reviewer_status', isset( $rates['review']['status'] ) ? $rates['review']['status'] : 'pending-qualified-estimator-review' ),
			'reviewerName'   => get_option( 'mcc_reviewer_name', '' ),
			'planningNote'   => get_option( 'mcc_planning_note', 'Jobsite CCI values are planning allowances, not published RSMeans City Cost Index figures.' ),
			'rateVersion'    => MCC_Rates::mcc_version(),
		);
		$banner_id  = (int) get_option( 'mcc_sponsor_banner_id', 0 );
		$banner_url = get_option( 'mcc_sponsor_banner_url', '' );
		if ( $banner_id ) {
			$resolved = wp_get_attachment_url( $banner_id );
			if ( $resolved ) {
				$banner_url = $resolved;
			}
		}
		$owner = get_option( 'mcc_owner_email', '' );

		wp_localize_script(
			self::HANDLE_JS,
			'mccData',
			array(
				'rates'             => $rates,
				'settings'          => $settings,
				'ong'               => array(
					'company'      => get_option( 'mcc_ong_company', '' ),
					'address'      => get_option( 'mcc_ong_address', '' ),
					'cityStateZip' => get_option( 'mcc_ong_city_state_zip', '' ),
					'phone'        => get_option( 'mcc_ong_phone', '' ),
					'email'        => get_option( 'mcc_ong_email', '' ),
					'website'      => get_option( 'mcc_ong_website', '' ),
				),
				'sponsorBannerUrl'  => $banner_url,
				'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
				'ajaxNonce'         => wp_create_nonce( 'mcc_submit_lead' ),
				'rfpNonce'          => wp_create_nonce( 'mcc_send_rfp' ),
				'rfpEmail'          => 'rfp@offsitenetworkglobal.com',
				'ownerConfigured'   => (bool) $owner,
			)
		);
	}

	public static function mcc_enqueue() {
		static $done = false;
		if ( $done ) {
			return;
		}
		self::mcc_register_front();
		wp_enqueue_style( self::HANDLE_CSS );
		wp_enqueue_style( self::HANDLE_PRINT );
		wp_enqueue_script( self::HANDLE_JS );
		$done = true;
	}

	public static function mcc_maybe_enqueue() {
		if ( is_admin() ) {
			return;
		}
		if ( ! is_singular() ) {
			return;
		}
		$post = get_post();
		if ( ! $post ) {
			return;
		}
		$has_shortcode = has_shortcode( $post->post_content, 'modular_cost_calculator' );
		$has_block     = function_exists( 'has_block' ) && has_block( 'mcc/modular-cost-calculator', $post );
		if ( $has_shortcode || $has_block ) {
			self::mcc_enqueue();
		}
	}

	public static function mcc_editor_assets() {
		wp_enqueue_script(
			'mcc-block-editor',
			MCC_PLUGIN_URL . 'block/index.js',
			array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor' ),
			MCC_VERSION,
			true
		);
	}
}
