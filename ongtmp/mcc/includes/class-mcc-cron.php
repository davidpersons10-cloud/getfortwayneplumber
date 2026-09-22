<?php
/**
 * Daily lead summary email (WP-Cron).
 *
 * @package ModularConstructionCalculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MCC_Cron {
	const HOOK = 'mcc_daily_lead_summary';

	public static function register() {
		self::mcc_ensure_scheduled();
		add_action( self::HOOK, array( __CLASS__, 'mcc_send_daily_summary' ) );
	}

	public static function mcc_ensure_scheduled() {
		if ( ! wp_next_scheduled( self::HOOK ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', self::HOOK );
		}
	}

	public static function mcc_unschedule() {
		$timestamp = wp_next_scheduled( self::HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::HOOK );
		}
		wp_clear_scheduled_hook( self::HOOK );
	}

	public static function mcc_send_daily_summary() {
		$owner = get_option( 'mcc_owner_email', '' );
		$owner = is_email( $owner ) ? $owner : '';
		if ( ! $owner ) {
			return;
		}
		$company = get_option( 'mcc_ong_company', '' );
		$since   = gmdate( 'Y-m-d H:i:s', time() - DAY_IN_SECONDS );
		$recent  = MCC_Leads::mcc_rows_since( $since );
		$all     = MCC_Leads::mcc_count();
		$new     = count( $recent );

		$subject = $company
			? sprintf( '[%s] Modular calculator daily lead summary', $company )
			: 'Modular calculator daily lead summary';

		$lines   = array();
		$lines[] = $company ? $company : 'Modular Construction Cost Calculator';
		$lines[] = '';
		$lines[] = sprintf( 'New leads in the last 24 hours: %d', $new );
		$lines[] = sprintf( 'All-time leads: %d', $all );
		$lines[] = '';
		if ( $recent ) {
			$lines[] = 'Leads in the last 24 hours:';
			foreach ( $recent as $row ) {
				$lines[] = sprintf(
					'- %s | %s | %s | modular %s | stick %s',
					$row['project_name'],
					$row['email'],
					$row['phone'],
					$row['modular_point'],
					$row['stick_point']
				);
			}
		} else {
			$lines[] = 'No new print-gate leads in the last 24 hours.';
		}
		$body = implode( "\n", $lines );

		$headers     = array( 'Content-Type: text/plain; charset=UTF-8' );
		$attachments = array();
		$tmp         = '';
		if ( $recent ) {
			$tmp  = wp_tempnam( 'mcc-leads-daily.csv' );
			$fh   = fopen( $tmp, 'w' );
			foreach ( MCC_Leads::mcc_csv_lines( $recent ) as $line ) {
				fputcsv( $fh, $line );
			}
			fclose( $fh );
			$attachments[] = $tmp;
		}
		wp_mail( $owner, $subject, $body, $headers, $attachments );
		if ( $tmp && file_exists( $tmp ) ) {
			wp_delete_file( $tmp );
		}
	}
}
