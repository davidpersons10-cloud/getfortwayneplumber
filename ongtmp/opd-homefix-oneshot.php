<?php
/**
 * One-shot: fix crocodile home blank hero + OPD nav/CTAs (2026-09-20 HOMEFIX).
 * Root cause: .opd-ui { min-height:100vh; background:#f7f8fb } wraps header widget → full-viewport blank above hero.
 */
declare(strict_types=1);

$root = dirname(__FILE__);
if (!file_exists($root . '/wp-load.php')) {
	fwrite(STDERR, "NO_WP_LOAD\n");
	exit(1);
}
require_once $root . '/wp-load.php';
if (!defined('ABSPATH')) {
	fwrite(STDERR, "NO_ABSPATH\n");
	exit(1);
}

$log = [];
$log[] = 'homefix_start ' . gmdate('c');

$PRIMARY = '#0066CC';
$PRIMARY_DARK = '#005EB8';
$TEXT = '#21242C';
$WHITE = '#FFFFFF';
$LIGHT = '#F5F7FA';

function opd_hf_eid(): string {
	try {
		return substr(bin2hex(random_bytes(4)), 0, 7);
	} catch (Throwable $e) {
		return substr(md5(uniqid((string) mt_rand(), true)), 0, 7);
	}
}

function opd_hf_widget(string $type, array $settings): array {
	return [
		'id' => opd_hf_eid(),
		'elType' => 'widget',
		'widgetType' => $type,
		'settings' => $settings,
		'elements' => [],
	];
}

function opd_hf_column(array $widgets, array $settings = []): array {
	$settings = array_merge(['_column_size' => 100, '_inline_size' => null], $settings);
	return [
		'id' => opd_hf_eid(),
		'elType' => 'column',
		'settings' => $settings,
		'elements' => $widgets,
		'isInner' => false,
	];
}

function opd_hf_section(array $columns, array $settings = [], bool $inner = false): array {
	return [
		'id' => opd_hf_eid(),
		'elType' => 'section',
		'settings' => $settings,
		'elements' => $columns,
		'isInner' => $inner,
	];
}

// —— 1) MU CSS: kill header .opd-ui 100vh blank + hero helpers ——
$css = <<<CSS
/* OPD HOMEFIX 2026-09-20 — header .opd-ui must NOT paint a full viewport */
:root {
  --e-global-color-primary: {$PRIMARY};
  --e-global-color-secondary: {$PRIMARY_DARK};
  --e-global-color-text: {$TEXT};
  --e-global-color-accent: {$PRIMARY};
  --opd-blue: {$PRIMARY};
  --opd-blue-dark: {$PRIMARY_DARK};
  --opd-primary: {$PRIMARY};
  --opd-teal: #00A8A8;
}
.elementor-widget-opd-directory-header .opd-ui,
.elementor-widget-opd-directory-header .elementor-widget-container > .opd-ui {
  min-height: 0 !important;
  height: auto !important;
  background: transparent !important;
  max-width: none !important;
  margin: 0 !important;
}
.opd-site-header {
  background: #fff !important;
}
/* Full-bleed photo hero */
.opd-home-hero-photo {
  min-height: 78vh !important;
  display: flex !important;
  align-items: center !important;
}
.opd-home-hero-photo .elementor-container {
  width: 100%;
}
.opd-home-hero-photo .elementor-heading-title,
.opd-home-hero-photo .elementor-widget-text-editor,
.opd-home-hero-photo .elementor-widget-text-editor * {
  color: #fff !important;
}
.opd-home-hero-photo .elementor-heading-title {
  font-size: clamp(1.75rem, 4vw, 3rem) !important;
  font-weight: 700 !important;
  line-height: 1.15 !important;
  max-width: 52rem;
  margin-left: auto;
  margin-right: auto;
}
.opd-home-ctas {
  display: flex !important;
  flex-wrap: wrap;
  gap: 0.85rem;
  justify-content: center;
  align-items: center;
  margin: 1.25rem auto 0;
}
.opd-home-cta-primary {
  display: inline-flex !important;
  align-items: center;
  gap: 0.4rem;
  background: {$PRIMARY} !important;
  color: #fff !important;
  font-weight: 700 !important;
  font-size: 1rem !important;
  padding: 0.85rem 1.4rem !important;
  border-radius: 8px !important;
  text-decoration: none !important;
  border: 2px solid {$PRIMARY} !important;
  box-shadow: 0 8px 24px rgba(0,102,204,0.35);
}
.opd-home-cta-primary:hover {
  background: {$PRIMARY_DARK} !important;
  border-color: {$PRIMARY_DARK} !important;
  color: #fff !important;
}
.opd-home-cta-ghost {
  display: inline-flex !important;
  align-items: center;
  background: transparent !important;
  color: #fff !important;
  font-weight: 600 !important;
  font-size: 1rem !important;
  padding: 0.85rem 1.4rem !important;
  border-radius: 8px !important;
  text-decoration: none !important;
  border: 2px solid rgba(255,255,255,0.92) !important;
}
.opd-home-cta-ghost:hover {
  background: rgba(255,255,255,0.12) !important;
  color: #fff !important;
}
.opd-home-stat-num .elementor-heading-title {
  color: #fff !important;
  font-size: 2.5rem !important;
  font-weight: 700 !important;
}
.opd-home-stat-label {
  color: rgba(255,255,255,0.92) !important;
}
/* Hide leftover welcome band if any residual markup */
.opd-home-welcome-hide {
  display: none !important;
}
/* Teal DIRECTORY wordmark hint if SVG text not present */
.opd-logo-bottom {
  color: #00A8A8 !important;
  letter-spacing: 0.08em !important;
}
CSS;

