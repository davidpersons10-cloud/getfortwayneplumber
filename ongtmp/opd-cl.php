<?php
header('Content-Type: application/json');
$root = __DIR__;
$removed = [];
foreach (glob($root . '/opd-*.php') as $f) {
  if (basename($f) === 'opd-cl.php') continue;
  if (@unlink($f)) $removed[] = basename($f);
}
foreach (['opd-meta-result.json','opd-dump.json'] as $j) {
  $p = $root . '/' . $j;
  if (is_file($p) && @unlink($p)) $removed[] = $j;
}
echo json_encode(['ok' => true, 'removed' => $removed]);
@unlink(__FILE__);
