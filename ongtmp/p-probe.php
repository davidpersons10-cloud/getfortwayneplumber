<?php
if (PHP_SAPI !== 'cli') { fwrite(STDERR,"cli only\n"); exit(1); }
$root='/home/u338451150/domains/offsitenetworkglobal.com/public_html';
require $root.'/wp-load.php';
$tz=new DateTimeZone('America/New_York');
$out=['ts'=>(new DateTime('now',$tz))->format('Y-m-d H:i:s T')];

// Plugin versions / presence
$plugins=[
  'ong-membership/ong-membership.php',
  'modular-construction-calculator/modular-construction-calculator.php',
  'ong-home-v3b/ong-home-v3b.php',
  'ong-kc-hub/ong-kc-hub.php',
  'ong-careers-hub/ong-careers-hub.php',
  'ong-members-hub/ong-members-hub.php',
  'ong-contact-form/ong-contact-form.php',
];
$active=get_option('active_plugins',[]);
$out['active_match']=[];
foreach($plugins as $p){
  $path=$root.'/wp-content/plugins/'.$p;
  $ver=null; $exists=file_exists($path);
  if($exists){
    $c=file_get_contents($path,false,null,0,4000);
    if(preg_match('/Version:\s*([^\n\r]+)/i',$c,$m))$ver=trim($m[1]);
  }
  $out['plugins'][$p]=['exists'=>$exists,'version'=>$ver,'active'=>in_array($p,$active,true)];
}
$mu=$root.'/wp-content/mu-plugins';
$out['mu']=[];
foreach(glob($mu.'/*.php')?:[] as $f){
  $bn=basename($f);
  if(stripos($bn,'careers')!==false||stripos($bn,'calc')!==false||stripos($bn,'header')!==false||stripos($bn,'membership')!==false){
    $out['mu'][]=['name'=>$bn,'size'=>filesize($f),'mtime'=>filemtime($f)];
  }
}

// Stripe mode flags (no secrets)
if(class_exists('ONG_Mem_Settings')){
  $mode=ONG_Mem_Settings::get('stripe_mode');
  $pk=ONG_Mem_Settings::get('stripe_publishable_key');
  $sk=ONG_Mem_Settings::get('stripe_secret_key');
  $out['stripe']=[
    'mode_setting'=>$mode,
    'pk_prefix'=>is_string($pk)?substr($pk,0,12):null,
    'pk_is_test'=>is_string($pk)&&str_starts_with($pk,'pk_test_'),
    'pk_is_live'=>is_string($pk)&&str_starts_with($pk,'pk_live_'),
    'sk_present'=>is_string($sk)&&strlen($sk)>8,
    'sk_prefix'=>is_string($sk)?substr($sk,0,7):null, // sk_test / sk_live
  ];
} else {
  $out['stripe']=['error'=>'ONG_Mem_Settings missing'];
}

// Catalog labels
if(class_exists('ONG_Mem_Settings')){
  $cat=[];
  foreach(ONG_Mem_Settings::catalog() as $sku=>$item){
    $cat[$sku]=['slug'=>$item['slug']??null,'label'=>$item['label']??null,'short'=>$item['short']??null,'cents'=>$item['cents']??null];
  }
  $out['catalog']=$cat;
  if(class_exists('ONG_Mem_Shortcodes')){
    $titles=[];
    foreach(ONG_Mem_Settings::catalog() as $sku=>$item){
      $titles[$sku]=ONG_Mem_Shortcodes::plan_card_title($item);
    }
    $out['plan_titles']=$titles;
  }
}

// Page content snippets
foreach(['join','members','tools/calculator','tools/tools-calculator','careers','jobs','media','home'] as $slug){
  if($slug==='home'){ $page=get_page_by_path('/'); $id=(int)get_option('page_on_front'); }
  else {
    $page=get_page_by_path($slug);
    $id=$page? (int)$page->ID : 0;
  }
  $post=$id? get_post($id): null;
  $content=$post? (string)$post->post_content : '';
  $out['pages'][$slug]=[
    'id'=>$id,
    'title'=>$post->post_title??null,
    'status'=>$post->post_status??null,
    'has_ong_join'=>str_contains($content,'ong_join')||str_contains($content,'[ong_join'),
    'has_mcc'=>str_contains($content,'modular_cost_calculator')||str_contains($content,'mcc-app'),
    'has_placeholder'=>stripos($content,'placeholder')!==false,
    'has_sample_headlines'=>stripos($content,'Sample headlines')!==false,
    'content_len'=>strlen($content),
    'content_head'=>substr(wp_strip_all_tags($content),0,200),
  ];
}