update_option('opd_match_custom_css', $css, false);
$log[] = 'css_option_updated len=' . strlen($css);

$mu_dir = WP_CONTENT_DIR . '/mu-plugins';
if (!is_dir($mu_dir)) {
	wp_mkdir_p($mu_dir);
}
$mu = <<<'MUPHP'
<?php
/**
 * Plugin Name: OPD Match Brand CSS
 * Description: Brand blues + home hero / header fixes for Offsite Pro Directory staging.
 * Version: 1.1.0-homefix
 */
defined('ABSPATH') || exit;

add_action('wp_head', static function (): void {
	$css = (string) get_option('opd_match_custom_css', '');
	if ($css === '') {
		return;
	}
	echo "<style id=\"opd-match-brand\">\n" . $css . "\n</style>\n";
}, 99);

add_action('elementor/frontend/after_enqueue_styles', static function (): void {
	$css = '.opd-ui{--opd-blue:#0066CC!important;--opd-blue-dark:#005EB8!important;--opd-primary:#0066CC!important;}'
		. '.elementor-widget-opd-directory-header .opd-ui{min-height:0!important;height:auto!important;background:transparent!important;}';
	wp_add_inline_style('ong-dir-front', $css);
}, 20);
MUPHP;
file_put_contents($mu_dir . '/opd-match-brand.php', $mu);
$log[] = 'mu_plugin_v1.1_written';

// —— 2) Soft-patch front.css .opd-ui min-height when possible ——
$front_css = WP_CONTENT_DIR . '/plugins/ong-directory/assets/css/front.css';
if (is_readable($front_css) && is_writable($front_css)) {
	$raw = file_get_contents($front_css);
	$patched = $raw;
	// Ensure brand vars
	$patched = preg_replace('/--opd-blue:\s*#[0-9A-Fa-f]{3,8};/', "--opd-blue: {$PRIMARY};", $patched, 1);
	$patched = preg_replace('/--opd-blue-dark:\s*#[0-9A-Fa-f]{3,8};/', "--opd-blue-dark: {$PRIMARY_DARK};", $patched, 1);
	$patched = preg_replace('/--opd-primary:\s*#[0-9A-Fa-f]{3,8};/', "--opd-primary: {$PRIMARY};", $patched, 1);
	// Append homefix block once
	$marker = '/* OPD-HOMEFIX-20260920 */';
	if (strpos($patched, $marker) === false) {
		$patched .= "\n{$marker}\n"
			. ".elementor-widget-opd-directory-header .opd-ui{min-height:0!important;height:auto!important;background:transparent!important;max-width:none!important;margin:0!important;}\n"
			. ".opd-home-hero-photo{min-height:78vh!important;}\n";
	}
	if ($patched !== $raw) {
		$bak = $front_css . '.bak-opd-homefix-20260920';
		if (!file_exists($bak)) {
			copy($front_css, $bak);
		}
		file_put_contents($front_css, $patched);
		$log[] = 'front_css_homefix_patched';
	} else {
		$log[] = 'front_css_unchanged';
	}
} else {
	$log[] = 'front_css_not_writable';
}

