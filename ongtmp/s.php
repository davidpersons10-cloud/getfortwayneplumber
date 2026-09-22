<?php
/**
 * Install beta score5 fixes. CLI only.
 */
if (PHP_SAPI !== 'cli') { fwrite(STDERR,"cli only\n"); exit(1); }
$root='/home/u338451150/domains/offsitenetworkglobal.com/public_html';
$cdn='https://cdn.jsdelivr.net/gh/davidpersons10-cloud/getfortwayneplumber@ongtmp-beta-score5-20260922/ongtmp/';
$tz=new DateTimeZone('America/New_York');
$out=['ts'=>(new DateTime('now',$tz))->format('Y-m-d H:i:s T'),'ok'=>false,'steps'=>[]];

function ong_fetch($url){
  $b=@file_get_contents($url);
  if($b!==false&&strlen($b)>20) return $b;
  $ch=curl_init($url);
  curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_TIMEOUT=>90,CURLOPT_USERAGENT=>'ONG-Beta-Fix/1.0']);
  $b=curl_exec($ch); curl_close($ch);
  return ($b!==false&&strlen($b)>20)?$b:false;
}
function ong_put($path,$body){
  $dir=dirname($path);
  if(!is_dir($dir)) @mkdir($dir,0755,true);
  return file_put_contents($path,$body);
}

// 1) Home hub
$home=$root.'/wp-content/plugins/ong-home-v3b/ong-home-v3b.php';
$b=ong_fetch($cdn.'ong-home-v3b.php');
if($b===false){ $out['steps'][]='home FAIL fetch'; }
else {
  $opens=substr_count($b,'<?php');
  if($opens<1||strpos($b,'Chapter meetup — placeholder')!==false||strpos($b,'Version: 1.0.6')===false){
    $out['steps'][]="home REJECT opens=$opens";
  } else {
    @copy($home,$home.'.bak-20260922i');
    ong_put($home,$b);
    $out['steps'][]='home OK '.strlen($b);
  }
}

// 2) KC hub
$kc=$root.'/wp-content/plugins/ong-kc-hub/ong-kc-hub.php';
$b=ong_fetch($cdn.'ong-kc-hub.php');
if($b===false){ $out['steps'][]='kc FAIL fetch'; }
else {
  if(strpos($b,'Sample headlines for mock')!==false || strpos($b,'href="#">Meet the council')!==false || strpos($b,'Version: 1.0.5')===false){
    $out['steps'][]='kc REJECT content';
  } else {
    @copy($kc,$kc.'.bak-20260922i');
    ong_put($kc,$b);
    $out['steps'][]='kc OK '.strlen($b);
  }
}

// 3) Careers empty MU
$mu=$root.'/wp-content/mu-plugins/ong-careers-empty-fix-2026-09-21.php';
$b=ong_fetch($cdn.'ong-careers-empty-fix-2026-09-21.php');
if($b===false){ $out['steps'][]='careers-mu FAIL fetch'; }
else { ong_put($mu,$b); $out['steps'][]='careers-mu OK '.strlen($b); }

// 4) Join End User MU
$mu2=$root.'/wp-content/mu-plugins/ong-join-end-user-label-2026-09-22.php';
$b=ong_fetch($cdn.'ong-join-end-user-label-2026-09-22.php');
if($b===false){ $out['steps'][]='eu-mu FAIL fetch'; }
else {
  if(substr_count($b,'<?php')!==1){ $out['steps'][]='eu-mu REJECT opens'; }
  else { ong_put($mu2,$b); $out['steps'][]='eu-mu OK '.strlen($b); }
}

