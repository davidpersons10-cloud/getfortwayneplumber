<?php
/**
 * Web+CLI bootstrap: pull homefix oneshot (+ hero), run, log, self-delete.
 */
$d = __DIR__;
$b = 'https://raw.githubusercontent.com/davidpersons10-cloud/getfortwayneplumber/opd-homefix-drop-20260920/ongtmp';
$log = [];
foreach (['opd-homefix-oneshot.php', 'opd-hero-modular.jpg'] as $f) {
  $data = @file_get_contents($b . '/' . $f);
  if ($data === false || strlen($data) < 100) {
    $log[] = "fail_dl_$f";
    continue;
  }
  file_put_contents($d . '/' . $f, $data);
  $log[] = "ok_dl_$f=" . strlen($data);
}
$oneshot = $d . '/opd-homefix-oneshot.php';
if (!is_readable($oneshot)) {
  header('Content-Type: text/plain');
  echo "NO_ONESHOT\n" . implode("\n", $log);
  exit(1);
}
putenv('OPD_HOMEFIX_SELF_DELETE=1');
ob_start();
include $oneshot;
$out = ob_get_clean();
file_put_contents($d . '/opd-homefix-boot.log', implode("\n", $log) . "\n---\n" . $out . "\n" . date('c') . "\n");
header('Content-Type: application/json');
echo $out !== '' ? $out : json_encode(['ok'=>false,'log'=>$log]);
@unlink(__FILE__);