// —— 3) Patch shortcodes header nav → OPD labels (crocodile only) ——
$sc = WP_CONTENT_DIR . '/plugins/ong-directory/includes/class-shortcodes.php';
if (is_readable($sc) && is_writable($sc)) {
	$src = file_get_contents($sc);
	$bak = $sc . '.bak-opd-homefix-20260920';
	if (!file_exists($bak)) {
		copy($sc, $bak);
	}
	$orig = $src;

	$nav_old = "\t\t\$nav = [\n"
		. "\t\t\t[ 'label' => __( 'Home', 'ong-directory' ), 'url' => home_url( '/' ), 'slug' => 'home' ],\n"
		. "\t\t\t[ 'label' => __( 'Members', 'ong-directory' ), 'url' => home_url( '/members/' ), 'slug' => 'members' ],\n"
		. "\t\t\t[ 'label' => __( 'Companies', 'ong-directory' ), 'url' => home_url( '/directory/' ), 'slug' => 'directory' ],\n"
		. "\t\t\t[ 'label' => __( 'Sponsors', 'ong-directory' ), 'url' => home_url( '/sponsors/' ), 'slug' => 'sponsors' ],\n"
		. "\t\t\t[ 'label' => __( 'Institute', 'ong-directory' ), 'url' => home_url( '/institute/' ), 'slug' => 'institute' ],\n"
		. "\t\t\t[ 'label' => __( 'Contact', 'ong-directory' ), 'url' => home_url( '/contact/' ), 'slug' => 'contact' ],\n"
		. "\t\t];";

	$nav_new = "\t\t\$nav = [\n"
		. "\t\t\t[ 'label' => __( 'Home', 'ong-directory' ), 'url' => home_url( '/' ), 'slug' => 'home' ],\n"
		. "\t\t\t[ 'label' => __( 'Directory', 'ong-directory' ), 'url' => home_url( '/directory/' ), 'slug' => 'directory' ],\n"
		. "\t\t\t[ 'label' => __( 'About', 'ong-directory' ), 'url' => home_url( '/about/' ), 'slug' => 'about' ],\n"
		. "\t\t\t[ 'label' => __( 'Sponsors', 'ong-directory' ), 'url' => home_url( '/sponsors/' ), 'slug' => 'sponsors' ],\n"
		. "\t\t\t[ 'label' => __( 'Resources', 'ong-directory' ), 'url' => home_url( '/resources/' ), 'slug' => 'resources' ],\n"
		. "\t\t\t[ 'label' => __( 'Contact Us', 'ong-directory' ), 'url' => home_url( '/contact/' ), 'slug' => 'contact' ],\n"
		. "\t\t];";

	if (strpos($src, $nav_old) !== false) {
		$src = str_replace($nav_old, $nav_new, $src, $c_nav);
		$log[] = 'nav_array_replaced count=' . $c_nav;
	} elseif (strpos($src, "__( 'Directory', 'ong-directory' )") !== false && strpos($src, "__( 'Members', 'ong-directory' ), 'url' => home_url( '/members/' )") === false) {
		$log[] = 'nav_already_opd';
	} else {
		$log[] = 'nav_array_exact_miss_trying_regex';
		$src2 = preg_replace('/\$nav\s*=\s*\[[\s\S]*?\];/', trim($nav_new), $src, 1, $count_nav);
		if ($count_nav === 1) {
			$src = $src2;
			$log[] = 'nav_array_regex_ok';
		} else {
			$log[] = 'nav_array_fail count=' . $count_nav;
		}
	}

	$src = str_replace(
		"echo '<a class=\"opd-logo\" href=\"' . esc_url( home_url( '/members/' ) ) . '\">';",
		"echo '<a class=\"opd-logo\" href=\"' . esc_url( home_url( '/' ) ) . '\">';",
		$src,
		$c_logo
	);
	$log[] = 'logo_link_home=' . $c_logo;

	$cur_old = "\t\t\$current = 'directory';\n"
		. "\t\tif ( is_page( 'sponsors' ) || is_page( 'advertise' ) ) {\n"
		. "\t\t\t\$current = 'sponsors';\n"
		. "\t\t} elseif ( is_page( 'members' ) || is_page( 'members-overview' ) || is_page( 'members-companies' ) || is_page( 'members-people' ) ) {\n"
		. "\t\t\t\$current = 'members';\n"
		. "\t\t}";

	$cur_new = "\t\t\$current = 'home';\n"
		. "\t\tif ( is_front_page() || is_page( 'home' ) ) {\n"
		. "\t\t\t\$current = 'home';\n"
		. "\t\t} elseif ( is_page( 'directory' ) || is_page( 'companies' ) ) {\n"
		. "\t\t\t\$current = 'directory';\n"
		. "\t\t} elseif ( is_page( 'about' ) ) {\n"
		. "\t\t\t\$current = 'about';\n"
		. "\t\t} elseif ( is_page( 'sponsors' ) || is_page( 'advertise' ) ) {\n"
		. "\t\t\t\$current = 'sponsors';\n"
		. "\t\t} elseif ( is_page( 'resources' ) ) {\n"
		. "\t\t\t\$current = 'resources';\n"
		. "\t\t} elseif ( is_page( 'contact' ) ) {\n"
		. "\t\t\t\$current = 'contact';\n"
		. "\t\t}";

	if (strpos($src, $cur_old) !== false) {
		$src = str_replace($cur_old, $cur_new, $src, $c_cur);
		$log[] = 'current_detection_replaced count=' . $c_cur;
	} else {
		$log[] = 'current_detection_exact_miss';
	}

	$src = str_replace(
		"esc_attr__( 'Members', 'ong-directory' )",
		"esc_attr__( 'Primary', 'ong-directory' )",
		$src,
		$c_aria
	);
	$log[] = 'aria_label_primary=' . $c_aria;

	if ($src !== $orig) {
		file_put_contents($sc, $src);
		$log[] = 'shortcodes_patched bytes=' . strlen($src);
	} else {
		$log[] = 'shortcodes_unchanged';
	}
} else {
	$log[] = 'shortcodes_not_writable';
}

