<?php
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');
$home = (int) get_option('page_on_front');
$att = (int) get_option('opd_match_hero_attachment_id');
if (!$att) { $att = 31; }
$path = get_attached_file($att);
$sz = $path && is_readable($path) ? (int) filesize($path) : null;
$wf = null; $w = null; $h = null;
if ($path && is_readable($path) && function_exists('imagecreatefromjpeg')) {
  $im = @imagecreatefromjpeg($path);
  if ($im) {
    $w = imagesx($im); $h = imagesy($im);
    $white = 0; $n = 0;
    for ($y = 0; $y < $h; $y += 6) {
      for ($x = 0; $x < $w; $x += 6) {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 255; $g = ($rgb >> 8) & 255; $b = $rgb & 255;
        $n++;
        if ($r > 240 && $g > 240 && $b > 240) { $white++; }
      }
    }
    $wf = $n ? round($white / $n, 5) : null;
    imagedestroy($im);
  }
}
$data = (string) get_post_meta($home, '_elementor_data', true);
$widgets = [];
$walk = function ($els) use (&$walk, &$widgets) {
  if (!is_array($els)) return;
  foreach ($els as $el) {
    if (!is_array($el)) continue;
    if (($el['elType'] ?? '') === 'widget') { $widgets[] = $el['widgetType'] ?? '?'; }
    if (!empty($el['elements'])) $walk($el['elements']);
  }
};
$walk(json_decode($data, true));
$counts = array_count_values($widgets);
$out = [
  'ok' => true,
  'home' => $home,
  'att' => $att,
  'path' => $path,
  'bytes' => $sz,
  'dims' => [$w, $h],
  'white_frac' => $wf,
  'widgets' => $widgets,
  'widget_counts' => $counts,
  'engage' => substr_count($data, 'Engage with'),
  'browse' => substr_count($data, 'Browse Companies'),
  'join' => substr_count($data, 'Join Directory') + substr_count($data, 'Join the Directory'),
  'stat500' => substr_count($data, '500+'),
  'has_hero_inner' => strpos($data, 'opd-home-hero-inner') !== false,
  'heading_widgets' => (int) ($counts['heading'] ?? 0),
  'button_widgets' => (int) ($counts['button'] ?? 0),
  'html_widgets' => (int) ($counts['html'] ?? 0),
];
echo wp_json_encode($out, JSON_PRETTY_PRINT);
@unlink(__FILE__);
