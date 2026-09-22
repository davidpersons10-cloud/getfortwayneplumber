<?php
require '/home/u338451150/domains/offsitenetworkglobal.com/public_html/wp-load.php';
$h=do_shortcode('[ong_join]');
$req=wp_remote_get(home_url('/join/'),['timeout'=>20,'sslverify'=>false,'headers'=>['Cache-Control'=>'no-cache','Pragma'=>'no-cache']]);
$body=is_wp_error($req)?'ERR':wp_remote_retrieve_body($req);
$out=[
 'sc_eu'=>stripos($h,'End User')!==false,
 'http_eu'=>stripos($body,'End User')!==false,
 'http_eu_before_250'=>(bool)preg_match('/End User[\s\S]{0,500}?\$\s*250/i',$body),
 'sc_snip'=>null,
 'http_snip'=>null,
];
if(preg_match('/data-sku="member_eu"[\s\S]{0,400}/i',$h,$m))$out['sc_snip']=$m[0];
if(preg_match('/data-sku="member_eu"[\s\S]{0,400}/i',$body,$m))$out['http_snip']=$m[0];
file_put_contents(WP_CONTENT_DIR.'/uploads/ong-eu-check-20260922.json',json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),"\n";
