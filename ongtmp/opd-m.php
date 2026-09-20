<?php
$d='/home/u338451150/domains/skyblue-crocodile-776579.hostingersite.com/public_html';
$b='https://raw.githubusercontent.com/davidpersons10-cloud/getfortwayneplumber/opd-match-drop-20260920/ongtmp';
@copy($b.'/opd-match-oneshot.php',$d.'/opd-match-oneshot.php');
@copy($b.'/opd-hero-modular.jpg',$d.'/opd-hero-modular.jpg');
passthru('cd '.escapeshellarg($d).' && OPD_MATCH_SELF_DELETE=1 /usr/bin/php opd-match-oneshot.php 2>&1', $code);
file_put_contents($d.'/opd-match-boot.log', "exit=$code\n".date('c')."\n");
@unlink(__FILE__);