// —— 4) Ensure About / Resources / Contact pages exist ——
foreach ([
	'about' => 'About',
	'resources' => 'Resources',
	'contact' => 'Contact',
	'directory' => 'Directory',
	'sponsors' => 'Sponsors',
	'join' => 'Join',
] as $slug => $title) {
	$p = get_page_by_path($slug);
	if (!$p) {
		$id = wp_insert_post([
			'post_title' => $title,
			'post_name' => $slug,
			'post_status' => 'publish',
			'post_type' => 'page',
			'post_content' => '',
		], true);
		$log[] = is_wp_error($id) ? "page_create_fail_{$slug}" : "page_created_{$slug}={$id}";
	} else {
		$log[] = "page_ok_{$slug}={$p->ID}";
	}
}

// —— 5) Hero attachment ——
$hero_att_id = (int) get_option('opd_match_hero_attachment_id', 0);
$hero_src = '';
if ($hero_att_id > 0 && wp_attachment_is_image($hero_att_id)) {
	$hero_src = (string) wp_get_attachment_url($hero_att_id);
	$log[] = "hero_reuse id={$hero_att_id}";
} else {
	// find by filename
	$q = new WP_Query([
		'post_type' => 'attachment',
		'post_status' => 'inherit',
		'posts_per_page' => 1,
		'meta_query' => [],
		's' => 'opd-hero-modular',
		'fields' => 'ids',
	]);
	if (!empty($q->posts)) {
		$hero_att_id = (int) $q->posts[0];
		$hero_src = (string) wp_get_attachment_url($hero_att_id);
		update_option('opd_match_hero_attachment_id', $hero_att_id, false);
		$log[] = "hero_found_search id={$hero_att_id}";
	} else {
		$local = $root . '/opd-hero-modular.jpg';
		if (is_readable($local)) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$copy = wp_tempnam('opd-hero.jpg');
			copy($local, $copy);
			$id = media_handle_sideload(['name' => 'opd-hero-modular.jpg', 'tmp_name' => $copy], 0, 'OPD home hero');
			if (!is_wp_error($id)) {
				$hero_att_id = (int) $id;
				$hero_src = (string) wp_get_attachment_url($hero_att_id);
				update_option('opd_match_hero_attachment_id', $hero_att_id, false);
				$log[] = "hero_sideloaded id={$hero_att_id}";
			} else {
				$log[] = 'hero_sideload_err ' . $id->get_error_message();
			}
		} else {
			$log[] = 'hero_unavailable';
		}
	}
}

