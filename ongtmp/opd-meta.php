<?php
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');
$home = (int) get_option('page_on_front');
$data = (string) get_post_meta($home, '_elementor_data', true);
$css = (string) get_option('opd_match_custom_css', '');
$decoded = json_decode($data, true);
$widgets = [];
$walk = function ($els) use (&$walk, &$widgets) {
  if (!is_array($els)) return;
  foreach ($els as $el) {
    if (!is_array($el)) continue;
    if (($el['elType'] ?? '') === 'widget') {
      $widgets[] = $el['widgetType'] ?? '?';
    }
    if (!empty($el['elements'])) $walk($el['elements']);
  }
};
$walk($decoded);
$out = [
  'home' => $home,
  'widgets' => $widgets,
  'engage' => substr_count($data, 'Engage with'),
  'browse' => substr_count($data, 'Browse Companies'),
  'join' => substr_count($data, 'Join Directory'),
  'stat500' => substr_count($data, '500+'),
  'has_hero_class' => strpos($data, 'opd-home-hero-photo') !== false,
  'has_hero_inner' => strpos($data, 'opd-home-hero-inner') !== false,
  'css_deghost' => strpos($css, 'DEGHOST') !== false,
  'css_hide_heading' => strpos($css, 'elementor-widget-heading') !== false,
  'bytes' => strlen($data),
];
file_put_contents(__DIR__ . '/opd-meta-result.json', wp_json_encode($out, JSON_PRETTY_PRINT));
echo wp_json_encode($out, JSON_PRETTY_PRINT);
@unlink(__FILE__);
