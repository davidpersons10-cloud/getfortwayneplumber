<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
require __DIR__ . '/wp-load.php';
header('Content-Type: application/json');
try {
  $att = (int) get_option('opd_match_hero_attachment_id');
  if (!$att) { $att = 31; }
  $path = get_attached_file($att);
  $regenerated = false;
  if ($path && is_readable($path)) {
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $meta = wp_generate_attachment_metadata($att, $path);
    if (is_array($meta)) {
      wp_update_attachment_metadata($att, $meta);
      $regenerated = true;
    }
  }
  $dir = $path ? dirname($path) : '';
  $out = ['ok' => true, 'att' => $att, 'regenerated' => $regenerated, 'files' => []];
  if ($dir) {
    foreach (glob($dir . '/opd-hero-modular*.jpg') ?: [] as $f) {
      $wf = null; $w = $h = null; $sz = @filesize($f);
      if (function_exists('imagecreatefromjpeg')) {
        $im = @imagecreatefromjpeg($f);
        if ($im) {
          $w = imagesx($im); $h = imagesy($im);
          $white = 0; $n = 0;
          for ($y = 0; $y < $h; $y += 10) {
            for ($x = 0; $x < $w; $x += 10) {
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
  }
  echo wp_json_encode($out, JSON_PRETTY_PRINT);
} catch (Throwable $e) {
  echo wp_json_encode(['ok' => false, 'err' => $e->getMessage()]);
}
@unlink(__FILE__);
