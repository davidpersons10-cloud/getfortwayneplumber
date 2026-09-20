<?php
/**
 * Fetch front page HTML and count Engage / Browse / hero markup.
 */
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');

$url = home_url('/?domprobe=' . time());
$r = wp_remote_get($url, [
	'timeout' => 45,
	'sslverify' => false,
	'headers' => ['Cache-Control' => 'no-cache', 'User-Agent' => 'OPD-DOM-Probe'],
]);
$html = is_wp_error($r) ? ('ERR ' . $r->get_error_message()) : (string) wp_remote_retrieve_body($r);

$out = [
	'html_len' => strlen($html),
	'engage' => substr_count($html, 'Engage with'),
	'browse' => substr_count($html, 'Browse Companies'),
	'join' => substr_count($html, 'Join Directory'),
	'stat500' => substr_count($html, '500+'),
	'hero_inner' => substr_count($html, 'opd-home-hero-inner'),
	'heading_widget' => substr_count($html, 'elementor-widget-heading'),
	'html_widget' => substr_count($html, 'elementor-widget-html'),
	'button_widget' => substr_count($html, 'elementor-widget-button'),
	'text_shadow_css' => substr_count($html, 'text-shadow'),
	'webkit_stroke' => substr_count($html, '-webkit-text-stroke'),
	'opd_match_brand' => substr_count($html, 'opd-match-brand'),
	'deghost_marker' => substr_count($html, 'DEGHOST'),
];

// Extract hero-ish snippet around first Engage
$pos = strpos($html, 'Engage with');
if ($pos !== false) {
	$snip = substr($html, max(0, $pos - 400), 1200);
	$out['snippet'] = $snip;
	file_put_contents(__DIR__ . '/opd-dom-snippet.html', $snip);
}

// Count h1 tags and list their text
if (preg_match_all('/<h1[^>]*>(.*?)<\\/h1>/is', $html, $m)) {
	$out['h1_count'] = count($m[1]);
	$out['h1_texts'] = array_map(static function ($t) {
		return trim(wp_strip_all_tags($t));
	}, $m[1]);
} else {
	$out['h1_count'] = 0;
	$out['h1_texts'] = [];
}

file_put_contents(__DIR__ . '/opd-dom-result.json', wp_json_encode($out, JSON_PRETTY_PRINT));
echo wp_json_encode($out, JSON_PRETTY_PRINT);
@unlink(__FILE__);
