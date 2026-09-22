<?php
/**
 * Plugin Name: ONG Careers Empty-Filter Fix 2026-09-21
 * Description: Empty-state must only show when zero visible #ong-c-jobs cards (.jcard / .ong-jb-card). Overrides Sep 14 theme syncEmpty.
 * Version: 1.0.0
 * Author: Offsite Network Global
 */
defined( 'ABSPATH' ) || exit;

add_action(
	'wp_footer',
	static function (): void {
		if ( is_admin() ) {
			return;
		}
		?>
<style id="ong-careers-empty-fix-20260921-css">
.ong-careers .filter-empty{display:none!important}
.ong-careers .filter-empty.is-visible{display:block!important}
</style>
<script id="ong-careers-empty-fix-20260921-js">
(function(){
  function syncEmpty(){
    var root = document.querySelector('.ong-careers');
    if (!root) return;
    var empty = root.querySelector('#ong-c-empty');
    if (!empty) return;
    var cards = root.querySelectorAll('#ong-c-jobs .jcard, #ong-c-jobs .ong-jb-card');
    var visible = 0;
    for (var i = 0; i < cards.length; i++) {
      var c = cards[i];
      if (c.classList.contains('hides')) continue;
      if (c.offsetParent !== null && getComputedStyle(c).display !== 'none' && getComputedStyle(c).visibility !== 'hidden') visible++;
    }
    if (!cards.length || visible > 0) {
      empty.classList.remove('is-visible');
      empty.style.display = 'none';
    } else {
      empty.classList.add('is-visible');
    }
  }
  document.addEventListener('DOMContentLoaded', syncEmpty);
  document.addEventListener('input', syncEmpty, true);
  document.addEventListener('change', syncEmpty, true);
  document.addEventListener('click', function(){ setTimeout(syncEmpty, 50); }, true);
  syncEmpty();
})();
</script>
		<?php
	},
	60
);
