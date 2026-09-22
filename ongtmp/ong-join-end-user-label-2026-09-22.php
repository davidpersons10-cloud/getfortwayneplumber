<?php
/**
 * Plugin Name: ONG Join End User label fix
 * Description: Ensures Join pricing shows a clear End User heading before the $250 tier (beta C-02). Server-side shortcode filter + light CSS/JS.
 * Version: 1.0.3
 * Author: ONG
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'do_shortcode_tag',
	static function ( $output, $tag ) {
		if ( 'ong_join' !== $tag || ! is_string( $output ) || '' === $output ) {
			return $output;
		}
		$output = preg_replace(
			'/(<article[^>]*data-sku=["\']member_eu["\'][^>]*>\s*)(<h3[^>]*>)(.*?)(<\/h3>)/is',
			'$1$2End User$4',
			$output,
			1
		);
		if ( preg_match( '/<article[^>]*data-sku=["\']member_eu["\'][^>]*>/i', $output, $m, PREG_OFFSET_CAPTURE ) ) {
			$chunk = substr( $output, $m[0][1], 800 );
			if ( ! preg_match( '/data-sku=["\']member_eu["\'][^>]*>\s*<h3/i', $chunk ) ) {
				$output = preg_replace(
					'/(<article[^>]*data-sku=["\']member_eu["\'][^>]*>)/i',
					'$1<h3>End User</h3>',
					$output,
					1
				);
			}
		}

		// Visible lead line for extractors / a11y
		if ( preg_match( '/(<article[^>]*data-sku=["\']member_eu["\'][^>]*>\s*<h3[^>]*>.*?<\/h3>)/is', $output, $mm ) ) {
			if ( stripos( $output, 'ong-eu-lead' ) === false ) {
				$output = preg_replace(
					'/(<article[^>]*data-sku=["\']member_eu["\'][^>]*>\s*<h3[^>]*>.*?<\/h3>)/is',
					'$1<p class="ong-eu-lead" style="margin:0 0 .35rem;color:#c9a227;font-weight:700;letter-spacing:.02em;text-transform:uppercase;font-size:.72rem">End User · $250 /yr</p>',
					$output,
					1
				);
			}
		}
		return $output;
	},
	20,
	2
);

add_action(
	'wp_head',
	static function () {
		if ( is_admin() ) {
			return;
		}
		echo '<style id="ong-join-eu-label-20260922-css">.ong-plan-card[data-sku="member_eu"] h3{display:block!important;visibility:visible!important;opacity:1!important;color:#fff!important;font-size:1.12rem!important;font-weight:700!important}</style>';
	},
	40
);

add_action(
	'wp_footer',
	static function () {
		if ( is_admin() ) {
			return;
		}
		echo '<script id="ong-join-eu-label-20260922-js">(function(){function fixMembers(){document.querySelectorAll("a.type, .type").forEach(function(card){var price=card.querySelector(".price");if(!price)return;if(!/\$\s*250/.test(price.textContent||""))return;var h=card.querySelector("h3");var body=card.querySelector(".body")||card;if(!h){h=document.createElement("h3");body.insertBefore(h,body.firstChild);}if(!/end\s*user/i.test((h.textContent||"").trim())){h.textContent="End User";}}); }if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",fixMembers);else fixMembers();})();</script>';
	},
	65
);
