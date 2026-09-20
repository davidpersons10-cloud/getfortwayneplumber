<?php
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');
$home = (int) get_option('page_on_front');
$html = '';
$err = '';
try {
	if (class_exists('\\Elementor\\Plugin')) {
		$doc = \Elementor\Plugin::$instance->documents->get($home);
		if ($doc) {
			$html = (string) $doc->get_content(true);
		} else {
			$err = 'no_document';
		}
	} else {
		$err = 'no_elementor';
	}
} catch (Throwable $e) {
	$err = $e->getMessage();
}

$out = [
	'home' => $home,
	'err' => $err,
	'html_len' => strlen($html),
	'engage' => substr_count($html, 'Engage with'),
	'browse' => substr_count($html, 'Browse Companies'),
	'join' => substr_count($html, 'Join Directory'),
	'stat500' => substr_count($html, '500+'),
	'heading_widget' => substr_count($html, 'elementor-widget-heading'),
	'html_widget' => substr_count($html, 'elementor-widget-html'),
	'button_widget' => substr_count($html, 'elementor-widget-button'),
	'text_editor_widget' => substr_count($html, 'elementor-widget-text-editor'),
	'hero_photo' => substr_count($html, 'opd-home-hero-photo'),
	'hero_inner' => substr_count($html, 'opd-home-hero-inner'),
];
if (preg_match_all('/<h1[^>]*>(.*?)<\\/h1>/is', $html, $m)) {
	$out['h1_count'] = count($m[1]);
	$out['h1_texts'] = array_map(static fn($t) => trim(wp_strip_all_tags($t)), $m[1]);
} else {
	$out['h1_count'] = 0;
	$out['h1_texts'] = [];
}
$pos = strpos($html, 'Engage with');
if ($pos !== false) {
	$out['snippet'] = substr($html, max(0, $pos - 500), 1500);
	file_put_contents(__DIR__ . '/opd-dom-snippet.html', $out['snippet']);
}
file_put_contents(__DIR__ . '/opd-dom-result.json', wp_json_encode($out, JSON_PRETTY_PRINT));
echo wp_json_encode($out, JSON_PRETTY_PRINT);
@unlink(__FILE__);
