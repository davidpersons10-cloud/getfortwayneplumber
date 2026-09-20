<?php
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');
$att = (int) get_option('opd_match_hero_attachment_id');
if (!$att) { $att = 31; }
$path = get_attached_file($att);
$dir = $path ? dirname($path) : '';
$out = ['ok' => true, 'att' => $att, 'full_bytes' => ($path && is_readable($path)) ? filesize($path) : null, 'files' => []];
if ($dir && is_dir($dir)) {
  foreach (glob($dir . '/opd-hero-modular*.jpg') ?: [] as $f) {
    $wf = null; $w = $h = null;
    if (function_exists('imagecreatefromjpeg')) {
      $im = @imagecreatefromjpeg($f);
      if ($im) {
        $w = imagesx($im); $h = imagesy($im);
        $white = 0; $n = 0;
        $step = max(8, (int) ceil(max($w, $h) / 200));
        for ($y = 0; $y < $h; $y += $step) {
          for ($x = 0; $x < $w; $x += $step) {
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
    $out['files'][basename($f)] = ['bytes' => filesize($f), 'dims' => [$w, $h], 'white_frac' => $wf];
  }
}
echo wp_json_encode($out, JSON_PRETTY_PRINT);
@unlink(__FILE__);
