<?php
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');
$q = new WP_Query([
	'post_type' => 'elementor_library',
	'post_status' => 'any',
	'posts_per_page' => 50,
	'fields' => 'ids',
]);
$items = [];
foreach ($q->posts as $id) {
	$type = get_post_meta($id, '_elementor_template_type', true);
	$data = (string) get_post_meta($id, '_elementor_data', true);
	$items[] = [
		'id' => $id,
		'title' => get_the_title($id),
		'status' => get_post_status($id),
		'type' => $type,
		'engage' => substr_count($data, 'Engage with'),
		'browse' => substr_count($data, 'Browse Companies'),
		'has_header_widget' => substr_count($data, 'opd-directory-header'),
		'bytes' => strlen($data),
	];
}
# Also check conditions option
$conds = get_option('elementor_pro_theme_builder_conditions', null);
$out = [
	'library_count' => count($items),
	'items' => $items,
	'pro_conditions' => $conds,
	'kit' => (int) get_option('elementor_active_kit', 0),
];
# Disable any theme-builder-like templates that contain Engage
$disabled = [];
foreach ($items as $it) {
	if ($it['engage'] > 0 && $it['type'] !== 'page' && $it['status'] === 'publish') {
		wp_update_post(['ID'=>$it['id'],'post_status'=>'draft']);
		$disabled[] = $it['id'];
	}
}
$out['disabled_ids'] = $disabled;
file_put_contents(__DIR__.'/opd-tb-result.json', wp_json_encode($out, JSON_PRETTY_PRINT));
echo wp_json_encode($out, JSON_PRETTY_PRINT);
@unlink(__FILE__);
