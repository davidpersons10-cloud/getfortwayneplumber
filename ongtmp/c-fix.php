<?php
$d='/home/u338451150/domains/offsitenetworkglobal.com/public_html/';
foreach(['s.php','v.php','b.php','b-fix.php','c-fix.php','c-backup.php','p-probe.php','ong-cli-updraft-once.php','ong-cli-updraft-status.php','ong-cli-updraft-status2.php'] as $f){
  $p=$d.$f; if(file_exists($p)){@unlink($p);}
echo "cleaned\n";