// —— 6) Rebuild Home Elementor (no Welcome band; dual CTAs in hero) ——
$home_id = (int) get_option('page_on_front');
$saved_pages = get_option('ong_dir_elementor_pages', []);
if (is_array($saved_pages) && !empty($saved_pages['home'])) {
	$home_id = (int) $saved_pages['home'];
}
if (!$home_id) {
	$p = get_page_by_path('home');
	if ($p) {
		$home_id = (int) $p->ID;
	}
}

$dir_url = home_url('/directory/');
$join_url = home_url('/join/');
if (!get_page_by_path('join')) {
	$join_url = home_url('/login/');
}

$header_section = opd_hf_section([
	opd_hf_column([
		opd_hf_widget('opd-directory-header', []),
	]),
], [
	'background_background' => 'classic',
	'background_color' => $WHITE,
	'padding' => [
		'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true,
	],
]);

$cta_html = '<div class="opd-home-ctas">'
	. '<a class="opd-home-cta-primary" href="' . esc_url($join_url) . '">Join Directory →</a>'
	. '<a class="opd-home-cta-ghost" href="' . esc_url($dir_url) . '">Browse Companies</a>'
	. '</div>';

$stat_col = static function (string $num, string $label) use ($WHITE): array {
	return opd_hf_column([
		opd_hf_widget('heading', [
			'title' => $num,
			'header_size' => 'h2',
			'align' => 'center',
			'title_color' => $WHITE,
			'css_classes' => 'opd-home-stat-num',
		]),
		opd_hf_widget('text-editor', [
			'editor' => '<p class="opd-home-stat-label" style="text-align:center;color:#FFFFFF;margin:0;opacity:0.9"><strong>' . esc_html($label) . '</strong></p>',
		]),
	], ['_column_size' => 33, '_inline_size' => 33]);
};

$stats_inner = opd_hf_section([
	$stat_col('500+', 'Companies'),
	$stat_col('50+', 'Countries'),
	$stat_col('15', 'Categories'),
], [
	'structure' => '33',
	'background_background' => 'classic',
	'background_color' => 'rgba(0,0,0,0)',
	'padding' => [
		'unit' => 'px', 'top' => '36', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false,
	],
], true);

$hero_settings = [
	'layout' => 'full_width',
	'gap' => 'no',
	'height' => 'min-height',
	'custom_height' => ['unit' => 'vh', 'size' => 78, 'sizes' => []],
	'background_background' => 'classic',
	'background_color' => $PRIMARY,
	'padding' => [
		'unit' => 'px', 'top' => '96', 'right' => '24', 'bottom' => '72', 'left' => '24', 'isLinked' => false,
	],
	'css_classes' => 'opd-home-hero-photo',
];
if ($hero_src !== '') {
	$hero_settings['background_image'] = [
		'url' => $hero_src,
		'id' => $hero_att_id,
		'alt' => 'Modular construction yard with cranes',
		'source' => 'library',
	];
	$hero_settings['background_position'] = 'center center';
	$hero_settings['background_repeat'] = 'no-repeat';
	$hero_settings['background_size'] = 'cover';
	$hero_settings['background_overlay_background'] = 'classic';
	$hero_settings['background_overlay_color'] = '#0a1628';
	$hero_settings['background_overlay_opacity'] = ['unit' => 'px', 'size' => 0.55, 'sizes' => []];
}

