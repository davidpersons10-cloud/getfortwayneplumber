<?php
/**
 * Calculator lead capture, table, and CSV export.
 *
 * @package ModularConstructionCalculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MCC_Leads {
	const TABLE_SUFFIX = 'mcc_leads';

	public static function register() {
		add_action( 'wp_ajax_mcc_submit_lead', array( __CLASS__, 'mcc_ajax_submit' ) );
		add_action( 'wp_ajax_nopriv_mcc_submit_lead', array( __CLASS__, 'mcc_ajax_submit' ) );
		add_action( 'wp_ajax_mcc_send_rfp', array( __CLASS__, 'mcc_ajax_send_rfp' ) );
		add_action( 'wp_ajax_nopriv_mcc_send_rfp', array( __CLASS__, 'mcc_ajax_send_rfp' ) );
		add_action( 'admin_menu', array( __CLASS__, 'mcc_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'mcc_maybe_download_csv' ) );
	}

	public static function mcc_table_name() {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_SUFFIX;
	}

	public static function mcc_install_table() {
		global $wpdb;
		$table   = self::mcc_table_name();
		$charset = $wpdb->get_charset_collate();
		$sql     = "CREATE TABLE $table (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			created_at datetime NOT NULL,
			project_name varchar(191) NOT NULL DEFAULT '',
			email varchar(191) NOT NULL DEFAULT '',
			phone varchar(64) NOT NULL DEFAULT '',
			consent tinyint(1) NOT NULL DEFAULT 1,
			address text NULL,
			city varchar(191) NOT NULL DEFAULT '',
			state varchar(8) NOT NULL DEFAULT '',
			inputs longtext NULL,
			modular_point decimal(18,2) NOT NULL DEFAULT 0,
			modular_low decimal(18,2) NOT NULL DEFAULT 0,
			modular_high decimal(18,2) NOT NULL DEFAULT 0,
			stick_point decimal(18,2) NOT NULL DEFAULT 0,
			stick_low decimal(18,2) NOT NULL DEFAULT 0,
			stick_high decimal(18,2) NOT NULL DEFAULT 0,
			rates_version varchar(32) NOT NULL DEFAULT '',
			page_url text NULL,
			PRIMARY KEY  (id),
			KEY created_at (created_at)
		) $charset;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	public static function mcc_ajax_submit() {
		check_ajax_referer( 'mcc_submit_lead', 'nonce' );
		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
		$name  = isset( $_POST['projectName'] ) ? sanitize_text_field( wp_unslash( $_POST['projectName'] ) ) : '';
		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => 'Valid email is required.' ), 400 );
		}
		$digits = preg_replace( '/\D+/', '', $phone );
		if ( strlen( $digits ) < 8 || strlen( $digits ) > 15 ) {
			wp_send_json_error( array( 'message' => 'Valid phone is required.' ), 400 );
		}
		if ( '' === $name ) {
			wp_send_json_error( array( 'message' => 'Project name is required.' ), 400 );
		}
		$consent = ! empty( $_POST['consent'] );
		if ( ! $consent ) {
			wp_send_json_error( array( 'message' => 'Contact authorization is required.' ), 400 );
		}

		$inputs_raw = isset( $_POST['inputs'] ) ? wp_unslash( $_POST['inputs'] ) : '';
		$inputs     = json_decode( $inputs_raw, true );
		if ( ! is_array( $inputs ) ) {
			$inputs = array();
		}
		$allowed_keys = array(
			'projectName', 'address', 'city', 'state', 'area', 'unit', 'projectType', 'finishQuality',
			'floors', 'locale', 'siteTerrain', 'structure', 'prefabSystem', 'complexity', 'roofType',
			'exteriorFinish', 'hvac', 'siteAccess', 'haulMiles', 'haulUnit', 'moduleCountOverride',
			'contingencyPct', 'sprinklerOverride', 'factoryRegion', 'setQuarter', 'sustainabilityPackage',
			'sprinklerDensity', 'standingSeamPremium',
		);
		$clean_inputs = array();
		foreach ( $allowed_keys as $key ) {
			if ( isset( $inputs[ $key ] ) ) {
				$clean_inputs[ $key ] = $inputs[ $key ];
			}
		}

		$row = array(
			'created_at'     => current_time( 'mysql', true ),
			'project_name'   => $name,
			'email'          => $email,
			'phone'          => $phone,
			'consent'        => 1,
			'address'        => isset( $_POST['address'] ) ? sanitize_text_field( wp_unslash( $_POST['address'] ) ) : '',
			'city'           => isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '',
			'state'          => isset( $_POST['state'] ) ? sanitize_text_field( wp_unslash( $_POST['state'] ) ) : '',
			'inputs'         => wp_json_encode( $clean_inputs ),
			'modular_point'  => self::mcc_money( $_POST['modularPoint'] ?? 0 ),
			'modular_low'    => self::mcc_money( $_POST['modularLow'] ?? 0 ),
			'modular_high'   => self::mcc_money( $_POST['modularHigh'] ?? 0 ),
			'stick_point'    => self::mcc_money( $_POST['stickPoint'] ?? 0 ),
			'stick_low'      => self::mcc_money( $_POST['stickLow'] ?? 0 ),
			'stick_high'     => self::mcc_money( $_POST['stickHigh'] ?? 0 ),
			'rates_version'  => isset( $_POST['ratesVersion'] ) ? sanitize_text_field( wp_unslash( $_POST['ratesVersion'] ) ) : '',
			'page_url'       => isset( $_POST['pageUrl'] ) ? esc_url_raw( wp_unslash( $_POST['pageUrl'] ) ) : '',
		);

		global $wpdb;
		$wpdb->insert( self::mcc_table_name(), $row );
		wp_send_json_success( array( 'id' => (int) $wpdb->insert_id ) );
	}


	/**
	 * Send calculator scenario + summary report to RFP inbox (server-side mail).
	 */
	public static function mcc_ajax_send_rfp() {
		check_ajax_referer( 'mcc_send_rfp', 'nonce' );

		$to = 'rfp@offsitenetworkglobal.com';

		$inputs_raw = isset( $_POST['inputs'] ) ? wp_unslash( $_POST['inputs'] ) : '';
		$inputs     = json_decode( $inputs_raw, true );
		if ( ! is_array( $inputs ) ) {
			$inputs = array();
		}
		$report_raw = isset( $_POST['report'] ) ? wp_unslash( $_POST['report'] ) : '';
		$report     = json_decode( $report_raw, true );
		if ( ! is_array( $report ) ) {
			$report = array();
		}
		$page_url  = isset( $_POST['pageUrl'] ) ? esc_url_raw( wp_unslash( $_POST['pageUrl'] ) ) : '';
		$share_url = isset( $_POST['shareUrl'] ) ? esc_url_raw( wp_unslash( $_POST['shareUrl'] ) ) : '';

		$project = isset( $inputs['projectName'] ) ? sanitize_text_field( (string) $inputs['projectName'] ) : '';
		if ( '' === $project ) {
			$project = 'Untitled calculator scenario';
		}

		$subject = sprintf( '[ONG Calculator RFP] %s', $project );

		$lines   = array();
		$lines[] = 'New Send for RFP request from the Modular Calculator.';
		$lines[] = '';
		$lines[] = 'Project: ' . $project;
		$lines[] = 'Page: ' . $page_url;
		$lines[] = 'Scenario link: ' . $share_url;
		$lines[] = 'Submitted (UTC): ' . gmdate( 'c' );
		$lines[] = '';
		$lines[] = '=== Report summary ===';
		foreach ( array(
			'modularPoint' => 'Modular point',
			'modularLow'   => 'Modular low',
			'modularHigh'  => 'Modular high',
			'stickPoint'   => 'Stick point',
			'stickLow'     => 'Stick low',
			'stickHigh'    => 'Stick high',
			'ratesVersion' => 'Rates version',
			'moduleCount'  => 'Module/panel count',
			'areaLabel'    => 'Area',
			'prefabSystem' => 'Prefab system',
			'city'         => 'City',
			'state'        => 'State',
		) as $key => $label ) {
			if ( isset( $report[ $key ] ) && '' !== (string) $report[ $key ] ) {
				$lines[] = $label . ': ' . sanitize_text_field( (string) $report[ $key ] );
			}
		}
		$lines[] = '';
		$lines[] = '=== Project inputs (JSON) ===';
		$lines[] = wp_json_encode( $inputs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		$lines[] = '';
		$lines[] = '=== Full report payload (JSON) ===';
		$lines[] = wp_json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

		$body    = implode( "\n", $lines );
		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'From: Offsite Network Global <connect@offsitenetworkglobal.com>',
			'Reply-To: connect@offsitenetworkglobal.com',
		);

		$sent = wp_mail( $to, $subject, $body, $headers );
		if ( ! $sent ) {
			wp_send_json_error( array( 'message' => 'Unable to send RFP email. Please try again or email rfp@offsitenetworkglobal.com.' ), 500 );
		}
		wp_send_json_success( array( 'message' => 'RFP sent to rfp@offsitenetworkglobal.com.' ) );
	}

	private static function mcc_money( $value ) {
		return round( floatval( $value ), 2 );
	}

	public static function mcc_menu() {
		add_submenu_page(
			'options-general.php',
			'Calculator leads',
			'Calculator leads',
			'manage_options',
			'mcc-leads',
			array( __CLASS__, 'mcc_page' )
		);
	}

	public static function mcc_maybe_download_csv() {
		if ( ! is_admin() ) {
			return;
		}
		if ( ! isset( $_GET['page'], $_GET['mcc_download'] ) || 'mcc-leads' !== $_GET['page'] || 'csv' !== $_GET['mcc_download'] ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		check_admin_referer( 'mcc_download_leads' );
		self::mcc_stream_csv( self::mcc_all_rows() );
	}

	public static function mcc_all_rows() {
		global $wpdb;
		$table = self::mcc_table_name();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC", ARRAY_A );
	}

	public static function mcc_rows_since( $since_gmt ) {
		global $wpdb;
		$table = self::mcc_table_name();
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE created_at >= %s ORDER BY created_at DESC", $since_gmt ),
			ARRAY_A
		);
	}

	public static function mcc_count() {
		global $wpdb;
		$table = self::mcc_table_name();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
	}

	public static function mcc_csv_lines( $rows ) {
		$header = array(
			'timestamp', 'projectName', 'email', 'phone', 'consent', 'address', 'city', 'state',
			'modularPoint', 'modularLow', 'modularHigh', 'stickPoint', 'stickLow', 'stickHigh',
			'ratesVersion', 'pageUrl', 'inputs',
		);
		$lines = array( $header );
		foreach ( $rows as $row ) {
			$lines[] = array(
				$row['created_at'],
				$row['project_name'],
				$row['email'],
				$row['phone'],
				$row['consent'] ? 'true' : 'false',
				$row['address'],
				$row['city'],
				$row['state'],
				$row['modular_point'],
				$row['modular_low'],
				$row['modular_high'],
				$row['stick_point'],
				$row['stick_low'],
				$row['stick_high'],
				$row['rates_version'],
				$row['page_url'],
				$row['inputs'],
			);
		}
		return $lines;
	}

	public static function mcc_stream_csv( $rows ) {
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=mcc-leads.csv' );
		$out = fopen( 'php://output', 'w' );
		foreach ( self::mcc_csv_lines( $rows ) as $line ) {
			fputcsv( $out, $line );
		}
		fclose( $out );
		exit;
	}

	public static function mcc_page() {
		$rows  = self::mcc_all_rows();
		$count = count( $rows );
		$url   = wp_nonce_url( admin_url( 'options-general.php?page=mcc-leads&mcc_download=csv' ), 'mcc_download_leads' );
		echo '<div class="wrap"><h1>Calculator leads</h1>';
		echo '<p>' . esc_html( $count ) . ' saved print-gate submissions. No extra PII is stored beyond this form.</p>';
		echo '<p><a class="button button-primary" href="' . esc_url( $url ) . '">Download CSV</a></p>';
		echo '<table class="widefat striped"><thead><tr>';
		echo '<th>When (UTC)</th><th>Project</th><th>Email</th><th>Phone</th><th>Place</th><th>Modular point</th><th>Stick point</th><th>Rates</th>';
		echo '</tr></thead><tbody>';
		if ( ! $rows ) {
			echo '<tr><td colspan="8">No leads yet.</td></tr>';
		}
		foreach ( $rows as $row ) {
			$place = trim( $row['city'] . ' ' . $row['state'] );
			echo '<tr>';
			echo '<td>' . esc_html( $row['created_at'] ) . '</td>';
			echo '<td>' . esc_html( $row['project_name'] ) . '</td>';
			echo '<td>' . esc_html( $row['email'] ) . '</td>';
			echo '<td>' . esc_html( $row['phone'] ) . '</td>';
			echo '<td>' . esc_html( $place ) . '</td>';
			echo '<td>' . esc_html( $row['modular_point'] ) . '</td>';
			echo '<td>' . esc_html( $row['stick_point'] ) . '</td>';
			echo '<td>' . esc_html( $row['rates_version'] ) . '</td>';
			echo '</tr>';
		}
		echo '</tbody></table></div>';
	}
}
