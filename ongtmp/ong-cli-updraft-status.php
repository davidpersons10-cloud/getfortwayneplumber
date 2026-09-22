<?php
if (PHP_SAPI !== 'cli') exit(1);
$r='/home/u338451150/domains/offsitenetworkglobal.com/public_html';
$f=$r.'/wp-content/uploads/ong-updraft-once-20260922h.json';
$l=$r.'/wp-content/uploads/ong-updraft-cli-log.txt';
$lock=$r.'/wp-content/uploads/ong-updraft-once.lock';
$out=['exists'=>file_exists($f),'lock'=>file_exists($lock),'log_tail'=>null,'json'=>null];
if(file_exists($l)){$out['log_tail']=implode('',array_slice(file($l),-40));}
if(file_exists($f))$out['json']=json_decode(file_get_contents($f),true);
$udir=$r.'/wp-content/updraft';
$files=[];
if(is_dir($udir)){
  foreach(array_merge(glob($udir.'/backup_*.zip')?:[],glob($udir.'/backup_*.gz')?:[],glob($udir.'/log.*.txt')?:[]) as $p){
    $files[]=['n'=>basename($p),'m'=>filemtime($p),'s'=>filesize($p)];
  }
  usort($files,fn($a,$b)=>$b['m']<=>$a['m']);
}
$out['newest']=array_slice($files,0,20);
echo json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)."\n";