$hero_section = opd_hf_section([
	opd_hf_column([
		opd_hf_widget('heading', [
			'title' => 'Engage with the Global Prefab and Offsite Construction Hub',
			'header_size' => 'h1',
			'align' => 'center',
			'title_color' => $WHITE,
		]),
		opd_hf_widget('text-editor', [
			'editor' => '<p style="text-align:center;color:#FFFFFF;opacity:0.95;max-width:48rem;margin:0.75rem auto 0;font-size:1.05rem;line-height:1.55">Discover developers, architects, suppliers, and industry professionals revolutionizing construction through modular and offsite methods.</p>',
		]),
		opd_hf_widget('spacer', ['space' => ['unit' => 'px', 'size' => 8]]),
		opd_hf_widget('html', [
			'html' => $cta_html,
		]),
		opd_hf_widget('spacer', ['space' => ['unit' => 'px', 'size' => 28]]),
		$stats_inner,
	], [
		'align' => 'center',
		'padding' => [
			'unit' => 'px', 'top' => '24', 'right' => '16', 'bottom' => '16', 'left' => '16', 'isLinked' => false,
		],
	]),
], $hero_settings);

$ticker_section = opd_hf_section([
	opd_hf_column([
		opd_hf_widget('opd-directory-sponsors-ticker', []),
	]),
]);

// Intentionally omit Welcome band (fights OPD look).
$elements = [$header_section, $hero_section, $ticker_section];

if ($home_id > 0) {
	$json = wp_json_encode($elements);
	update_post_meta($home_id, '_elementor_edit_mode', 'builder');
	update_post_meta($home_id, '_elementor_template_type', 'wp-page');
	update_post_meta($home_id, '_elementor_version', '3.16.0');
	update_post_meta($home_id, '_elementor_data', wp_slash($json));
	update_post_meta($home_id, '_wp_page_template', 'elementor_canvas');
	// Force Elementor CSS regen
	delete_post_meta($home_id, '_elementor_css');
	wp_update_post([
		'ID' => $home_id,
		'post_status' => 'publish',
		'post_title' => 'Home',
	]);
	update_option('show_on_front', 'page');
	update_option('page_on_front', $home_id);
	$log[] = "home_rebuilt id={$home_id} bytes=" . strlen((string) $json);
} else {
	$log[] = 'home_page_missing';
}

// —— 7) Clear Elementor + object caches ——
if (class_exists('\\Elementor\\Plugin')) {
	$plugin = \Elementor\Plugin::$instance;
	if (isset($plugin->files_manager) && is_object($plugin->files_manager) && method_exists($plugin->files_manager, 'clear_cache')) {
		$plugin->files_manager->clear_cache();
		$log[] = 'elementor_cache_cleared';
	}
}
if (function_exists('wp_cache_flush')) {
	wp_cache_flush();
	$log[] = 'wp_cache_flushed';
}
do_action('litespeed_purge_all');
$log[] = 'litespeed_purge_action';

update_option('opd_homefix_log', [
	'at' => gmdate('c'),
	'log' => $log,
	'home_id' => $home_id,
	'hero_src' => $hero_src,
], false);

$out = [
	'ok' => true,
	'homefix' => true,
	'log' => $log,
	'home_id' => $home_id,
	'hero_src' => $hero_src,
];
file_put_contents($root . '/opd-homefix-result.json', wp_json_encode($out, JSON_PRETTY_PRINT));
header('Content-Type: application/json');
echo wp_json_encode($out, JSON_PRETTY_PRINT) . "\n";

if (getenv('OPD_HOMEFIX_SELF_DELETE') === '1' || (isset($_GET['self_delete']) && $_GET['self_delete'] === '1')) {
	@unlink(__FILE__);
}
