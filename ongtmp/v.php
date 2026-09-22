<?php
if(PHP_SAPI!=='cli') exit(1);
$root='/home/u338451150/domains/offsitenetworkglobal.com/public_html';
require $root.'/wp-load.php';
$tz=new DateTimeZone('America/New_York');
$out=['ts'=>(new DateTime('now',$tz))->format('Y-m-d H:i:s T')];
$checks=[];

// Home
$home=file_get_contents($root.'/wp-content/plugins/ong-home-v3b/ong-home-v3b.php');
$checks['home_ver']= (strpos($home,'Version: 1.0.6')!==false);
$checks['home_no_placeholder']= (strpos($home,'Chapter meetup — placeholder')===false);
$checks['home_chapter_hubs']= (strpos($home,'Chapter hubs')!==false);

// KC
$kc=file_get_contents($root.'/wp-content/plugins/ong-kc-hub/ong-kc-hub.php');
$checks['kc_ver']=(strpos($kc,'Version: 1.0.5')!==false);
$checks['kc_no_sample']=(strpos($kc,'Sample headlines for mock')===false);
$checks['kc_no_hash_meet']=(strpos($kc,'href="#">Meet the council')===false);
$checks['kc_contact_council']=(strpos($kc,'/contact/')!==false && strpos($kc,'council')!==false);

// Careers MU
$checks['careers_mu']=file_exists($root.'/wp-content/mu-plugins/ong-careers-empty-fix-2026-09-21.php');
$checks['eu_mu']=file_exists($root.'/wp-content/mu-plugins/ong-join-end-user-label-2026-09-22.php');

// MCC
$mcc=$root.'/wp-content/plugins/modular-construction-calculator';
$checks['mcc_plugin']=file_exists($mcc.'/modular-construction-calculator.php');
$checks['mcc_dist_js']=file_exists($mcc.'/assets/dist/mcc-app.js');
$checks['mcc_shortcode_pe']=file_exists($mcc.'/includes/class-mcc-shortcode.php') && strpos(file_get_contents($mcc.'/includes/class-mcc-shortcode.php'),'Project inputs')!==false;
$checks['mcc_active']=function_exists('is_plugin_active') ? is_plugin_active('modular-construction-calculator/modular-construction-calculator.php') : null;
if(!function_exists('is_plugin_active')){ require_once ABSPATH.'wp-admin/includes/plugin.php'; $checks['mcc_active']=is_plugin_active('modular-construction-calculator/modular-construction-calculator.php'); }

// HTTP pages
foreach(['/tools/calculator/','/join/','/','/media/','/careers/','/jobs/'] as $path){
  $req=wp_remote_get(home_url($path),['timeout'=>25,'sslverify'=>false,'headers'=>['Cache-Control'=>'no-cache']]);
  if(is_wp_error($req)){ $checks['http'][$path]=['err'=>$req->get_error_message()]; continue; }
  $body=wp_remote_retrieve_body($req);
  $row=['code'=>wp_remote_retrieve_response_code($req),'len'=>strlen($body)];
  if($path==='/tools/calculator/'){
    $row['has_inputs']= (bool)preg_match('/<(input|select)\b/i',$body);
    $row['has_results']= (stripos($body,'Results')!==false || stripos($body,'mcc-results')!==false);
    $row['has_mcc_host']= (strpos($body,'data-mcc-root')!==false || strpos($body,'mcc-app-host')!==false);
    $row['has_project_inputs']= (stripos($body,'Project inputs')!==false);
  }
  if($path==='/join/'){
    $row['has_end_user']= (stripos($body,'End User')!==false);
    $row['eu_before_250']= (bool)preg_match('/End User[\s\S]{0,400}?\$\s*250/i',$body);
    preg_match_all('/ong-plan-card[^>]*data-sku="([^"]+)"[\s\S]*?<h3[^>]*>(.*?)<\/h3>/i',$body,$mm);
    $row['plan_titles']=[];
    if(!empty($mm[1])){ foreach($mm[1] as $i=>$sku){ $row['plan_titles'][$sku]=trim(html_entity_decode(strip_tags($mm[2][$i]))); } }
  }
  if($path==='/'){
    $row['no_chapter_placeholder']=(stripos($body,'Chapter meetup — placeholder')===false);
    $row['has_chapter_hubs']=(stripos($body,'Chapter hubs')!==false);
  }
  if($path==='/media/'){
    $row['no_sample_mock']=(stripos($body,'Sample headlines for mock')===false);
    $row['no_meet_hash']=(strpos($body,'href="#">Meet the council')===false && strpos($body,"href='#'>Meet the council")===false);
    $row['has_contact_council']=(stripos($body,'Contact the council')!==false || (stripos($body,'/contact/')!==false && stripos($body,'council')!==false));
  }
  if($path==='/careers/' || $path==='/jobs/'){
    $row['has_jobs']=(substr_count($body,'ong-jb-card')>0 || stripos($body,'Modular Plant Manager')!==false);
    $row['empty_is_visible']=(bool)preg_match('/id="ong-c-empty"[^>]*class="[^"]*is-visible/i',$body) || (bool)preg_match('/class="[^"]*filter-empty[^"]*is-visible/i',$body);
    $row['has_empty_fix_js']=(strpos($body,'ong-careers-empty-fix-20260921-js')!==false);
    $row['empty_text_present']=(stripos($body,'No jobs match your filters')!==false); // may be in DOM hidden
  }
  $checks['http'][$path]=$row;
}

$out['checks']=$checks;
$out['pass']= !in_array(false,[
  $checks['home_ver'],$checks['home_no_placeholder'],$checks['kc_ver'],$checks['kc_no_sample'],
  $checks['careers_mu'],$checks['eu_mu'],$checks['mcc_dist_js'],$checks['mcc_shortcode_pe'],
  $checks['http']['/tools/calculator/']['has_project_inputs']??false,
  $checks['http']['/join/']['has_end_user']??false,
  $checks['http']['/']['no_chapter_placeholder']??false,
  $checks['http']['/media/']['no_sample_mock']??false,
],true);
file_put_contents($root.'/wp-content/uploads/ong-beta-verify-20260922i.json',json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),"\n";
