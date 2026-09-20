<?php
/**
 * FINAL: keep single-HTML hero; kill visual ghost (overlay blend / motion layers / shadows); purge caches.
 */
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');
$log = [];
$home = (int) get_option('page_on_front');
$data = (string) get_post_meta($home, '_elementor_data', true);
$log[] = 'widgets_heading=' . substr_count($data, '"widgetType":"heading"');
$log[] = 'widgets_html=' . substr_count($data, '"widgetType":"html"');
$log[] = 'widgets_button=' . substr_count($data, '"widgetType":"button"');
$log[] = 'engage=' . substr_count($data, 'Engage with');
$log[] = 'browse=' . substr_count($data, 'Browse Companies');

// If somehow headings crept back, strip to single HTML again (reuse same structure as deghost).
if (substr_count($data, '"widgetType":"heading"') > 0 || substr_count($data, 'Engage with') !== 1) {
	$log[] = 'rebuild_needed';
	// Pull hero url
	$hero_id = (int) get_option('opd_match_hero_attachment_id', 31);
	$hero_src = wp_attachment_is_image($hero_id) ? (string) wp_get_attachment_url($hero_id) : home_url('/wp-content/uploads/2026/09/opd-hero-modular.jpg');
	$dir = home_url('/directory/');
	$join = get_page_by_path('join') ? home_url('/join/') : home_url('/login/');
	$eid = static function () { return substr(bin2hex(random_bytes(4)), 0, 7); };
	$w = static function ($type, $s) use ($eid) {
		return ['id'=>$eid(),'elType'=>'widget','widgetType'=>$type,'settings'=>$s,'elements'=>[]];
	};
	$c = static function ($els, $s = []) use ($eid) {
		return ['id'=>$eid(),'elType'=>'column','settings'=>array_merge(['_column_size'=>100,'_inline_size'=>null],$s),'elements'=>$els,'isInner'=>false];
	};
	$s = static function ($cols, $set = []) use ($eid) {
		return ['id'=>$eid(),'elType'=>'section','settings'=>$set,'elements'=>$cols,'isInner'=>false];
	};
	$html = '<div class="opd-home-hero-inner">'
		. '<h1>Engage with the Global Prefab and Offsite Construction Hub</h1>'
		. '<p class="opd-home-sub">Discover developers, architects, suppliers, and industry professionals revolutionizing construction through modular and offsite methods.</p>'
		. '<div class="opd-home-ctas">'
		. '<a class="opd-home-cta-primary" href="' . esc_url($join) . '">Join Directory →</a>'
		. '<a class="opd-home-cta-ghost" href="' . esc_url($dir) . '">Browse Companies</a>'
		. '</div>'
		. '<div class="opd-home-stats">'
		. '<div class="opd-home-stat"><span class="opd-home-stat-num">500+</span><span class="opd-home-stat-label">Companies</span></div>'
		. '<div class="opd-home-stat"><span class="opd-home-stat-num">50+</span><span class="opd-home-stat-label">Countries</span></div>'
		. '<div class="opd-home-stat"><span class="opd-home-stat-num">15</span><span class="opd-home-stat-label">Categories</span></div>'
		. '</div></div>';
	$els = [
		$s([$c([$w('opd-directory-header',[])])], [
			'background_background'=>'classic','background_color'=>'#FFFFFF',
			'padding'=>['unit'=>'px','top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','isLinked'=>true],
		]),
		$s([$c([$w('html',['html'=>$html])], [
			'align'=>'center',
			'padding'=>['unit'=>'px','top'=>'24','right'=>'16','bottom'=>'16','left'=>'16','isLinked'=>false],
		])], [
			'layout'=>'full_width','gap'=>'no','height'=>'min-height',
			'custom_height'=>['unit'=>'vh','size'=>78,'sizes'=>[]],
			'background_background'=>'classic','background_color'=>'#0066CC',
			'background_image'=>['url'=>$hero_src,'id'=>$hero_id,'source'=>'library'],
			'background_position'=>'center center','background_repeat'=>'no-repeat','background_size'=>'cover',
			'background_overlay_background'=>'classic','background_overlay_color'=>'#0a1628',
			'background_overlay_opacity'=>['unit'=>'px','size'=>0.55,'sizes'=>[]],
			'background_overlay_blend_mode'=>'normal',
			'padding'=>['unit'=>'px','top'=>'96','right'=>'24','bottom'=>'72','left'=>'24','isLinked'=>false],
			'css_classes'=>'opd-home-hero-photo',
			'background_motion_fx_motion_fx_scrolling'=>'',
			'motion_fx_motion_fx_scrolling'=>'',
		]),
		$s([$c([$w('opd-directory-sponsors-ticker',[])])]),
	];
	$json = wp_json_encode($els);
	update_post_meta($home, '_elementor_edit_mode', 'builder');
	update_post_meta($home, '_elementor_template_type', 'wp-page');
	update_post_meta($home, '_elementor_data', wp_slash($json));
	update_post_meta($home, '_wp_page_template', 'elementor_canvas');
	wp_update_post(['ID'=>$home,'post_content'=>'','post_status'=>'publish']);
	$log[] = 'rebuilt_bytes=' . strlen($json);
} else {
	$log[] = 'structure_ok_single_html';
	// Still force overlay blend normal on existing hero settings if present
	$decoded = json_decode($data, true);
	if (is_array($decoded)) {
		$changed = false;
		foreach ($decoded as &$sec) {
			if (!is_array($sec)) continue;
			$cls = (string) ($sec['settings']['css_classes'] ?? '');
			if (strpos($cls, 'opd-home-hero-photo') !== false) {
				$sec['settings']['background_overlay_blend_mode'] = 'normal';
				$sec['settings']['background_motion_fx_motion_fx_scrolling'] = '';
				$sec['settings']['motion_fx_motion_fx_scrolling'] = '';
				$changed = true;
			}
		}
		unset($sec);
		if ($changed) {
			update_post_meta($home, '_elementor_data', wp_slash(wp_json_encode($decoded)));
			$log[] = 'overlay_blend_forced_normal';
		}
	}
}

