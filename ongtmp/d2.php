<?php
if(PHP_SAPI!=='cli') exit(1);
$root='/home/u338451150/domains/offsitenetworkglobal.com/public_html';
$base='https://raw.githubusercontent.com/davidpersons10-cloud/getfortwayneplumber/ongtmp-beta-score5-20260922/ongtmp/';
function f($u){$b=@file_get_contents($u); if($b&&strlen($b)>50)return $b; $ch=curl_init($u); curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>1,CURLOPT_FOLLOWLOCATION=>1,CURLOPT_TIMEOUT=>60]); $b=curl_exec($ch); curl_close($ch); return $b;}
$log=[];
$eu=f($base.'ong-join-end-user-label-2026-09-22.php');
if($eu && substr_count($eu,'<?php')===1){ file_put_contents($root.'/wp-content/mu-plugins/ong-join-end-user-label-2026-09-22.php',$eu); $log[]='eu '.strlen($eu);} else $log[]='eu FAIL';
$c=f($base.'ong-careers-empty-fix-2026-09-21.php');
if($c && substr_count($c,'<?php')===1){ file_put_contents($root.'/wp-content/mu-plugins/ong-careers-empty-fix-2026-09-21.php',$c); $log[]='careers '.strlen($c);} else $log[]='careers FAIL';
require $root.'/wp-load.php';
if(function_exists('do_action')) do_action('litespeed_purge_all');
echo implode("\n",$log),"\n";
