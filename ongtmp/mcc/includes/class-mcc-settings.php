<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class MCC_Settings {
	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'mcc_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'mcc_save' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'mcc_admin_assets' ) );
	}

	public static function mcc_operator_instructions() {
		return "How to add ONG info to daily reports\n\n"
			. "1. In WordPress admin, open Settings → Modular Cost Calculator.\n"
			. "2. Enter the Owner daily-report email. Daily lead summaries go only to this address. If it is blank, the plugin skips the send.\n"
			. "3. Fill in every ONG field: company name, street address, city/state/ZIP, phone, email, and website.\n"
			. "4. Click Save settings. The ONG company name is used in the daily-report subject and header. The same ONG contact block prints on each calculator report.\n\n"
			. "How to add a sponsor banner to the printout\n\n"
			. "1. In WordPress admin, open Settings → Modular Cost Calculator.\n"
			. "2. Under Sponsor banner, click Select image and choose a file from the media library, or paste a full image URL.\n"
			. "3. Check the on-screen preview.\n"
			. "4. Click Save settings. The banner prints at the top of the report. If no banner is set, print shows a dashed placeholder: “Sponsor banner (add in plugin settings).”";
	}

	public static function mcc_menu() {
		add_options_page( 'Modular Cost Calculator', 'Modular Cost Calculator', 'manage_options', 'mcc-settings', array( __CLASS__, 'mcc_page' ) );
	}

	public static function mcc_admin_assets( $hook ) {
		if ( 'settings_page_mcc-settings' !== $hook ) {
			return;
		}
		wp_enqueue_script( 'jquery' );
		wp_enqueue_media();
		wp_add_inline_script(
			'jquery',
			"jQuery(function($){ $('.mcc-media-select').on('click', function(e){ e.preventDefault(); var frame = wp.media({ title: 'Sponsor banner', button: { text: 'Use this image' }, multiple: false }); frame.on('select', function(){ var att = frame.state().get('selection').first().toJSON(); $('#mcc_sponsor_banner_id').val(att.id); $('#mcc_sponsor_banner_url').val(att.url); $('#mcc-sponsor-preview').attr('src', att.url).show(); }); frame.open(); }); });"
		);
	}

	public static function mcc_save() {
		if ( ! is_admin() ) {
			return;
		}
		if ( ! isset( $_POST['mcc_settings_nonce'] ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mcc_settings_nonce'] ) ), 'mcc_save_settings' ) ) {
			return;
		}
		$fields = array(
			'mcc_owner_email'          => 'email',
			'mcc_ong_company'          => 'text',
			'mcc_ong_address'          => 'text',
			'mcc_ong_city_state_zip'   => 'text',
			'mcc_ong_phone'            => 'text',
			'mcc_ong_email'            => 'email',
			'mcc_ong_website'          => 'url',
			'mcc_sponsor_banner_url'   => 'url',
			'mcc_sponsor_banner_id'    => 'int',
			'mcc_reviewer_status'      => 'text',
			'mcc_reviewer_name'        => 'text',
			'mcc_planning_note'        => 'text',
		);
		foreach ( $fields as $key => $type ) {
			$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
			if ( 'email' === $type ) {
				update_option( $key, sanitize_email( $raw ) );
			} elseif ( 'url' === $type ) {
				update_option( $key, esc_url_raw( $raw ) );
			} elseif ( 'int' === $type ) {
				update_option( $key, absint( $raw ) );
			} else {
				update_option( $key, sanitize_text_field( $raw ) );
			}
		}
		add_settings_error( 'mcc_settings', 'mcc_saved', 'Settings saved.', 'updated' );
	}

	public static function mcc_field( $name, $label, $value, $type = 'text', $help = '' ) {
		echo '<tr><th scope="row"><label for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label></th><td>';
		echo '<input class="regular-text" type="' . esc_attr( $type ) . '" id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" />';
		if ( $help ) {
			echo '<p class="description">' . esc_html( $help ) . '</p>';
		}
		echo '</td></tr>';
	}

	public static function mcc_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		settings_errors( 'mcc_settings' );
		$rates     = MCC_Rates::mcc_load();
		$version   = MCC_Rates::mcc_version();
		$effective = isset( $rates['effectiveDate'] ) ? $rates['effectiveDate'] : '';
		$status    = get_option( 'mcc_reviewer_status', isset( $rates['review']['status'] ) ? $rates['review']['status'] : '' );
		echo '<div class="wrap"><h1>Modular Cost Calculator</h1>';
		echo '<p>Label: Included modular stack (2026). Rate-book ' . esc_html( $version ) . ' effective ' . esc_html( $effective ) . '. Reviewer status: ' . esc_html( $status ) . '.</p>';
		echo '<p>Planning-allowance note: jobsite CCI values are planning allowances, not published RSMeans figures. Florida is 102. Factory-region National is 1.00. Factory costs are reduced 20% after the matrix. Live shipping is 0.05% of factory cost. The product band is −10% / +10%.</p>';

		echo '<h2>Operator instructions</h2>';
		echo '<div class="mcc-operator-help" style="max-width:720px;background:#fff;border:1px solid #c3c4c7;padding:12px 16px;">';
		echo '<h3>How to add ONG info to daily reports</h3>';
		echo '<ol>';
		echo '<li>In WordPress admin, open Settings → Modular Cost Calculator.</li>';
		echo '<li>Enter the Owner daily-report email. Daily lead summaries go only to this address. If it is blank, the plugin skips the send.</li>';
		echo '<li>Fill in every ONG field: company name, street address, city/state/ZIP, phone, email, and website.</li>';
		echo '<li>Click Save settings. The ONG company name is used in the daily-report subject and header. The same ONG contact block prints on each calculator report.</li>';
		echo '</ol>';
		echo '<h3>How to add a sponsor banner to the printout</h3>';
		echo '<ol>';
		echo '<li>In WordPress admin, open Settings → Modular Cost Calculator.</li>';
		echo '<li>Under Sponsor banner, click Select image and choose a file from the media library, or paste a full image URL.</li>';
		echo '<li>Check the on-screen preview.</li>';
		echo '<li>Click Save settings. The banner prints at the top of the report. If no banner is set, print shows a dashed placeholder: “Sponsor banner (add in plugin settings).”</li>';
		echo '</ol></div>';

		echo '<form method="post">';
		wp_nonce_field( 'mcc_save_settings', 'mcc_settings_nonce' );
		echo '<h2>Owner daily report</h2>';
		echo '<table class="form-table"><tbody>';
		self::mcc_field( 'mcc_owner_email', 'Owner daily-report email', get_option( 'mcc_owner_email', '' ), 'email', 'WP-Cron hook mcc_daily_lead_summary emails this address. Leave blank to skip sending.' );
		echo '</tbody></table>';

		echo '<h2>ONG contact (print and daily report)</h2>';
		echo '<table class="form-table"><tbody>';
		self::mcc_field( 'mcc_ong_company', 'ONG company name', get_option( 'mcc_ong_company', '' ) );
		self::mcc_field( 'mcc_ong_address', 'Street address', get_option( 'mcc_ong_address', '' ) );
		self::mcc_field( 'mcc_ong_city_state_zip', 'City / state / ZIP', get_option( 'mcc_ong_city_state_zip', '' ) );
		self::mcc_field( 'mcc_ong_phone', 'Phone', get_option( 'mcc_ong_phone', '' ) );
		self::mcc_field( 'mcc_ong_email', 'Email', get_option( 'mcc_ong_email', '' ), 'email' );
		self::mcc_field( 'mcc_ong_website', 'Website', get_option( 'mcc_ong_website', '' ), 'url' );
		echo '</tbody></table>';

		$banner_url = get_option( 'mcc_sponsor_banner_url', '' );
		$banner_id  = (int) get_option( 'mcc_sponsor_banner_id', 0 );
		if ( $banner_id ) {
			$resolved = wp_get_attachment_url( $banner_id );
			if ( $resolved ) {
				$banner_url = $resolved;
			}
		}
		echo '<h2>Sponsor banner</h2>';
		echo '<table class="form-table"><tbody>';
		echo '<tr><th scope="row">Media library</th><td>';
		echo '<input type="hidden" id="mcc_sponsor_banner_id" name="mcc_sponsor_banner_id" value="' . esc_attr( (string) $banner_id ) . '" />';
		echo '<button type="button" class="button mcc-media-select">Select image</button>';
		echo '<p class="description">Or paste a URL below.</p></td></tr>';
		self::mcc_field( 'mcc_sponsor_banner_url', 'Banner URL', $banner_url, 'url' );
		echo '<tr><th scope="row">Preview</th><td>';
		if ( $banner_url ) {
			echo '<img id="mcc-sponsor-preview" src="' . esc_url( $banner_url ) . '" alt="" style="max-width:360px;height:auto;" />';
		} else {
			echo '<img id="mcc-sponsor-preview" src="" alt="" style="max-width:360px;height:auto;display:none;" />';
			echo '<p class="description">No banner set.</p>';
		}
		echo '</td></tr></tbody></table>';

		echo '<h2>Reviewer / rate notes</h2>';
		echo '<table class="form-table"><tbody>';
		self::mcc_field( 'mcc_reviewer_status', 'Reviewer status', $status );
		self::mcc_field( 'mcc_reviewer_name', 'Reviewer name', get_option( 'mcc_reviewer_name', '' ) );
		self::mcc_field( 'mcc_planning_note', 'Planning-allowance note', get_option( 'mcc_planning_note', 'Jobsite CCI values are planning allowances, not published RSMeans City Cost Index figures.' ) );
		echo '</tbody></table>';
		submit_button( 'Save settings' );
		echo '</form></div>';
	}
}
