<?php
$d='/home/u338451150/domains/offsitenetworkglobal.com/public_html/';
$rm=['ong-cli-updraft-once.php','ong-cli-updraft-status.php','b-backup.php','c-backup.php'];
$log=[];
foreach($rm as $f){ $p=$d.$f; if(file_exists($p)){ @unlink($p); $log[]="rm $f"; } else { $log[]="miss $f"; } }
echo implode("\n",$log),"\n";
