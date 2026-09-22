<?php
if (PHP_SAPI !== 'cli') exit(1);
$r='/home/u338451150/domains/offsitenetworkglobal.com/public_html';
$out=[];
foreach(['ong-updraft-once-20260922h.json','ong-updraft-once-20260922i.json','ong-updraft-once-20260922g.json','ong-updraft-once-20260922f.json'] as $n){
  $f=$r.'/wp-content/uploads/'.$n;
  $out[$n]=file_exists($f)?json_decode(file_get_contents($f),true):null;
}
$udir=$r.'/wp-content/updraft';
$files=[];
if(is_dir($udir)){
  foreach(array_merge(glob($udir.'/backup_*.zip')?:[],glob($udir.'/backup_*.gz')?:[],glob($udir.'/log.*.txt')?:[]) as $p){
    $files[]=['n'=>basename($p),'m'=>filemtime($p),'s'=>filesize($p)];
  }
  usort($files,fn($a,$b)=>$b['m']<=>$a['m']);
}
$out['newest']=array_slice($files,0,15);
$out['lock']=file_exists($r.'/wp-content/uploads/ong-updraft-once.lock');
echo json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),"\n";
