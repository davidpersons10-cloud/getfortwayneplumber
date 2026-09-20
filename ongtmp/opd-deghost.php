<?php
/**
 * Deghost: one clean hero layer (single HTML widget) + header + ticker. No duplicate widgets.
 */
declare(strict_types=1);
$root = dirname(__FILE__);
require_once $root . '/wp-load.php';
if (!defined('ABSPATH')) { exit(1); }

$log = [];
$log[] = 'deghost_start ' . gmdate('c');

$PRIMARY = '#0066CC';
$PRIMARY_DARK = '#005EB8';
$WHITE = '#FFFFFF';

function opd_dg_id(): string {
	try { return substr(bin2hex(random_bytes(4)), 0, 7); }
	catch (Throwable $e) { return substr(md5(uniqid((string) mt_rand(), true)), 0, 7); }
}
function opd_dg_w(string $type, array $s): array {
	return ['id' => opd_dg_id(), 'elType' => 'widget', 'widgetType' => $type, 'settings' => $s, 'elements' => []];
}
function opd_dg_c(array $els, array $s = []): array {
	$s = array_merge(['_column_size' => 100, '_inline_size' => null], $s);
	return ['id' => opd_dg_id(), 'elType' => 'column', 'settings' => $s, 'elements' => $els, 'isInner' => false];
}
function opd_dg_s(array $cols, array $s = [], bool $inner = false): array {
	return ['id' => opd_dg_id(), 'elType' => 'section', 'settings' => $s, 'elements' => $cols, 'isInner' => $inner];
}

$home_id = (int) get_option('page_on_front');
$saved = get_option('ong_dir_elementor_pages', []);
if (is_array($saved) && !empty($saved['home'])) {
	$home_id = (int) $saved['home'];
}
if (!$home_id) {
	$p = get_page_by_path('home');
	if ($p) { $home_id = (int) $p->ID; }
}

$before = (string) get_post_meta($home_id, '_elementor_data', true);
$log[] = 'before_engage=' . substr_count($before, 'Engage with');
$log[] = 'before_browse=' . substr_count($before, 'Browse Companies');
$log[] = 'before_widget_button=' . substr_count($before, '"widgetType":"button"');
$log[] = 'before_widget_heading=' . substr_count($before, '"widgetType":"heading"');
$log[] = 'before_widget_html=' . substr_count($before, '"widgetType":"html"');
$log[] = 'before_bytes=' . strlen($before);

$hero_id = (int) get_option('opd_match_hero_attachment_id', 0);
$hero_src = ($hero_id > 0 && wp_attachment_is_image($hero_id)) ? (string) wp_get_attachment_url($hero_id) : '';
if ($hero_src === '') {
	$hero_src = home_url('/wp-content/uploads/2026/09/opd-hero-modular.jpg');
	$hero_id = 31;
}
$dir_url = home_url('/directory/');
$join_url = get_page_by_path('join') ? home_url('/join/') : home_url('/login/');

