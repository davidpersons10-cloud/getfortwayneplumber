<?php
$d=__DIR__;
$b='https://raw.githubusercontent.com/davidpersons10-cloud/getfortwayneplumber/opd-homefix-drop-20260920/ongtmp';
$data=@file_get_contents($b.'/opd-deghost.php');
if($data===false||strlen($data)<200){header('Content-Type:text/plain');echo "FAIL_DL\n";exit(1);}
file_put_contents($d.'/opd-deghost.php',$data);
putenv('OPD_DEGHOST_SELF_DELETE=1');
ob_start();include $d.'/opd-deghost.php';$out=ob_get_clean();
file_put_contents($d.'/opd-deghost-boot.log',$out."\n".date('c')."\n");
header('Content-Type: application/json');echo $out!==''?$out:json_encode(['ok'=>false]);
@unlink(__FILE__);