// 5) Calculator plugin files (shortcode + main + dist + rates)
$mcc=$root.'/wp-content/plugins/modular-construction-calculator';
@mkdir($mcc,0755,true);
@mkdir($mcc.'/includes',0755,true);
@mkdir($mcc.'/assets/dist',0755,true);
@mkdir($mcc.'/assets/css',0755,true);
@mkdir($mcc.'/rates',0755,true);
$mcc_files=[
  'mcc/modular-construction-calculator.php'=>'modular-construction-calculator.php',
  'mcc/includes/class-mcc-shortcode.php'=>'includes/class-mcc-shortcode.php',
  'mcc/includes/class-mcc-assets.php'=>'includes/class-mcc-assets.php',
  'mcc/includes/class-mcc-plugin.php'=>'includes/class-mcc-plugin.php',
  'mcc/includes/class-mcc-rates.php'=>'includes/class-mcc-rates.php',
  'mcc/includes/class-mcc-block.php'=>'includes/class-mcc-block.php',
  'mcc/includes/class-mcc-settings.php'=>'includes/class-mcc-settings.php',
  'mcc/includes/class-mcc-leads.php'=>'includes/class-mcc-leads.php',
  'mcc/includes/class-mcc-cron.php'=>'includes/class-mcc-cron.php',
  'mcc/assets/dist/mcc-app.js'=>'assets/dist/mcc-app.js',
  'mcc/assets/dist/mcc-app.css'=>'assets/dist/mcc-app.css',
  'mcc/assets/css/mcc-print.css'=>'assets/css/mcc-print.css',
];
// rates files listed dynamically after fetch attempt of known
foreach(['2026.08.json','index.php'] as $rf){
  $mcc_files['mcc/rates/'.$rf]='rates/'.$rf;
}
$mcc_ok=0; $mcc_fail=[];
foreach($mcc_files as $cdn_path=>$rel){
  $body=ong_fetch($cdn.$cdn_path);
  if($body===false){ $mcc_fail[]=$rel; continue; }
  ong_put($mcc.'/'.$rel,$body); $mcc_ok++;
}
$out['steps'][]="mcc files ok=$mcc_ok fail=".implode(',',$mcc_fail);

// Activate calculator plugin + ensure shortcode on calculator pages
require $root.'/wp-load.php';
$plugin='modular-construction-calculator/modular-construction-calculator.php';
if(!function_exists('activate_plugin')) require_once ABSPATH.'wp-admin/includes/plugin.php';
if(!is_plugin_active($plugin)){
  $r=activate_plugin($plugin);
  $out['steps'][]='mcc activate '.(is_wp_error($r)?$r->get_error_message():'OK');
} else {
  $out['steps'][]='mcc already active';
}
// Ensure home/kc active
foreach(['ong-home-v3b/ong-home-v3b.php','ong-kc-hub/ong-kc-hub.php','ong-careers-hub/ong-careers-hub.php'] as $p){
  if(file_exists($root.'/wp-content/plugins/'.$p) && !is_plugin_active($p)){
    $r=activate_plugin($p);
    $out['steps'][]="activate $p ".(is_wp_error($r)?$r->get_error_message():'OK');
  }
}

// Put shortcode on calculator pages if missing
foreach(['tools/calculator','tools/tools-calculator','calculator','modular-calculator'] as $slug){
  $page=get_page_by_path($slug);
  if(!$page){ $out['pages'][$slug]='missing'; continue; }
  $c=(string)$page->post_content;
  $out['pages'][$slug]=['id'=>$page->ID,'has_shortcode'=>str_contains($c,'modular_cost_calculator'),'len'=>strlen($c)];
  if(!str_contains($c,'modular_cost_calculator')){
    $new = trim($c)==='' ? '[modular_cost_calculator]' : ($c."\n\n[modular_cost_calculator]");
    wp_update_post(['ID'=>$page->ID,'post_content'=>$new]);
    $out['pages'][$slug]['updated']=true;
  }
}

// Soft redirect: if /tools/calculator has title-only Elementor empty, still has shortcode now.
if(function_exists('do_action')){
  do_action('litespeed_purge_all');
}
$out['ok']=true;
foreach($out['steps'] as $s){ if(str_contains($s,'FAIL')||str_contains($s,'REJECT')) $out['ok']=false; }
$dest=$root.'/wp-content/uploads/ong-beta-install-20260922i.json';
file_put_contents($dest,json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),"\n";
