<?php
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');
$att = (int) get_option('opd_match_hero_attachment_id');
if (!$att) { $att = 31; }
$path = get_attached_file($att);
$meta = wp_generate_attachment_metadata($att, $path);
if (is_array($meta)) {
  wp_update_attachment_metadata($att, $meta);
}
$dir = dirname($path);
$out = ['ok' => true, 'att' => $att, 'regenerated' => !empty($meta), 'files' => []];
foreach (glob($dir . '/opd-hero-modular*.jpg') as $f) {
  $wf = null; $w = $h = null; $sz = filesize($f);
  if (function_exists('imagecreatefromjpeg')) {
    $im = @imagecreatefromjpeg($f);
    if ($im) {
      $w = imagesx($im); $h = imagesy($im);
      $white = 0; $n = 0;
      for ($y = 0; $y < $h; $y += 8) {
        for ($x = 0; $x < $w; $x += 8) {
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
  $out['files'][basename($f)] = ['bytes' => $sz, 'dims' => [$w, $h], 'white_frac' => $wf];
}
if (function_exists('elementor_flush_css') || class_exists('\\Elementor\\Plugin')) {
  try {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
  } catch (Throwable $e) {}
}
if (function_exists('opcache_reset')) { @opcache_reset(); }
echo wp_json_encode($out, JSON_PRETTY_PRINT);
@unlink(__FILE__);
