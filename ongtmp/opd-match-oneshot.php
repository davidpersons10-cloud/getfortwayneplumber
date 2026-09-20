<?php
/**
 * One-shot: OPD visual match for skyblue-crocodile (palette, home hero photo, header, import).
 * Safe to re-run (idempotent). Deletes itself when OPD_MATCH_SELF_DELETE=1.
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
$log[] = 'start ' . gmdate('c');

$PRIMARY = '#0066CC';
$PRIMARY_DARK = '#005EB8';
$ACCENT = '#0079D6';
$TEAL = '#00A8A8';
$TEXT = '#21242C';
$LIGHT = '#F5F7FA';
$WHITE = '#FFFFFF';

function opd_eid(): string {
	try {
		return substr(bin2hex(random_bytes(4)), 0, 7);
	} catch (Throwable $e) {
		return substr(md5(uniqid((string) mt_rand(), true)), 0, 7);
	}
}

function opd_widget(string $type, array $settings): array {
	return [
		'id' => opd_eid(),
		'elType' => 'widget',
		'widgetType' => $type,
		'settings' => $settings,
		'elements' => [],
	];
}

function opd_column(array $widgets, array $settings = []): array {
	$settings = array_merge(['_column_size' => 100, '_inline_size' => null], $settings);
	return [
		'id' => opd_eid(),
		'elType' => 'column',
		'settings' => $settings,
		'elements' => $widgets,
		'isInner' => false,
	];
}

function opd_section(array $columns, array $settings = []): array {
	return [
		'id' => opd_eid(),
		'elType' => 'section',
		'settings' => $settings,
		'elements' => $columns,
		'isInner' => false,
	];
}

// —— 1) Backup marker (no Updraft on this site) ——
update_option('opd_match_pre_edit_note', [
	'at' => gmdate('c'),
	'note' => 'No Updraft on crocodile; Hostinger daily backups are the restore point. Pre-edit marker set before OPD match oneshot.',
	'siteurl' => home_url('/'),
], false);
$log[] = 'backup_marker_set';

// —— 2) Custom CSS (brand variables + Elementor default overrides) ——
$css = <<<CSS
/* OPD match 2026-09-20 — brand blues vs Elementor defaults */
:root {
  --e-global-color-primary: {$PRIMARY};
  --e-global-color-secondary: {$PRIMARY_DARK};
  --e-global-color-text: {$TEXT};
  --e-global-color-accent: {$ACCENT};
  --opd-blue: {$PRIMARY};
  --opd-blue-dark: {$PRIMARY_DARK};
  --opd-primary: {$PRIMARY};
  --opd-teal: {$TEAL};
}
.opd-ui {
  --opd-blue: {$PRIMARY} !important;
  --opd-blue-dark: {$PRIMARY_DARK} !important;
  --opd-primary: {$PRIMARY} !important;
}
/* Soften leftover Elementor kit blues */
.elementor-kit-1, body.elementor-page {
  --e-global-color-primary: {$PRIMARY};
}
.opd-home-hero-photo {
  min-height: 72vh;
}
.opd-home-hero-photo .elementor-heading-title,
.opd-home-hero-photo .elementor-widget-text-editor {
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
CSS;

$existing = (string) get_option('opd_match_custom_css', '');
update_option('opd_match_custom_css', $css, false);
if (!has_action('wp_head', 'opd_match_print_custom_css')) {
	// Ensure MU or oneshot registers print on this request only; persist via option + mu stub below.
}
$log[] = 'custom_css_option_set len=' . strlen($css);

// Write durable MU plugin for CSS + soft Elementor kit
$mu_dir = WP_CONTENT_DIR . '/mu-plugins';
if (!is_dir($mu_dir)) {
	wp_mkdir_p($mu_dir);
}
$mu = <<<'MUPHP'
<?php
/**
 * Plugin Name: OPD Match Brand CSS
 * Description: Brand blues + home hero helpers for Offsite Pro Directory staging match.
 * Version: 1.0.0
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
	$css = '.opd-ui{--opd-blue:#0066CC!important;--opd-blue-dark:#005EB8!important;--opd-primary:#0066CC!important;}';
	wp_add_inline_style('ong-dir-front', $css);
}, 20);
MUPHP;
file_put_contents($mu_dir . '/opd-match-brand.php', $mu);
$log[] = 'mu_plugin_written';

// —— 3) Elementor kit system colors ——
$kit_id = (int) get_option('elementor_active_kit');
if ($kit_id > 0) {
	$settings = get_post_meta($kit_id, '_elementor_page_settings', true);
	if (!is_array($settings)) {
		$settings = [];
	}
	$settings['system_colors'] = [
		['_id' => 'primary', 'title' => 'Primary', 'color' => $PRIMARY],
		['_id' => 'secondary', 'title' => 'Secondary', 'color' => $PRIMARY_DARK],
		['_id' => 'text', 'title' => 'Text', 'color' => $TEXT],
		['_id' => 'accent', 'title' => 'Accent', 'color' => $ACCENT],
	];
	$settings['system_typography'] = $settings['system_typography'] ?? [];
	update_post_meta($kit_id, '_elementor_page_settings', $settings);
	$log[] = "kit_colors_updated kit={$kit_id}";
} else {
	$log[] = 'kit_missing';
}

// —— 4) Sideload hero image ——
$hero_url_candidates = [
	'https://raw.githubusercontent.com/davidpersons10-cloud/getfortwayneplumber/opd-match-drop-20260920/ongtmp/opd-hero-modular.jpg',
	home_url('/opd-hero-modular.jpg'),
];
$hero_att_id = (int) get_option('opd_match_hero_attachment_id', 0);
$hero_src = '';
if ($hero_att_id > 0 && wp_attachment_is_image($hero_att_id)) {
	$hero_src = (string) wp_get_attachment_url($hero_att_id);
	$log[] = "hero_reuse id={$hero_att_id}";
} else {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$local_drop = $root . '/opd-hero-modular.jpg';
	$tmp = '';
	if (is_readable($local_drop)) {
		$tmp = $local_drop;
		$log[] = 'hero_from_local_drop';
	} else {
		foreach ($hero_url_candidates as $u) {
			$r = wp_remote_get($u, ['timeout' => 60, 'redirection' => 5]);
			if (is_wp_error($r) || (int) wp_remote_retrieve_response_code($r) !== 200) {
				$log[] = 'hero_fetch_fail ' . $u;
				continue;
			}
			$body = wp_remote_retrieve_body($r);
			if (strlen($body) < 10000) {
				$log[] = 'hero_too_small ' . $u;
				continue;
			}
			$tmp = wp_tempnam('opd-hero.jpg');
			file_put_contents($tmp, $body);
			$log[] = 'hero_fetched ' . $u . ' bytes=' . strlen($body);
			break;
		}
	}
	if ($tmp && is_readable($tmp)) {
		$file_array = [
			'name' => 'opd-hero-modular.jpg',
			'tmp_name' => $tmp,
		];
		// media_handle_sideload expects uploaded tmp in sys temp; copy if local drop
		if ($tmp === $local_drop) {
			$copy = wp_tempnam('opd-hero.jpg');
			copy($local_drop, $copy);
			$file_array['tmp_name'] = $copy;
		}
		$id = media_handle_sideload($file_array, 0, 'OPD home hero modular construction');
		if (is_wp_error($id)) {
			$log[] = 'hero_sideload_err ' . $id->get_error_message();
		} else {
			$hero_att_id = (int) $id;
			$hero_src = (string) wp_get_attachment_url($hero_att_id);
			update_option('opd_match_hero_attachment_id', $hero_att_id, false);
			$log[] = "hero_sideloaded id={$hero_att_id} url={$hero_src}";
		}
	} else {
		$log[] = 'hero_unavailable';
	}
}

// —— 5) Rebuild Home Elementor data ——
$home_id = (int) get_option('page_on_front');
if (!$home_id) {
	$p = get_page_by_path('home');
	if ($p) {
		$home_id = (int) $p->ID;
	}
}
$saved_pages = get_option('ong_dir_elementor_pages', []);
if (is_array($saved_pages) && !empty($saved_pages['home'])) {
	$home_id = (int) $saved_pages['home'];
}
$dir_url = home_url('/directory/');
$join_url = home_url('/login/');

$header_section = opd_section([
	opd_column([
		opd_widget('opd-directory-header', []),
	]),
], [
	'background_background' => 'classic',
	'background_color' => $WHITE,
	'padding' => [
		'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true,
	],
]);

$hero_widgets = [
	opd_widget('heading', [
		'title' => 'Engage with the Global Prefab and Offsite Construction Hub',
		'header_size' => 'h1',
		'align' => 'center',
		'title_color' => $WHITE,
	]),
	opd_widget('text-editor', [
		'editor' => '<p style="text-align:center;color:#FFFFFF;opacity:0.95;max-width:48rem;margin:0.5rem auto 0">Discover developers, architects, suppliers, and industry professionals revolutionizing construction through modular and offsite methods.</p>',
	]),
	opd_widget('spacer', ['space' => ['unit' => 'px', 'size' => 16]]),
	opd_widget('button', [
		'text' => 'Browse Companies',
		'link' => ['url' => $dir_url, 'is_external' => false, 'nofollow' => false],
		'align' => 'center',
		'background_color' => $WHITE,
		'button_text_color' => $PRIMARY,
		'background_color_hover' => $LIGHT,
		'hover_color' => $PRIMARY_DARK,
		'border_radius' => ['unit' => 'px', 'top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '8', 'isLinked' => true],
	]),
	opd_widget('spacer', ['space' => ['unit' => 'px', 'size' => 36]]),
];

$stat_col = static function (string $num, string $label) use ($WHITE): array {
	return opd_column([
		opd_widget('heading', [
			'title' => $num,
			'header_size' => 'h2',
			'align' => 'center',
			'title_color' => $WHITE,
			'_css_classes' => 'opd-home-stat-num',
		]),
		opd_widget('text-editor', [
			'editor' => '<p class="opd-home-stat-label" style="text-align:center;color:#FFFFFF;margin:0;opacity:0.9"><strong>' . esc_html($label) . '</strong></p>',
		]),
	], ['_column_size' => 33, '_inline_size' => 33]);
};

$hero_settings = [
	'background_background' => 'classic',
	'background_color' => $PRIMARY,
	'padding' => [
		'unit' => 'px', 'top' => '80', 'right' => '24', 'bottom' => '64', 'left' => '24', 'isLinked' => false,
	],
	'_css_classes' => 'opd-home-hero-photo',
];
if ($hero_src !== '') {
	$hero_settings['background_image'] = [
		'url' => $hero_src,
		'id' => $hero_att_id,
		'alt' => 'Modular construction yard',
		'source' => 'library',
	];
	$hero_settings['background_position'] = 'center center';
	$hero_settings['background_repeat'] = 'no-repeat';
	$hero_settings['background_size'] = 'cover';
	$hero_settings['background_overlay_background'] = 'classic';
	$hero_settings['background_overlay_color'] = '#0a1628';
	$hero_settings['background_overlay_opacity'] = ['unit' => 'px', 'size' => 0.55, 'sizes' => []];
}

// Inner section for stats inside hero: use nested section
$stats_inner = [
	'id' => opd_eid(),
	'elType' => 'section',
	'settings' => [
		'structure' => '33',
		'background_background' => 'classic',
		'background_color' => 'rgba(0,0,0,0)',
	],
	'elements' => [
		$stat_col('500+', 'Companies'),
		$stat_col('50+', 'Countries'),
		$stat_col('15', 'Categories'),
	],
	'isInner' => true,
];

$hero_column_widgets = array_merge($hero_widgets, [$stats_inner]);
$hero_section = opd_section([
	opd_column($hero_column_widgets, [
		'padding' => [
			'unit' => 'px', 'top' => '40', 'right' => '20', 'bottom' => '24', 'left' => '20', 'isLinked' => false,
		],
	]),
], $hero_settings);

$ticker_section = opd_section([
	opd_column([
		opd_widget('opd-directory-sponsors-ticker', []),
	]),
]);

$welcome_section = opd_section([
	opd_column([
		opd_widget('heading', [
			'title' => 'Welcome to Offsite Pro Directory',
			'header_size' => 'h2',
			'align' => 'center',
			'title_color' => '#082A45',
		]),
		opd_widget('text-editor', [
			'editor' => '<p style="text-align:center;color:' . $TEXT . ';max-width:42rem;margin:0.75rem auto">The world\'s premier platform connecting professionals in the offsite construction industry. Discover innovative companies, cutting-edge technologies, and build meaningful partnerships.</p>',
		]),
		opd_widget('spacer', ['space' => ['unit' => 'px', 'size' => 12]]),
		opd_widget('button', [
			'text' => 'Join Directory',
			'link' => ['url' => $join_url, 'is_external' => false, 'nofollow' => false],
			'align' => 'center',
			'background_color' => $PRIMARY,
			'button_text_color' => $WHITE,
			'background_color_hover' => $PRIMARY_DARK,
		]),
	], [
		'padding' => [
			'unit' => 'px', 'top' => '48', 'right' => '20', 'bottom' => '48', 'left' => '20', 'isLinked' => false,
		],
	]),
], [
	'background_background' => 'classic',
	'background_color' => $WHITE,
]);

$elements = [$header_section, $hero_section, $welcome_section, $ticker_section];

if ($home_id > 0) {
	$json = wp_json_encode($elements);
	update_post_meta($home_id, '_elementor_edit_mode', 'builder');
	update_post_meta($home_id, '_elementor_template_type', 'wp-page');
	update_post_meta($home_id, '_elementor_version', '3.16.0');
	update_post_meta($home_id, '_elementor_data', wp_slash($json));
	update_post_meta($home_id, '_wp_page_template', 'elementor_canvas');
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

// —— 6) Soft-patch front.css CSS variables (brand) ——
$front_css = WP_CONTENT_DIR . '/plugins/ong-directory/assets/css/front.css';
if (is_readable($front_css) && is_writable($front_css)) {
	$raw = file_get_contents($front_css);
	$patched = $raw;
	$patched = preg_replace('/--opd-blue:\s*#[0-9A-Fa-f]{3,8};/', "--opd-blue: {$PRIMARY};", $patched, 1);
	$patched = preg_replace('/--opd-blue-dark:\s*#[0-9A-Fa-f]{3,8};/', "--opd-blue-dark: {$PRIMARY_DARK};", $patched, 1);
	$patched = preg_replace('/--opd-primary:\s*#[0-9A-Fa-f]{3,8};/', "--opd-primary: {$PRIMARY};", $patched, 1);
	if ($patched !== $raw) {
		// backup once
		$bak = $front_css . '.bak-opd-match-20260920';
		if (!file_exists($bak)) {
			copy($front_css, $bak);
		}
		file_put_contents($front_css, $patched);
		$log[] = 'front_css_patched';
	} else {
		$log[] = 'front_css_unchanged_or_already';
	}
} else {
	$log[] = 'front_css_not_writable';
}

// —— 7) Import companies if empty ——
$company_count = 0;
if (post_type_exists('opd_company')) {
	$q = new WP_Query(['post_type' => 'opd_company', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids']);
	$company_count = (int) $q->found_posts;
} elseif (post_type_exists('ong_company')) {
	$q = new WP_Query(['post_type' => 'ong_company', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids']);
	$company_count = (int) $q->found_posts;
}
$log[] = "companies_before={$company_count}";

if ($company_count < 1 && class_exists('ONG_Dir_Import') && method_exists('ONG_Dir_Import', 'run')) {
	$result = ONG_Dir_Import::run();
	$log[] = 'import_result ' . wp_json_encode($result);
	if (method_exists('ONG_Dir_Import', 'sideload_logos')) {
		try {
			$side = ONG_Dir_Import::sideload_logos();
			$log[] = 'sideload ' . wp_json_encode($side);
		} catch (Throwable $e) {
			$log[] = 'sideload_err ' . $e->getMessage();
		}
	}
} else {
	$log[] = 'import_skipped';
}

// Recount
$company_count_after = 0;
foreach (['opd_company', 'ong_company', 'opd_dir_company'] as $pt) {
	if (!post_type_exists($pt)) {
		continue;
	}
	$q = new WP_Query(['post_type' => $pt, 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids']);
	$company_count_after = max($company_count_after, (int) $q->found_posts);
	$log[] = "cpt_{$pt}={$q->found_posts}";
}

// —— 8) Clear Elementor CSS cache ——
if (class_exists('\\Elementor\\Plugin')) {
	$plugin = \Elementor\Plugin::$instance;
	if (isset($plugin->files_manager) && is_object($plugin->files_manager) && method_exists($plugin->files_manager, 'clear_cache')) {
		$plugin->files_manager->clear_cache();
		$log[] = 'elementor_cache_cleared';
	}
}
if (class_exists('LiteSpeed\Purge')) {
	try {
		\LiteSpeed\Purge::purge_all();
		$log[] = 'litespeed_purged';
	} catch (Throwable $e) {
		$log[] = 'litespeed_purge_err';
	}
}
do_action('litespeed_purge_all');

update_option('opd_match_oneshot_log', [
	'at' => gmdate('c'),
	'log' => $log,
	'companies_after' => $company_count_after,
	'home_id' => $home_id,
	'hero_id' => $hero_att_id,
	'hero_src' => $hero_src,
], false);

$out = [
	'ok' => true,
	'log' => $log,
	'companies_after' => $company_count_after,
	'home_id' => $home_id,
	'hero_src' => $hero_src,
];
file_put_contents($root . '/opd-match-oneshot-result.json', wp_json_encode($out, JSON_PRETTY_PRINT));
echo wp_json_encode($out, JSON_PRETTY_PRINT) . "\n";

if (getenv('OPD_MATCH_SELF_DELETE') === '1' || (isset($_GET['self_delete']) && $_GET['self_delete'] === '1')) {
	@unlink(__FILE__);
	$log[] = 'self_deleted';
}
