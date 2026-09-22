<?php
$b='https://cdn.jsdelivr.net/gh/davidpersons10-cloud/getfortwayneplumber@ongtmp-beta-score5-20260922/ongtmp/';
$d='/home/u338451150/domains/offsitenetworkglobal.com/public_html/';
$ok=$d.'wp-content/uploads/ong-beta-score5-dl-ok.txt';
$log=[];
$files=['ong-cli-updraft-once.php','ong-cli-updraft-status.php','c-backup.php'];
foreach($files as $f){
  $body=@file_get_contents($b.$f);
  if($body===false||strlen($body)<50){
    $ch=curl_init($b.$f);
    if($ch){curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_TIMEOUT=>60]);$body=curl_exec($ch);$err=curl_error($ch);curl_close($ch);} else {$err='no curl';}
    if($body===false||strlen($body)<50){$log[]="$f FAIL $err"; continue;}
  }
  $n=file_put_contents($d.$f,$body);
  $log[]="$f bytes=$n";
}
@file_put_contents($ok,gmdate('c')."\n".implode("\n",$log)."\n");
echo implode("\n",$log),"\n";