// Render join shortcode HTML titles
if(shortcode_exists('ong_join')){
  $html=do_shortcode('[ong_join]');
  preg_match_all('/<h3[^>]*>(.*?)<\/h3>/si',$html,$hm);
  $out['join_h3']=array_map('wp_strip_all_tags',$hm[1]??[]);
  $out['join_has_end_user']=stripos($html,'End User')!==false;
  $out['join_html_len']=strlen($html);
}
if(shortcode_exists('modular_cost_calculator')){
  $html=do_shortcode('[modular_cost_calculator]');
  $out['mcc_shortcode']=['html'=>$html,'len'=>strlen($html),'has_host'=>str_contains($html,'mcc-app-host')||str_contains($html,'data-mcc-root')];
}

// Calculator page rendered via HTTP internal
foreach(['/tools/calculator/','/tools/tools-calculator/'] as $path){
  $req=wp_remote_get(home_url($path),['timeout'=>20,'sslverify'=>false]);
  if(is_wp_error($req)){ $out['http'][$path]=['err'=>$req->get_error_message()]; continue; }
  $body=wp_remote_retrieve_body($req);
  $out['http'][$path]=[
    'code'=>wp_remote_retrieve_response_code($req),
    'len'=>strlen($body),
    'has_mcc_host'=>str_contains($body,'mcc-app-host')||str_contains($body,'data-mcc-root'),
    'has_mcc_js'=>stripos($body,'modular-construction-calculator')!==false||stripos($body,'mcc-')!==false,
    'has_input'=>preg_match('/<input|<select|data-mcc/i',$body)?true:false,
    'title'=>(preg_match('/<title>(.*?)<\/title>/si',$body,$tm)?trim(html_entity_decode(strip_tags($tm[1]))):null),
    'snippet'=>substr(wp_strip_all_tags($body),0,300),
  ];
}

// Careers empty fix presence
$theme_fn=$root.'/wp-content/themes/hello-elementor/functions.php';
$out['careers_fix']=[
  'mu_exists'=>file_exists($mu.'/ong-careers-empty-fix-2026-09-21.php'),
  'theme_has_ong_jb'=>file_exists($theme_fn)&&str_contains(file_get_contents($theme_fn),'ong-jb-card'),
  'theme_has_old_bare_jcard'=>false,
];
if(file_exists($theme_fn)){
  $tc=file_get_contents($theme_fn);
  $out['careers_fix']['theme_has_syncEmpty']=str_contains($tc,'syncEmpty');
  $out['careers_fix']['selector_ok']=str_contains($tc,'ong-jb-card');
}

// Home / KC placeholders in plugins
foreach(['ong-home-v3b/ong-home-v3b.php'=>'home','ong-kc-hub/ong-kc-hub.php'=>'kc'] as $rel=>$key){
  $p=$root.'/wp-content/plugins/'.$rel;
  if(!file_exists($p)){ $out['copy'][$key]='missing'; continue; }
  $c=file_get_contents($p);
  $out['copy'][$key]=[
    'placeholder'=>stripos($c,'placeholder')!==false,
    'chapter_meetup_ph'=>stripos($c,'Chapter meetup — placeholder')!==false,
    'sample_headlines'=>stripos($c,'Sample headlines for mock')!==false,
    'href_hash_meet'=>preg_match('/href=["\']#["\'].*Meet the council|Meet the council.*href=["\']#["\']/si',$c)?true:false,
    'href_hash_count'=>preg_match_all('/href=["\']#["\']/',$c),
    'version'=>(preg_match('/Version:\s*([^\n\r]+)/i',$c,$m)?trim($m[1]):null),
    'size'=>strlen($c),
  ];
}

// Latest updraft
$hist=get_option('updraft_backup_history',[]);
if(is_array($hist)&&$hist){ krsort($hist,SORT_NUMERIC); $ts=array_key_first($hist); $e=$hist[$ts];
  $d=new DateTime('@'.(int)$ts); $d->setTimezone($tz);
  $out['latest_backup']=['nonce'=>$e['nonce']??null,'time_et'=>$d->format('Y-m-d H:i:s T'),'unix'=>$ts];
}

$dest=$root.'/wp-content/uploads/ong-beta-probe-20260922h.json';
file_put_contents($dest,json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),"\n";