$css = <<<'CSS'
/* OPD FINAL ANTI-GHOST 2026-09-20 */
.elementor-widget-opd-directory-header .opd-ui {
  min-height: 0 !important;
  height: auto !important;
  background: transparent !important;
}
.opd-home-hero-photo {
  min-height: 78vh !important;
  overflow: hidden;
}
.opd-home-hero-photo > .elementor-background-overlay,
.opd-home-hero-photo .elementor-background-overlay {
  mix-blend-mode: normal !important;
  background-blend-mode: normal !important;
  filter: none !important;
  opacity: 0.55 !important;
  z-index: 0 !important;
}
.opd-home-hero-photo .elementor-motion-effects-container,
.opd-home-hero-photo .elementor-motion-effects-layer {
  display: none !important;
}
.opd-home-hero-photo .elementor-container,
.opd-home-hero-photo .elementor-widget-wrap,
.opd-home-hero-photo .elementor-widget-container {
  position: relative !important;
  z-index: 2 !important;
  isolation: isolate !important;
  filter: none !important;
  mix-blend-mode: normal !important;
}
.opd-home-hero-inner {
  max-width: 52rem;
  margin: 0 auto;
  text-align: center;
  color: #fff;
  position: relative;
  z-index: 3;
  isolation: isolate;
  transform: translateZ(0);
  -webkit-font-smoothing: antialiased;
}
.opd-home-hero-inner,
.opd-home-hero-inner * {
  text-shadow: none !important;
  -webkit-text-stroke: 0 !important;
  filter: none !important;
  mix-blend-mode: normal !important;
  opacity: 1 !important;
  background-clip: border-box !important;
  -webkit-background-clip: border-box !important;
}
.opd-home-hero-inner::before,
.opd-home-hero-inner::after,
.opd-home-hero-inner h1::before,
.opd-home-hero-inner h1::after,
.opd-home-hero-inner p::before,
.opd-home-hero-inner p::after,
.opd-home-cta-primary::before,
.opd-home-cta-ghost::before,
.opd-home-stat-num::before,
.opd-home-stat-num::after {
  content: none !important;
  display: none !important;
}
.opd-home-hero-inner h1 {
  margin: 0 auto;
  font-size: clamp(1.75rem, 3.6vw, 2.75rem);
  font-weight: 700;
  line-height: 1.2;
  color: #fff !important;
}
.opd-home-sub {
  margin: 0.85rem auto 0;
  max-width: 44rem;
  font-size: 1.05rem;
  line-height: 1.55;
  color: rgba(255,255,255,0.95) !important;
}
.opd-home-ctas {
  display: flex;
  flex-wrap: wrap;
  gap: 0.85rem;
  justify-content: center;
  margin: 1.35rem auto 0;
}
.opd-home-cta-primary {
  display: inline-flex;
  align-items: center;
  background: #0066CC !important;
  color: #fff !important;
  font-weight: 700;
  font-size: 1rem;
  padding: 0.85rem 1.4rem;
  border-radius: 8px;
  text-decoration: none !important;
  border: 2px solid #0066CC;
}
.opd-home-cta-ghost {
  display: inline-flex;
  align-items: center;
  background: transparent !important;
  color: #fff !important;
  font-weight: 600;
  font-size: 1rem;
  padding: 0.85rem 1.4rem;
  border-radius: 8px;
  text-decoration: none !important;
  border: 2px solid rgba(255,255,255,0.92);
}
.opd-home-stats {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 2.5rem;
  margin: 2.5rem auto 0;
}
.opd-home-stat { text-align: center; min-width: 6rem; }
.opd-home-stat-num {
  display: block;
  font-size: 2.35rem;
  font-weight: 700;
  color: #fff !important;
  line-height: 1.1;
}
.opd-home-stat-label {
  display: block;
  margin-top: 0.25rem;
  color: rgba(255,255,255,0.92) !important;
  font-weight: 600;
  font-size: 0.95rem;
}
.opd-home-hero-photo .elementor-widget-heading,
.opd-home-hero-photo .elementor-widget-button,
.opd-home-hero-photo .elementor-widget-text-editor,
.opd-home-hero-photo .elementor-widget-spacer {
  display: none !important;
}
.opd-home-hero-photo .elementor-widget-html { display: block !important; }
CSS;
update_option('opd_match_custom_css', $css, false);
$log[] = 'css_final_set len=' . strlen($css);