// Deghost CSS — kill shadows, strokes, ::before/::after duplicates, leftover button widgets
$css = <<<CSS
/* OPD DEGHOST 2026-09-20 — single-layer hero */
:root {
  --e-global-color-primary: {$PRIMARY};
  --e-global-color-secondary: {$PRIMARY_DARK};
  --opd-blue: {$PRIMARY};
  --opd-blue-dark: {$PRIMARY_DARK};
  --opd-primary: {$PRIMARY};
}
.elementor-widget-opd-directory-header .opd-ui,
.elementor-widget-opd-directory-header .elementor-widget-container > .opd-ui {
  min-height: 0 !important;
  height: auto !important;
  background: transparent !important;
  max-width: none !important;
  margin: 0 !important;
}
.opd-home-hero-photo {
  min-height: 78vh !important;
  position: relative;
}
.opd-home-hero-photo .elementor-background-overlay {
  z-index: 0 !important;
}
.opd-home-hero-photo .elementor-container {
  position: relative;
  z-index: 1;
}
.opd-home-hero-inner {
  max-width: 52rem;
  margin: 0 auto;
  text-align: center;
  color: #fff;
  position: relative;
  z-index: 2;
}
.opd-home-hero-inner * {
  text-shadow: none !important;
  -webkit-text-stroke: 0 !important;
  filter: none !important;
}
.opd-home-hero-inner::before,
.opd-home-hero-inner::after,
.opd-home-hero-inner h1::before,
.opd-home-hero-inner h1::after,
.opd-home-hero-inner p::before,
.opd-home-hero-inner p::after {
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
.opd-home-hero-inner .opd-home-sub {
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
  background: {$PRIMARY} !important;
  color: #fff !important;
  font-weight: 700;
  font-size: 1rem;
  padding: 0.85rem 1.4rem;
  border-radius: 8px;
  text-decoration: none !important;
  border: 2px solid {$PRIMARY};
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
/* Hide any stray Elementor heading/button/text leftovers inside hero if cache mixes */
.opd-home-hero-photo .elementor-widget-heading,
.opd-home-hero-photo .elementor-widget-button,
.opd-home-hero-photo .elementor-widget-text-editor,
.opd-home-hero-photo .elementor-widget-spacer {
  display: none !important;
}
.opd-home-hero-photo .elementor-widget-html {
  display: block !important;
}
CSS;
update_option('opd_match_custom_css', $css, false);
$log[] = 'css_set len=' . strlen($css);

// Ensure MU prints CSS
$mu = WP_CONTENT_DIR . '/mu-plugins/opd-match-brand.php';
if (!is_dir(dirname($mu))) { wp_mkdir_p(dirname($mu)); }
file_put_contents($mu, <<<'MUP'
<?php
/**
 * Plugin Name: OPD Match Brand CSS
 * Version: 1.2.0-deghost
 */
defined('ABSPATH') || exit;
add_action('wp_head', static function (): void {
	$css = (string) get_option('opd_match_custom_css', '');
	if ($css === '') return;
	echo "<style id=\"opd-match-brand\">\n{$css}\n</style>\n";
}, 99);
add_action('elementor/frontend/after_enqueue_styles', static function (): void {
	wp_add_inline_style('ong-dir-front',
		'.elementor-widget-opd-directory-header .opd-ui{min-height:0!important;height:auto!important;background:transparent!important;}'
		. '.opd-home-hero-photo .elementor-widget-heading,.opd-home-hero-photo .elementor-widget-button,.opd-home-hero-photo .elementor-widget-text-editor{display:none!important;}'
	);
}, 20);
MUP);
$log[] = 'mu_v1.2_written';

// Append front.css guard once
$front = WP_CONTENT_DIR . '/plugins/ong-directory/assets/css/front.css';
if (is_readable($front) && is_writable($front)) {
	$raw = file_get_contents($front);
	$marker = '/* OPD-DEGHOST-20260920 */';
	if (strpos($raw, $marker) === false) {
		$raw .= "\n{$marker}\n"
			. ".elementor-widget-opd-directory-header .opd-ui{min-height:0!important;height:auto!important;background:transparent!important;}\n"
			. ".opd-home-hero-photo .elementor-widget-heading,.opd-home-hero-photo .elementor-widget-button,.opd-home-hero-photo .elementor-widget-text-editor,.opd-home-hero-photo .elementor-widget-spacer{display:none!important;}\n"
			. ".opd-home-hero-inner h1,.opd-home-stat-num{text-shadow:none!important;-webkit-text-stroke:0!important;}\n";
		file_put_contents($front, $raw);
		$log[] = 'front_css_deghost_appended';
	} else {
		$log[] = 'front_css_deghost_present';
	}
}

$hero_html = '<div class="opd-home-hero-inner">'
	. '<h1>Engage with the Global Prefab and Offsite Construction Hub</h1>'
	. '<p class="opd-home-sub">Discover developers, architects, suppliers, and industry professionals revolutionizing construction through modular and offsite methods.</p>'
	. '<div class="opd-home-ctas">'
	. '<a class="opd-home-cta-primary" href="' . esc_url($join_url) . '">Join Directory →</a>'
	. '<a class="opd-home-cta-ghost" href="' . esc_url($dir_url) . '">Browse Companies</a>'
	. '</div>'
	. '<div class="opd-home-stats">'
	. '<div class="opd-home-stat"><span class="opd-home-stat-num">500+</span><span class="opd-home-stat-label">Companies</span></div>'
	. '<div class="opd-home-stat"><span class="opd-home-stat-num">50+</span><span class="opd-home-stat-label">Countries</span></div>'
	. '<div class="opd-home-stat"><span class="opd-home-stat-num">15</span><span class="opd-home-stat-label">Categories</span></div>'
	. '</div>'
	. '</div>';

$header = opd_dg_s([
	opd_dg_c([opd_dg_w('opd-directory-header', [])]),
], [
	'background_background' => 'classic',
	'background_color' => $WHITE,
	'padding' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true],
]);

$hero = opd_dg_s([
	opd_dg_c([
		opd_dg_w('html', ['html' => $hero_html]),
	], [
		'align' => 'center',
		'padding' => ['unit' => 'px', 'top' => '24', 'right' => '16', 'bottom' => '16', 'left' => '16', 'isLinked' => false],
	]),
], [
	'layout' => 'full_width',
	'gap' => 'no',
	'height' => 'min-height',
	'custom_height' => ['unit' => 'vh', 'size' => 78, 'sizes' => []],
	'background_background' => 'classic',
	'background_color' => $PRIMARY,
	'background_image' => [
		'url' => $hero_src,
		'id' => $hero_id,
		'alt' => 'Modular construction yard',
		'source' => 'library',
	],
	'background_position' => 'center center',
	'background_repeat' => 'no-repeat',
	'background_size' => 'cover',
	'background_overlay_background' => 'classic',
	'background_overlay_color' => '#0a1628',
	'background_overlay_opacity' => ['unit' => 'px', 'size' => 0.55, 'sizes' => []],
	'padding' => ['unit' => 'px', 'top' => '96', 'right' => '24', 'bottom' => '72', 'left' => '24', 'isLinked' => false],
	'css_classes' => 'opd-home-hero-photo',
]);

$ticker = opd_dg_s([
	opd_dg_c([opd_dg_w('opd-directory-sponsors-ticker', [])]),
]);

$elements = [$header, $hero, $ticker];
$json = wp_json_encode($elements);

if ($home_id > 0) {
	update_post_meta($home_id, '_elementor_edit_mode', 'builder');
	update_post_meta($home_id, '_elementor_template_type', 'wp-page');
	update_post_meta($home_id, '_elementor_version', '3.16.0');
	update_post_meta($home_id, '_elementor_data', wp_slash($json));
	update_post_meta($home_id, '_wp_page_template', 'elementor_canvas');
	delete_post_meta($home_id, '_elementor_css');
	delete_post_meta($home_id, '_elementor_page_assets');
	delete_post_meta($home_id, '_elementor_controls_usage');
	delete_post_meta($home_id, '_elementor_element_cache');
	// Clear post_content so theme never echoes leftover blocks
	wp_update_post([
		'ID' => $home_id,
		'post_status' => 'publish',
		'post_title' => 'Home',
		'post_content' => '',
	]);
	update_option('show_on_front', 'page');
	update_option('page_on_front', $home_id);
	$log[] = 'home_rebuilt_single_html id=' . $home_id . ' bytes=' . strlen($json);
} else {
	$log[] = 'home_missing';
}

$after = (string) get_post_meta($home_id, '_elementor_data', true);
$log[] = 'after_engage=' . substr_count($after, 'Engage with');
$log[] = 'after_browse=' . substr_count($after, 'Browse Companies');
$log[] = 'after_widget_button=' . substr_count($after, '"widgetType":"button"');
$log[] = 'after_widget_heading=' . substr_count($after, '"widgetType":"heading"');
$log[] = 'after_widget_html=' . substr_count($after, '"widgetType":"html"');

if (class_exists('\\Elementor\\Plugin')) {
	$p = \Elementor\Plugin::$instance;
	if (isset($p->files_manager) && method_exists($p->files_manager, 'clear_cache')) {
		$p->files_manager->clear_cache();
		$log[] = 'elementor_cache_cleared';
	}
}
if (function_exists('wp_cache_flush')) { wp_cache_flush(); $log[] = 'wp_flushed'; }
do_action('litespeed_purge_all');

// Soft-disable element cache experiment for this site if set
update_option('elementor_element_cache_ttl', 'disable', false);
$log[] = 'element_cache_ttl_disable';

update_option('opd_deghost_log', ['at' => gmdate('c'), 'log' => $log, 'home_id' => $home_id], false);
$out = ['ok' => true, 'deghost' => true, 'log' => $log, 'home_id' => $home_id, 'hero_src' => $hero_src];
file_put_contents($root . '/opd-deghost-result.json', wp_json_encode($out, JSON_PRETTY_PRINT));
header('Content-Type: application/json');
echo wp_json_encode($out, JSON_PRETTY_PRINT);
if (getenv('OPD_DEGHOST_SELF_DELETE') === '1') { @unlink(__FILE__); }
