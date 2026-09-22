<?php
/**
 * Plugin Name: ONG Careers Empty-Filter Fix 2026-09-21
 * Description: Empty-state only when zero visible #ong-c-jobs cards. Server-side blanks default empty copy; JS restores only when filters hide all cards (beta B-07).
 * Version: 1.0.2
 * Author: Offsite Network Global
 */
defined( 'ABSPATH' ) || exit;

add_action(
	'template_redirect',
	static function (): void {
		if ( is_admin() ) {
			return;
		}
		ob_start(
			static function ( string $html ): string {
				if ( false === stripos( $html, 'ong-c-empty' ) ) {
					return $html;
				}
				$html = preg_replace(
					'/(<p[^>]*\bid=["\']ong-c-empty["\'][^>]*>)(.*?)(<\/p>)/is',
					'$1$3',
					$html,
					1
				);
				return is_string( $html ) ? $html : '';
			}
		);
	},
	0
);

add_action(
	'wp_footer',
	static function (): void {
		if ( is_admin() ) {
			return;
		}
		echo '<style id="ong-careers-empty-fix-20260921-css">.ong-careers .filter-empty{display:none!important}.ong-careers .filter-empty.is-visible{display:block!important}</style>';
		echo '<script id="ong-careers-empty-fix-20260921-js">(function(){var MSG="No jobs match your filters. Try clearing keywords or location.";function syncEmpty(){var root=document.querySelector(".ong-careers");if(!root)return;var empty=root.querySelector("#ong-c-empty");if(!empty)return;var cards=root.querySelectorAll("#ong-c-jobs .jcard, #ong-c-jobs .ong-jb-card"),visible=0;for(var i=0;i<cards.length;i++){var c=cards[i];if(c.classList.contains("hides"))continue;if(c.offsetParent!==null&&getComputedStyle(c).display!=="none"&&getComputedStyle(c).visibility!=="hidden")visible++;}if(!cards.length||visible>0){empty.classList.remove("is-visible");empty.style.display="none";empty.setAttribute("hidden","hidden");empty.setAttribute("aria-hidden","true");empty.textContent="";}else{empty.classList.add("is-visible");empty.style.display="block";empty.removeAttribute("hidden");empty.setAttribute("aria-hidden","false");empty.textContent=MSG;}}document.addEventListener("DOMContentLoaded",syncEmpty);document.addEventListener("input",syncEmpty,true);document.addEventListener("change",syncEmpty,true);document.addEventListener("click",function(){setTimeout(syncEmpty,50);},true);syncEmpty();})();</script>';
	},
	60
);