// MU refresh
$mu = WP_CONTENT_DIR . '/mu-plugins/opd-match-brand.php';
file_put_contents($mu, <<<'MUP'
<?php
/** Plugin Name: OPD Match Brand CSS */ /** Version: 1.3.0-final */
defined('ABSPATH') || exit;
add_action('wp_head', static function (): void {
	$css = (string) get_option('opd_match_custom_css', '');
	if ($css === '') return;
	echo "<style id=\"opd-match-brand\">\n{$css}\n</style>\n";
}, 99);
MUP);
$log[] = 'mu_1.3_written';

// Delete Elementor generated CSS so it regenerates clean
$css_dir = WP_CONTENT_DIR . '/uploads/elementor/css';
$deleted = 0;
if (is_dir($css_dir)) {
	foreach (glob($css_dir . '/post-' . $home . '*.css') ?: [] as $f) {
		@unlink($f);
		$deleted++;
	}
	foreach (glob($css_dir . '/post-*.css') ?: [] as $f) {
		// only home + kit small set — delete home specifically already; also clear global
		if (preg_match('/post-(?:' . $home . '|0)\.css$/', basename($f))) {
			@unlink($f);
			$deleted++;
		}
	}
}
delete_post_meta($home, '_elementor_css');
delete_post_meta($home, '_elementor_page_assets');
delete_post_meta($home, '_elementor_element_cache');
$log[] = 'elementor_css_files_deleted=' . $deleted;

if (class_exists('\\Elementor\\Plugin')) {
	$p = \Elementor\Plugin::$instance;
	if (isset($p->files_manager) && method_exists($p->files_manager, 'clear_cache')) {
		$p->files_manager->clear_cache();
		$log[] = 'elementor_cache_cleared';
	}
}
if (function_exists('wp_cache_flush')) { wp_cache_flush(); $log[] = 'wp_flushed'; }
do_action('litespeed_purge_all');
update_option('elementor_element_cache_ttl', 'disable', false);

$after = (string) get_post_meta($home, '_elementor_data', true);
$out = [
	'ok' => true,
	'final' => true,
	'log' => $log,
	'home' => $home,
	'after_heading' => substr_count($after, '"widgetType":"heading"'),
	'after_html' => substr_count($after, '"widgetType":"html"'),
	'after_engage' => substr_count($after, 'Engage with'),
	'after_browse' => substr_count($after, 'Browse Companies'),
];
file_put_contents(__DIR__ . '/opd-final-result.json', wp_json_encode($out, JSON_PRETTY_PRINT));
echo wp_json_encode($out, JSON_PRETTY_PRINT);
@unlink(__FILE__);
