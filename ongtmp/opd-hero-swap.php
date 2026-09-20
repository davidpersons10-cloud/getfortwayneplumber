<?php
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');
$log = [];
$att_id = (int) get_option('opd_match_hero_attachment_id', 31);
if ($att_id < 1) $att_id = 31;
$file = get_attached_file($att_id);
$log[] = 'att=' . $att_id . ' file=' . (string) $file;

$url = 'https://raw.githubusercontent.com/davidpersons10-cloud/getfortwayneplumber/opd-homefix-drop-20260920/ongtmp/opd-hero-clean.jpg';
$bin = @file_get_contents($url);
if ($bin === false || strlen($bin) < 10000) {
	echo wp_json_encode(['ok'=>false,'err'=>'dl_fail','len'=>strlen((string)$bin)]);
	exit;
}
$log[] = 'dl_bytes=' . strlen($bin);

if (!$file || !file_exists($file)) {
	// sideload new
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam('opd-hero-clean.jpg');
	file_put_contents($tmp, $bin);
	$id = media_handle_sideload(['name'=>'opd-hero-clean.jpg','tmp_name'=>$tmp], 0, 'OPD home hero clean');
	if (is_wp_error($id)) {
		echo wp_json_encode(['ok'=>false,'err'=>$id->get_error_message()]);
		exit;
	}
	$att_id = (int) $id;
	update_option('opd_match_hero_attachment_id', $att_id, false);
	$file = get_attached_file($att_id);
	$log[] = 'sideloaded=' . $att_id;
} else {
	file_put_contents($file, $bin);
	$log[] = 'overwrote';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$meta = wp_generate_attachment_metadata($att_id, $file);
	if (!empty($meta)) wp_update_attachment_metadata($att_id, $meta);
	$log[] = 'meta_regen';
}

$url_att = wp_get_attachment_url($att_id);
$log[] = 'url=' . $url_att;

# Point home Elementor hero background at this attachment (keep single HTML content)
$home = (int) get_option('page_on_front');
$data = json_decode((string) get_post_meta($home, '_elementor_data', true), true);
if (is_array($data)) {
	foreach ($data as &$sec) {
		if (!is_array($sec)) continue;
		$cls = (string) ($sec['settings']['css_classes'] ?? '');
		if (strpos($cls, 'opd-home-hero-photo') !== false) {
			$sec['settings']['background_image'] = [
				'url' => $url_att,
				'id' => $att_id,
				'source' => 'library',
			];
			$sec['settings']['background_overlay_blend_mode'] = 'normal';
			$log[] = 'hero_bg_updated';
		}
	}
	unset($sec);
	update_post_meta($home, '_elementor_data', wp_slash(wp_json_encode($data)));
}
delete_post_meta($home, '_elementor_css');
$cssdir = WP_CONTENT_DIR . '/uploads/elementor/css';
if (is_dir($cssdir)) {
	foreach (glob($cssdir . '/post-' . $home . '*.css') ?: [] as $f) @unlink($f);
}
if (class_exists('\\Elementor\\Plugin')) {
	$p = \Elementor\Plugin::$instance;
	if (isset($p->files_manager) && method_exists($p->files_manager, 'clear_cache')) {
		$p->files_manager->clear_cache();
		$log[] = 'el_cleared';
	}
}
if (function_exists('wp_cache_flush')) wp_cache_flush();
do_action('litespeed_purge_all');

$out = ['ok'=>true,'log'=>$log,'att'=>$att_id,'url'=>$url_att,'home'=>$home];
file_put_contents(__DIR__.'/opd-hero-swap-result.json', wp_json_encode($out, JSON_PRETTY_PRINT));
echo wp_json_encode($out, JSON_PRETTY_PRINT);
@unlink(__FILE__);
