<?php
$r='/home/u338451150/domains/offsitenetworkglobal.com/public_html';
$u=$r.'/wp-content/uploads';
$out=[];
foreach(glob($u.'/ong-updraft-once-20260922*.json')?:[] as $f){
  $j=json_decode(@file_get_contents($f),true);
  $out[basename($f)]=['ok'=>$j['ok']??null,'nonce'=>$j['nonce']??null,'time'=>$j['backup_time_et']??null,'purpose'=>$j['purpose']??null];
}
$udir=$r.'/wp-content/updraft';
$files=[];
foreach(array_merge(glob($udir.'/backup_*.zip')?:[],glob($udir.'/backup_*.gz')?:[]) as $p){
  $files[]=['n'=>basename($p),'m'=>date('Y-m-d H:i:s T',filemtime($p)),'s'=>filesize($p)];
}
usort($files,fn($a,$b)=>strcmp($b['m'],$a['m']));
$out['newest']=array_slice($files,0,8);
$out['once_head']=substr(@file_get_contents($r.'/ong-cli-updraft-once.php')?:'',0,200);
echo json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),"\n";
