<?php
/**
 * Plugin Name: ONG Join End User label fix
 * Description: Ensures Join pricing shows a clear End User heading before the $250 tier (beta C-02). Server-side shortcode filter + light CSS.
 * Version: 1.0.1
 * Author: ONG
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ensure member_eu plan card h3 is exactly "End User".
 *
 * @param string $output Shortcode HTML.
 * @param string $tag    Shortcode tag.
 * @return string
 */
add_filter(
	'do_shortcode_tag',
	static function ( $output, $tag ) {
		if ( 'ong_join' !== $tag || ! is_string( $output ) || '' === $output ) {
			return $output;
		}
		// Force End User heading on EU card regardless of catalog label quirks.
		$output = preg_replace(
			'/(<article[^>]*data-sku=["\']member_eu["\'][^>]*>\s*)(<h3[^>]*>)(.*?)(<\/h3>)/is',
			'$1$2End User$4',
			$output,
			1
		);
		// If EU card somehow lacks an h3, inject one before first child content after opening article.
		if ( preg_match( '/<article[^>]*data-sku=["\']member_eu["\'][^>]*>/i', $output, $m, PREG_OFFSET_CAPTURE ) ) {
			$start = $m[0][1];
			$chunk = substr( $output, $start, 800 );
			if ( ! preg_match( '/data-sku=["\']member_eu["\'][^>]*>\s*<h3/i', $chunk ) ) {
				$output = preg_replace(
					'/(<article[^>]*data-sku=["\']member_eu["\'][^>]*>)/i',
					'$1<h3>End User</h3>',
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

/**
 * Members hub: if End User h3 stripped from a.type card near $250, reinject via footer DOM (hub is inline HTML in plugin).
 */
add_action(
	'wp_footer',
	static function () {
		if ( is_admin() ) {
			return;
		}
		?>
<script id="ong-join-eu-label-20260922-js">
(function(){
  function fixMembers(){
    document.querySelectorAll('a.type, .type').forEach(function(card){
      var price = card.querySelector('.price');
      if (!price) return;
      if (!/\$\s*250/.test(price.textContent || '')) return;
      var h = card.querySelector('h3');
      var body = card.querySelector('.body') || card;
      if (!h) {
        h = document.createElement('h3');
        body.insertBefore(h, body.firstChild);
      }
      if (!/end\s*user/i.test((h.textContent || '').trim())) {
        h.textContent = 'End User';
      }
    });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fixMembers);
  else fixMembers();
})();
</script>
		<?php
	},
	65
);
